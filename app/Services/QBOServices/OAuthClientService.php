<?php

namespace App\Services\QBOServices;

use App\Models\QuickBooksConfig;
use App\Traits\DataServiceConnector;
use App\Traits\Responser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\Exception\ServiceException;

class OAuthClientService
{
    use DataServiceConnector, Responser;

    /**
     * @throws SdkException
     */
    public function connect(): string
    {
        $dataService = $this->QBOConnector();
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper(); //First, use the $OAuth2LoginHelper object to generate Authorization Code URL:

        return $OAuth2LoginHelper->getAuthorizationCodeURL();
    }

    /**
     * @throws SdkException
     * @throws ServiceException
     */
    public function callback(Request $request)
    {

        $string = $request->getQueryString();

        $dataService = $this->QBOConnector();

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        // Get the Authorization URL from the SDK
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

        $parseUrl = $this->parseAuthRedirectUrl($string);
        /*
         * Update the OAuth2Token
         */

        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);

        /*
         * Setting the accessToken for session variable
         */

        return self::updateAuthTokens($accessToken);

    }

    public function refresh_token(): JsonResponse
    {
        try {
            $accessTokenObj = Session::get('quickbooksAccessToken');
            // Prep Data Services
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientSecret' => env('CLIENT_SECRETE'),
                'ClientID' => env('CLIENT_ID'),
                //get the refresh token from session or database
                'refreshTokenKey' => $accessTokenObj->getRefreshToken(),
                'QBORealmID' => $accessTokenObj->getRealmId(),
                'baseUrl' => 'Development',
            ]);

            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();

            $error = $OAuth2LoginHelper->getLastError();

            if ($error) {
                return $this->errorResponse($error, 500);
            } else {
                //Refresh Token is called successfully
                $dataService->updateOAuth2Token($refreshedAccessTokenObj);
                $Oauth2LoginHelper = $dataService->getOAuth2LoginHelper();
                session(['quickbooksAccessToken' => $Oauth2LoginHelper->getAccessToken()]);
                //                return $this->successResponse('Token refreshed successfully', 200);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    private function parseAuthRedirectUrl($url): array
    {
        $data = parse_str($url, $details);

        return [
            'code' => $details['code'],
            'realmId' => $details['realmId'],
        ];
    }

    public static function updateAuthTokens($data)
    {
        $refreshTokenExpiry = Carbon::parse($data->getRefreshTokenExpiresAt());
        $accessTokenExpiry = Carbon::parse($data->getAccessTokenExpiresAt());
        try {
            if (Auth::check()) {
                $qbo_user = QuickBooksConfig::where('user_id', auth()->user()->id)->first();

                if (!is_null($qbo_user)) {
                    $qbo_user->auth_token = $data->getAccessToken();
                    $qbo_user->refresh_token = $data->getRefreshToken();
                    $qbo_user->auth_expiry = $accessTokenExpiry;
                    $qbo_user->refresh_token_expiry = $refreshTokenExpiry;
                    $qbo_user->update();
//                    echo 'Successfully updated authentication';
                  return redirect()->route('quickbooks.index')->with('success','Token updated successfully');
                } else {
                    $qbo_user = new QuickBooksConfig;
                    $qbo_user->user_id = auth()->user()->id;
                    $qbo_user->company_id =  1;
//                    $qbo_user->intuit_company_id =  1;
                    $qbo_user->auth_token = $data->getAccessToken();
                    $qbo_user->refresh_token = $data->getRefreshToken();
                    $qbo_user->auth_expiry = $accessTokenExpiry;
                    $qbo_user->refresh_token_expiry = $refreshTokenExpiry;
                    $qbo_user->save();
                  return redirect()->route('quickbooks.index')->with('success','Token updated successfully');
                }

            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
