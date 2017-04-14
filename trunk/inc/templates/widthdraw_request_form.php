<?php
global $mngpd_o;
//$total_due_display = dokan_get_seller_balance( get_current_user_id(), true );
$data_widthdraw = $mngpd_o->mangopay_dokan_get_seller_balance();

?>

<form class="dokan-form-horizontal withdraw" role="form" method="post">
    <div class="dokan-form-group">
        <label for="withdraw-amount" class="dokan-w3 dokan-control-label">
            <?php _e( 'Withdraw Amount', 'dokan-lite' ); ?>
        </label>

        <div class="dokan-w5 dokan-text-left">
            <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php echo $data_widthdraw['total_due_display']; ?></span>
                <input 
                    name="witdraw_amount" 
                    required 
                    number 
                    min="<?php echo esc_attr( dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 ) ); ?>" 
                    class="dokan-form-control" 
                    id="withdraw-amount" 
                    name="price" 
                    type="hidden" 
                    placeholder="0.00" 
                    value="<?php echo $data_widthdraw['total_due']; ?>"  
                    >
                <input name="list_orders_id" type="hidden" value="<?php echo implode(',',$data_widthdraw['list_ids']); ?>" >
            </div>
        </div>
    </div>

    <div class="dokan-form-group">
        <label for="withdraw-method" class="dokan-w3 dokan-control-label">
            <?php _e( 'Payment Method', 'dokan-lite' ); ?>
        </label>

        <div class="dokan-w5 dokan-text-left">
            <span class="dokan-input-group-addon">Mangopay</span>
            <input type="hidden" required name="withdraw_method" value="mangopay" id="withdraw-method">
        </div>
    </div>

    <?php if(isset($data_widthdraw['give_button']) && $data_widthdraw['give_button'] == true ) : ?>
    <div class="dokan-form-group">
        <div class="dokan-w3 ajax_prev" style="margin-left:19%; width: 200px;">
            <?php wp_nonce_field( 'dokan_withdraw', 'dokan_withdraw_nonce' ); ?>
            <input type="submit" class="dokan-btn dokan-btn-theme" value="<?php esc_attr_e( 'Submit Request', 'dokan-lite' ); ?>" name="withdraw_submit">
        </div>
    </div>
    <?php endif; ?>
</form>