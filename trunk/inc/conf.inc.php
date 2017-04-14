<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin configuration class
 * 
 * @author yann@abc.fr
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 **/
class mangopayDKConfig {
	
	/** Class constants **/
	const DEBUG 			= false;							// Turns debugging messages on or off (should be false for production)
	const WC_PLUGIN_PATH	= 'woocommerce/woocommerce.php';
	const DK_PLUGIN_PATH	= 'dokan-lite/dokan.php';//'dokan/dokan.php';
	const DK_PLUGIN_CLASS	= 'WeDevs_Dokan';
	
	/** Default plugin options (the ones that will be stored in mangopayDKConfig::OPTION_KEY) **/
	public static $defaults = array(
	);
}
?>