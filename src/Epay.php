<?php
namespace Dosarkz\EPayKazCom;

class Epay
{
    /**
     * @var
     */
    public  $merchant_certificate_id;
    /**
     * @var
     */
    public  $merchant_name;
    /**
     * @var
     */
    public  $merchant_id;
    /**
     * @var
     */
    public  $private_key_path;
    /**
     * @var
     */
    public  $private_key_pass;
    /**
     * @var
     */
    public  $public_key_path;
    /**
     * @var
     */
    public $amount;
    /**
     * @var
     */
    public $currency = 398;
    /**
     * @var
     */
    public $order_id;
    /**
     * @var
     */
    public $person_id;
    /**
     * @var
     */
    public $session_id;
    /**
     * @var
     */
    public $approve;
    /**
     * @var
     */
    public $abonent_id;
    /**
     * @var
     */
    public $command_type;
    /**
     * @var
     */
    public $recepient;
    /**
     * @var
     */
    public $service_id;
    /**
     * @var
     */
    public $reference;
    /**
     * @var
     */
    public $email;
    /**
     * @var
     */
    public $approval_code;
	/**
	 * @var
	 */
	public $reason;
    /**
     * @var
     */
    public $kkb;
    /**
     * @var
     */
    public $template_path;
    /**
     * @var int
     */
    public $recur_freq = 10;
    /**
     * @var int
     */
    public $recur_exp = 20221231;
    /**
     * @var
     */
    public $template;

    /**
     * @var
     */
    public $appendix;

	/**
	 * @var string
	 */
	public $epay_server_url;

	/**
	 * @var int
	 */
	public $request_timeout = 10;

    public function __construct()
    {
        config('epay.pay_test_mode',true) ? $this->setTestParams() : $this->setProductionParams();
        $this->kkb = $this->authBank();
    }

    /**
     * @param $params
     * @return RegularPay
     */
    public function regularPay($params)
    {
        return new RegularPay($params);
    }

    /**
     * @param $params
     * @return RecurAuthPay
     */
    public function recurrentAuthPay($params)
    {
        return new RecurAuthPay($params);
    }

    /**
     * @param $params
     * @return BasicAuth
     */
    public function basicAuth($params)
    {
        return new BasicAuth($params);
    }

	/**
	 * @param $params
	 * @return CheckPay
	 */
	public function checkPay($params)
	{
		return new CheckPay($params);
	}

	/**
	 * @param $params
	 * @return ControlPay
	 */
	public function controlPay($params)
	{
		return new ControlPay($params);
	}

    public function recurrentAuth($params)
    {
        return new RecurAuthPay($params);
    }

    private function setTestParams()
    {
        $this->merchant_certificate_id  = '00C182B189';
        $this->merchant_name            = 'Demo Shop';
        $this->merchant_id              = '92061101';
        $this->private_key_path         = __DIR__.'/certificates/test_prv.pem';
        $this->private_key_pass         = 'nissan';
        $this->public_key_path          = __DIR__.'/certificates/kkbca.pem';
        $this->epay_server_url          = 'https://testpay.kkb.kz';

    }

    private function setProductionParams()
    {
        $this->merchant_certificate_id  = config('epay.MERCHANT_CERTIFICATE_ID');
        $this->merchant_name            = config('epay.MERCHANT_NAME');
        $this->merchant_id              = config('epay.MERCHANT_ID');
        $this->private_key_path         = config('epay.PRIVATE_KEY_PATH');
        $this->private_key_pass         = config('epay.PRIVATE_KEY_PASS');
        $this->public_key_path          = config('epay.PUBLIC_KEY_PATH');
	    $this->epay_server_url          = 'https://epay.kkb.kz';

    }


    public function getRemoteParams($params)
    {
        $this->order_id = $params['order_id'];
        $this->currency = $params['currency'];
        $this->amount = $params['amount'];
        $this->reference = $params['reference'];
        $this->command_type = $params['command_type'];
        $this->approval_code = $params['approval_code'];

        $kkb = $this->authBank();
        $template = $this->getTemplate($params['type'], $kkb);

        return $template;
    }

    public function authBank()
    {
        $kkb = new KkbSign;
        $kkb->invert();
        if (!$kkb->load_private_key($this->private_key_path, $this->private_key_pass)) {
            if ($kkb->ecode > 0) {
                return $kkb->estatus;
            };
        };
        return $kkb;
    }

    public function getTemplate($type, $kkb)
    {
        $template = null;

        switch($type)
        {
            case 'remote':
                $array = [
                    'ORDER_ID',
                    'CURRENCY',
                    'MERCHANT_ID',
                    'AMOUNT',
                    'REFERENCE',
                    'COMMAND_TYPE',
                    'APPROVAL_CODE'
                ];

                $params = $this->getListParams($array);
                $header_template = $this->setParamsToTemplate($this->remote_template,$params);
                $template =  $this->generateXmlTemplate($header_template,$kkb);

                break;

        }

        return $template;
    }

    /**
     * @param $array
     * @return array
     */
    public function getListParams($array)
    {
        $params = [];
        foreach (get_object_vars($this) as $key => $value) {

            if (in_array(strtoupper($key), $array))
            {
                $params[strtoupper($key)] = $value;
            }
        }

        return $params;
    }

    /**
     * @param $template
     * @param $kkb
     * @return string
     */
    public function generateXmlTemplate($template, $kkb)
    {
        $result_sign = '<merchant_sign type="RSA">' . $kkb->sign64($template) . '</merchant_sign>';
        $xml = "<document>" . $template . $result_sign . "</document>";

        return $xml;
    }

    /**
     * @param $template
     * @param $request
     * @return mixed
     */
    public function setParamsToTemplate($template, $request)
    {
        foreach ($request as $key => $value) {
            $template = preg_replace('/\['.$key.'\]/', $value, $template);
        }

        return $template;
    }

    /**
     * @return string
     */
    public function generateAppendix()
    {
        $template = '<document><item number="'.$this->order_id.'" name="payment" quantity="1" amount="'.$this->amount.'"/></document>';

        return base64_encode($template);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->request_timeout); // times out after 4s
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $result = curl_exec($ch); // run the whole process
        curl_close($ch);
        return $result;
    }

	/**
	 * @param $path
	 * @return string
	 */
	public  function readXmlFile($path)
	{
		$doc = new \DOMDocument();
		$doc->load($path);
		return $doc->saveXML($doc->documentElement);
	}
}