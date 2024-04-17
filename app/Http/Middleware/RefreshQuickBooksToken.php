<?php

namespace App\Http\Middleware;

use App\Facades\UtilityFacades;
use App\Models\QuickBooksConfig;
use App\Services\QBOServices\OAuthClientService;
use App\Services\QuickBooksServiceHelper;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use OAuthException;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\Exception\ServiceException;

class RefreshQuickBooksToken
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @throws SdkException
     * @throws ServiceException
     */
    public function handle(Request $request, Closure $next)
    {

        try {
//            if (tenant('id') == null) {
                if (Auth::check()) {
                    // Get the OAuth2 access token from the database
                    $qbo_user = QuickBooksConfig::where('user_id', auth()->user()->id)->first();
                    $check_client_id = UtilityFacades::getsettings('client_id');
                    if (empty($check_client_id)){
                        return redirect()->route('settings')->with('failed','Quickbooks congiurations are missing');
                    }
                    $url = $this->qbo_url();

                    if ((isset($qbo_user)) && (isset($qbo_user->auth_expiry))) {
                        $currentTime = Carbon::parse(Carbon::now()->toDateTimeString());
                        $accessTokenExpiry = Carbon::parse($qbo_user->auth_expiry);
                        $refreshTokenExpiry = Carbon::parse($qbo_user->refresh_token_expiry);

                        // check if access token has expired but refresh token has not
                        if (($currentTime->greaterThan($accessTokenExpiry)) && ($refreshTokenExpiry->greaterThan($accessTokenExpiry))) {

                            $accessToken = $qbo_user->auth_token;
                            $refreshToken = $qbo_user->refresh_token;
                            if ($accessToken && $refreshToken) {
                                //The first parameter of OAuth2LoginHelper is the ClientID, second parameter is the client Secret
                                $oauth2LoginHelper = new OAuth2LoginHelper(UtilityFacades::getsettings('client_id'), UtilityFacades::getsettings('client_secrete'));
                                $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);
                                $accessTokenValue = $accessTokenObj->getAccessToken();
                                $refreshTokenValue = $accessTokenObj->getRefreshToken();


                                try {
                                    $qbo_user->auth_token = $accessTokenValue;
                                    $qbo_user->refresh_token = $refreshTokenValue;
                                    $qbo_user->auth_expiry = Carbon::parse($accessTokenObj->getAccessTokenExpiresAt())->format('Y-m-d H:i:s');
                                    $qbo_user->refresh_token_expiry = Carbon::parse($accessTokenObj->getRefreshTokenExpiresAt())->format('Y-m-d H:i:s');
                                    $qbo_user->update();
                                } catch (OAuthException $e) {
                                    // Handle exception here
                                    // QuickBooksServiceHelper::logToFile($e->getMessage());
                                }
                            }
                        }
                        if (($currentTime->greaterThan($accessTokenExpiry)) && ($currentTime->greaterThan($refreshTokenExpiry))) {
                            return response()->view('QboAuth', compact('url'));
                        }
                    } else {
                        return response()->view('QboAuth', compact('url'));
                    }

                Redirect::route('login');
            }

        } catch (OAuthException $exception) {
            // Handle OAuth exceptions, e.g. log error and redirect to login page
            return view('QboAuth',compact('url'));
        }

        return $next($request);
    }

    public function qbo_url()
    {
        $new = new OAuthClientService;

        return $new->connect();
    }
}
