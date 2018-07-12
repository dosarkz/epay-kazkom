<?php

namespace Dosarkz\EPayKazCom;

use Illuminate\Support\Facades\Validator;

/**
 * Class ControlPay
 * @package Dosarkz\EPayKazCom
 */
class ControlPay extends Epay {

	/**
	 * @var string
	 */
	public $template_path  = 'templates/remote_template.xml';

	/**
	 * @var string
	 */
	private $request_url = '/jsp/remote/control.jsp';

	/**
	 * ControlPay constructor.
	 *
	 * @param array $params
	 */
	public function __construct(array $params = [])
	{
		parent::__construct();

		$this->order_id = isset($params['order_id']) ? $params['order_id'] : null;
		$this->command_type = isset($params['command_type']) ? $params['command_type'] : null;
		$this->reference = isset($params['reference']) ? $params['reference'] : null;
		$this->approval_code = isset($params['approval_code']) ? $params['approval_code'] : null;
		$this->amount = isset($params['amount']) ? $params['amount'] : null;
		$this->currency = isset($params['currency']) ? $params['currency'] : null;
		$this->reason = isset($params['reason']) ? $params['reason'] : null;

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
				'MERCHANT_ID',
				'ORDER_ID',
				'COMMAND_TYPE',
				'REFERENCE',
				'APPROVAL_CODE',
				'AMOUNT',
				'CURRENCY',
				'REASON'
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
			'order_id'      => $this->order_id,
			'command_type'  => $this->command_type,
			'reference'     => $this->reference,
			'approval_code' => $this->approval_code,
			'amount'        => $this->amount,
			'currency'      => $this->currency,
			'reason'        => $this->reason
		]);

		$validator = Validator::make( $params->all(),
			[
				'order_id'          => 'required',
				'command_type'      => 'required|in:reverse,complete,refund',
				'reference'         => 'required',
				'approval_code'     => 'required',
				'amount'            => 'required',
				'currency'          => 'required',
				'reason'            => 'string',
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