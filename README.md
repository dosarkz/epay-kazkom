# kazcom epay service
Payment package kazcom epay api for laravel 5.2 & 5.3
## Install
```
composer require dosarkz/epay-kazcom
```

##Service provider to config/app.php

```
  Dosarkz\EPayKazCom\EpayServiceProvider::class
```

##Facade 

``` 
'Epay' => \Dosarkz\EPayKazCom\Facades\Epay::class
```

##Publish config file 

```
  php artisan vendor:publish
```

###Basic auth pay example:

```
$pay =  Epay::basicAuth([
              'order_id' => 01111111111,
              'currency' => '398',
              'amount' => 9999,
              'hashed' => true,
        ]);
          
$pay->generateUrl();
```

###Recurrent auth pay example:
```
$pay =  Epay::recurrentAuthPay([
                'order_id' => 01111111111,
                'currency' => '398',
                'person_id' => 1,
                'amount' =>  9999,
                'recur_freq' => 100,
                'recur_exp' => 20221231,
                'hashed' => true,
]);
```
