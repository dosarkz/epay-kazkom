<?php
namespace Dosarkz\EPayKazCom\Tests;

use Dosarkz\EPayKazCom\Facades\Epay;
use Illuminate\Foundation\Testing\TestCase;

class PayTest extends TestCase
{
    /**
     * https://testpay.kkb.kz/doc/htm/fields_description.html
     *
     * @return bool
     */
    public function testBasicPay()
    {
        $pay = Epay::basicAuth([
            'order_id' => rand(111111111111, 999999999999999),
            'currency' => '398',
            'amount' => rand(100, 9999),
            'hashed' => true,
        ]);

        if ($pay->generateUrl())
        {
            return true;
        }

        return false;
    }


    /**
     * https://testpay.kkb.kz/doc/htm/regul_pay_rrn.html
     * 1. Запрос на проведение первичного платежа с последующей регулярностью.
     *
     * @return bool
     */
    public function testRecurrentAuth()
    {
        $pay = Epay::recurrentAuth([
            'order_id' => rand(111111111111, 999999999999999),
            'currency' => '398',
            'amount' => rand(100, 9999),
            'hashed' => true,
            'recur_freq' => '10',
            'recur_exp' => '20221231',
            'person_id' => '135'
        ]);

        if ($pay->generateUrl())
        {
            return true;
        }

        return false;
    }

    /**
     * 2. Как сделать Регулярный платеж. (Первичный платеж уже был сделан).
     * https://testpay.kkb.kz/doc/htm/regul_pay_rrn.html
     *
     * @return bool
     */
    public function testRegularPay()
    {
       $regular_pay = Epay::regularPay([
           'order_id' => rand(111111111111, 999999999999999),
           'currency' => '398',
           'amount' => '50',
           'email'  => 'client@kkb.kz',
           'hashed' => true,
           'reference' => '150218150813'
       ]);

        $xml =  simplexml_load_string($regular_pay->generateUrl());
        $xml_to_array  = json_decode(json_encode((array)$xml), TRUE);

        // check american express card

        if (array_key_exists('@attributes', $xml_to_array['error']))
        {
            if ($xml_to_array['error']['@attributes']['input'] != null ||
                $xml_to_array['error']['@attributes']['payment'] != null ||
                $xml_to_array['error']['@attributes']['system'] != null
            )
            {
               return  $xml_to_array['error']['@attributes'];
            }
        }

        $payment_array = $xml_to_array['payment']['@attributes'];

        if($payment_array['message'] == "Approved")
        {
            return $payment_array;
        }

        return false;
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}