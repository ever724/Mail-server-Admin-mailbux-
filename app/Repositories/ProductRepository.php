<?php

namespace App\Repositories;

use App\Interfaces\ProductInterface;
use App\Models\Product;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class ProductRepository implements ProductInterface
{
    /**
     * Return paginated and filtered results of products by company.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Product
     */
    public function getPaginatedFilteredProducts(Request $request)
    {
        return QueryBuilder::for(Product::findByCompany($request->currentCompany->id))
            ->where('hide', false)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::exact('unit_id'),
            ])
            ->allowedIncludes([
                'company',
                'unit',
                'taxes',
            ])
            ->oldest()
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param mixed $product_id
     *
     * @return \App\Models\Product
     */
    public function getProductById(Request $request, $product_id)
    {
        return Product::with(['company', 'unit', 'taxes'])->findByCompany($request->currentCompany->id)->findOrFail($product_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\Product
     */
    public function newProduct(Request $request)
    {
        $product = new Product();

        // Fill model with old input
        if (!empty($request->old())) {
            $product->fill($request->old());
        }

        return $product;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Product
     */
    public function createProduct(Request $request)
    {
        $company = $request->currentCompany;

        // Create Product and Store in Database
        $product = Product::create([
            'name' => $request->name,
            'company_id' => $company->id,
            'unit_id' => $request->unit_id,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        // Add custom field values
        $product->addCustomFields($request->custom_fields);

        // Add Product Taxes
        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                $product->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Record product
        //$company->subscription('main')->recordFeatureUsage('products');

        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $product_id
     *
     * @return \App\Models\Product
     */
    public function updateProduct(Request $request, $product_id)
    {
        $product = $this->getProductById($request, $product_id);

        // Update the Expense
        $product->update([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        // Update custom field values
        $product->updateCustomFields($request->custom_fields);

        // Remove old Product Taxes
        $product->taxes()->delete();

        // Update Product Taxes
        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                $product->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $product_id
     *
     * @return bool
     */
    public function deleteProduct(Request $request, $product_id)
    {
        $product = $this->getProductById($request, $product_id);

        // If the product already in use in Invoice Items
        // then return back and flash an alert message
        if ($product->invoice_items()->exists() && $product->invoice_items()->count() > 0) {
            session()->flash('alert-success', __('messages.product_cant_deleted_invoice'));

            return false;
        }

        // If the product already in use in Estimate Items
        // then return back and flash an alert message
        if ($product->estimate_items()->exists() && $product->estimate_items()->count() > 0) {
            session()->flash('alert-success', __('messages.product_cant_deleted_estimate'));

            return false;
        }

        // Delete Product Taxes from Database
        if ($product->taxes()->exists() && $product->taxes()->count() > 0) {
            $product->taxes()->delete();
        }

        // Reduce feature
        $product->company->subscription('main')->reduceFeatureUsage('products');

        return $product->delete();
    }
}
