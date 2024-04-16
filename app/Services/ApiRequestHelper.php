<?php

namespace App\Services;

use App\Facades\UtilityFacades;
use App\Traits\DataServiceConnector;

class ApiRequestHelper
{
    use DataServiceConnector;

    /**
     * URL to the API server
     *
     * @var type
     */
    public $api_url;

    public $qb_url;

    public $api;

    public $currentTin;

    public $token;

    public function __construct($system_api)
    {
        $this->api = $system_api;
        $this->api_url = UtilityFacades::getsettings('efris_middleware_api_url');
    }

    /**
     * Make a POST request
     *
     * @param  string  $endpoint
     * @param  array  $params
     */
    public function makePost($endpoint, $params): mixed
    {
        $curl = curl_init();
        //Options
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url.'/'.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization:Bearer '.$this->token,
            ],
        ]);
        //Server Response
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * Make an update
     *
     * @param  array  $params
     */
    public function makePatch(string $endpoint, $params): bool|type|string
    {
        $curl = curl_init();
        //Options
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url.'/'.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization:Bearer '.$this->token,
            ],
        ]);
        //Server Response
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /*     * PATCH
 * Make a GET Request
 */

    public function makeGet($endpoint): bool|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url.'/'.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization:Bearer '.$this->token,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
