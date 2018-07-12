<?php

namespace Dosarkz\EPayKazCom;

use Illuminate\Support\Facades\Validator;

/**
 * Class CheckPay
 * @package Dosarkz\EPayKazCom
 */
class CheckPay extends Epay {

	/**
	 * @var string
	 */
	public $template_path  = 'templates/check_order_template.xml';

	/**
	 * @var string
	 */
	private $request_url = '/jsp/remote/checkOrdern.jsp';

	/**
	 * CheckPay constructor.
	 *
	 * @param array $params
	 */
	public function __construct(array $params = [])
	{
		parent::__construct();

		$this->order_id = isset($params['order_id']) ? $params['order_id'] : null;

		$this->template = $this->generateXml();
	}

	/**
	 * @return null|string
	 */
	private function generateXml()
	{
		if(file_exists(__DIR__.'/'.$this->template_path)){

			$xml = $this->readXmlFile(__DIR__.'/'.$this->template_path);

			$array = [
				'ORDER_ID',
				'MERCHANT_ID'
			];

			$params = $this->getListParams($array);
			$header_template = $this->setParamsToTemplate($xml,$params);
			$template =  $this->generateXmlTemplate($header_template,$this->kkb);

			return $template;
		}

		return null;
	}

	/**
	 * Generate url for Epay request
	 *
	 * @return string|array
	 */
	public function generateUrl(){

		$params = collect( [
			'order_id' => $this->order_id
		]);

		$validator = Validator::make( $params->all(),
			[
				'order_id'      => 'required'
			]
		);

		if ($validator->fails())
		{
			return $validator->errors();
		}

		$url = $this->epay_server_url . $this->request_url;

		return $url . '?' . urlencode( $this->template );

	}
}