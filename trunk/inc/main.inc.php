<?php
/**
 * Marketplace-gateway-for-Dokan-and-MANGOPAY plugin main class
 * 
 * @author yann@abc.fr
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 *
 * Comment shorthand notations:
 * WP = WordPress core
 * WC = WooCommerce plugin
 * DK = Dokan plugin
 * DB = MANGOPAY dashboard
 *
 */
class mangopayDKMain {
	
	/** Configuration variables loaded from conf.inc.php by load_config() **/
	private $defaults;				// Will hold plugin default values
		
	/** Class variables **/
	private $mp;				// This will store our mpAccess class instance
	public $options;			// Public because shared with mangopayDKAdmin. TODO: refactor
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $version='0.1.0' ) {
	
		/** Load configuration values from config.inc.php **/
		$this->load_config();
		
		/** Switch PHP debug mode on/off **/
		if( mangopayDKConfig::DEBUG ) {
			error_reporting( -1 );	// to enable all errors
			ini_set( 'display_errors', 1 );
			ini_set( 'display_startup_errors', 1 );
		}
		                
		/** Instantiate mpAccess (need to do before decrypt options because need tmp dir) **/
		//$this->mp = mpAccess::getInstance();
	
		/** Get stored plugin settings **/
		$this->defaults['plugin_version'] = $version;
			
		/** The activation hook must be a static function **/
		register_activation_hook( __FILE__, array( 'mangopayDKPlugin', 'on_plugin_activation' ) );

		/** Instantiate admin interface class if necessary **/
		$mangopayDKAdmin = null;
		if( is_admin() )
			$mangopayDKAdmin = new mangopayDKAdmin( $this );
		
		/** Setup all our WP/WC/DK hooks **/
		mangopayDKHooks::set_hooks( $this, $mangopayDKAdmin );        
	}        

	/**
	 * Load plugin configuration and default values from config.inc.php
	 * 
	 */
	private function load_config() {
		$this->defaults				= mangopayDKConfig::$defaults;
	}
}
?>