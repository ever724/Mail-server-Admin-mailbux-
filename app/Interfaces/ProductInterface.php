<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ProductInterface
{
    public function getPaginatedFilteredProducts(Request $request);

    public function newProduct(Request $request);

    public function createProduct(Request $request);

    public function getProductById(Request $request, $product_id);

    public function updateProduct(Request $request, $product_id);

    public function deleteProduct(Request $request, $product_id);
}
