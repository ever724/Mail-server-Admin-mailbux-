<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddPasswordFieldToCustomers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->rememberToken();
        });

        // Get all customers
        $customers = Customer::all();
        // Set random password for all customers
        foreach ($customers as $customer) {
            $customer->password = Hash::make(Str::random(8));
            $customer->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropRememberToken();
        });
    }
}
