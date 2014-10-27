<?php
/*
* replace the values in this array with your Braintree API keys, and set the redirectUrl to
* the script that will confirm the TR transaction. In production, this should ALWAYS use SSL (https://)
*/
$settings = [
'environment' => 'sandbox', // possible values are sandbox or production
'merchantId' =>  'YOUR_MERCHANT_ID',
'publicKey' => 'YOUR_PUBLIC_KEY',
'privateKey' => 'YOUR_PRIVATE_KEY',
'redirectUrl' => 'https://DOMAIN.COM' // no trailing slash
];