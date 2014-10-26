<?php
    require ('vendor/autoload.php');

    /*
     * replace the values in this array with your Braintree API keys, and set the redirectUrl to
     * the script that will confirm the TR transaction. In production, this should ALWAYS use SSL (https://)
     */
    $settings = [
      'environment' => 'sandbox', // possible values are sandbox or production
      'merchantId' =>  'YOUR_MERCHANT_ID',
      'publicKey' => 'YOUR_PUBLIC_KEY',
      'privateKey' => 'YOUR_PRIVATE_KEY',
      'redirectUrl' => 'http://DOMAIN.COM/transparent-redirect-form-basic.php'
    ];

    /*
     * replace the following with the configuration code from the Braintree Control Panel, which
     * will contain your unique API keys
     */
    Braintree_Configuration::environment($settings['environment']);
    Braintree_Configuration::merchantId($settings['merchantId']);
    Braintree_Configuration::publicKey($settings['publicKey']);
    Braintree_Configuration::privateKey($settings['privateKey']);

    $status = '';

    if(isset($_GET['http_status']) && $_GET['http_status'] == '200') {

        try {
            $result = Braintree_TransparentRedirect::confirm($_SERVER['QUERY_STRING']);
            if ($result->success) {
                $status = 'Your transaction was processed succesfully.';
            } else {
                $status = $result->message;
            }
        } catch (Braintree_Exception_NotFound $e) {
            $status = 'Due to security reasons, the reload button has been disabled on this page.';
        }

    }

    $tr_data = Braintree_TransparentRedirect::transactionData([
         'transaction' => [
            'type' => Braintree_Transaction::SALE,
            'amount' => '100.00',
            'options' => [
                'submitForSettlement' => true
            ]
        ],
        'redirectUrl' => $settings['redirectUrl']
    ]);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Braintree Payment Solutions Transparent Redirect Demonstration</title>
    <link href="style.css" type="text/css" rel="stylesheet">
</head>
<body>
    <div id="wrap">
        <?php if ($status):?>
            <div class="status"><?= $status?></div>
        <?php endif;?>
        <form method="post" action="<?= Braintree_TransparentRedirect::url()?>" autocomplete="off">
            <label>Name on Card: <input type="text" name="transaction[credit_card][cardholder_name]"></label>

            <label>Card Number: <input type="text" name="transaction[credit_card][number]"></label>

            <label>CVV: <input type="text" name="transaction[credit_card][cvv]" class="short"></label>

            <label>Expiration Date (MM/YYYY): <input type="text" name="transaction[credit_card][expiration_date]" class="short"></label>

            <input type="submit" value="submit payment">

            <input type="hidden" name="tr_data" value="<?=$tr_data?>">
        </form>
    </div>
</body>
</html>