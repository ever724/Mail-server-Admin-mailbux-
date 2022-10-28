<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class UpdateEmailVerifiedAtColumnForExistingUser extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $users = User::whereNull('email_verified_at')->get();
        foreach ($users as $user) {
            $user->update([
                'email_verified_at' => now(),
            ]);
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
