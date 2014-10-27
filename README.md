##Braintree Transparent Redirect Integration using PHP Client Library ##

This is a very simple demonstration of an integration of [Braintree Payment System's](http://www.braintreepayments.com) Transparent Redirect method, which was written in support of an [article](http://www.sitepoint.com) on Sitepoint.com.

After cloning this repository, you will need to install the library using [Composer](http://www.getcomposer.org) by running the following command:

	/path/to/php composer.phar install

or

	composer install

if you have Composer installed globally. 

Finally, open `settings.php` and replace the default values with your Braintree API keys and your domain name.

### Requirements ###

I used PHP's [short array syntax](https://wiki.php.net/rfc/shortsyntaxforarrays) in these examples, so you'll need PHP 5.4 to use the examples as-is. Additionally, your system will need to meet the minimum requirements to run the Braintree PHP client library. You can find those [here](https://github.com/braintree/braintree_php).
