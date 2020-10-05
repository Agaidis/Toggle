<?php

namespace App\Http\Controllers;

use App\Console\Commands\GetPermits;
use App\ErrorLog;
use Illuminate\Support\Facades\Log;


class APIManager
{

    private $apiKey;
    private $apiToken;
    private $clientId;
    private $clientSecret;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiKey = env('DRILLING_API_KEY');
        $this->apiToken = env('DRILLING_ACCESS_TOKEN');
        $this->clientId = env('CLIENT_ID');
        $this->clientSecret = env('CLIENT_SECRET');
    }


    public function getToken () {
        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => "https://di-api.drillinginfo.com/v2/direct-access/tokens?grant_type=client_credentials&client_secret=f35662b3-1b5a-4a61-a6db-b4783d35d6da&client_id=21007-direct-access",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "X-API-KEY: e89c4d8b6edf1a7b5c9739e6ae5e4235",
                "Content-Type: application/x-www-form-urlencoded"
            ),
                )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    public function getPermits ($county, $token, $date, $interestArea) {

        if ($interestArea == 'nm' || $interestArea == 'admin') {
            $url = "https://di-api.drillinginfo.com/v2/direct-access/permits?approveddate=ge(".$date.")&countyparish=".$county."&drilltype=in(H,V)&pagesize=10000";
        } else {
            $url = "https://di-api.drillinginfo.com/v2/direct-access/permits?submitteddate=ge(".$date.")&countyparish=".$county."&drilltype=in(H,V)&pagesize=10000";
        }
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-api-key: e89c4d8b6edf1a7b5c9739e6ae5e4235",
                    "Content-Type: application/x-www-form-urlencoded",
                    "Authorization:  Bearer " . $token
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return "cURL Error #:" . $err;
            }
            return $response;
    }

    public function getWellRollUps ($token, $county, $linkUrl) {
        try {

            if ($linkUrl == '') {
                $url = "https://di-api.drillinginfo.com/v2/direct-access/well-rollups?CountyParish=".$county."&pagesize=1500";
            } else {
                $url = "https://di-api.drillinginfo.com/v2/direct-access/well-rollups" . $linkUrl;
            }

            $url = str_replace(['\'', ' '], ['', ''], $url);

            $curl = curl_init();
            Log::info($url);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 50,
                CURLOPT_HEADER => 1,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-api-key: e89c4d8b6edf1a7b5c9739e6ae5e4235",
                    "Accept: */*",
                    "Authorization: Bearer " . $token
                ),
            ));



            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $response = rtrim($response);
            $data = explode("\n",$response);

            if (isset($data[5])) {
                $nextUrl = str_replace(['Links: </well-rollups', '>; rel="next"'], ['',''], $data[5]);
            } else {
                $nextUrl = '';
            }

            if (strpos($nextUrl, 'openresty')) {
                $nextUrl = '';
            }

            if (isset($data[16])) {
                $jsonData = $data[16];
            } else {
                $jsonData = '';
            }

            $responseWithLink = [0 => $jsonData, 1 => $nextUrl];

            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return $responseWithLink;
            }

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
        }
    }

    public function getLandtracLeases ($county, $token, $linkUrl) {

        if ($linkUrl == '') {
            $url = "https://di-api.drillinginfo.com/v2/direct-access/landtrac-leases?countyparish=".$county."&pagesize=2000";
        } else {
            $url = "https://di-api.drillinginfo.com/v2/direct-access/landtrac-leases" . $linkUrl;
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 50,
                CURLOPT_HEADER => 1,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-api-key: e89c4d8b6edf1a7b5c9739e6ae5e4235",
                    "Accept: */*",
                    "Authorization: Bearer " . $token
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $response = rtrim($response);
            $response = explode('Connection: keep-alive', $response);

            $headerInfo = explode("\n",$response[0]);
            $jsonData = ltrim($response[1]);

            if (isset($headerInfo[5])) {
                $urlArray = explode(',', $headerInfo[5]);
                if (isset($urlArray[1])) {
                    $nextUrl = str_replace(['</landtrac-leases', '>; rel="next"'], ['',''], $urlArray[1]);
                } else {
                    $nextUrl = '';
                }
            } else {
                $nextUrl = '';
            }

            if (strpos($nextUrl, 'openresty')) {
                $nextUrl = '';
            }

            $responseWithLink = [0 => $jsonData, 1 => $nextUrl];

            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return $responseWithLink;
            }
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
        }
    }

    public function getLegalLeases ($county, $token, $linkUrl) {
<<<<<<< HEAD

        if ($linkUrl == '') {
=======
        Log::info('Here is the link url beginning of get legal leases: ' . $linkUrl);
        if ($linkUrl === '') {
>>>>>>> e4268c60f567da46b5b3713d288a3876bd93bea7
            $url = "https://di-api.drillinginfo.com/v2/direct-access/legal-leases?countyparish=".$county."&pagesize=2000";
        } else {
            $url = "https://di-api.drillinginfo.com/v2/direct-access/legal-leases" . $linkUrl;
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 50,
                CURLOPT_HEADER => 1,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-api-key: e89c4d8b6edf1a7b5c9739e6ae5e4235",
                    "Accept: */*",
                    "Authorization: Bearer " . $token
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $response = rtrim($response);
            $response = explode('Connection: keep-alive', $response);

            $headerInfo = explode("\n",$response[0]);
            $jsonData = ltrim($response[1]);

           // Log::info($headerInfo[5]);
            if (isset($headerInfo[5])) {
                $nextUrl = str_replace(['Links:','</legal-leases', '>; rel="next"'], ['','',''], $headerInfo[5]);
            } else {
                $nextUrl = '';
            }

            if (strpos($nextUrl, 'openresty')) {
                $nextUrl = '';
            }

            $responseWithLink = [0 => $jsonData, 1 => $nextUrl];

            Log::info('Response Link: ' . $responseWithLink[1]);

            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return $responseWithLink;
            }
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
        }
    }
}
