<?php

namespace App\Traits;

trait QueryBuilder
{
    use DataServiceConnector;

    public function invoicesRange($body)
    {
      $curl = curl_init();
      $realm = env('QBOREALMID');
      $company_url = env('BASE_URL');

      curl_setopt_array($curl, [
        CURLOPT_URL => "{$company_url}/v3/company/{$realm}/query?minorversion=14",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
          'User-Agent: {{UserAgent}}',
          'Accept: application/json',
          'Content-Type: application/text',
          'Authorization: Bearer '.$this->getToken(),
        ],
      ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);

        return $response;
    }
}
