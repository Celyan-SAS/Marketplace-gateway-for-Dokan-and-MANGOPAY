<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin admin methods class
 * This class is only loaded and instanciated if is_admin() is true
 *
 * @author yann@abc.fr
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 **/
class mangopayDKAdmin{
	
	private $options;
	private $mp;					// This will store our mpAccess class instance
	private $mangopayDKMain;		// The mangopayDKMain object that instanciated us
	private $mangopayDKValidation;	// Will hold user profile validation class
  
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $mangopayDKMain=NULL ) {
		$this->mangopayDKMain		= $mangopayDKMain;
		$this->options 				= $mangopayDKMain->options;
		//$this->mp 					= mpAccess::getInstance();
    
    /** Instantiate user profile field validations class **/
		$this->mangopayDKValidation = new mangopayDKValidation( $this );        
	}
}
?>