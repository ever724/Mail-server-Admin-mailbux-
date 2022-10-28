<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class SeedPermissionsIntoDatabase extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $array = [
            'view company settings',
            'update company settings',

            'view membership',
            'update membership',

            'view preferences',
            'update preferences',

            'view invoice settings',
            'update invoice settings',

            'view estimate settings',
            'update estimate settings',

            'view payment settings',
            'update payment settings',

            'view product settings',
            'update product settings',

            'view email templates',
            'update email templates',

            'view online payment gateways',
            'update online payment gateway',

            'view payment types',
            'create payment type',
            'update payment type',
            'delete payment type',

            'view product units',
            'create product unit',
            'update product unit',
            'delete product unit',

            'view team members',
            'create team member',
            'update team member',
            'delete team member',

            'view tax types',
            'create tax type',
            'update tax type',
            'delete tax type',

            'view custom fields',
            'create custom field',
            'update custom field',
            'delete custom field',

            'view expense categories',
            'create expense category',
            'update expense category',
            'delete expense category',

            'view credit notes',
            'create credit note',
            'update credit note',
            'delete credit note',

            'view dashboard',

            'view customers',
            'create customer',
            'update customer',
            'delete customer',

            'view estimates',
            'create estimate',
            'update estimate',
            'delete estimate',

            'view expenses',
            'create expense',
            'update expense',
            'delete expense',

            'view invoices',
            'create invoice',
            'update invoice',
            'delete invoice',

            'view payments',
            'create payment',
            'update payment',
            'delete payment',

            'view products',
            'create product',
            'update product',
            'delete product',

            'view vendors',
            'create vendor',
            'update vendor',
            'delete vendor',

            'view reports',
            'view customer sales report',
            'view expenses report',
            'view product sales report',
            'view profit loss report',
            'view vendors report',
        ];

        foreach ($array as $name) {
            Permission::create(['name' => $name]);
        }

        // Fetch permissions
        $permissions = Permission::all()->pluck('name');
        $users = User::all();

        foreach ($users as $user) {
            $user->syncPermissions($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
    }
}
