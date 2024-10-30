<?php
 /*
 * Plugin name: Kitz Pro Builder
 * Description: Kitz Builder helps you build Divi sites ten times faster with cloud storage and drop-in features.
 * Version: 6.12.24
 * Author: The Open Source Ageny
 * Author URI: https://theopensourceagency.com
 * License: GPL v2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('kitz_url', plugin_dir_url(__FILE__));
define('kitz_path', plugin_dir_path(__FILE__));
define('kitz_plugin', plugin_basename(__FILE__));
define('kitz_apiurl', "https://app.divikitz.com/");
define('kitz_version', "6.12.24");

//define( 'ALLOW_UNFILTERED_UPLOADS', true );
define('kitz_CURRENT_SITEURL', get_site_url());
define('kitz_BUILDR_PLUGIN_SLUG','buildr');
define('kitz_UPDATE_PLUGIN_URL','https://app.divikitz.com/repository/');
define('kitz_code_updated','yes');


update_option('kitz_unauth_section_api', esc_url(kitz_apiurl.'unauth-section-api'));
update_option('kitz_unauth_layout_api', esc_url(kitz_apiurl.'unauth-layout-api/'));


//update_option('kitz_drop_returnuri', kitz_url.'dropbox.php');
update_option('kitz_drop_returnuri', esc_url(site_url().'/wp-json/kitz/dropbox_params/'));

require_once 'includes/kitz_core.php';
require_once 'kitztemplater.php';
require_once 'templates/kitz_dashboard_api.php';
require_once 'templates/kitz_builderapi.php';
require_once 'templates/kitz_dropbox.php';


    

register_activation_hook(__FILE__, 'kitz_buildr_plugin_activated'); 

function kitz_buildr_plugin_activated() {
    $response = wp_remote_get( kitz_apiurl.'wp-json/kitzdropbox/connect' );
    $body_resp= wp_remote_retrieve_body( $response );
    $decode_resp=json_decode($body_resp, true);
    //update_option('testE', $decode_resp);
    update_option('kitz_dropbox', sanitize_text_field("disable"));
    update_option('kitz_connect', sanitize_text_field("no"));
    if($decode_resp['code']=="200"){
        update_option('kitz_dropbox_settings', sanitize_text_field("enable"));
        update_option('kitz_dropbox', sanitize_text_field("disable"));
        update_option('kitz_drop_api', sanitize_text_field( $decode_resp['drop_api']) );
        update_option('kitz_drop_secret', sanitize_text_field( $decode_resp['drop_secret']) );
    }
}



register_deactivation_hook( __FILE__, 'kitz_buildr_plugin_deactivated' );

function kitz_buildr_plugin_deactivated() {
    update_option('kitz_connect', sanitize_text_field("no"));
    update_option('kitz_dropbox_settings', sanitize_text_field("disable"));
    update_option('kitz_dropbox', sanitize_text_field("disable"));
    update_option('kitz_drop_api', sanitize_text_field(''));
    update_option('kitz_drop_secret', sanitize_text_field(''));
}


if (!function_exists('kitz_pre')) {
    function kitz_pre($arr){
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}




if (!function_exists('kitz_db_refresh_token_function')) {
    function kitz_db_refresh_token_function(){
        $drop_key= get_option('kitz_drop_api', true);
        $drop_secret= get_option('kitz_drop_secret', true);
        $refresh_token=get_option('kitz_db_refresh_token', true);
        // echo $refresh_token;
        // die("HJERE");

        $args = array(
            'body'        => array( 
                "grant_type"    => "refresh_token", 
                "refresh_token" => $refresh_token, 
                "client_id"     => $drop_key, 
                "client_secret" => $drop_secret 
            ),

            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array('Content-Type: application/x-www-form-urlencoded'),
            'cookies'     => array(),
        );

        // echo "<pre>";
        // print_r($args);
        // die();

        $dropbox_query = wp_remote_post( 'https://api.dropbox.com/oauth2/token', $args );
        $response     = wp_remote_retrieve_body( $dropbox_query );

        $decode_drop_box= json_decode($response, true);

        // echo "<pre>";
        // print_r($decode_drop_box);
        // echo "</pre>";
        // die();

        $access_token= $decode_drop_box['access_token'];
        $token_type= $decode_drop_box['token_type'];
        $expires_in= $decode_drop_box['expires_in'];

        $result_token= array(
            "access_token"=> $access_token,
            "token_type"=> $token_type,
            "expires_in"=> $expires_in
        );

        // echo "<pre>";
        // print_r($result_token);
        // echo "</pre>";
        // die();        
        
        update_option('kitz_dropbox_token_detail', $result_token);
        //die("HERE");
    }
}

$result= kitz_db_refresh_token_function();



if( function_exists('kitz_head_callback')) {
    add_action( 'wp_head', 'kitz_head_callback' );
    function kitz_head_callback() {
        $bloxxbuilder_connect_type = get_option('kitz_connect_type');
        $is_bloxxbuilder_connect = get_option('kitz_connect');
        if($bloxxbuilder_connect_type=='simple'){
            $auth_type = 'simple';
        }else{
            $auth_type = '';
        }
        ?>
        <input type="hidden" id="auth_type" value="<?php echo esc_html(sanitize_text_field($auth_type)); ?>">
        <input type="hidden" id="site_url" value="<?php echo esc_url(kitz_CURRENT_SITEURL); ?>">
        <input type="hidden" id="is_bloxxbuilder_connect" value="<?php echo esc_html(sanitize_text_field($is_bloxxbuilder_connect)); ?>">
        
        <?php
    }
}


if( function_exists('kitz_insertValueAtPosition')) {
    function kitz_insertValueAtPosition($arr, $insertedArray, $position) {
        $i = 0;
        $new_array=[];
        foreach ($arr as $key => $value) {
            if ($i == $position) {
                foreach ($insertedArray as $ikey => $ivalue) {
                    $new_array[$ikey] = $ivalue;
                }
            }
            $new_array[$key] = $value;
            $i++;
        }
        return $new_array;
    }
}


if( function_exists('kitz_main_add_edit_link_filters')) {
    add_action( 'admin_init', 'kitz_main_add_edit_link_filters' );

    function kitz_main_add_edit_link_filters() {
        add_filter( 'page_row_actions', 'main_add_edit_link', 10, 2 );
    }

    function kitz_main_add_edit_link( $actions, $post ) {
        $post_link = get_permalink( $post->ID ).'?kitz_builder=enable';
        $edit_action = array(
            'divi2' => sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                esc_url( $post_link ),
                esc_attr(
                    sprintf(
                        __( 'Edit “%s” in Divi', 'et_builder' ),
                        esc_url( $post_link )
                    )
                ),
                esc_html__( 'Edit With Buildr', 'et_builder' )
            ),
        );

        $actions = array_merge( $actions, $edit_action );
        //print_r($actions);exit;
        return $actions;
    }
}
?>