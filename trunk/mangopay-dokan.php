<?php
/**
 * @package Marketplace-gateway-for-Dokan-and-MANGOPAY
 * @author Yann Dubois
 * @version 0.1.0
 * @see: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
 */

/*
 Plugin Name: Marketplace gateway for Dokan and MANGOPAY
Plugin URI: https://github.com/Celyan-SAS/Marketplace-gateway-for-Dokan-and-MANGOPAY
Description: WordPress payment gateway add-on to make the MANGOPAY-WooCommerce plugin compatible with Dokan.
Version: 0.1.0
Author: Yann Dubois
Author URI: http://www.yann.com/
Text Domain: mangopay4dokan
Domain Path: /languages
License: GPL2
*/

/**
 * @copyright 2016  Yann Dubois & Silver ( email : yann _at_ abc.fr )
 *
 *  Original development of this plugin was kindly funded by Pharmizz ( http://pharmizz.fr/ )
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Revision 0.1.0:
 * - Original alpha release 00 of 2017/03/27
 */

$version = '0.1.0';

/** Custom classes includes **/
include_once( dirname( __FILE__ ) . '/inc/conf.inc.php' );			// Configuration class
include_once( dirname( __FILE__ ) . '/inc/hooks.inc.php' );			// Action and filter hooks class (will include the payment gateway class when appropriate)
include_once( dirname( __FILE__ ) . '/inc/main.inc.php' );			// Main plugin class
include_once( dirname( __FILE__ ) . '/inc/validation.inc.php' );	// User profile field validation methods
if( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX )
	include_once( dirname( __FILE__ ) . '/inc/ajax.inc.php' );		// Ajax methods

if( is_admin() )
	include_once( dirname( __FILE__ ) . '/inc/admin.inc.php' );		// Admin specific methods

/** Main plugin class instantiation **/
global $mngpd_o;
$mngpd_o = new mangopayDKMain( $version );
?>