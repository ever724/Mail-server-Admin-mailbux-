<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rules\ReCaptchaRule;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');

        if ($request->plan) {
            $this->redirectTo = route('order.checkout', ['plan' => $request->plan]);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['required', 'string'],
        ];

        if (env('GOOGLE_CAPTCHA_PRIVATE_KEY')) {
            $validator['g-recaptcha-response'] = ['required', new ReCaptchaRule($data['g-recaptcha-response'])];
        }

        return Validator::make($data, $validator);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Create new User
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'api_token' => hash('sha256', Str::random(60)),
        ]);

        // Create Company
        $company = Company::create([
            'name' => $data['company_name'],
            'owner_id' => $user->id,
        ]);

        // Assign Role
        $user->assignRole('admin');

        // Assign permissions
        $permissions = Permission::all()->pluck('name');
        $user->syncPermissions($permissions);

        // Attach User to Company
        $user->attachCompany($company);

        if (!get_system_setting('verify_user_email_address')) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        }

        return $user;
    }
}
