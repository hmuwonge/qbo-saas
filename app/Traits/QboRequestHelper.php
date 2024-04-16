<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;

trait QboRequestHelper
{
    /**
     * @throws SdkException
     */
    public function makeApiCall(): DataService
    {
        $accessToken = Session::get('sessionAccessToken');

        return DataService::Configure(
            [
                'auth_mode' => 'oauth2',
                'ClientID' => env('CLIENT_ID'),
                'ClientSecret' => env('CLIENT_SECRETE'),
                'accessTokenKey' => $accessToken->getAccessToken(),
                'refreshTokenKey' => $accessToken->getRefreshToken(),
                'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
                'expires_in' => $accessToken->getAccessTokenExpiresAt(),
                'QBORealmID' => '4620816365243988890',
                'baseUrl' => 'development',
                'minorversion' => 15,
            ]
        );
    }

    private function getAccessToken(mixed $token, $refresh, $token_expires_at, $ref_expires_at)
    {
        try {

            return [
                'auth_mode' => 'oauth2',
                'token_type' => 'bearer',
                'access_token' => $token,
                'refresh_token' => $refresh,
                'x_refresh_token_expires_in' => $$ref_expires_at,
                'expires_in' => $token_expires_at,
                'QBORealmID' => env('QBOREALMID'),
                'ClientSecret' => env('CLIENT_SECRETE'),
                'ClientID' => env('CLIENT_ID'),
                'baseUrl' => 'Development',
            ];

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
