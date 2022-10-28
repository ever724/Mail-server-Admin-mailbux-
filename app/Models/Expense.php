<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasCustomFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'expense_category_id',
        'amount',
        'company_id',
        'vendor_id',
        'expense_date',
        'notes',
        'attachment_receipt',
        'is_recurring',
        'cycle',
        'next_recurring_at',
        'parent_expense_id',
    ];

    /**
     * Automatically cast date attributes to Carbon.
     *
     * @var array
     */
    protected $dates = [
        'expense_date',
        'next_recurring_at',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['fields'];

    /**
     * Define Relation with ExpenseCategory Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    /**
     * Define Relation with Company Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Define Relation with Vendor Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Set currency_code attribute from customer.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getCurrencyCodeAttribute($value)
    {
        return $this->company->currency->short_code;
    }

    /**
     * Set formatted_expense_date attribute by custom date format
     * from Company Settings.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getFormattedExpenseDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('date_format', $this->company_id);

        return Carbon::parse($this->expense_date)->format($dateFormat);
    }

    /**
     * Set formatted_created_at attribute by custom date format
     * from Company Settings.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    /**
     * Scope a query to only include Customers of a given company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $company_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    /**
     * Scope a query to only return Expenses which has expense_date
     * greater or equal then given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Date                                  $from
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFrom($query, $from)
    {
        $query->where('expense_date', '>=', $from);
    }

    /**
     * Scope a query to only return Expenses which has expense_date
     * less or equal then given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Date                                  $to
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTo($query, $to)
    {
        $query->where('expense_date', '<=', $to);
    }

    /**
     * Scope a query to only return recurring Expenses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecurring($query)
    {
        $query->where('is_recurring', '!=', 0)->where(function ($query) {
            $query->where('cycle', -1)->orWhere('cycle', '>', 0);
        });
    }
}
