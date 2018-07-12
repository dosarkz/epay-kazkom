<?php

namespace Dosarkz\EPayKazCom;


/**
 * Base Epay response with params validating
 *
 * Class Response
 * @package Dosarkz\EPayKazCom
 */
abstract class Response {

	/**
	 * Array of parameters to be checked
	 *
	 * @var array
	 */
	protected $validatingParams = [ ];

	/**
	 * Array of parameters to be mapped
	 *
	 * @var array
	 */
	protected $mappedParams = [ ];

	/**
	 * Response parser
	 *
	 * @var ResponseParser
	 */
	protected $parsedResponse = null;

	/**
	 * Response constructor.
	 * @param ResponseParser $parsedResponse
	 * @param array $mappedParams
	 */
	public function __construct( $parsedResponse,  $mappedParams = []) {

		$this->mappedParams = array_merge( $this->mappedParams, $mappedParams );

		$this->parsedResponse = $parsedResponse;
	}


	/**
	 * Validate mapped params to provided params
	 *
	 * @param $params
	 * @return bool
	 */
	protected function validateParams( $params ){

		foreach ($this->validatingParams as $param){

			if ( $this->parsedResponse->getResponse( $this->mappedParams[$param] ) != $params[$param])
				return false;
		}

		return true;
	}

	/**
	 * Determines the overall result of the response
	 *
	 * @param array $params
	 * @return bool
	 */
	public function isSuccess( array $params = [] ){

		return $this->validateParams( $params );
	}

	/**
	 * Magic access to mapped params
	 *
	 * @param $name
	 * @return mixed|null
	 */
	public function __get( $name ) {

		return isset($this->mappedParams[$name])
			? $this->parsedResponse->getResponse( $this->mappedParams[$name] )
			: null ;
	}


	/**
	 * Get whole parsed response data
	 *
	 * @return mixed|null
	 */
	 public function getResponse(){

		return $this->parsedResponse ? $this->parsedResponse->getResponse() : null;
	}

}