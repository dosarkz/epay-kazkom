# kazcom epay service
Payment package kazcom epay api for laravel 5.2
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
  php artisan vendor:publish --force
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

