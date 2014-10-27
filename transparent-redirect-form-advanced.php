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
        'redirectUrl' => 'http://DOMAIN.COM/transparent-redirect-form-advanced.php'
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
    $formValues = [
      'first_name' => '',
      'last_name' => '',
      'email' => '',
      'color' => ''
    ];

    if(isset($_GET['http_status']) && $_GET['http_status'] == '200') {

        try {
            $result = Braintree_TransparentRedirect::confirm($_SERVER['QUERY_STRING']);
            if ($result->success) {
                /*
                 *  Braintree_Result_Success has a "transaction" property, which is an object
                 *  with details of the transaction
                 */
                $customer = $result->transaction->customer;
                $customFields = $result->transaction->customFields;
                if ($customer['email'] == '') {
                    Braintree_Transaction::void($result->transaction->id);
                    $status = 'Email address is a required field';

                } else {
                    $status = 'Your transaction was processed successfully.';
                }
            } else {
                $status = $result->message;
                /*
                 *  Braintree_Result_Error has a "params" property, which contains the form data
                 *  in array format
                 */
                $customer = $result->params['transaction']['customer'];
                $customFields = $result->params['transaction']['customFields'];
            }
            $formValues['first_name'] = $customer['firstName'];
            $formValues['last_name'] = $customer['lastName'];
            $formValues['email'] = $customer['email'];
            $formValues['color'] = $customFields['color'];
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
            <label>Customer First Name: <input type="text" name="transaction[customer][first_name]" value="<?= $formValues['first_name']?>"></label>

            <label>Customer Last Name: <input type="text" name="transaction[customer][last_name]" value="<?= $formValues['last_name']?>"></label>

            <label>Customer Email: <input type="text" name="transaction[customer][email]" value="<?= $formValues['email']?>"></label>

            <label>Customer Favorite Color: <input type="text" name="transaction[custom_fields][color]" value="<?= $formValues['color']?>"></label>

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