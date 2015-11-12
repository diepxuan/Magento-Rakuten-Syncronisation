<?php

class Tws_Rakuten_Helper_Ean 
{

	public function isValidEan($value)
	{
         
		$length			= 0;
		$checkSum		= 0;
		$char			= '';
		$validLengths	= array(13); 

		if(!is_string($value)) {
			$value = (string) $value;
		}
		
		$length = strlen($value);
		
			
		if(!in_array($length, $validLengths)) {
			return false;
		}
		
			
		for($i = 0; $i < $length; $i++) {
			
			$char = substr($value, $i, 1);
			
			if(!preg_match('~\d~', $char)) {
				return false;
			}
			
			if($i % 2 == 0) {
				$checkSum += ((integer) $char) * 1;
			} else {
				$checkSum += ((integer) $char) * 3;
			}

		}
		if($checkSum % 10 != 0) {
			return false;
		}
		
		return true;
	}
}

?>