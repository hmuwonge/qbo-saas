<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthenticatedSessionController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function create()
    {
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        return view('auth.login', compact('lang'));
    }

    public function store(LoginRequest $request)
    {
        $central_domain = config('tenancy.central_domains')[0];
        $current_domain = tenant('domains');
        if (!empty($current_domain)) {
            $current_domain = $current_domain->pluck('domain')->toArray()[0];
        }
        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            if ($user->active_status == 1) {
                $credentials = $request->only('email', 'password');
                if (Auth::attempt($credentials)) {
                    if ($user->type == 'Super Admin' && empty($user->tenant_id)) {
                        if (Auth::attempt($credentials)) {
                            $request->session()->regenerate();
                            return redirect()->intended(RouteServiceProvider::HOME);
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password.'));
                        }
                    } elseif (!empty($current_domain) && !empty($user->tenant_id)) {
                        $user_admin = tenancy()->central(function ($tenant) {
                            return User::where('tenant_id', $tenant->id)->where('type', 'Admin')->first();
                        });
                        if ($user_admin->active_status == 0) {
                            return redirect()->back()->with('errors', __('Please Contact to administrator'));
                        }
                        if ($user_admin->plan_id != '1' && !empty($user_admin->plan_expired_date) && Carbon::now() >= $user_admin->plan_expired_date) {
                            $user_admin->assignPlan(1);
                        }
                        if (Auth::attempt($credentials)) {
                            if (UtilityFacades::getsettings('sms_setting_enable') == 'on' && $user->phone_verified_at == ''  && UtilityFacades::getsettings('sms_verification') == '1') {
                                $request->session()->regenerate();
                                return redirect()->route('smsindex.noticeverification');
                            } else {
                                $request->session()->regenerate();
                                return redirect()->intended(RouteServiceProvider::HOME);
                            }
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password.'));
                        }
                    } else {
                        $user = User::where('email', $request->email)->first();
                        if (!Hash::check($request['password'], $user->password)) {
                            return redirect()->back()->with('errors', __('Invalid username or password'));
                        } else {
                            $current_domain = $user->tenant->domains->first()->domain;
                            $redirectUrl = '/home';
                            $token = tenancy()->impersonate($user->tenant, 1, $redirectUrl);
                            return redirect("http://$current_domain/tenant-impersonate/{$token->token}");
                        }
                    }
                } else {
                    return redirect()->back()->with('errors', __('Invalid username or password.'));
                }
            } else {
                return redirect()->back()->with('errors', __('Please Contact to administrator.'));
            }
        } else {
            return redirect()->back()->with('errors', __('User not found.'));
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
