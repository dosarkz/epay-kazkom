<?php

namespace Dosarkz\EPayKazCom;


/**
 * Parser for ControlPay response
 *
 * Class ControlPayResponse
 * @package Dosarkz\EPayKazCom
 */
class ControlPayResponse extends Response {

	use ResponseBankSignValidatable;

	protected $mappedParams = [
		'merchant_id' => 'bank.merchant.@attributes.id',
		'response_code' => 'bank.response.@attributes.code',
		'response_message' => 'bank.response.@attributes.message',
		'remaining_amount' => 'bank.response.@attributes.remaining_amount',
	];

	protected $validatingParams = [ 'merchant_id', 'response_code' ];

	/**
	 * ControlPayResponse constructor.
	 * @param string $rawResponse
	 * @param array|null $mappedParams
	 */
	public function __construct( $rawResponse, $mappedParams = [] ) {

		parent::__construct( new ResponseParser($rawResponse), $mappedParams );
	}

	/**
	 * @return string
	 */
	public function getResponseCode(){

		return $this->response_code;
	}

	/**
	 * @return string
	 */
	public function getResponseMessage(){

		return $this->response_message;
	}

	/**
	 * @return string
	 */
	public function getRemainingAmount(){

		return $this->remaining_amount;
	}

	public function isSuccess( array $params = [] ) {

		$defaultParams = [
			'merchant_id' => app()->get('epay')->merchant_id,
			'response_code' => '00'
		];

		$params = array_merge( $params, $defaultParams );

		return $this->validateSign() && parent::isSuccess( $params );
	}
}