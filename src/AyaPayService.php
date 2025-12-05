<?php

namespace AyaPayment;

use GuzzleHttp\Client as GuzzleHttpClient;

class AyaPayService
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

    public function __constuct()
    {
        $this->consumerKey = config('ayapayment.pay.consumer_key');
        $this->consumerSecret = config('ayapayment.pay.consumer_secret');
        $this->merchantPhone = config('ayapayment.pay.merchant_phone');
        $this->merchantPassword = config('ayapayment.pay.merchant_password');
        $this->baseUrl = config('ayapayment.pay.payment_url');
        $this->currency = config('ayapayment.pay.currency');

        $this->urlPrefix = $this->baseUrl . 'merchant/1.0.0/thirdparty/merchant/';
        $this->merchantTokenUrl = $this->baseUrl . 'token';
        $this->merchantLoginUrl = $this->urlPrefix . 'login';
        $this->paymentRequestUrl = $this->urlPrefix . 'requestPushayapayment';
        $this->QRRequestUrl = $this->urlPrefix . 'requestQRPayment';
        $this->refundPaymentUrl = $this->urlPrefix . 'refundPayment';
    }

    public function accessToken()
    {
        try {
            $encrypt_data = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

            $http = new GuzzleHttpClient(
                [
                    'base_uri' => $this->merchantTokenUrl,
                    'headers' => [
                        'Authorization' => 'Basic ' . $encrypt_data,
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]
            );
            $data['grant_type'] = 'client_credentials';
            $response = $http->post('', [
                'form_params' => $data
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

    public function requestPushPayment($customer_phone,  $amount, $currency = 'MMK', $externalTransactionId, $externalAdditionalData = '')
    {
        try {
            $loginKeys = $this->merchantLogin();
            if (!$loginKeys) {
                return false;
            }
            $access_token = $loginKeys['access_token'];
            $authorization_key = $loginKeys['token']['token'];

            $form['url'] = $this->paymentRequestUrl;
            $form['accessToken'] = $access_token;
            $form['authorizationKey'] = $authorization_key;
            $form['contentType'] = 'application/json';
            $form['values'] =
                "<input type='hidden' id='customerPhone' name='customerPhone' value='" . $customer_phone . "'/>" .
                "<input type='hidden' id='amount' name='amount' value='" . $amount . "'/>" .
                "<input type='hidden' id='currency' name='currency' value='" . $currency . "'/>" .
                "<input type='hidden' id='externalTransactionId' name='externalTransactionId' value='" . $externalTransactionId . "'/>" .
                "<input type='hidden' id='externalAdditionalData' name='externalAdditionalData' value='" . $externalAdditionalData . "'/>";
            return $form;
        } catch (\Exception $e) {
            \Log::error('Aya pay push payment error :' . $e);
            return false;
        }
    }

    public function requestQRPayment($amount, $currency = 'MMK', $externalTransactionId, $externalAdditionalData = '')
    {
        try {
            $loginKeys = $this->merchantLogin();
            if (!$loginKeys) {
                return false;
            }
            $access_token = $loginKeys['access_token'];
            $authorization_key = $loginKeys['token']['token'];

            $form['url'] = $this->QRRequestUrl;
            $form['accessToken'] = $access_token;
            $form['authorizationKey'] = $authorization_key;
            $form['contentType'] = 'application/x-www-form-urlencoded';
            $form['values'] =
                "<input type='hidden' id='amount' name='amount' value='" . $amount . "'/>" .
                "<input type='hidden' id='currency' name='currency' value='" . $currency . "'/>" .
                "<input type='hidden' id='externalTransactionId' name='externalTransactionId' value='" . $externalTransactionId . "'/>" .
                "<input type='hidden' id='externalAdditionalData' name='externalAdditionalData' value='" . $externalAdditionalData . "'/>";
            return $form;
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

    function encrypt_decrypt($data, $key)
    {
        $cipher = "AES-256-ECB";
        $chiperRaw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA);
        $ciphertext = trim(base64_encode($chiperRaw));
        $cipherHex = bin2hex($chiperRaw);

        $chiperRaw = base64_decode($ciphertext);

        $originalData = openssl_decrypt($chiperRaw, $cipher, $key, OPENSSL_RAW_DATA);
        return $originalData;
    }
}