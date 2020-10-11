<?php 

/**
 * This class aims to provide a common language between almost all possible user
 * inputs.
 *
 * It is a translator of sorts.
 */

namespace App\Internal\Keys;

class Babylone{

	const FALSE = 0;
	const TRUE 	= 1;

	private static $matches = array(
		self::TRUE 		=> 	array(1,"true","True","yes","Yes"),
		self::FALSE 	=>	array(0,"false","False","no","No")
	);

	public static function is_equivalent_to($left,$right,$firstTry=true){

		$return = false;

		// If $left is $right's aliases (self::$matches)
		// if $right is $left's
		if(isset(self::$matches[$right])){
			if(in_array($left , self::$matches[$right])){
				$return = true;
			}
		}

		// Left and right can be inteverted
		if(!$return && !$firstTry){
			$return = self::is_equivalent_to($right,$left,false);
		}

		return $return;

	}

}