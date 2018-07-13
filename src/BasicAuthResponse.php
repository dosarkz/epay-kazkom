<?php

namespace Dosarkz\EPayKazCom;

/**
 * Parser for BasicPay response
 *
 * Class BasicAuthResponse
 * @package Dosarkz\EPayKazCom
 */
class BasicAuthResponse extends Response {

	use ResponseBankSignValidatable;

	protected $mappedParams = [
		'amount'        => 'bank.results.payment.@attributes.amount',
		'merchant_id'   => 'bank.results.payment.@attributes.merchant_id',
		'response_code' => 'bank.results.payment.@attributes.response_code',
		'reference'     => 'bank.results.payment.@attributes.reference',
		'approval_code' => 'bank.results.payment.@attributes.approval_code',
		'currency'      => 'bank.customer.merchant.order.@attributes.currency',
		'order_id'      => 'bank.customer.merchant.order.@attributes.order_id'
	];

	protected $validatingParams = [ 'amount', 'merchant_id', 'response_code' ];

	/**
	 * BasicAuthResponse constructor.

	 * @param string $rawResponse
	 * @param array|null $mappedParams
	 */
	public function __construct( $rawResponse, $mappedParams = [] ) {

		parent::__construct( new ResponseParser($rawResponse), $mappedParams );
	}

	/**
	 * @return string
	 */
	public function getReference(){

		return $this->reference;
	}

	/**
	 * @return string
	 */
	public function getApprovalCode(){

		return $this->approval_code;
	}

	/**
	 * @return string
	 */
	public function getAmount(){

		return $this->amount;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode(){

		return $this->currency;
	}

	/**
	 * @return string
	 */
	public function getOrderId(){

		return $this->order_id;
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