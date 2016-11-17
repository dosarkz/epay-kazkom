<?php
namespace Dosarkz\EPayKazCom;
/**
 * Class RegularPay
 * @package Dosarkz\EPayKazCom
 */
class RegularPay extends Epay
{
    /**
     * @var string
     */
    public $template_path    = 'templates/regular_template.xml';

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct();

        $this->order_id         = isset($params['order_id'])    ? $params['order_id'] : null;
        $this->currency         = isset($params['currency'])    ? $params['currency'] : $this->currency;
        $this->amount           = isset($params['amount'])      ? $params['amount'] : 0;
        $this->email            = isset($params['email'])       ? $params['email'] : "";
        $this->hashed           = isset($params['hashed'])      ? $params['hashed'] : false;
        $this->reference        = isset($params['reference'])  ? $params['reference'] : null;

        $this->template         = $this->generateXml();
    }

    /**
     * @return null|string
     */
    private function generateXml()
    {
        if(file_exists(__DIR__.'/'.$this->template_path)){

            $xml = $this->readXmlFile(__DIR__.'/'.$this->template_path);

            $array = [
                'MERCHANT_CERTIFICATE_ID',
                'MERCHANT_NAME',
                'ORDER_ID',
                'CURRENCY',
                'MERCHANT_ID',
                'AMOUNT',
                'REFERENCE',
                'EMAIL',
            ];

            $params = $this->getListParams($array);
            $header_template = $this->setParamsToTemplate($xml,$params);
            $template =  $this->generateXmlTemplate($header_template,$this->kkb);

            if ($this->hashed)
            {
                return base64_encode($template);
            }

            return $template;
        }

        return null;
    }

    /**
     * @return string
     */
    public function generateUrl()
    {
        $params = http_build_query(['Signed_Order_B64' => $this->template]);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $params
            ),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $context  = stream_context_create($opts);

        if(config('epay.pay_test_mode') == true)
        {
            $result = file_get_contents('https://testpay.kkb.kz/jsp/hbpay/rec.jsp', false, $context);
        }else{
            $result = file_get_contents('https://epay.kkb.kz/jsp/hbpay/rec.jsp', false, $context);
        }

        return $result;

    }

}