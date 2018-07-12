<?php
/**
 * Created by PhpStorm.
 * User: dr_sharp
 * Date: 11.07.2018
 * Time: 14:24
 */

namespace Dosarkz\EPayKazCom;


trait ResponseBankSignValidatable {

	private function validateSign() {

		$bankField = $this->parsedResponse->getRawBankField();

		$bankFieldSign = $this->parsedResponse->getResponse('bank_sign');

		if ($bankField && $bankFieldSign) {

			$kkb = new KkbSign();

			$kkb->invert();

			return ( $kkb->check_sign64($bankField, $bankFieldSign, app()->get('epay')->public_key_path ) == 1 );
		} else {

			return false;
		}
	}
}