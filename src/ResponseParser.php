<?php

namespace Dosarkz\EPayKazCom;

use Illuminate\Support\Arr;

/**
 * Base Epay response parser
 *
 * Class ResponseParser
 * @package Dosarkz\EPayKazCom
 */
class ResponseParser {

	/**
	 * @var string
	 */
	protected $rawResponse;

	/**
	 * @var array
	 */
	protected $parsedData;

	/**
	 * EpayResponseParser constructor.
	 *
	 * @param string $rawResponse
	 */
	public function __construct( $rawResponse ) {

		$this->rawResponse = $rawResponse;

		$xmlElement = simplexml_load_string($rawResponse);

		$this->parsedData = json_decode( json_encode( (array) $xmlElement ), true);
	}

	/**
	 * Get raw bank field
	 *
	 * @return string
	 */
	public function getRawBankField() {

		$matches = [];

		preg_match('/(\<bank[\s\>].*\<\/bank>)/', $this->rawResponse, $matches);

		return count($matches) ? $matches[0] : '';
	}

	/**
	 * Get response field value, using dot notation
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function getResponse( $key = null ) {

		return $key ? Arr::get($this->parsedData, $key) : $this->parsedData;
	}

}