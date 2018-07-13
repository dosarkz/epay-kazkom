<?php

namespace Dosarkz\EPayKazCom;

/**
 * Parser for CheckPay response
 *
 * Class CheckPayResponse
 * @package Dosarkz\EPayKazCom
 */
class CheckPayResponse extends Response {

	use ResponseBankSignValidatable;

	protected $mappedParams = [
		'order_id'          => 'bank.merchant.order.@attributes.id',
		'merchant_id'       => 'bank.merchant.@attributes.id',
		'amount'            => 'bank.response.@attributes.amount',
		'status'            => 'bank.response.@attributes.status',
		'result'            => 'bank.response.@attributes.result',
		'payment'           => 'bank.response.@attributes.payment',
		'currencycode'      => 'bank.response.@attributes.currencycode',
		'timestamp'         => 'bank.response.@attributes.timestamp',
		'reference'         => 'bank.response.@attributes.reference',
		'cardhash'          => 'bank.response.@attributes.cardhash',
		'card_to'           => 'bank.response.@attributes.card_to',
		'approval_code'     => 'bank.response.@attributes.approval_code',
		'msg'               => 'bank.response.@attributes.msg',
		'secure'            => 'bank.response.@attributes.secure',
		'card_bin'          => 'bank.response.@attributes.card_bin',
		'payername'         => 'bank.response.@attributes.payername',
		'payermail'         => 'bank.response.@attributes.payermail',
		'payerphone'        => 'bank.response.@attributes.payerphone',
		'c_hash'            => 'bank.response.@attributes.c_hash',
		'recur'             => 'bank.response.@attributes.recur',
		'recur_freq'        => 'bank.response.@attributes.recur_freq',
		'recur_exp'         => 'bank.response.@attributes.recur_exp',
		'person_id'         => 'bank.response.@attributes.person_id',

		'OrderID'           => 'bank.response.@attributes.OrderID',
		'SessionID'         => 'bank.response.@attributes.SessionID',
		'intreference'      => 'bank.response.@attributes.intreference',
		'AcceptRejectCode'  => 'bank.response.@attributes.AcceptRejectCode',

	];

	protected $validatingParams = [ 'merchant_id' ];

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

		return $this->currencycode;
	}

	/**
	 * @return string
	 */
	public function getPayState(){

		if ($this->payment == 'false' && $this->status == 2 ) return 'CANCELED';
		if ($this->payment == 'true' && $this->status == 2 && $this->result == 0) return 'APPROVED';
		if ($this->payment == 'true' && $this->status == 0 && $this->result == 0) return 'AUTH';
		if ($this->payment == 'false' && $this->status == 7 && $this->result == 7) return 'NOT_FOUND';
		if ($this->payment == 'false' && $this->status == 8 && $this->result == 8) return 'NOT_PAYED';
		if ($this->payment == 'false' && $this->status == 9 && $this->result == 9) return 'SYSTEM_ERROR';

		return 'UNKNOWN';
	}

	public function isSuccess( array $params = [] ) {

		$defaultParams = [
			'merchant_id' => app()->get('epay')->merchant_id,
		];

		$params = array_merge( $params, $defaultParams );

		return $this->validateSign()
		       && parent::isSuccess( $params )
		       && in_array( $this->getPayState(), [ 'APPROVED', 'AUTH'] );
	}

}