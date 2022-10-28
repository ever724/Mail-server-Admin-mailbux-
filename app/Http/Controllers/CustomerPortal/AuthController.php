<?php

namespace App\Http\Controllers\CustomerPortal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display login view.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('customer_portal.auth.login');
    }

    /**
     * Handle an incoming portal authentication request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login_submit(Request $request)
    {
        // Get current customer
        $currentCustomer = Customer::findByUid($request->customer);

        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (Auth::guard('customer_portal')->attempt(['id' => $currentCustomer->id, 'email' => $request->email, 'password' => $request->password], true)) {
            // if successful, then redirect to their intended location
            return redirect()->intended(route('customer_portal.dashboard', $currentCustomer->uid));
        }

        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    /**
     * Display forgot password view.
     *
     * @return \Illuminate\View\View
     */
    public function forgot_password()
    {
        return view('customer_portal.auth.forgot_password');
    }

    /**
     * Handle an incoming portal authentication request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forgot_password_submit(Request $request)
    {
        // Validate the form data
        $request->validate(['email' => 'required|email']);

        // Get current customer
        $currentCustomer = Customer::findByUid($request->customer);

        // Check if the email addresses matched
        if ($currentCustomer->email == $request->email) {
            //Create Password Reset Token
            $token = Str::random(60);
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            // Get reset link
            $resetLink = route('customer_portal.reset_password', ['customer' => $currentCustomer->uid, 'token' => $token]);

            try {
                // Send notification to customer
                Notification::route('mail', [
                    $currentCustomer->email => $currentCustomer->display_name,
                ])->notify(new ResetPassword($resetLink));

                // Redirect back
                return redirect()->route('customer_portal.forgot_password', $currentCustomer->uid)->with('status', 'A password reset link has been sent to your email address.');
            } catch (\Exception $e) {
                return redirect()->back()->withInput($request->only('email'))->with('error', 'The email address you entered does not match our records.');
            }
        }

        // Redirect to back with error
        return redirect()->back()->withInput($request->only('email'))->with('error', 'The email address you entered does not match our records.');
    }

    /**
     * Display reset password view.
     *
     * @return \Illuminate\View\View
     */
    public function reset_password()
    {
        return view('customer_portal.auth.reset_password');
    }

    /**
     * Handle an incoming portal authentication request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset_password_submit(Request $request)
    {
        // Get current customer
        $currentCustomer = Customer::findByUid($request->customer);

        // Validate request
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validate token
        $token = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$token || $token->email != $currentCustomer->email) {
            return redirect()->back()->withErrors(['password' => __('The password reset token is invalid.')]);
        }

        // Update password
        $currentCustomer->password = Hash::make($request->password);
        $currentCustomer->save();

        // Attempt to log the user in
        if (Auth::guard('customer_portal')->attempt(['id' => $currentCustomer->id, 'email' => $currentCustomer->email, 'password' => $request->password], true)) {
            // if successful, then redirect to their intended location
            return redirect()->intended(route('customer_portal.dashboard', $currentCustomer->uid));
        }

        return redirect()->back()->withErrors(['password' => __('A Network Error occurred. Please try again.')]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('customer_portal')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
