<?php
namespace Dosarkz\EPayKazCom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class RecurAuthPay
 * @package packages\dosarkz\epayKazcom\src
 */
class RecurAuthPay extends Epay
{
    /**
     * @var string
     */
    public $template_path  = 'templates/regular_auth_template.xml';

    /**
     * @param array $params
     *
     */
    public function __construct(array $params = [])
    {
        parent::__construct();

        $this->order_id         = isset($params['order_id'])    ? $params['order_id'] : null;
        $this->currency         = isset($params['currency'])    ? $params['currency'] : $this->currency;
        $this->person_id        = isset($params['person_id'])   ? $params['person_id'] : null;
        $this->amount           = isset($params['amount'])      ? $params['amount'] : 0;
        $this->email            = isset($params['email'])       ? $params['email'] : "";
        $this->hashed           = isset($params['hashed'])      ? $params['hashed'] : false;
        $this->recur_freq       = isset($params['recur_freq'])  ? $params['recur_freq'] : $this->recur_freq;
        $this->recur_exp        = isset($params['recur_exp'])   ? $params['recur_exp'] : $this->recur_exp;
        $this->template         = $this->generateXml();
        $this->appendix         = $this->generateAppendix();
    }

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
                'PERSON_ID',
                'SESSION_ID',
                'APPROVE',
                'SERVICE_ID',
                'ABONENT_ID',
                'RECEPIENT',
                'RECUR_FREQ',
                'RECUR_EXP'
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

    public function generateUrl()
    {
        $request = new Request();
        $back_link = config('epay.EPAY_BACK_LINK');
        $post_link = config('epay.EPAY_POST_LINK');
        $form_template = config('epay.EPAY_FORM_TEMPLATE');

        $request->merge([
            'Signed_Order_B64'  =>  $this->template,
            'recur_freq'        =>  $this->recur_freq,
            'recur_exp'         =>  $this->recur_exp,
            'person_id'         =>  $this->person_id,
            'BackLink'          =>  isset($back_link) ? config('epay.EPAY_BACK_LINK') : null,
            'PostLink'          =>  isset($post_link) ? config('epay.EPAY_POST_LINK') : null,
            'appendix'          =>  $this->appendix,
            'template'          =>  isset($form_template) ? $form_template : null
        ]);

        $validator = Validator::make($request->all(),
            [
                'Signed_Order_B64'  => 'required',
                'email'             => 'email',
                'appendix'          => 'required',
                'BackLink'         => 'required',
                'PostLink'         => 'required',
                'FailureBackLink'    => 'string',
                'template'          => 'string',
                'recur_freq'        => 'required',
                'recur_exp'         => 'required',
                'person_id'         => 'required'
            ]
        );

        if ($validator->fails())
        {
            return $validator->errors();
        }

        $params = http_build_query($request->only(['Signed_Order_B64','email','BackLink','PostLink',
            'template','recur_freq', 'recur_exp', 'person_id']));

        if(config('epay.pay_test_mode') == true)
        {
            $url = 'https://testpay.kkb.kz/jsp/process/logon.jsp';
        }else{
            $url = 'https://epay.kkb.kz/jsp/process/logon.jsp';
        }

        return $url.'?'.$params;
    }



}
