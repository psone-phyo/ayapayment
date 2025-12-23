<?php

namespace AyaPayment;

use GuzzleHttp\Client as GuzzleHttpClient;

class AYAPayService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $merchantTokenUrl;
    protected $merchantLoginUrl;
    protected $merchantPhone;
    protected $merchantPassword;
    protected $baseUrl;
    protected $currency;
    protected $urlPrefix;
    protected $paymentRequestUrl;
    protected $QRRequestUrl;
    protected $refundPaymentUrl;
    protected $serviceCode;
    protected $checkStatusUrl;
    protected  $decryptionKey;

    public function __construct()
    {
        $this->consumerKey = config('ayapayment.pay.consumer_key');
        $this->consumerSecret = config('ayapayment.pay.consumer_secret');
        $this->merchantPhone = config('ayapayment.pay.merchant_phone');
        $this->merchantPassword = config('ayapayment.pay.merchant_password');
        $this->baseUrl = config('ayapayment.pay.payment_url');
        $this->currency = config('ayapayment.pay.currency');
        $this->serviceCode = config('ayapayment.pay.service_code');
        $this->decryptionKey = config('ayapayment.pay.decryption_key');

        $this->urlPrefix = $this->baseUrl . 'om/1.0.0/thirdparty/merchant/';
        $this->merchantTokenUrl = $this->baseUrl . 'token';
        $this->merchantLoginUrl = $this->urlPrefix . 'login';
        $this->paymentRequestUrl = $this->urlPrefix . 'v2/requestPushPayment';
        $this->QRRequestUrl = $this->urlPrefix . 'v2/requestQRPayment';
        $this->refundPaymentUrl = $this->urlPrefix . 'refundPayment';
        $this->checkStatusUrl = $this->urlPrefix . 'checkRequestPayment';
    }

    public function accessToken()
    {
        try {
            $encrypt_data = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

            $http = new GuzzleHttpClient([
                'headers' => [
                    'Authorization' => 'Basic ' . $encrypt_data,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            $response = $http->post($this->merchantTokenUrl, [
                'form_params' => ['grant_type' => 'client_credentials']
            ]);

            $response = json_decode($response->getBody(), true);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Aya pay getting token error :' . $e);
            return false;
        }
    }

    public function merchantLogin()
    {
        try {
            $accessToken = $this->accessToken();
            if (!$accessToken) {
                return false;
            }
            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->merchantLoginUrl,
                    'headers' => [
                        'Token' => $accessToken['token_type'] . ' ' . $accessToken['access_token'],
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $data['phone'] = $this->merchantPhone;
            $data['password'] = $this->merchantPassword;

            $res = $http->post('', [
                'form_params' => $data
            ]);
            $res = json_decode($res->getBody(), true);
            return array_merge($res, $accessToken);
        } catch (\Exception $e) {
            \Log::error('Aya pay merchant login error :' . $e);
            return false;
        }
    }

    public function getPayloadForPayment($customer_phone,  $amount, $currency = 'MMK', $externalTransactionId, $externalAdditionalData = '')
    {
        try {
            $loginKeys = $this->merchantLogin();
            if (!$loginKeys) {
                return false;
            }
            $accessToken = $loginKeys['access_token'];
            $authorizationKey = $loginKeys['token']['token'];
            $serviceCode = $this->serviceCode;
            $form['url'] = $this->paymentRequestUrl;
            $form['accessToken'] = $accessToken;
            $form['authorizationKey'] = $authorizationKey;
            $form['contentType'] = 'application/x-www-form-urlencoded';
            $form['payload'] = [
                'customerPhone' => $customer_phone,
                'amount' =>$amount,
                'currency'  => $currency,
                'externalTransactionId' => $externalTransactionId,
                'externalAdditionalData' => $externalAdditionalData,
                'serviceCode' => $serviceCode
            ];
            $form['values'] =
                "<input type='hidden' id='customerPhone' name='customerPhone' value='" . $customer_phone . "'/>" .
                "<input type='hidden' id='amount' name='amount' value='" . $amount . "'/>" .
                "<input type='hidden' id='currency' name='currency' value='" . $currency . "'/>" .
                "<input type='hidden' id='externalTransactionId' name='externalTransactionId' value='" . $externalTransactionId . "'/>" .
                "<input type='hidden' id='externalAdditionalData' name='externalAdditionalData' value='" . $externalAdditionalData . "'/>" .
                "<input type='hidden' id='serviceCode' name='serviceCode' value='" . $serviceCode . "'/>" .
                "<input type='hidden' id='accessToken' name='accessToken' value='" . $accessToken . "'/>" .
                "<input type='hidden' id='authorizationKey' name='authorizationKey' value='" . $authorizationKey . "'/>" .
                "<input type='hidden' id='url' name='url' value='" . $form['url'] . "'/>";
            return $form;
        } catch (\Exception $e) {
            \Log::error('Aya pay push payment error :' . $e);
            return false;
        }
    }


    public function requestPushPayment($accessToken, $authorizationKey, $customer_phone,  $amount, $currency = 'MMK', $externalTransactionId, $externalAdditionalData = '')
    {
        try {
            $payload = [
                'customerPhone' => $customer_phone,
                'amount' => $amount,
                'currency'  => $currency,
                'externalTransactionId' => $externalTransactionId,
                'externalAdditionalData' => $externalAdditionalData,
                'serviceCode' => $this->serviceCode
            ];
            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->paymentRequestUrl,
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Token' => 'Bearer ' . $accessToken,
                        'Authorization' => 'Bearer ' . $authorizationKey,
                        'Accept-Language' => 'en'
                    ]
                ]
            );

            $response = $http->post('', [
                'form_params' => $payload
            ]);

            $response = json_decode($response->getBody(), true);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Aya pay push payment error :' . $e);
            return false;
        }
    }

    public function requestQRPayment($accessToken, $authorizationKey, $customer_phone,  $amount, $currency = 'MMK', $externalTransactionId, $externalAdditionalData = '')
    {
        try {
            $payload = [
                'customerPhone' => $customer_phone,
                'amount' => $amount,
                'currency'  => $currency,
                'externalTransactionId' => $externalTransactionId,
                'externalAdditionalData' => $externalAdditionalData,
                'serviceCode' => $this->serviceCode
            ];
            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->QRRequestUrl,
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Token' => 'Bearer ' . $accessToken,
                        'Authorization' => 'Bearer ' . $authorizationKey,
                        'Accept-Language' => 'en'
                    ]
                ]
            );

            $response = $http->post('', [
                'form_params' => $payload
            ]);

            $response = json_decode($response->getBody(), true);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Aya pay push payment error :' . $e);
            return false;
        }
    }

    public function refundPayment($referenceNumber, $externalTransactionId)
    {
        try {
            $loginKeys = $this->merchantLogin();
            if (!$loginKeys) {
                return false;
            }
            $access_token = $loginKeys['access_token'];
            $authorization_key = $loginKeys['token']['token'];

            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->refundPaymentUrl,
                    'headers' => [
                        'Token' => $access_token,
                        'Authorization' => $authorization_key,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $data['referenceNumber'] = $referenceNumber;
            $data['externalTransactionId'] = $externalTransactionId;

            $res = $http->post('', [
                'form_params' => $data
            ]);

            return json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Aya pay Refund Payment error :' . $e);
            return false;
        }
    }

    public function checkStatus($referenceNumber, $externalTransactionId)
    {
        try {
            $loginKeys = $this->merchantLogin();
            if (!$loginKeys) {
                return false;
            }
            $access_token = $loginKeys['access_token'];
            $authorization_key = $loginKeys['token']['token'];

            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->checkStatusUrl,
                    'headers' => [
                        'Token' => $access_token,
                        'Authorization' => $authorization_key,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $data['referenceNumber'] = $referenceNumber;
            $data['externalTransactionId'] = $externalTransactionId;

            $res = $http->post('', [
                'form_params' => $data
            ]);

            return json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Aya pay Refund Payment error :' . $e);
            return false;
        }
    }

    function encrypt_decrypt($data, $key = null)
    {
        $key = $key ?? $this->decryptionKey;
        $cipher = "AES-256-ECB";
        $chiperRaw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA);
        $ciphertext = trim(base64_encode($chiperRaw));
        $cipherHex = bin2hex($chiperRaw);

        $chiperRaw = base64_decode($ciphertext);

        $originalData = openssl_decrypt($chiperRaw, $cipher, $key, OPENSSL_RAW_DATA);
        return $originalData;
    }

    function encrypt($plaintext,  $key = null)
    {
        $key = $key ?? $this->decryptionKey;
        $cipher = "AES-256-ECB";
        $chiperRaw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA);
        return trim(base64_encode($chiperRaw));
    }

    function decrypt($ciphertext, $key = null)
    {
        $key = $key ?? $this->decryptionKey;
        $cipher = "AES-256-ECB";
        $raw = base64_decode($ciphertext);
        return openssl_decrypt($raw, $cipher, $key, OPENSSL_RAW_DATA);
    }
}
