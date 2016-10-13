<?php
/**
 * Add a title dropdown for customer details
 *
 * Plugin Name: WooCommerce Customer Title
 * Plugin URI: https://github.com/Pamps/woocommerce-customer-title
 * Description: Add a title (Mr, Mrs, etc.) dropdown for customer details
 * Version: 1.0.0
 * Author: Darren Lambert
 * Author URI: http://darrenlambert.com
 * License: GPLv3 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: woocommerce-customer-title
 *
 * Copyright 2016 Darren Lambert
 *		
 */

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

class WooCommerceCustomerTitle{

	function __construct(){
		
		// Add filters
		add_filter('woocommerce_checkout_fields' , array(&$this, 'add_titles' ) );
		add_filter('woocommerce_formatted_address_replacements', array(&$this,'add_billing_title_to_replacements'), 20, 2);
		add_filter('woocommerce_localisation_address_formats', array(&$this,'add_billing_title_to_address_format'));
		add_filter('woocommerce_my_account_my_address_formatted_address', array(&$this,'add_billing_title_to_arguments'), 10, 3);
		add_filter('woocommerce_order_formatted_billing_address', array(&$this,'add_billing_title_to_arguments'), 10, 2);
		add_filter('woocommerce_order_formatted_shipping_address', array(&$this,'add_billing_title_to_arguments'), 10, 2);
	}
	
	
	// Adds the titles to the checkout fields
	function add_titles( $fields ) {

		// Create the title fields
		$title = array(
			'label'     => __('Title', 'woocommerce'),
			'placeholder'   => _x('Title', 'placeholder', 'woocommerce'),
			'required'    => false,
			'clear'       => false,
			'type'        => 'select',
			'class'     => array('form-row-wide'),
			'options'     => array(
			    'Mr' => __('Mr', 'woocommerce' ),
			    'Mrs' => __('Mrs', 'woocommerce' ),
			    'Miss' => __('Miss', 'woocommerce' ),
			    'Ms' => __('Ms', 'woocommerce' ),
			    'Dr' => __('Dr', 'woocommerce' ),
			    'Professor' => __('Professor', 'woocommerce' ),
			    )
			);			
			
		// Add to the billing (at the end)
		$fields['billing']['billing_title'] = $title;

		// Now move to the start of the fields			
		$billing = $fields['billing'];
		$billing = array('billing_title' => $billing['billing_title']) + $billing;		
		$fields['billing'] = $billing;
			
		// Add to the shipping (at the end)
 		$fields['shipping']['shipping_title'] = $title;

		// Now move to the start of the fields			
		$shipping = $fields['shipping'];
		$shipping = array('shipping_title' => $shipping['shipping_title']) + $shipping;		
		$fields['shipping'] = $shipping;		
	
		return $fields;
	}

	// Adds the billing title to the list of allowed replacements of WooCommerce 
	function add_billing_title_to_replacements($replacements, $args) {
	  
	  // Add billing title
	  $replacements['{billing_title}'] = $args['billing_title'];
	  return $replacements;
	  
	} 

	// Replaces all {name} occurencies for {billing_title} {name} in addresses formats 
	function add_billing_title_to_address_format($addressFormats) {
	  
	  $addressFormats = array_map(function($address) {
	    return str_replace('{name}', '{billing_title} {name}', $address);
	  }, $addressFormats);
	  
	  return $addressFormats;
	  
	} 

	// Adds the billing title to the available information in addresses 
	function add_billing_title_to_arguments($args, $costumer_id = false, $name = false) {

	  if (is_object($costumer_id)) $costumer_id = $costumer_id->user_id;
	  
	  $args['billing_title'] = get_user_meta((int) $costumer_id, 'billing_title', true);
	  return $args;
	  
	}	
			
}


// Create the class instance
$woocommerce_customer_title = new WooCommerceCustomerTitle();