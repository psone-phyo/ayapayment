# 💳 AYAPay In-app Laravel Package

A Laravel package to integrate **AYAPay In-app**.

---

## 📑 Table of Contents

- [Installation](#-installation)
- [Configuration](#%EF%B8%8F-configuration)
- [Example Usage](#-example-usage)
- [Methods](#-methods)
- [License](#-license)
- [Credits](#-credits)

---

## 📦 Installation

Install via Composer:

```bash
composer require gmbfgp/ayapayment
```

## ⚙️ Configuration

1. Publish Config File:

```bash
php artisan vendor:publish --provider="AyaPayment\AyaPaymentServiceProvider" --tag="ayapayment-config"
```

2. Add the following to your .env file:

```bash
AYA_PAY_CONSUMER_KEY="your_consumer_key"
AYA_PAY_CONSUMER_SECRET="your_consumer_secret"
AYA_PAY_MERCHANT_PHONE="your_merchant_phone"
AYA_PAY_MERCHANT_PASSWORD="your_merchant_password"
AYA_PAY_PAYMENT_URL="https://payment_base_url.com/"
AYA_PAY_CURRENCY="curreny"
AYA_PAY_CALLBACK_URL="https://example.com/callback/"
AYA_PAY_SERVICE_CODE="service_code"
AYA_PAY_DEEP_LINK="ayapay://mLink/screen=DynamicConfirm?requestedId="
AYA_PAY_DECRYPTION_KEY="your_decryption_key"
```

## 🚀 Example Usage

```bash
use AyaPayment\AYAPayService;
$ayaService = new AYAPayService();
```

## 🧰 Methods

1. ### getPayloadForPayment($customer_phone, $amount, $currency, $externalTransactionId, $externalAdditionalData)

Description: Prepares the HTML form and payload array for frontend payment submission.
Note: Merchant login process included.

Example:
```bash
//input
$payload = $aya->getPayloadForPayment(
    '09912345678',
    15000,
    'MMK',
    $externalTransactionId,
    $externalAdditionalData
);

//output
[
    'url' => 'https://api.ayapay.com/...',
    'accessToken' => 'access_token_string',
    'authorizationKey' => 'authorization_key_string',
    'contentType' => 'application/x-www-form-urlencoded',
    'payload' => [
        'customerPhone' => '09912345678',
        'amount' => 15000,
        'currency' => 'MMK',
        'externalTransactionId' => 'TXN-001',
        'externalAdditionalData' => 'order-payment',
        'serviceCode' => 'service_code'
    ],
    'values' => "<input type='hidden' ... />" // full HTML form fields
]

```

2. ### requestPushPayment($accessToken, $authorizationKey, $customer_phone, $amount, $currency, $externalTransactionId, $externalAdditionalData)

Description: Initiates a push noti payment.

Example:
```bash
//input
$response = $aya->requestPushPayment(
    $accessToken,
    $authorizationKey,
    '09912345678',
    15000,
    'MMK',
    $externalTransactionId,
    $externalAdditionalData
);

//output
[
    'err' => 200
    'message' => Success
    'data' => [
            'externalTransactionId' => 4003700,
            'referenceNumber' => 66d9647b93dd38e431150afb
    ]
]

```

3. ### requestQRPayment($accessToken, $authorizationKey, $customer_phone, $amount, $currency, $externalTransactionId, $externalAdditionalData)

Description: Initiates a qr payment.

Example:
```bash
//input
$response = $aya->requestPushPayment(
    $accessToken,
    $authorizationKey,
    '09912345678',
    15000,
    'MMK',
    $externalTransactionId,
    $externalAdditionalData
);

//output
[
    'err' => 200,
    'message' => 'Success',
    'data' => [
        'qrdata' => '000201010212504200245e785a3cf9cf61168c30cc850110WND02907245148002466a9ee9be89 5b1e1af824633011620005999200059995204test540410005303MMK5802MM5918INYA LAN TOLL GATE6006Yangon62820106T00001032466a9ee9be895b1e1af82463705 122109435 0415 850245e216cc54d067ef2b7adafb264280002my0118INYA LAN TOLL GATE6304fe91',
        'mmqrdata' => '',
        'merchantId' => '',
        'externalTransactionId' => 'T00001',
        'referenceNumber' => '210943504158',
        'amount' => 1000,
        'fees' => [
            'debitFee' => 0,
            'creditFee' => 0
        ],
        'currency' => 'MMK',
        'expiredAt' => '2024-09-06T05:00:04.186Z'
    ]
]

```

4. ### checkStatus($referenceNumber, $externalTransactionId)

Description: Check payment status.

Example:
```bash
//input
$refund = $aya->checkStatus($referenceNumber, $externalTransactionId);

//output
[
    "err": 200,
    "message": "Success", "status": "pending",
    "transRefId": ""
]

```

5. ### Encryption Utilities

encrypt($plaintext, $key = null) → returns AES-256-ECB Base64 string

decrypt($ciphertext, $key = null) → decrypts the encrypted string

encrypt_decrypt($data, $key = null) → test method for encrypt/decrypt round

Example:
```bash
//input
$decrypted = $aya->decrypt($encrypted); //$key=null will use the decryption key from config

//output (json string)
"{"err":200,"message":"Success","data":{"externalTransactionId":"T00001"..."

```

## 📝 License

MIT License — See the LICENSE file for more information.

## 👨‍💻 Credits

Developed by GMBF Group.

