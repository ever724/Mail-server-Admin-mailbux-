<?php

namespace App\Models;

use App\Traits\HasAddresses;
use App\Traits\HasSubscriptions;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasAddresses;
    use UUIDTrait;
    use HasSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_id',
        'vat_number',
    ];

    /**
     * Define Relation with User Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('\App\Models\User', 'company_user', 'company_id', 'user_id')->withTimestamps();
    }

    /**
     * Define Relation with Addressable Model
     * This indicates the owner of the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper function to determine if a user is part
     * of this company.
     *
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->users()->where($user->getKeyName(), $user->getKey())->first() ? true : false;
    }

    /**
     * Define Relation with CompanySetting Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany(CompanySetting::class);
    }

    /**
     * Define Relation with Vendor Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Define Relation with TaxType Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tax_types()
    {
        return $this->hasMany(TaxType::class);
    }

    /**
     * Define Relation with ProductUnit Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * Define Relation with Product Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Define Relation with PaymentMethod Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment_methods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Define Relation with Payment Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Define Relation with Invoice Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Define Relation with InvoiceItem Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Define Relation with ExpenseCategory Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expense_categories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    /**
     * Define Relation with Expense Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Define Relation with Estimate Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    /**
     * Define Relation with EstimateItem Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function estimate_items()
    {
        return $this->hasMany(EstimateItem::class);
    }

    /**
     * Define Relation with Customer Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Define Relation with CustomField Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function custom_fields()
    {
        return $this->hasMany(CustomField::class);
    }

    /**
     * Define Relation with CustomFieldValue Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function custom_field_values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    /**
     * Get Company Specified setting.
     *
     * @param string $key
     *
     * @return string
     */
    public function getSetting($key)
    {
        return CompanySetting::getSetting($key, $this->id);
    }

    /**
     * Set Company Specified setting.
     *
     * @param string $key
     * @param string $value
     */
    public function setSetting($key, $value)
    {
        return CompanySetting::setSetting($key, $value, $this->id);
    }

    /**
     * Get Currency Attribute.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getCurrencyAttribute($value)
    {
        return Currency::find($this->getSetting('currency_id')) ?? Currency::where('short_code', 'USD')->first();
    }

    /**
     * Check if Paypal Gateway is Active.
     *
     * @return bool
     */
    public function isPaypalActive()
    {
        if (
            $this->getSetting('paypal_active')
            && $this->getSetting('paypal_username') != ''
            && $this->getSetting('paypal_password') != ''
            && $this->getSetting('paypal_signature') != ''
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if Stripe Gateway is Active.
     *
     * @return bool
     */
    public function isStripeActive()
    {
        if (
            $this->getSetting('stripe_active')
            && $this->getSetting('stripe_secret_key') != ''
            && $this->getSetting('stripe_public_key') != ''
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if Razorpay Gateway is Active.
     *
     * @return bool
     */
    public function isRazorpayActive()
    {
        if (
            $this->getSetting('razorpay_active')
            && $this->getSetting('razorpay_id') != ''
            && $this->getSetting('razorpay_secret_key') != ''
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if Mollie Gateway is Active.
     *
     * @return bool
     */
    public function isMollieActive()
    {
        if (
            $this->getSetting('mollie_active')
            && $this->getSetting('mollie_api_key') != ''
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return Default Company Avatar Url.
     *
     * @return string (url)
     */
    public function getDefaultAvatar()
    {
        return asset('assets/images/avatar/company.png');
    }

    /**
     * Get User's Company Url || Default Avatar.
     *
     * @return string (url)
     */
    public function getAvatarAttribute()
    {
        $avatar = CompanySetting::getSetting('avatar', $this->id);

        return $avatar ? asset($avatar) : $this->getDefaultAvatar();
    }

    /**
     * Update current company's settings.
     *
     * @param mixed $request
     */
    public function updateModel($request)
    {
        // Update Company
        $this->update($request->validated());

        // Update Company Address
        if ($request->billing) {
            $address = $request->input('billing');
            $address['name'] = $this->name;
            $this->updateAddress('billing', $address);
        }

        // Update Company Logo
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('company_logos', 'logo-' . $this->id . '.' . $request->avatar->getClientOriginalExtension(), 'public_dir');
            $this->setSetting('avatar', asset('/uploads/' . $path), $this->id);
        }

        return $this;
    }
}
