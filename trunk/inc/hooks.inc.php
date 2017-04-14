<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin filter and action hooks class
 *
 * @author yann@abc.fr
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 **/
class mangopayDKHooks {
	public static function set_hooks( $mangopayDKMain, $mangopayDKAdmin=NULL ) {
		
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
		add_filter( 'dokan_sync_order_net_amount', array( $mangopayDKMain, 'mangopay_dokan_sync_order_net_amount' ),10,2 );
        add_filter( 'dokan_order_net_amount', array( $mangopayDKMain, 'mangopay_dokan_order_net_amount' ),10,2 );
        add_filter( 'dokan_get_seller_amount_from_order', array( $mangopayDKMain, 'mangopay_dokan_get_seller_amount_from_order' ),10,3 );
        /* add mangopay method */
        add_filter( 'dokan_withdraw_methods', array( $mangopayDKMain, 'mangopay_dokan_withdraw_methods' ),10, 1);
        /* change the transfert information (from wc vendors to dokan) */
        add_filter( 'mangopay_order_complete_list_wallet_transfert', array( $mangopayDKMain, 'mangopay_order_complete_list' ),10, 3);
        /* overload template to withdraw */
        add_filter( 'dokan_get_template_part', array( $mangopayDKMain, 'mangopay_dokan_get_template_part' ),10, 3);
        
        add_action( 'template_redirect', array( $mangopayDKMain, 'mangopay_handle_withdraws' ),90 );
        
        /* change key name of vendor to seller */
        add_action( 'mangopay_vendor_role', array( $mangopayDKMain, 'mangopay_vendor_role' ),1);
        
        /* test if plugin is activated for other plugins */
        add_filter( 'mangopay_vendors_plugin_test', array( $mangopayDKMain, 'mangopay_vendors_plugin_test' ),10,1 );
        
		/** BACK OFFICE HOOKS **/
		
		/**
		 * Back-office WP hooks
		 *
		*/
		if ( !is_admin() )
			return;
		
		/**
		 * Back-office MANGOPAY-WooCommerce plugin hooks
		 *
		 */
		add_filter( 'mangopay_vendors_plugin_name', array( $mangopayDKAdmin, 'filter_plugin_name' ) );
		add_filter( 'mangopay_vendors_plugin_path', array( $mangopayDKAdmin, 'filter_plugin_path_return_url' ) );
		add_filter( 'mangopay_vendors_required_class', array( $mangopayDKAdmin, 'filter_plugin_class' ) );
		/**
		 * Back-office WC hooks
		 *
		 */
		
		
		/**
		 * Back-office DK hooks
		 *
		 */        
        /** add fields in the admin **/
        add_filter( 'dokan_settings_fields',  array( $mangopayDKAdmin, 'add_mangopay_commission_in_dokan_general' ),20,1 ); 
        /* approuved ajax call withdraw-> do the pay out*/
        add_action( 'wp_ajax_dokan_withdraw_form_action', array( $mangopayDKAdmin, 'handle_withdraw_action' ) );
	}
}
?>