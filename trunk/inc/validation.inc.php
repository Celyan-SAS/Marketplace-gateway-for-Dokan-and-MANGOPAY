<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin admin methods class
 * This class handles user profile field validations
 *
 * @author yann@abc.fr, Silver
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 **/
class mangopayDKValidation{
	
	private $mangopayDKMain;		// The mangopayWCMain object that instanciated us
	  
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $mangopayDKMain=NULL ) {
		$this->mangopayDKMain		= $mangopayDKMain;
	}
}
?>