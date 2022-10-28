<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $company = Company::create([
            'name' => 'My Awesome Company',
            'owner_id' => $user->id,
        ]);

        // Assign Role
        $user->assignRole('super_admin');

        // Attach User to Company
        $user->attachCompany($company);
    }
}
