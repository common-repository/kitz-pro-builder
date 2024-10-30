<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kitz_builderapi {

    public function __construct() {
        add_action('rest_api_init', array($this, 'kitz_globally_header_footer'));
        add_action('rest_api_init', array($this, 'kitz_login_api_hooks'));
        add_action('rest_api_init', array($this, 'kitz_create_page_hooks'));

        //Page Content
        add_action('rest_api_init', array($this, 'kitz_insert_page_hooks'));

        //Trash Page
        add_action('rest_api_init', array($this, 'kitz_delete_page_hooks'));

        //Page Content
        add_action('rest_api_init', array($this, 'kitz_edit_page_hooks'));

        //Update Page API
        add_action('rest_api_init', array($this, 'kitz_update_page_hooks'));

        //Update Page API
        add_action('rest_api_init', array($this, 'kitz_default_page_hooks'));

        //Permalink Retrive API
        add_action('rest_api_init', array($this, 'kitz_permalink_page_hooks'));
        
        //Pages Permalink API
        add_action('rest_api_init', array($this, 'kitz_page_permalinks'));
        
        add_action('rest_api_init', array($this, 'kitz_update_blog'));
		
		  //Update Page title API
        add_action('rest_api_init', array($this, 'kitz_update_page_title_hooks'));

        //Sync Pages Array
        add_action('rest_api_init', array($this, 'kitz_sync_all_pages'));

        //Sync Pages Array
        add_action('rest_api_init', array($this, 'kitz_duplicate_page'));

        //add_action('rest_api_init', array($this, 'kitz_sitefavicon_set'));
    }



    // Update Page API
    function kitz_duplicate_page() {
        @register_rest_route(
            'builder-page', '/duplicate/', array(
                    'methods' => 'POST',
                    'callback' => array($this, 'kitz_builder_page_duplicate'),
            )
        );
    }
    
    function kitz_builder_page_duplicate($request) {

        global $wpdb;
        $page_id = $request["server_page_id"];        

        if ($page_id != "") {
            $tb_name = $wpdb->prefix . 'posts';
            $page_query = $wpdb->prepare("SELECT * FROM $tb_name where ID= %d", $page_id);
            $pages_result = $wpdb->get_row($page_query);
            
            $page_title=$pages_result->post_title;
            $page_content=$pages_result->post_content;


            $create_page = array(
                'post_title' => $page_title." Copy",
                'post_status' => 'publish',
                'post_author' => 1,
                'post_content'=> $page_content,
                'post_type' => 'page'
            );
            $pid = wp_insert_post($create_page);

            $result = array(
                "code" => 200,
                "page_id" => $pid,
                "message" => "Dulicate $page_title page created successfully",
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to create duplicate $page_title page"                
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }



    // Update Page API
    function kitz_sync_all_pages() {
        register_rest_route(
            'builder-page', '/sync/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_page_data_array'),
            )
        );
    }
    
    function kitz_sync_page_data_array($request) {

        global $wpdb;
        $sync_page = $request["sync_page"];        

        if ($sync_page == "yes") {
            $tb_name = $wpdb->prefix . 'posts';
            $k_page= "page";
            $k_post_status= "publish";
            $page_query = "SELECT * FROM $tb_name where post_type=%s and post_status= %s";
            $pages_result = $wpdb->get_results($wpdb->prepare($page_query, $k_page, $k_post_status));
            
            $page_array=array();

            foreach($pages_result as $mypages){
                $page_id= $mypages->ID;
                $permalink_url= get_the_permalink($page_id);
                $page_array[]=array(
                    'ID'=> $page_id,
                    'post_title'=> $mypages->post_title,
                    'post_slug'=> $mypages->post_name,
                    'page_url' => $permalink_url
                );
            }

            $permalink_url = get_the_permalink($page_id);
            $result = array(
                "code" => 200,
                "page_data" => $page_array,
                "message" => "Page sync successfully",
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to page sync from app hosted website"                
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }


    
	// Update Page API
    function kitz_update_page_title_hooks() {
        register_rest_route(
            'builder-page', '/update_content_title/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_update_page_title'),
            )
        );
    }
    
    function kitz_sync_update_page_title($request) {
        global $wpdb;
        $page_id = $request["server_page_id"];
        $project_title = $request["project_title"];

        if ($page_id != "") {
            $tb_name = $wpdb->prefix . 'posts';
            $wpdb->update($tb_name, array('post_title' => $project_title), array('ID' => $page_id));

            $permalink_url = get_the_permalink($page_id);
            $result = array(
                "code" => 200,
                "page_link" => $permalink_url,
                "message" => "Page Updated Successfully",
                'page_id' => $page_id
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to update page data to reference website by API",
                'content' => $content
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }
	
    function kitz_update_blog(){
        register_rest_route(
            'bloginfo', '/update/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_update_bloginfo'),
            )
        );
    }
    
    
    function kitz_update_bloginfo($request) {
        global $wpdb;
        $bloginfo = $request["server_bloginfo"];

        if ($bloginfo != "") {
            $tb_name = $wpdb->prefix . 'options';
            $wpdb->update($tb_name, array('option_value' => $bloginfo), array('option_name' => "blogname"));            
            $result = array(
                "code" => 200,
                "message" => "Blog info update Successfully"
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to update blog info with API"                
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }
    
    
    
    
    
    function kitz_page_permalinks(){
        register_rest_route(
            'pages', '/getpermalinks/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_result_page_permalinks'),
            )
        );
    }
    
    function kitz_result_page_permalinks($request){
        $page_ids=$request['pages_permalinks'];
        if(isset($page_ids) && !empty($page_ids)){
            $page_permalinks=array();
            foreach ($page_ids as $page_id):
                $get_permalinks=get_the_permalink($page_id);
                $page_permalinks[]=array(
                    $page_id => $get_permalinks
                );                
            endforeach;            
            
            $result=array(
                "code" => 200,
                "page_permalink"=> $page_permalinks,
                "message" => "Permalink retrive successfully"                
            );
        } else {
            $result=array(
              "code" => 202,
              "message" => "Failed to get response from permalinks"                
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }
    
    

    // Create Divi Library API
    function kitz_globally_header_footer() {
        register_rest_route(
            'globally', '/header_footer/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_assign_globally'),
            )
        );
    }

    function kitz_sync_assign_globally($request) {
        global $wpdb;
        $page_name = $request["post_title"];
        $page_content = $request['post_content'];
        $page_slug = $request['post_slug'];
        $user_id = $request['user_id'];
        $type = $request['type'];
        $server_page_id=$request['server_pageid'];
        update_post_meta($server_page_id, 'kitz_page_referesh', 'yes');

        if ($type == "assign_header") {
            $post_type = 'et_header_layout';
            $meta_name = '_et_header_layout_id';
            $meta_enable = '_et_header_layout_enabled';
            //$msg = "$page_name header";
            $msg = "Header";
            $guid = site_url() . "/?post_type=et_header_layout&p=";
        } else {
            $post_type = 'et_footer_layout';
            $meta_name = '_et_footer_layout_id';
            //$msg = "$page_name footer";
            $msg = "Footer";
            $meta_enable = '_et_footer_layout_enabled';
            $guid = site_url() . "/?post_type=et_footer_layout&p=";
        }

        global $wpdb;
        $theme_query= $wpdb->prepare("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_name = %s", 'theme-builder');
        $theme_builder_query = $wpdb->get_row($theme_query, 'ARRAY_A');
        if (null === $theme_builder_query) {
            $create_post = array(
                'post_content' => '',
                'post_title' => 'Theme Builder',
                'post_status' => 'publish',
                'post_author' => $user_id,
                'post_type' => 'et_theme_builder'
            );
            $theme_builder_id = wp_insert_post($create_post);
        } else {
            $theme_builder_id = $theme_builder_query['ID'];
        }


        $default_temp_qry= $wpdb->prepare("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_name = %s", 'default-website-template');

        $default_template_query = $wpdb->get_row($default_temp_qry, 'ARRAY_A');

        if (null === $default_template_query) {
            $create_post = array(
                'post_content' => '',
                'post_title' => 'Default Website Template',
                'post_status' => 'publish',
                'post_author' => $user_id,
                'post_type' => 'et_template'
            );
            $default_template_id = wp_insert_post($create_post);
        } else {
            $default_template_id = $default_template_query['ID'];
        }

        update_post_meta($theme_builder_id, '_et_template', sanitize_text_field($default_template_id) );
        update_post_meta($default_template_id, '_et_default', 1);
        update_post_meta($default_template_id, $meta_enable, 1);
        update_post_meta($default_template_id, '_et_enabled', 1);
        update_post_meta($default_template_id, '_et_body_layout_enabled', 1);


        $main_query = $wpdb->prepare("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_type = %s", $post_type);
        $page_row = $wpdb->get_row($main_query, 'ARRAY_A');
        if (null === $page_row) {            
            
            $tb_name = $wpdb->prefix . 'posts'; 
            $create_post = array(                
                'post_title' => $page_name,
                'post_status' => 'publish',
                'post_author' => $user_id,
                'post_type' => $post_type
            );
            $wpdb->insert($tb_name, $create_post);
            $pid = $wpdb->insert_id;
            
            
            
            //$pid = wp_insert_post($create_post);

            $update_page = array(
                'ID' => $pid,
                'guid' => $guid . $pid
            );
            wp_update_post($update_page);

            update_post_meta($default_template_id, $meta_name, sanitize_text_field($pid));

            update_post_meta($pid, '_et_pb_use_builder', 'on');
            update_post_meta($pid, '_et_pb_show_page_creation', 'on');
            update_post_meta($pid, '_et_pb_built_for_post_type', 'on');
            
            $wpdb->update($tb_name, array('post_content' => $page_content), array('ID' => $pid));
            $result = array(
                "code" => 200,
                "message" => "$msg set globally for all pages"
            );
        } else {
            
            $page_id = $page_row['ID'];
//            $update_page = array(
//                'ID' => $page_id,
//                'post_title' => $page_name,
//                'post_content' => $page_content,
//            );
//            wp_update_post($update_page);
            $tb_name = $wpdb->prefix . 'posts';            
            $wpdb->update($tb_name, array('post_content' => $page_content), array('ID' => $page_id));

            update_post_meta($default_template_id, $meta_name, sanitize_text_field($page_id));
            update_post_meta($page_id, '_et_pb_use_builder', 'on');
            update_post_meta($page_id, '_et_pb_show_page_creation', 'on');
            update_post_meta($page_id, '_et_pb_built_for_post_type', 'on');
            $result = array(
                "code" => 200,
                "message" => "$msg updated globally for all pages"
            );
        }


        echo wp_json_encode($result);
        wp_die();
    }

    //Login User By API
    public function kitz_login_api_hooks() {
        register_rest_route(
            'builder-auth', '/login/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_builderlogin')
            )
        );
    }

    //Login API CAll Back Function
    public function kitz_builderlogin($request) {
        $creds = array();
        $creds['user_login'] = $request["username"];
        $creds['user_password'] = $request["password"];
        $creds['remember'] = true;
        $user = wp_signon($creds, false);
        return $user;
    }

    //insert_page_hooks
    function kitz_insert_page_hooks() {
        register_rest_route(
            'builder-page', '/insert/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_syn_insert_page'),
            )
        );
    }

    function kitz_syn_insert_page($request) {
        $page_content = $request['project_content'];
        $page_name = $request["project_title"];
        $user_id = $request['user_id'];

        $create_page = array(
            'post_content' => $page_content,
            'post_title' => $page_name,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'page'
        );

        $pid = wp_insert_post($create_page);
        update_post_meta($page_id, 'kitz_page_referesh', "yes");
        update_post_meta($pid, '_et_pb_page_layout', 'et_no_sidebar');
        update_post_meta($pid, '_et_pb_use_builder', 'on');

        $result = array(
            "code" => 200,
            "message" => "Page Created Successfully",
            'page_id' => $pid,
            'content' => $content
        );
        echo wp_json_encode($result);
        wp_die();
    }

    // Create Page API
    function kitz_create_page_hooks() {
        register_rest_route(
            'builder-page', '/sync_page/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_create_page'),
            )
        );
    }

    function kitz_sync_create_page($request) {
        $page_content = $request['project_content'];

        global $wpdb;
        $page_qry= $wpdb->prepare("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_name = %s", $page_slug);
        $page_row = $wpdb->get_row($page_qry, 'ARRAY_A');
        if (null === $page_row) {
            $page_name = $request["project_title"];
            $page_slug = $request['project_slug'];
            $user_id = $request['server_userid'];
             $main_server_page_id = $request['main_server_page_id'];


            $create_page = array(
                'post_content' => $page_content,
                'post_title' => $page_name,
                'post_status' => 'publish',
                'post_author' => $user_id,
                'post_type' => 'page'
            );
            $pid = wp_insert_post($create_page);
            update_post_meta($pid, 'kitz_page_referesh', "yes");
            update_post_meta($pid, 'kitz_main_server_page_id', sanitize_text_field($main_server_page_id));
            
            update_post_meta($pid, '_et_pb_page_layout', 'et_no_sidebar');
            update_post_meta($pid, '_et_pb_use_builder', 'on');
            $result = array(
                "code" => 200,
                "message" => "Page Created Successfully",
                'page_id' => $pid,
                'content' => $content
            );
        } else {
            $page_id = $page_row['ID'];
            $update_page = array(
                'ID' => $page_id,
                'post_content' => $page_content,
            );
            wp_update_post($update_page);

            update_post_meta($page_id, '_et_pb_page_layout', 'et_no_sidebar');
            update_post_meta($page_id, '_et_pb_use_builder', 'on');

            $result = array(
                "code" => 200,
                "message" => "Page Updated Successfully",
                'page_id' => $page_id,
                'content' => $content
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

    // Delete Page API
    function kitz_delete_page_hooks() {
        register_rest_route(
            'builder-page', '/delete/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_delete_page'),
            )
        );
    }

    function kitz_sync_delete_page($request) {
        $page_id = $request["server_page_id"];

        if ($page_id != "") {
            wp_delete_post($page_id);
            $result = array(
                "code" => 200,
                "message" => "Page Deleted Successfully",
                'content' => $content
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to delete page remotely, You can delete from wordpress administrator account",
                'content' => $content
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

    // Edit Page API Get Content
    function kitz_edit_page_hooks() {
        register_rest_route(
            'builder-page', '/content/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_edit_page'),
            )
        );
    }

    function kitz_sync_edit_page($request) {
        global $wpdb;
        $page_id = $request["server_pageid"];

        if ($page_id != "") {
            $conn_site = $wpdb->prefix . 'posts';
            $project_query = $wpdb->prepare("SELECT * FROM $conn_site where ID= %d order by ID desc limit 1", $page_id);
            $con_details = $wpdb->get_row($project_query);

            $output = $con_details->post_content;

            $result = array(
                "code" => 200,
                "message" => "Page sync Successfully",
                'content' => $output
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to sync page data from reference website",
                'content' => $content
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

    // Update Page API
    function kitz_update_page_hooks() {
        register_rest_route(
            'builder-page', '/update_content/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_update_page'),
            )
        );
    }

    function kitz_sync_update_page($request) {
        global $wpdb;
        $page_id = $request["server_page_id"];
        $content = $request["project_content"];

        if ($page_id != "") {
            update_post_meta($page_id, 'page_referesh', "yes");
            
            $tb_name = $wpdb->prefix . 'posts';
            $wpdb->update($tb_name, array('post_content' => $content), array('ID' => $page_id));

            /*
              $update_page = array(
              'ID'           => $page_id,
              'post_content' => $content,
              );
              wp_update_post($update_page); */
            $utils = ET_Core_Data_Utils::instance();

            do_action('et_update_post', $page_id);
            apply_filters('et_fb_ajax_save_verification_result', $content);
            
            
            /*  UPDATE DIVI OPTIONS  */
            // $options=get_option('et_divi', true);
            // $arr_replace=array("et_pb_static_css_file"=>"off", "et_enable_classic_editor"=>"on");
            // $basket = array_replace($options, $arr_replace);

            // update_option("et_divi", $basket);
            
            /* END UPDATE DIVI OPTIONS */

            update_post_meta($page_id, '_et_pb_page_layout', sanitize_text_field('et_no_sidebar'));
            update_post_meta($page_id, '_et_pb_use_builder', sanitize_text_field('on'));
            
            
            
            $permalink_url = get_the_permalink($page_id);
            $result = array(
                "code" => 200,
                "page_link" => $permalink_url,
                "message" => "Page Updated Successfully",
                'page_id' => $page_id
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to update page data to reference website by API",
                'content' => $content
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

    // Update Page API
    function kitz_permalink_page_hooks() {
        register_rest_route(
            'builder-page', '/permalink/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_retrive_permalink'),
            )
        );
    }

    function kitz_retrive_permalink($request) {
        global $wpdb;
        $page_id = $request["server_page_id"];

        if ($page_id != "") {
            $project_link = get_the_permalink($page_id);
            $result = array(
                "code" => 200,
                "message" => "Page Updated Successfully",
                'website_link' => $project_link,
                'page_id' => $page_id
            );
        } else {
            $result = array(
                "code" => 202,
                "message" => "Failed to update page data to reference website by API"
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

    // API for set by default Home Page
    function kitz_default_page_hooks() {
        register_rest_route(
            'builder-page', '/homepage/', array(
                'methods' => 'POST',
                'callback' => array($this, 'kitz_sync_set_homepage'),
            )
        );
    }

    function kitz_sync_set_homepage($request) {
        global $wpdb;
        $page_id = $request["server_pageid"];

        if ($page_id != "") {
            update_option('show_on_front', sanitize_text_field('page'));
            update_option('page_on_front', sanitize_text_field($page_id));
            $result = array(
                "code" => 200,
                "message" => "Home page set successfully",
                'page_id' => $page_id
            );
        } else {
            $result = array(
                "code" => 202,
                'page_id' => $page_id,
                "message" => "Failed to set homepage for reference website by API"
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

}

$kitz_builderapi = new Kitz_builderapi();
