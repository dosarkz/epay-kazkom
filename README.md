# Qazcom epay service

Payment package kazcom epay api for laravel 5.2 - 5.6

<a href="https://testpay.kkb.kz/doc/htm/">Epay docs</a>

## Install
```
composer require dosarkz/epay-kazcom
```

## For laravel 5.4 

### Service provider to config/app.php

```
  Dosarkz\EPayKazCom\EpayServiceProvider::class
```

### Facade 

``` 
'Epay' => \Dosarkz\EPayKazCom\Facades\Epay::class
```

## Publish config file 

```
  php artisan vendor:publish
```

## Epay requests

### Basic auth pay example
```php
$pay =  Epay::basicAuth([
              'order_id' => '01111111111',
              'currency' => '398',
              'amount' => 9999,
              'email' => 'your-email@gmail.com',
              'hashed' => true,
        ]);
          
$pay->generateUrl();
```

### Check pay example
```php
$checkPay = Epay::checkPay( [ 'order_id' => '01111111111' ] );

$response = Epay::request( $checkPay->generateUrl() );
```

### Control pay example
```php
$controlPay = Epay::controlPay( [
    'order_id' => '01111111111',
    'amount' => 9999,
    'approval_code' => '170407',
    'reference' => '180711170407',
    'currency' => '398',
    'command_type' => 'complete', 
    'reason' => 'for test'
    ] );

$response = Epay::request( $controlPay->generateUrl() );
```

## Epay responses

### Basic auth POST_LINK response parser
```php
$response = request()->input('response');

if ($response)
{
    $payResponse = Epay::handleBasicAuth( $response );

    $orderId = $payResponse->getOrderId();

    // ... get order amount for check
    
    $amount = 9999;
    Log::info( $payResponse->getResponse() );
    Log::info( 'pay status=' . ($payResponse->isSuccess( [ 'amount' => $amount ] ) ? 'success' : 'fail') );

} 
```

### Check pay response parser
```php
$checkPay = Epay::checkPay( [ 'order_id' => '01111111111' ] );

$response = Epay::request( $checkPay->generateUrl() );

if ($response) {
    $checkPayResponse = Epay::handleCheckPay( $response );
    
    Log::info( 'state=' . $checkPayResponse->getPayState() );
    Log::info( 'status=' . ( $checkPayResponse->isSuccess() ? 'success' : 'fail' ));
    Log::info( $checkPayResponse->getResponse() );
}
```

### Control pay response parser
```php
$controlPay = Epay::controlPay( [
            'order_id' => '01111111111',
            'amount' => 9999,
            'approval_code' => '170407',
            'reference' => '180711170407',
            'currency' => '398',
            'command_type' => 'complete', //reverse || complete || refund
            'reason' => 'for test'
            ] );
            
$url = $controlPay->generateUrl();
            
if ( is_string($url) ) {

    $response = Epay::request( $url );

    if ($response) {

        $controlPayResponse = Epay::handleControlPay( $response );

        Log::info( 'message=' . $controlPayResponse->getResponseMessage() );
        Log::info( 'status=' . ( $controlPayResponse->isSuccess() ? 'success' : 'fail' ));

        Log::info( $controlPayResponse->getResponse() ); 
    } 
} 
```


   
