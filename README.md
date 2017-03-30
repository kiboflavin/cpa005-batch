# cpa005-batch

RBC specific(?) at the moment, but I'll accept pull requests.

This library works, but I'm not sure how happy I am with the code - it needs tests.


## example

```php

<?php

require_once('vendor/autoload.php');

$batch = new \CPA005\Request();
$batch->processing_centre = \CPA005\ProcessingCentre::TORONTO;
$batch->client_number = rand(1000000000, 9999999999);
$batch->short_company_name = 'Fifteen Charact';
$batch->long_company_name = 'Thirty Character LongLong Name';

for ($x = 0; $x < 7; $x++) {

	$trans = new \CPA005\Transaction();
	$trans->transaction_code = \CPA005\TransactionCode::INTERNET_ACCESS_PAYMENT;
	$trans->institution = rand(10000,99999);
	$trans->transit = rand(100, 999);
	$trans->account = rand(1000000, 9999999);
	$trans->customer_name = 'Robert Dobbs';
	$trans->identifier = rand(1000, 99999);
	$trans->amount = rand(500, 9999);

	$batch->add_transaction($trans);	
}

echo $batch->dump();

```
