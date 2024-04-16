<?php

namespace App\Traits;

use App\Models\QuickBooksConfig;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\QueryFilter\QueryMessage;

trait DataServiceConnector
{
    /**
     * @throws SdkException
     */
    public function QBOConnector(): DataService
    {
        return DataService::Configure(
            [
                'auth_mode' => 'oauth2',
                'ClientID' => env('CLIENT_ID'),
                'ClientSecret' => env('CLIENT_SECRETE'),
                'RedirectURI' => route('callback'),
                'scope' => 'com.intuit.quickbooks.accounting',
                 'baseUrl' => env('BASE_DEV'),
                'minorversion' => 14,
            ]
        );
    }

    public function urlQueryBuilderById($model, $itemId)
    {
      $company_id = env('INUIT_COMPANY_ID');
      $company_url = env('BASE_URL');
      $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer'.' '.$this->getToken(),
      ])->get("{$company_url}/v3/company/{$company_id}/{$model}/{$itemId}?minorversion=57");
      $qb_details = json_decode($response->body(), true);

        return response()->json($qb_details);
    }

    public function urlQueryBuilderAll($model)
    {
        // $QB = new QuickBooks($request->header('tin'));
        //Post Data
        try {
          $company_id = env('INUIT_COMPANY_ID');
          $company_url = env('BASE_URL');
          $query = "select * from {$model}";
          $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer'.' '.$this->getToken(),
          ])->get("{$company_url}/v3/company/{$company_id}/query", [
            'query' => $query,
            'minorversion' => 57,
          ]);
          ;

            return json_decode($response->body(), true);
        } catch (Exception $exception) {
            return Log::error($exception->getMessage());
        }

    }

    public function queryString($query)
    {
      $company_id = env('INUIT_COMPANY_ID');
      $company_url = env('BASE_URL');
      $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer'.' '.$this->getToken(),
      ])->get("{$company_url}/v3/company/{$company_id}{$query}");
        return json_decode($response->body(), true);
    }

  public function makeQuery($query)
  {
    $company_id = env('INUIT_COMPANY_ID');
    $company_url = env('BASE_URL');
    //        $query = "select * from {$query}";
    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Authorization' => 'Bearer'.' '.$this->getToken(),
    ])->get("{$company_url}/v3/company/{$company_id}/{$query}");
    return json_decode($response->body(), true);
  }

    public function QueryBuilder($entity, $orderBy = null, $startPosition = null, $maxResult = null)
    {
        // try {
        // Prep Data Services
        $dataService = $this->getDataService();

        // Build a query
        $oneQuery = new QueryMessage();
        $oneQuery->sql = 'SELECT';
        $oneQuery->entity = $entity;
        if ($orderBy !== null) {
            $oneQuery->orderByClause = $orderBy;
        }

        $oneQuery->startposition = $startPosition;
        $oneQuery->maxresults = $maxResult;

        // Run a query
        $queryString = $oneQuery->getString();
        $entities = $dataService->Query($queryString);
        $error = $dataService->getLastError();

        if ($error) {
            if ($error->getHttpStatusCode() == 401) {
                return redirect()->route('dashboard.integrator');
            } else {
                echo 'The Status code is: '.$error->getHttpStatusCode()."\n";
                echo 'The Helper message is: '.$error->getOAuthHelperError()."\n";
                echo 'The Response message is: '.$error->getResponseBody()."\n";
                exit();
            }
        }

        return $entities;
        // } catch (\Throwable $th) {
        //     return response()->json($th->getMessage());
        // }
    }

    /**
     * @param  mixed  $accessToken
     *
     * @throws SdkException
     */
    public function getDataService(): DataService
    {

      $company_id = env('INUIT_COMPANY_ID');
      $company_url = env('BASE_URL');

      return DataService::Configure([
        'auth_mode' => 'oauth2',
        'ClientID' => env('CLIENT_ID'),
        'ClientSecret' => env('CLIENT_SECRETE'),
        'accessTokenKey' => $this->getToken(),
        'refreshTokenKey' => $this->getUserRefreshToken(),
        'QBORealmID' => $company_id,
        'baseUrl' => env('BASE_DEV','Development'),
        'minorversion' => 57,
      ]);
    }

    public function getToken()
    {
        return $this->user()->auth_token;
    }

    public function getUserRefreshToken()
    {

        return $this->user()->refresh_token;
    }

    public function user()
    {
        return QuickBooksConfig::where('user_id', auth()->user()->id)->first();
    }
}
