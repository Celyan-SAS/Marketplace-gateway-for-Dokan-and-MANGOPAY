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
	
	public function filter_plugin_name( $plugin_name ) {
		return 'Dokan';
	}
    	
	public function filter_plugin_path( $plugin_path ) {
		return mangopayDKConfig::DK_PLUGIN_PATH;
	}
	
	public function filter_plugin_class( $plugin_class ) {
		return mangopayDKConfig::DK_PLUGIN_CLASS;
	}    
    
    public function filter_plugin_path_return_url( $plugin_path ) {
        $lite = is_plugin_active('dokan-lite/dokan.php');
        $dokan = is_plugin_active('dokan/dokan.php');
        $pro = is_plugin_active('dokan-pro/dokan.php');
        $plug = is_plugin_active('dokan-plugin/dokan.php');
        
        if($lite){
            return 'dokan-lite/dokan.php';
        }
        
        if( $dokan){
            return 'dokan/dokan.php';
        }
        
        if($pro){
            return 'dokan-pro/dokan.php';
        }
        
        if($plug){
            return 'dokan-plugin/dokan.php';
        }
	}
    
    public function handle_withdraw_action(){
        /* get mangopay object*/
        global $wpdb;
        global $mngpd_o;
        $this->mp = mpAccess::getInstance();
        
        parse_str( $_POST['formData'], $postdata );        
        $bulk_action = $_POST['status'];
        $withdraw_id = $_POST['withdraw_id'];
        
        $umeta_key = 'mp_account_id';
        
        if( !$this->mp->is_production() ){
            $umeta_key .= '_sandbox';
        }
        
        /* get the last enter for the withdraw */
        $table_name = $wpdb->prefix . 'dokan_withdraw';        
        $query_select = 'SELECT * FROM '.$table_name.' WHERE id = '.$withdraw_id.'';
        $result = $wpdb->get_row($query_select, ARRAY_A);        
        
        $mp_account_id = get_user_meta( $result['user_id'], $umeta_key, true );
        
        $currency = get_option('woocommerce_currency');
        
        if($result && $bulk_action == 'approve' && $mp_account_id && $mp_account_id!="" ){
                 
            $payout_result = $this->mp->payout(
                $result['user_id'],
                $mp_account_id,
                $result['note'], 
                $currency, 
                $result['amount'],	//$amount
                0 //$fees
            );
            
            $exploded_ids = explode(",", $result['note']);
            foreach($exploded_ids as $order_id){
                update_post_meta( $order_id, $mngpd_o->_mp_commission_id, 'paid' );
            }
        
        }
        
    }
      
    public function add_mangopay_commission_in_dokan_general($settings_fields){
        /** add field to the selling general **/
        
        $options = get_option( 'dokan_selling', [] );
        $dokan_mangopay_commission_fixe = 0;
        if(isset($options['dokan_mangopay_commission_fixe'])){
            $dokan_mangopay_commission_fixe = $options['dokan_mangopay_commission_fixe'];
        }
        if(isset($options['dokan_mangopay_commission_perc'])){
            $dokan_mangopay_commission_fixe = $options['dokan_mangopay_commission_perc'];
        }
        
        $settings_fields['dokan_selling']['dokan_mangopay_commission_fixe'] =
             array(
                    'name'    => 'dokan_mangopay_commission_fixe',
                    'label'   => __( 'Mangopay commission fixe', 'dokan' ),
                    'desc'    => __( 'TEXT TODO add_mangopay_commission_in_dokan (general)', 'dokan' ),
                    'type'    => 'text',
                    'default' => '0'
             );
        $settings_fields['dokan_selling']['dokan_mangopay_commission_perc'] =
             array(
                    'name'    => 'dokan_mangopay_commission_perc',
                    'label'   => __( 'Mangopay commission %', 'dokan' ),
                    'desc'    => __( 'TEXT TODO add_mangopay_commission_in_dokan (general)', 'dokan' ),
                    'type'    => 'text',
                    'default' => '0'
             );        
        return $settings_fields;
    }    
    
}
?>