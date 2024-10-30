<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kitz_dashboard_api {

    public function __construct() {
        //Enable/Disable Page meta ready for bloxx builder
        add_action("wp_ajax_kitz_update_metabox", array($this, "kitz_update_metabox"));
        add_action("wp_ajax_nopriv_kitz_update_metabox", array($this, "kitz_update_metabox"));


        //Swich Page Meta for enable bloxx builder to next page
        add_action("wp_ajax_kitz_switch_metabox", array($this, "kitz_switch_metabox"));
        add_action("wp_ajax_nopriv_kitz_switch_metabox", array($this, "kitz_switch_metabox"));

        

        add_action("wp_head", array($this, "kitz_wp_head_add_html"));

        
        
        add_action("wp_ajax_kitz_saveproject", array($this, "kitz_saveproject"));
        add_action("wp_ajax_nopriv_kitz_saveproject", array($this, "kitz_saveproject"));
        
        
        add_action("wp_ajax_kitz_kitz_et_builder_load_css", array($this, "kitz_et_builder_load_css"));
        add_action("wp_ajax_nopriv_kitz_et_builder_load_css", array($this, "kitz_et_builder_load_css"));
        
        add_action("wp_ajax_kitz_kitz_headfooter_assign", array($this, "kitz_headfooter_assign"));
        add_action("wp_ajax_nopriv_kitz_headfooter_assign", array($this, "kitz_headfooter_assign"));



        add_action("wp_ajax_kitz_createpage", array($this, "kitz_createpage"));
        add_action("wp_ajax_nopriv_kitz_createpage", array($this, "kitz_createpage"));
        
        
        // add_action("wp_ajax_kitz_cat_industry", array($this, "kitz_cat_industry"));
        // add_action("wp_ajax_nopriv_kitz_cat_industry", array($this, "kitz_cat_industry"));
    }

    // public function kitz_cat_industry(){
    //     check_ajax_referer( 'divikitz', '_nonce' );
    //     $neo_type= sanitize_text_field($_POST['neo_type']);

    //     $user_email= get_option('builder_username', true);
    //     $user_id= get_option('bloxx_user_id', true);
    //     $curl_url = kitz_apiurl."wp-json/neo_directory/assets_cats";                
    //     $neo_cloud_cats = array(
    //         'neo_type'  => $neo_type,
    //         'user_id'  => $user_id
    //     );
    //     $neo_cats = json_encode($neo_cloud_cats);


    //     $args_cats = array(
    //         'body'        => $neo_cats,
    //         'timeout'     => '5',
    //         'redirection' => '5',
    //         'httpversion' => '1.0',
    //         'blocking'    => true,
    //         'headers'     => array("cache-control: no-cache", "content-type: application/json"),
    //         'cookies'     => array(),
    //     );
    //     $neo_cat_query = wp_remote_post( $curl_url, $args_cats );
    //     $response     = wp_remote_retrieve_body( $neo_cat_query );
    //     echo $response;
    //     die();
    // }


    

    public function kitz_createpage(){
        check_ajax_referer( 'divikitz', '_nonce' );
        $pnm= sanitize_text_field($_POST['pnm']);
        $user_id = get_current_user_id();
        $create_page = array(
            'post_content' => "",
            'post_title' => $pnm,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'page'
        );

        $pid = wp_insert_post($create_page);
        update_post_meta($pid, '_et_pb_page_layout', 'et_no_sidebar');
        update_post_meta($pid, '_et_pb_use_builder', 'on');

        $page_permalink=get_the_permalink($pid);

        $enable_bloxx=$page_permalink."?kitz_builder=enable";
        $result = array(
            "code" => 200,
            "message" => "$pnm Page published successfully",
            'page_link' => $enable_bloxx
        );
        echo wp_json_encode($result);
        wp_die();
    }

    

    function kitz_wp_head_add_html(){

        $user = get_user_by('id', get_current_user_id());
        $user_email = $user->user_email;
        ?>
        <style type="text/css">
            a.the_plan_button_client_popup{
                background: #ba0ea4;
                color: #fff;
                padding: 10px 15px;
                border-radius: 5px;
                display: inline-block;
                margin-top: 15px;
            }
        </style>
            <input type="hidden" id="get_user_id_bloxx_client" value="<?php echo esc_html($user_email); ?>">
        <?php
    }

    

    public function kitz_switch_metabox() {
        check_ajax_referer( 'divikitz', '_nonce' );
        $post_id= sanitize_text_field($_POST['post_id']);
        $old_page= sanitize_text_field($_POST['old_page']);
        $meta_type= sanitize_text_field($_POST['meta_type']);


        update_post_meta($post_id, '_et_pb_page_layout', sanitize_text_field('et_no_sidebar'));
        update_post_meta($post_id, '_et_pb_use_builder', sanitize_text_field('on') );
        $user_id = get_current_user_id();
        //update_user_meta($user_id, 'show_admin_bar_front', 'false');

        if ($old_page != "" && $post_id !="") {
            //Disable Builder for old page 
            update_post_meta($old_page, 'kitz_builder', sanitize_text_field('disable'));
            update_post_meta($old_page, '_wp_page_template', sanitize_text_field('default'));
            
            
            //Enable Builder for new page
            update_post_meta($post_id, 'kitz_builder', sanitize_text_field('enable'));
            //update_post_meta($post_id, '_wp_page_template', 'bloxx_call_template.php');
            
            
            $result = array(
                'code' => 200,
                'message' => "Kitzpro builder switched successfully"
            );
        } else {
            $result = array(
                'code' => 202,
                'message' => "Failed to switch Kitzpro builder"
            );
        }        
        echo wp_json_encode($result);
        wp_die();
    }
    
    

    public function kitz_update_metabox() {
        check_ajax_referer( 'divikitz', '_nonce' );

        $meta_type= sanitize_text_field($_POST['meta_type']);
        update_post_meta($post_id, '_et_pb_page_layout', 'et_no_sidebar');
        update_post_meta($post_id, '_et_pb_use_builder', 'on');
        $user_id = get_current_user_id();
        $admin_bar_front = get_user_meta($user_id, 'show_admin_bar_front', true);
        update_user_meta($user_id, 'adminbar_usersetting', sanitize_text_field($admin_bar_front));
        
        if ($meta_type == "kitz_enable") {
            update_user_meta($user_id, 'show_admin_bar_front', false);
            update_post_meta($post_id, 'kitz_builder', 'enable');
            $result = array(
                'code' => 200,
                'message' => "Kitzpro builder ready to launch"
            );
        } else if ($meta_type == "kitz_disable") {
            $usersetting=get_user_meta($user_id, "adminbar_usersetting", true);
            update_user_meta($user_id, 'show_admin_bar_front', sanitize_text_field($usersetting));
            update_post_meta($post_id, 'kitz_builder', 'disable');
            update_post_meta($post_id, '_wp_page_template', 'default');
            $result = array(
                'code' => 200,
                'message' => "Kitzpro builder exit successfully"
            );
        } else {
            $result = array(
                'code' => 202,
                'message' => "Failed to load Kitzpro builder"
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }
    
    
    
    
    //Save Page Code Here
    public function kitz_saveproject() {
        check_ajax_referer( 'divikitz', '_nonce' );
        global $wpdb;
        if (isset($_POST['json_content']) && !empty($_POST['json_content'])) {
            $project_nm = sanitize_text_field($_REQUEST['builder_prj_title']);
            $project_id = sanitize_text_field($_REQUEST['builder_prj_id']);
            $project_title= get_the_title($project_id);
            
            // $rm_cache= WP_CONTENT_DIR."/et-cache/$project_id";
            // exec("rm -rf $rm_cache");
            
            $action_to_perform = sanitize_text_field($_REQUEST['action_to_perform']);

            // echo "<pre>";
            // print_r($_REQUEST['json_content']);
            // die();

            $json_content = sanitize_text_field(json_encode($_REQUEST['json_content']));
            $kitz_content= json_decode($json_content);

            // echo "<pre>";
            // print_r($kitz_content);
            // die();
            
            //$kitz_content = $_REQUEST['json_content'];

            $ajax_content = "";
            foreach ($kitz_content as $drop_content):
                $ajax_content .= $drop_content;                
            endforeach;

            $remove_farward_slash = str_replace('\\', '', $ajax_content);


            $kitz_save_content= str_replace('&quot;', '"', $remove_farward_slash);
            
            $my_post = array(
                'ID' => $project_id,
                'post_content' => $kitz_save_content,
            );
            wp_update_post($my_post);
            
            update_post_meta($project_id, 'kitz_page_referesh', 'yes');
            update_post_meta($project_id, '_et_pb_page_layout', 'no');

            

            $result = array(
                'code' => 200,
                'project_id' => $project_id,
                'message' => "$project_title page updated successfully"
            );
            
        } else {
            $result = array(
                'code' => 202,
                'message' => 'Please select layout before create save project'
            );
        }

        echo wp_json_encode($result);
        wp_die();
    }
    


    
    
    
    public function kitz_et_builder_load_css() {
        check_ajax_referer( 'divikitz', '_nonce' );

        $page_id= sanitize_text_field($_POST['page_id']);
        
        $time = time();

        $link_url = get_the_permalink($page_id);

        $shot_nm = "project_$time.png";   //Demo Variable
        $version = $link_url . "?ver=" . $time;

        //$scriptpath = "node " . siteblox_path . "/runpage_nodescript.js {$version} {$shot_nm}";

        //exec($scriptpath, $output);
        //$myJSON = $output;
        //$node_result = implode($myJSON);

        if ($version != "") {
            //$homepage = file_get_contents($cache_css);
            $page_html = file_get_contents($version);
            $resp = array(
                "page_html" => $page_html,
                "code" => 200,
                "message" => "Page css loaded successfully"
            );
        } else {
            $resp = array(
                "code" => 202,
                "message" => "Failed to load page css"
            );
        }

        echo wp_json_encode($result);
        wp_die();
    }
    
    
    public function kitz_headfooter_assign(){
        check_ajax_referer( 'divikitz', '_nonce' );
        $assign_type= sanitize_text_field($_POST['assign_type']);
        $server_page_id= sanitize_text_field($_POST['server_page_id']);
        $page_content= sanitize_text_field($_POST['page_content']);


        global $wpdb;   
        $user_id = get_current_user_id();

        $page_content = str_replace('\\', '', $page_content);
        
        $wp_posts = $wpdb->prefix . 'posts';
        $pages_query = "SELECT ID FROM $wp_posts where post_type='page' and post_status='publish'";
        $pages_result= $wpdb->get_results($pages_query);

        foreach($pages_result as $allpages):
            $page_ids=$allpages->ID;
            update_post_meta($page_ids, 'kitz_page_referesh', 'yes');
        endforeach;
        
        
        
        if ($assign_type == "assign_header") {
            $post_type = 'et_header_layout';
            $meta_name = '_et_header_layout_id';
            $meta_enable = '_et_header_layout_enabled';
            $page_name= "header_assign";
            //$msg = "$page_name header";
            $msg = "Header";
            $guid = site_url() . "/?post_type=et_header_layout&p=";
        } else {
            $post_type = 'et_footer_layout';
            $meta_name = '_et_footer_layout_id';
            $page_name= "footer_assign";
            //$msg = "$page_name footer";
            $msg = "Footer";
            $meta_enable = '_et_footer_layout_enabled';
            $guid = site_url() . "/?post_type=et_footer_layout&p=";
        }
        
        $theme_builder_query = $wpdb->get_row("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_name = 'theme-builder'", 'ARRAY_A');
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
        
        
        
        $default_template_query = $wpdb->get_row("SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_name = 'default-website-template'", 'ARRAY_A');

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
        
        
        $main_query = "SELECT post_name, ID FROM {$wpdb->prefix}posts WHERE post_type = '$post_type'";

        $tb_name = $wpdb->prefix . 'posts'; 

        $page_row = $wpdb->get_row($main_query, 'ARRAY_A');
        if (null === $page_row) {            
            $create_post = array(                
                'post_title' => $page_name,
                'post_content' => $page_content,
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

            update_post_meta($default_template_id, $meta_name, sanitize_text_field($pid) );

            update_post_meta($pid, '_et_pb_use_builder', 'on');
            update_post_meta($pid, '_et_pb_show_page_creation', 'on');
            update_post_meta($pid, '_et_pb_built_for_post_type', 'on');
            
            $result = array(
                "code" => 200,
                "message" => "$msg set globally for all pages"
            );
        } else {
            $page_id = $page_row['ID'];            

            //Delete Before Insert
            $post_name=$page_id."-revision-v1";
            $wpdb->delete( $tb_name, array( 'id' => $page_id ) );
            $wpdb->delete( $tb_name, array( 'post_name' => $post_name ) );  //Also Delete revision


            $create_post = array(                
                'post_title' => $page_name,
                'post_content' => $page_content,
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
            

            
            $result = array(
                "code" => 200,
                "message" => "$msg updated globally for all pages"
            );
        }
        echo wp_json_encode($result);
        wp_die();
    }

}

$kitz_dashboard = new Kitz_dashboard_api();