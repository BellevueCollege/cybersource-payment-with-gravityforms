<?php
/*
Plugin Name: CyberSource Payment With GravityForms
Plugin URI: https://github.com/TriptiSharma/cybersource-payment-with-gravityforms
Description: 
Author: Bellevue College Technology Development and Communications
Version: 1.0

*/
require_once("cybersource-options.php");

// Adding Application Fees setting to Form Settings
add_filter( 'gform_form_settings', 'form_fees_setting', 10, 2 );
function form_fees_setting( $settings, $form ) {
    $settings['Form Basics']['form_application_fees'] = '
        <tr>
            <th><label for="form_application_fees">Application Fees</label></th>
            <td><input value="' . rgar($form, 'form_application_fees') . '" name="form_application_fees"></td>
        </tr>';

    return $settings;
}

// save your custom form setting
add_filter( 'gform_pre_form_settings_save', 'save_my_custom_form_setting' );
function save_my_custom_form_setting($form) {
    $form['form_application_fees'] = rgpost( 'form_application_fees' );
    return $form;
}
// End of Form Settings

// Redirecting to cybersource using post data

add_action( 'gform_confirmation', 'post_to_cybersource', 10, 4 );
function post_to_cybersource($confirmation, $form, $entry ) {
    $form_object = GFAPI::get_form($form['id']);    
    $options = get_option('cybersource');
    $secret_key = $options["cybersource_secret_key"];  
    $post_url = $options['cybersource_form_post_url'];  
    //error_log(print_r($entry,true));
    //error_log("Form object:".print_r($form,true));
    $reference_number =$entry['id']; //$form['id']."-".$entry['id'];  
    $transaction_uuid = uniqid();   
    $fields_map = array (
                        'access_key' => $options['cybersource_access_key'],
                        'locale' => $options['cybersource_locale'],                               
                        'profile_id' => $options['cybersource_profile_id'],
                        'bill_to_address_country' => $options['cybersource_bill_to_address_country'],
                        'bill_to_address_state' => $options['cybersource_bill_to_address_state'],
                        'currency' => $options['cybersource_currency'],
                        'customer_ip_address' => $_SERVER['REMOTE_ADDR'],
                        'item_0_name' => $form_object['title'],
                        'item_0_quantity' => '1',
                        'item_0_unit_price' => $form_object['form_application_fees'],
                        'line_item_count' => '1',                        
                        'reference_number' => $reference_number,
                        'signed_date_time' => &$signed_date_time,
                        'signed_field_names' => &$signed_field_names,
                        'transaction_type' => $options['cybersource_transaction_type'],
                        'transaction_uuid' => $transaction_uuid,
                        'unsigned_field_names' => '',
                      );
   $signed_date_time = gmdate( 'Y-m-d\TH:i:s\Z' );
   $signed_field_names = implode(',',array_keys( $fields_map ));
   $signature = set_signature($fields_map,$secret_key) ;   
   extract($fields_map);   

//$confirmation =  "";
$confirmation .= "<form name='redirect' action = '". $post_url . "'  method='POST'>";
$confirmation .= "<input type='hidden' id= 'access_key' name='access_key' value='". $access_key . "'>";
$confirmation .= "<input type='hidden' id='locale' name='locale' value='" .$locale . "'>";
$confirmation .= "<input type='hidden' id='profile_id' name='profile_id' value='" . $profile_id . "'>";
$confirmation .= "<input type='hidden' id='bill_to_address_country' name='bill_to_address_country' value='". $bill_to_address_country ."' />";
$confirmation .= "<input type='hidden' id='bill_to_address_state' name='bill_to_address_state' value='". $bill_to_address_state ."' />";
$confirmation .= "<input type='hidden' id='currency' name='currency' value='". $currency ."' />";
$confirmation .= "<input type='hidden' id='customer_ip_address' name='customer_ip_address' value= '". $customer_ip_address ."' /> ";
$confirmation .= "<input type='hidden' id='item_0_name' name='item_0_name' value='" . $item_0_name ."' />";
$confirmation .= "<input type='hidden' id='item_0_quantity' name='item_0_quantity' value='". $item_0_quantity . "' />";
$confirmation .= "<input type='hidden' id='item_0_unit_price' name='item_0_unit_price' value='" . $item_0_unit_price . "'>";
$confirmation .= "<input type='hidden' id='line_item_count' name='line_item_count' value='" . $line_item_count . "' />";
$confirmation .= "<input type='hidden' id='reference_number' name='reference_number' value= '". $reference_number ."' />";
$confirmation .= "<input type='hidden' id='signed_date_time' name='signed_date_time' value='". $signed_date_time ."' /> ";
$confirmation .= "<input type='hidden' id='signed_field_names' name='signed_field_names' value= '" . $signed_field_names ."' />";
$confirmation .= "<input type='hidden' id='transaction_type' name='transaction_type' value='". $transaction_type ."' />";
$confirmation .= "<input type='hidden' id='transaction_uuid' name='transaction_uuid' value='" . $transaction_uuid ."' /> ";
$confirmation .= "<input type='hidden' id='unsigned_field_names' name='unsigned_field_names' value='". $unsigned_field_names ."' />";
$confirmation .= "<input type='hidden' id='signature' name='signature' value='". $signature ."' />";
//$confirmation .= "<p> Thank You. Please continue to payment. </p>";
$confirmation .= "<input type='submit' value='Payment'>";
$confirmation .= "</form>";


return $confirmation;
}

function set_signature($fields_map,$secret_key) 
{
    $data_to_sign = '';
    foreach ( $fields_map as $field => $value )
    {
            $data_to_sign .= "$field=$value,";
    }
    //error_log("data : ".$data_to_sign);
    // Remove trailing comma
    $data_to_sign = substr( $data_to_sign, 0, -1 );
    $hash = hash_hmac(
            'sha256',
            $data_to_sign,
            $secret_key,
            true
    );
    $signature = base64_encode( $hash );
    return $signature;
}


// Create cron job

register_activation_hook(__FILE__, 'cybersource_cron_activation');

function cybersource_cron_activation() {
    if (! wp_next_scheduled ( 'my_hourly_event' )) {
	wp_schedule_event(time(), 'hourly', 'cybersource_hourly_event');
    }
}

add_action('cybersource_hourly_event', 'get_cybersource_settlements');

function get_cybersource_settlements() {
	// curl to cybersource report url and get settlement information, and update
    
    
    
    
}
?>