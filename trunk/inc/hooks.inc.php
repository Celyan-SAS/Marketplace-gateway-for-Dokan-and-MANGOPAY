<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin filter and action hooks class
 *
 * @author yann@abc.fr
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 **/
class mangopayDKHooks {
	public static function set_hooks( $mangopayWCMain, $mangopayWCAdmin=NULL ) {
		
		/** SITE WIDE HOOKS **/
		
		/**
		 * Site-wide WP hooks
		 *
		 */
		
		/** Load i18n **/
		//add_action( 'plugins_loaded', array( 'mangopayDKPlugin', 'load_plugin_textdomain' ) );
		
		
		/**
		 * Site-wide WC hooks
		 *
		*/
		
		
		
		/**
		 * Site-wide DK hooks
		 *
		*/
		
		
		
		/** FRONT END HOOKS **/
		
		/**
		 * Front-end WP hooks
		 * 
		 */
		
		
		/**
		 * Front-end WC hooks
		 *
		 */
		
		
		/**
		 * Front-end DK hooks
		 *
		*/
		
		
		/** BACK OFFICE HOOKS **/
		
		/**
		 * Back-office WP hooks
		 *
		*/
		if ( !is_admin() )
			return;
		
		
		/**
		 * Back-office WC hooks
		 *
		 */
		
		
		/**
		 * Back-office DK hooks
		 *
		 */
		
	}
}
?>