# 💳 AYA Gateway Laravel Service

A Laravel service to integrate **AYA Payment Gateway** for MPU, AYA Pay, and other AYA-supported channels.

---

## 📑 Table of Contents

- [Features](#-features)
- [Installation](#-installation)
- [Configuration](#%EF%B8%8F-configuration)
- [Example Usage](#-example-usage)
- [Methods](#-methods)
- [License](#-license)
- [Credits](#-credits)

---

## ✅ Features

- Fetch supported payment services (MPU, AYA Pay, etc.)
- Generate HTML form data for payments
- Enquiry transaction status
- Checksum generation for secure requests
- Cache service list for 5 minutes to reduce API calls

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
AYA_GATEWAY_PAYMENT_URL=https://pgw.ayainnovation.com/
AYA_GATEWAY_APP_KEY=your_app_key
AYA_GATEWAY_APP_SECRET=your_app_secret
AYA_GATEWAY_FRONTEND_URL=https://example.com/redirect/
AYA_GATEWAY_BACKEND_URL=https://example.com/callback/
```

## 🚀 Example Usage

```bash
use AyaPayment\AYAGatewayService;

$ayaService = new AYAGatewayService();
```

## 🧰 Methods

1. ### getServiceList()

Description: Fetches supported payment services from AYA Gateway.

```bash
$services = $ayaService->getServiceList();
```

2. ### enquiryTransaction($merchOrderId)

Description: Checks status of a payment transaction.

```bash
$response = $ayaService->enquiryTransaction($merchOrderId);
```

3. ### htmlFormData($merchOrderId, $amount, $channel, $method, $userRef1 = "",$userRef2= "", $userRef3= "", $userRef4= "", $userRef5= "",$description = "")

Description: Generate HTML Form Data for Payment

```bash
//input
$formData = $ayaService->htmlFormData(
    $merchOrderId = 12345,
    $amount = 1000,
    $channel = 'NOTI',
    $method = 'aya_pay',
    $userRef1 = '',
    $userRef2 = '',
    $userRef3 = '',
    $userRef4 = '',
    $userRef5 = '',
    $description = 'note'
);

//output
[
    'url' => "https://aya-pay-gateway-example.com/v1/payment/request",
    'values'  => "<input type='hidden' name='merchOrderId' value='1234'><input type='hidden'..."
]

// Blade View
<form method="POST" action="{{ $form['url'] }}">
    {!! $form['values'] !!}
    <button type="submit">Pay Now</button>
</form>
```

4. ### generateCheckSum($dataArray)

Description: Generates HMAC SHA256 checksum for secure requests.

```bash
//input
$response = $ayaService->generateCheckSum($arrayData);

//output
"hased_value"
```

## 📝 License

MIT License — See the LICENSE file for more information.

## 👨‍💻 Credits

Developed by GMBF Group.

