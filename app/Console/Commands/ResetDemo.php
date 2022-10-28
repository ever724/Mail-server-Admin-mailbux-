<?php

namespace App\Console\Commands;

use App\Helpers\Installer\DatabaseManager;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class ResetDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Make sure we are in the demo
        if (!config('app.is_demo')) {
            return;
        }

        // Purge database
        $this->purge_db();

        // Run default migrations & seeds
        $database_manager = new DatabaseManager();
        $database_manager->migrateDatabase();

        // Change admin email
        $user = User::where('email', 'user@example.com')->update(['email' => 'admin@example.com']);

        // Create 3 plans, features
        $this->create_plans();
        $plan = Plan::where('id', 1)->first();

        // Create Owner
        factory(\App\Models\User::class, 1)->create([
            'email' => 'owner@example.com',
        ]);
        $user = User::where('email', 'owner@example.com')->first();
        $company = Company::create(['name' => 'Awesome Corp.', 'owner_id' => $user->id, 'vat_number' => '111111111']);
        $company->address('billing', [
            'name' => 'Awesome Corp.',
            'address_1' => 'Address 1',
            'state' => 'State',
            'city' => 'City',
            'zip' => '1111111',
            'country_id' => 1,
        ]);
        $user->assignRole('admin');
        $permissions = Permission::all()->pluck('name');
        $user->syncPermissions($permissions);
        $user->attachCompany($company);
        $company->newSubscription('main', $plan);

        // Create Customer
        factory(\App\Models\Customer::class, 1)->create([
            'display_name' => 'Example Customer',
            'contact_name' => 'John Doe',
            'email' => 'customer@example.com',
            'company_id' => $company->id,
            'password' => Hash::make('password'),
        ]);
        $customer = Customer::where('email', 'customer@example.com')->first();
        $customer->uid = '6151ca1148156';
        $customer->save();

        // Create example product
        $product_unit_id = ProductUnit::findByCompany($company->id)->first();
        Product::create([
            'name' => 'Example Product',
            'company_id' => $company->id,
            'unit_id' => $product_unit_id->id,
            'price' => 1000,
            'description' => 'Product description',
        ]);
    }

    private function create_plans()
    {
        /** @var Plan $plan */
        $plan = Plan::query()
            ->create([
                'slug' => 'basic',
                'name' => 'Example Plan',
                'description' => 'Example Description',
                'is_active' => true,
                'price' => 1000,
                'invoice_period' => 1,
                'trial_period' => 0, // trial days
                'trial_interval' => 'day',
                'order' => 0,
            ]);

        $plan->updatePlanFeatures([
            'customers' => -1,
            'products' => -1,
            'estimates_per_month' => -1,
            'invoices_per_month' => -1,
            'view_reports' => true,
            'advertisement_on_mails' => true,
        ]);
    }

    private function purge_db()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $table = implode(json_decode(json_encode($table), true));
            Schema::drop($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
