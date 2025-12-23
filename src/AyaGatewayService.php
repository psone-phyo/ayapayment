<?php

namespace AyaPayment;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as GuzzleHttpClient;

class AyaGatewayService
{
    // Define the base URL for AYA Payment Gateway
    protected $baseUrl;
    protected $client;
    protected $serviceCacheKey = 'AYA_SERVICE_LIST_CACHE';

    public function __construct()
    {
        $this->baseUrl = config('ayapayment.gateway.payment_url');
        $this->client = new GuzzleHttpClient([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function getServiceList()
    {
        if (Cache::has($this->serviceCacheKey)) {
            return Cache::get($this->serviceCacheKey);
        }
        $timestamp = Carbon::now()->timestamp;
        try {
            $response = $this->client->post($this->baseUrl . '/v1/payment/services', [
                'body' => json_encode([
                    'appKey' => config('ayapayment.gateway.app_key'),
                    'timestamp' => $timestamp,
                    'checkSum' => $this->generateCheckSum([
                        config('ayapayment.gateway.app_key'),
                        config('ayapayment.gateway.app_secret'),
                        $timestamp
                    ])
                ])
            ]);
            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody()->getContents(), true);
                Cache::put($this->serviceCacheKey, $responseData['data'], Carbon::now()->addMinutes(5));
                return $responseData['data'];
            } else {
                Log::error('Get AYA Service List Error: ' . $response->getBody()->getContents());
            }
        } catch (\Throwable $th) {
            Log::error('Get AYA Service List Error: ' . $th->getMessage());
        }
        return [];
    }


    public function enquiryTransaction($merchOrderId)
    {
        $timestamp = Carbon::now()->timestamp;
        $response = $this->client->post($this->baseUrl . '/v1/payment/enquiry', [
            'body' => json_encode([
                'appKey' => config('ayapayment.gateway.app_key'),
                'timestamp' => $timestamp,
                'merchOrderId' => $merchOrderId,
                'checkSum' => $this->generateCheckSum([
                    $merchOrderId,
                    $timestamp,
                    config('ayapayment.gateway.app_key')
                ])
            ])
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }


    public function htmlFormData($merchOrderId, $amount, $channel, $method, $userRef1 = "",$userRef2= "", $description = ""): array
    {
        $timestamp = Carbon::now()->timestamp;
        $form['url'] = config('payment.aya_gateway.payment_url') . "/v1/payment/request";
        $form["values"] = "";
        $formData = [
            'merchOrderId' => $merchOrderId,
            'amount' => ceil($amount),
            'appKey' => config('ayapayment.gateway.app_key'),
            'timestamp' => $timestamp,
            'userRef1' => $userRef1,
            'userRef2' => $userRef2,
            'userRef3' => "",
            'userRef4' => "",
            'userRef5' => "",
            'description' => $description,
            'currencyCode' => 104,
            'channel' => $channel,
            'method' => $method,
            'overrideFrontendRedirectUrl' => config('ayapayment.gateway.frontend_url'),
        ];
        $formData['checkSum'] = $this->generateCheckSum(array_values($formData));
        foreach ($formData as $key => $value) {
            $form["values"] .= "<input type='hidden' name='" . $key . "' value='" . $value . "'>";
        }
        return $form;
    }


    public function generateCheckSum($dataArray)
    {
        $hashString = implode(':', $dataArray);
        $checkSum = hash_hmac('sha256', $hashString, config('ayapayment.gateway.app_secret'));
        return $checkSum;
    }

    public function testing(){
        return "Aya Payment Service is working!";
    }
}
