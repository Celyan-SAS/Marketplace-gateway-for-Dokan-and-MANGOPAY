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
	
    public $_mp_commission_id = "mp_commission";
    public $_mp_commission_data_id = "mp_commission_data";
    
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $version='0.1.0' ) {
	
		/** Load configuration values from config.inc.php **/
		$this->load_config();
		
		/** Switch PHP debug mode on/off **/
		if( true ) { //mangopayDKConfig::DEBUG
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
     * test all the plugin folders possibility
     * @return boolean
     */
    public function mangopay_vendors_plugin_test(){
        
        $lite = is_plugin_active('dokan-lite/dokan.php');
        $dokan = is_plugin_active('dokan/dokan.php');
        $pro = is_plugin_active('dokan-pro/dokan.php');
        $plug = is_plugin_active('dokan-plugin/dokan.php');
        
        $test = false;
        if($lite || $dokan || $pro || $plug){
            $test = true;
        }
        
        return $test;
    }

	/**
	 * Load plugin configuration and default values from config.inc.php
	 * 
	 */
	private function load_config() {
		$this->defaults				= mangopayDKConfig::$defaults;
	}
    
    public function mangopay_dokan_withdraw_methods( $methods ){
        
        $methods['mangopay'] = array(
            'title'    =>  "Mangopay",
            'callback' => 'dokan_withdraw_method_mangopay'
        );
        
        return $methods;
    }
    
    /**
     * 
     */
    public function mangopay_vendor_role($role_name){
        return 'seller';
    }
    
    /**
     * add data to method details column in admin
     * @param type $store_settings
     */
    public function dokan_withdraw_method_mangopay($store_settings){
    }
    
    public function mangopay_dokan_get_template_part($template, $slug, $name){
        if($slug == 'withdraw/request-form'){            
            $dir = plugin_dir_path(__FILE__);            
            $template = $dir.'templates/widthdraw_request_form.php';
        }
        return $template;
    }
    
    public function substract_commission_from_earnings($net_amount,$order){
        /* get options from dokan */
        $options = get_option( 'dokan_selling', [] );
        /* get the % mangopay commission */
        $perc_commission = $options['dokan_mangopay_commission_perc'];
        $perc_commission = str_replace(',', '.', $perc_commission);
        /* get the fix mangopay commission */
        $fixe_commission = $options['dokan_mangopay_commission_fixe'];
        $fixe_commission = str_replace(',', '.', $fixe_commission);
        
        //get the total from woocommerce
        $order          = new WC_Order( $order->id );
        $order_total    = $order->get_total();
        if ( $order->get_total_refunded() ) {
            $order_total = $order_total - $order->get_total_refunded();
        }
        /* shipping total */
        $shipping_total = $order->get_total_shipping();
        
        /* get mangopay part from total */
        $perc_total = (floatval($perc_commission)/100)*$order_total;
        
        /* 1 get total for mangopay */
        $total_to_get_for_mangopay = $perc_total+floatval($fixe_commission);
        
        /* 2 substract from total the shipping */
        $order_total_without_shipping = $order_total - $shipping_total;
        
        /* 3 get out the mangopay part from total (without shipping*/
        $net_amount = $order_total_without_shipping - $total_to_get_for_mangopay;
        
        return $net_amount;
    }
    
    public function mangopay_dokan_get_seller_balance(){
        
        $data_to_return = array();
        $data_to_return['total_due_display'] = 0;
        $data_to_return['total_due'] = 0;
        $data_to_return['list_ids'] = array();
        $data_to_return['give_button'] = true;
        $withdraw_limit = (int) dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 );
        
        /* get the orders */
        $list_orders = dokan_get_seller_orders(get_current_user_id(),"all");
        if($list_orders && count($list_orders)>0){
            
            foreach($list_orders as $order_data){
                                
                $order = new WC_Order( $order_data->order_id );
                
                /* get order status, only take the one corresponding to the selected option */
                $order_status = dokan_get_option( 'withdraw_order_status', 'dokan_withdraw', array( 'wc-completed' ) );
                if(!isset($order_status[$order->post_status])){
                    continue;
                }
                
                /* get the commission meta */
                $mp_comission = get_post_meta($order_data->order_id,$this->_mp_commission_id,true);
                $mp_comission_data = get_post_meta($order_data->order_id,$this->_mp_commission_data_id,true);
                
                /* calculate the fee */
                $the_fee = $this->substract_commission_from_earnings(0, $order);
                
                /* if meta exist already else save it */
                if($mp_comission){
                    if($mp_comission == "due"){
                        /* add to earnings and add the id */
                        $data_to_return['total_due'] = $data_to_return['total_due']+$the_fee;
                        $data_to_return['list_ids'][] = $order_data->order_id;
                    }
                }else{
                    update_post_meta( $order_data->order_id, $this->_mp_commission_id, 'due' );
                    $data_meta = array();
                    $data_meta['date'] = time();
                    $data_meta['total_order'] = $order->get_total();
                    $data_meta['fee'] = $the_fee;
                    update_post_meta( $order_data->order_id, $this->_mp_commission_data_id, $data_meta );  
                    
                    $data_to_return['total_due'] = $data_to_return['total_due']+$the_fee;
                    $data_to_return['list_ids'][] = $order_data->order_id;
                }
                
            }//end foreach
            
        }//if orders
                        
        if($data_to_return['total_due'] < $withdraw_limit){
            $data_to_return['give_button'] = false;
            $data_to_return['total_due'] = 0;
        }
        
        $data_to_return['total_due_display'] = wc_price( $data_to_return['total_due'] );
        
        return $data_to_return;
    }
    
    public function mangopay_handle_withdraws(){
        global $wpdb;
        
        if ( !isset( $_POST['withdraw_submit'] ) ) {
            return;
        }
                
        if(!isset($_POST['list_orders_id'])){
            return;
        }
        
        /* get the last enter for the withdraw */
        $table_name = $wpdb->prefix . 'dokan_withdraw';        
        $query_select = 'SELECT * FROM '.$table_name.' WHERE user_id = '.get_current_user_id().' ORDER BY id DESC LIMIT 1';
        $result = $wpdb->get_row($query_select, ARRAY_A);        
        if($result){
            /*ajout de la note dans la ligne withdraw */
            $wpdb->update( 
                $table_name, 
                array( 'note' => sanitize_text_field( $_POST['list_orders_id'] ) ), 
                array( 'id' => $result['id'] ) 
                );
        }
        
    }
      
        
    /**
     * This will go to wallet transfert to MP
     * @param type $list_to_transfert
     * @param type $order
     * @param type $mp_transaction_id
     * @return type
     */
    public function mangopay_order_complete_list($list_to_transfert, $order, $mp_transaction_id ){
        global $wpdb;
        /* old list is reset */
        $list_to_transfert = array();
                
        $total = $order->get_total();
        $for_vendor = $this->substract_commission_from_earnings(0,$order);
        
        $transfert = array();
        $transfert['order_id'] = $order->id;
        $transfert['mp_transaction_id'] = $mp_transaction_id;
        $transfert['wp_user_id'] = $order->customer_user;
        $transfert['mp_amount'] = $total;
        $transfert['mp_fees'] = $total - $for_vendor;
        $transfert['mp_currency'] = $order->order_currency;

        //get vendor if
        $seller_id = dokan_get_seller_id_by_order($order->id);
        if ($seller_id && $seller_id != 0) {
            $transfert['vendor_id'] = $seller_id;
        }//else multivendor
        
        $list_to_transfert[] = $transfert;
                
        return $list_to_transfert;
    }
    
    public function mangopay_dokan_get_seller_amount_from_order( $net_amount, $order, $seller_id ){
        return $this->substract_commission_from_earnings($net_amount, $order);
    }
    
    /**
     * return net amout that wil be saved (net amount -  )
     * @param type $net_amount
     * @param type $order
     * @return int
     */
    public function mangopay_dokan_sync_order_net_amount($net_amount, $order){
        return $this->substract_commission_from_earnings($net_amount, $order);
    }
    
    /**
     * WHEN YOU ORDER (before payement)
     * @param type $net_amount
     * @param type $order
     * @return type
     */
    public function mangopay_dokan_order_net_amount($net_amount, $order){
        return $this->substract_commission_from_earnings($net_amount, $order);
    }
    
}
?>