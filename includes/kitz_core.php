<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kitz_core {

    public function __construct() {
        add_action('wp_footer', array($this, 'footer_css'));
        add_action('admin_menu', array($this, 'builder_menu'));

        add_filter( 'plugin_action_links_kitz-pro-builder/kitz.php', array($this, 'kitz_setting_link'));

        add_action('admin_bar_menu', array($this, 'kitz_enable_builder'), 9999);

        add_action('wp_enqueue_scripts', array($this, 'kitz_plugin_css_jsscripts'));

        //add_action('admin_enqueue_scripts', array($this, 'plugin_css_jsscripts'));

        add_action('admin_enqueue_scripts', array($this, 'kitz_admin_css_jsscripts'));

        add_action('admin_notices', array($this, 'check_divi_theme'));
        //add_action('admin_notices', array($this, 'check_bloxxkey'), 10);

        //CSS for Admin Bar
        add_action('admin_head', array($this, 'kitz_adminbar_bloxxbuilder_css'));
        add_action('wp_head', array($this, 'kitz_adminbar_bloxxbuilder_css'));

        //add_action('wp_head', array($this, 'load_template'));

        add_filter('template_include', array($this, 'kitz_load_template'));

        add_action('wp_head', array($this, "kitz_reload_cssjs"));

        //Admin check API
        add_action("wp_ajax_kitz_siteblox_key_saved", array($this, "kitz_siteblox_key_saved"));
        add_action("wp_ajax_nopriv_kitz_siteblox_key_saved", array($this, "kitz_siteblox_key_saved"));


        //Admin DropBox
        add_action("wp_ajax_kitz_dropbox", array($this, "kitz_dropbox"));
        add_action("wp_ajax_nopriv_kitz_dropbox", array($this, "kitz_dropbox"));



        //Admin DropBox
        add_action("wp_ajax_kitz_dropbox_create_folder", array($this, "kitz_dropbox_create_folder"));
        add_action("wp_ajax_nopriv_kitz_dropbox_create_folder", array($this, "kitz_dropbox_create_folder"));

        //Admin DropBox
        add_action("wp_ajax_kitzdropbox_upload", array($this, "kitzdropbox_upload"));
        add_action("wp_ajax_nopriv_kitzdropbox_upload", array($this, "kitzdropbox_upload"));

        add_action( 'init', array($this, "kitz_is_bloxxpage_open"));
        add_filter('upload_mimes', array($this, 'kitz_allow_mime_types'), 10, 1);
        add_action('rest_api_init', array($this, 'dropbox_return_params'));
    }

    function kitz_allow_mime_types($mimes) {
        $mimes['json'] = 'application/json'; 
        return $mimes; 
    } 

    
    function footer_css(){
        if(isset($_GET['kitzdb'])){
            ?>
            <style>
                header, footer {
                    display: none !important;
                }
            </style>
            <?php
        }
    }

    function kitz_dropbox_create_folder(){
        check_ajax_referer( 'divikitz', '_nonce' );
        $kitz_parent_type= sanitize_text_field($_POST['kitz_parent_type']);
        $section_title= sanitize_text_field($_POST['section_title']);

        if(!get_option('kitz_dropbox', true)){
            $result=array(
                "code"=> 202,
                "message"=> "You need to connect your drop box for continue"
            );
        } else {
            // echo $section_title;
            // die();
            $listing_folder=array(
                "autorename"=> false,
                "path"=> "/kitzbuilder/".$kitz_parent_type."/".$section_title
            );

            $dropbox_token_detail= get_option('kitz_dropbox_token_detail');
            $access_token= $dropbox_token_detail['access_token'];

            $header= array(
                'Content-Type'=> 'application/json',
                'Authorization'=> 'Bearer '.$access_token
            );

            $body_args= json_encode($listing_folder);

            $args = array(
                'body'        => $body_args,
                'timeout'     => '10',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => $header,
                'cookies'     => array(),
            );

            $dropbox_query = wp_remote_post( 'https://api.dropboxapi.com/2/files/create_folder_v2', $args );
            $response     = wp_remote_retrieve_body( $dropbox_query );

            $resp_decode=json_decode($response, true);
            if(isset($resp_decode['error_summary']) && !empty($resp_decode['error_summary'])){
                $result=array(
                    "code"=> 202,
                    "message"=> "May be folder already created on Dropbox"
                );
            } else {
                $entry_folder= "/kitzbuilder/".$kitz_parent_type."/";
                
                $curl_folder= array(
                    "include_deleted"=> false,
                    "include_has_explicit_shared_members"=> false,
                    "include_media_info"=> false,
                    "include_mounted_folders"=> true,
                    "include_non_downloadable_files"=> true,
                    "path"=> $entry_folder,
                    "recursive"=> false
                );

                $header_list= array(
                    'Content-Type'=> 'application/json',
                    'Authorization'=> 'Bearer '.$access_token
                );

                $curl_list_folder= json_encode($curl_folder);

                $list_args = array(
                    'body'        => $curl_list_folder,
                    'timeout'     => '10',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => $header_list,
                    'cookies'     => array(),
                );

                $dropbox_querylist = wp_remote_post( 'https://api.dropboxapi.com/2/files/list_folder', $list_args );
                $resp     = wp_remote_retrieve_body( $dropbox_querylist );

                $get_direct= json_decode($resp, true);
                $data=array();
                if(isset($get_direct['error_summary']) && !empty($get_direct['error_summary'])){
                    $data=array();
                } else {
                    foreach ($get_direct['entries'] as $folders) {
                        $data[]=array(
                            "subfolder"=> $folders['name']
                        );
                    }
                }

                $result=array(
                    "code"=> 200,
                    "subdirectory"=> $data,
                    "message"=> "$section_title folder created under $kitz_parent_type parent category in Dropbox."
                );
            }
        }
        echo wp_json_encode($result);
        wp_die();
    }



    function kitzdropbox_upload(){
        //extract($_REQUEST);
        check_ajax_referer( 'divikitz', '_nonce' );

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $section_title= sanitize_text_field($_POST['section_title']);
        $kitz= sanitize_text_field($_POST['kitz']);
        $kitz_subdirectory= sanitize_text_field($_POST['kitz_subdirectory']);

        kitz_db_refresh_token_function();
        $tmp_nm= sanitize_text_field($_FILES['json_url']['tmp_name']);
        $fn= sanitize_text_field($_FILES["json_url"]['name']);
        $fn_str=str_replace("-", "", $fn);      

        $file_nm= round(microtime(true));
        $filename = $file_nm.".json";
        $filename_img = $file_nm.".png";

        $plugin_path= kitz_path."/dropbox/json/";

        $uploadedfile = $_FILES['json_url'];
        $upload_overrides = array( 'test_form' => false );

        try {
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if($movefile['error']){
                $result=array(
                    'code' => 202,  
                    'message' => $movefile['error']
                );
                echo wp_json_encode($result);
                die("HERE");
            }


            $file_url= $movefile['url'];
            $file_path= $movefile['file'];


            $request = wp_remote_get( $file_url);
            $remote_json = wp_remote_retrieve_body( $request, true );
            $remote_data = json_decode( $remote_json, true, JSON_UNESCAPED_SLASHES);
            $remote_data_renew = json_decode( $remote_json, true, JSON_UNESCAPED_SLASHES);
            $remote_json_data= $remote_data['data'];
            $j=1;

            foreach($remote_json_data as $remote_data):
                if($j==1) {
                    if(isset($remote_data['post_content'])){                            
                        $remote_content= $remote_data['post_content'];
                    } else {                            
                        $remote_content= $remote_data;
                    }

                    //Create
                    $new_post = array(
                        'post_title' => $section_title,
                        'post_content' => $remote_content,
                        'post_status' => 'publish',
                        'post_type' => 'page'
                    );
                    $pid = wp_insert_post($new_post);                       
                    update_post_meta( $pid, '_et_pb_page_layout', sanitize_text_field('et_no_sidebar') );
                    update_post_meta( $pid, '_et_pb_use_builder', sanitize_text_field('on') );
                    $section_permalink= get_the_permalink($pid)."?kitzdb=yes";
                    
                    //Generating image Curl response
                    $image_process= $this->appkitz_createimage($section_permalink);
                    wp_delete_post($pid);
                    wp_trash_post( $pid );
                    if($image_process['code']==200){
                        //Save Image Code from URL
                        $generate_url = $image_process['image_url'];
                        $kitz_imgstorage_path = kitz_path."/dropbox/images/".$filename_img;
                        file_put_contents($kitz_imgstorage_path, file_get_contents($generate_url));
                        

                        //File Move to dropbox
                        $save_file = fopen($file_path,"wb");
                        fwrite($save_file, $remote_content);
                        fclose($save_file);

                        

                        $drop_path_filename="/kitzbuilder/".$kitz."/".$kitz_subdirectory."/".$filename;
                        $upload_return= $this->dropbox_movefiles($file_path, $drop_path_filename, $drop_path_filename);
                        
                        if(@$upload_return['error']['.tag']=="path"){
                            $result=array(
                                'code' => 202,  
                                'message' => "May be you deleted the folder structure"
                            );
                        } else {
                            //Image Move to dropbox
                            $drop_path_img="/kitzbuilder/".$kitz."/".$kitz_subdirectory."/".$filename_img;
                            $this->dropbox_movefiles($kitz_imgstorage_path, $drop_path_img, $filename_img);

                            unlink($kitz_imgstorage_path);
                            $result=array(
                                'code' => 200,  
                                'message' => "Upload json data has been moved to drop box successfully"
                            );
                        }
                    } else {
                        $result=array(
                            'code' => 202,  
                            'message' => $image_process['message']
                        );
                    }
                }
            endforeach;
        } catch(Exception $e) {
            $result=array(
                'code' => 202,  
                'message' => $e->getMessage()
            );
        }

        //echo json_encode($result);
        echo wp_json_encode($result);
        wp_die();
    }


    function appkitz_createimage($page_link){
        $connect_url= esc_url(kitz_apiurl."wp-json/divikitz/screenshot");
        $post_field=array(
            "kitz_url"=> esc_url($page_link)
        );

        $headers = [
          'Content-Type' => 'application/json'
        ];

        $encode= json_encode($post_field);  

        $args = array(
            'body'        => $encode,
            'timeout'     => '10',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $headers,
            'cookies'     => array(),
        );

        $dropbox_query = wp_remote_post( $connect_url, $args );
        $response     = wp_remote_retrieve_body( $dropbox_query );

        $kitz_resp= json_decode($response, true);
        return $kitz_resp;
    }


    function dropbox_movefiles($file_path, $drop_path, $trigger_nm){
        $dropbox_token_detail= get_option('kitz_dropbox_token_detail');
        $access_token= $dropbox_token_detail['access_token'];
        
        $fp = fopen($file_path, 'rb');
        $size = filesize($file_path);

        $cheaders = array(
            'Authorization'=> 'Bearer '.$access_token,
            'Content-Type' => 'application/octet-stream',
            'Dropbox-API-Arg' => '{"path":"'.$drop_path.'", "mode":"add"}'
        );

        $args = array(
            'body'        => fread($fp, $size),
            'timeout'     => '10',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $cheaders,
            'cookies'     => array()
        );

        $dropbox_query = wp_remote_post( 'https://content.dropboxapi.com/2/files/upload', $args );
        $response     = wp_remote_retrieve_body( $dropbox_query );

        $upload_data=json_decode($response, true);
        return $upload_data;
    }
    
    
    public function kitz_setting_link($links){
        // Build and escape the URL.
        $url = esc_url( add_query_arg(
            'page',
            'kitz_settings',
            get_admin_url() . 'admin.php'
        ) );

        // Create the link.
        $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
        // Adds the link to the end of the array.
        array_push($links, $settings_link);
        return $links;
    }



    function kitz_is_bloxxpage_open() {
        $user_id = get_current_user_id();
        if(isset($_REQUEST['kitz_builder'])){
            update_user_meta($user_id, 'show_admin_bar_front', 'false');
        } else {
            update_user_meta($user_id, 'show_admin_bar_front', 'true');
        }
    }

    public function builder_menu() {
        add_menu_page('Kitz Pro', 'Kitz Pro', 'manage_options', 'kitz_settings', array($this, 'bloxx_connect'), kitz_url.'images/wpmenu_icon.png');

        $builder_connect="disable";
        
        if(get_option('kitz_dropbox', true)!="disable"){
            $builder_connect = "enable";
        }

        if ($builder_connect == "enable") { 
            add_submenu_page('kitz_settings','Kitz Drop Box', 'Kitz Drop Box','manage_options','kitz_dropbox', array($this, 'kitzdropbox'),10);
        }

    } 

    public function bloxx_buildr_page_callback(){
        ?>
        <div class="wrap">
            <h1>Buildr Page</h1>
            <p>This is buildr plugin page</p>
        </div>

        <?php
    }

   
    public function bloxx_pixr_page_callback(){
        ?>
        <div class="wrap">
            <h1>Pixr Page</h1>
            <p>This is pixr plugin page</p>
        </div>

        <?php
    }


    public function bloxx_connect() {
        $content= include( kitz_path . 'admin/templates/settings-page.php' );
        return $content;
    }


    public function kitzdropbox(){
        $kdb_content =include( kitz_path . 'admin/templates/kitz_dropbox.php' );
        return $kdb_content;
    }

    public function kitz_reload_cssjs() {
        global $wpdb;
        global $wp_query;
        @$page_id = $wp_query->post->ID;

        //Enable Admin Bar
        $user_id = get_current_user_id();
        /*if(isset($_REQUEST['neo_builder'])){            
            update_user_meta($user_id, 'show_admin_bar_front', 'false');
        } else {
            update_user_meta($user_id, 'show_admin_bar_front', 'true');
        }*/

        if ($page_id != "" && is_user_logged_in()) {
            @$page_refresh = get_post_meta($page_id, 'kitz_page_referesh', true);

            if (@$page_refresh == "yes" && is_user_logged_in()) {

                $posts = $wpdb->prefix . 'posts';
                //$page_query = "SELECT * FROM $posts where ID='$page_id' limit 1";

                $page_query =  $wpdb->prepare("SELECT * FROM $posts where ID= %d limit 1", $page_id);


                $page_data = $wpdb->get_row($page_query);
                $page_content = $page_data->post_content;

                $update = wp_update_post(
                        array(
                            'ID' => $page_id,
                            'post_content' => $page_content,
                            'post_status' => "publish",
                        )
                );
                update_post_meta($page_id, "kitz_page_referesh", "no");
                ?>
                <script>
                    window.location.href = "";
                </script>
                <?php
            }
        }
        return true;
    }

    public function kitz_load_template($template) {
        global $wp_admin_bar, $wp_the_query;
        $post_id = get_the_ID();
        $bloxx_enable = get_post_meta($post_id, 'kitz_builder', true);

        if (is_user_logged_in() && isset($_REQUEST['kitz_builder'])) {
            $temp_path = kitz_path . "kitz_call_template.php";
            return $temp_path;
            //load_template($temp_path);
        } else {
            return $template;
        }
    }

    function kitz_enable_builder() {
        global $wp_admin_bar, $wp_the_query;
        $post_id = get_the_ID();

        $is_divi_library = 'et_pb_layout' === get_post_type($post_id);
        $page_url = $is_divi_library ? get_edit_post_link($post_id) : get_permalink($post_id);

        if ( is_plugin_active( 'buildr/neo_builder.php' ) ) {
            $buildr_topbar_menu = '<li id="wp-admin-buildr_topbar_menu"><a class="neo-item neo-item-buildr" href="'.get_the_permalink($post_id).'?kitz_builder=enable">Buildr</a></li>';
        }else{
            $buildr_topbar_menu = '';
        }

        if ( is_plugin_active( 'WritrAI/Writr.php' ) ) {
            $writr_topbar_menu = '<li id="wp-admin-writr_topbar_menu"><a class="neo-item neo-item-writr" href="javascript:void(0)">WritrAI</a></li>';
        }else{
            $writr_topbar_menu = '';
        }
        

      

        //if (!is_admin() && !isset($_REQUEST['et_fb'])) {
        if (!is_admin()) {
            if (isset($_REQUEST['kitz_builder'])) {
                $wp_admin_bar->add_menu(
                        array(
                            'id' => 'exit-bloxx-builder',
                            'name' => $post_id,
                            'title' => esc_html__('Exit Kitz', 'neo_builder'),
                            'href' => esc_url($page_url),
                            'meta' => array(
                                'title' => $post_id
                            )
                        )
                );
            } else {
                $use_neo_builder_url = add_query_arg(
                        array('kitz_builder' => "enable"), $page_url
                );
                $wp_admin_bar->add_menu(
                        array(
                            'id' => 'et-bloxx-builder',
                            'class' => $post_id,
                            'href' => esc_url($use_neo_builder_url),
                            'title' => esc_html__('Kitz', 'neo_builder'),
                             
                            'class' => 'neo_menupop'
                        )
                );
            }
        }
        return;
    }

    function kitz_adminbar_bloxxbuilder_css() {


        if (is_admin_bar_showing()) {
            ?>
            <style type="text/css">
                .neo-topbar-submenu{
                    display: none;
                }
                #wp-admin-bar-et-bloxx-builder:hover .neo-topbar-submenu{
                    display: block;
                    background: #1d2327;
                    position: absolute;
                    width: 125px;

                }
                .neo-topbar-submenu li {
                    display: block;
                    width: 100% !important;
                    float: left !important;
                }
                .neo-topbar-submenu li a{
                    color: #fff;
                }
                li#wp-admin-bar-et-bloxx-builder a, li#wp-admin-bar-exit-bloxx-builder a {
                    background: url('<?php echo esc_url(kitz_url); ?>images/logoicon.png') no-repeat left center !important;
                    background-size: 20px auto !important;
                    font-weight: 600;
                    padding: 0 12px 0 30px !important;
                    position: relative;
                }

                li#wp-admin-bar-et-bloxx-builder a:hover, li#wp-admin-bar-exit-bloxx-builder a:hover {
                    background: #231942 url('<?php echo esc_url(kitz_url); ?>images/logoicon.png') no-repeat left center !important;
                    background-size: 20px auto !important;
                    color: #fff !important;
                }
                

                li#wp-admin-bar-et-bloxx-builder ul.neo-topbar-submenu li a{
                    background: none !important;
                }
            </style>

            <script>
                jQuery(function ($) {

                    $("body").on("click", "li a.neo-item-writr", function (event) {
                        $('#checkebr').trigger('click');
                    });

                    $("body").on("click", "li#wp-admin-bar-et-bloxx-builder a", function (event) {
                        event.preventDefault();
                        var $this = $(this);
                        var page_id = $this.attr('title');
                        var meta_type = "kitz_enable";
                        update_bloxx_metas(page_id, meta_type, $this);
                    });

                    $("body").on("click", "li#wp-admin-bar-exit-bloxx-builder a", function (event) {
                        event.preventDefault();
                        var $this = $(this);
                        var page_id = $this.attr('title');
                        var meta_type = "kitz_disable";
                        update_bloxx_metas(page_id, meta_type, $this);
                    });


                    function update_bloxx_metas(page_id, meta_type, $this) {
                        var ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            dataType: "json",
                            data: {
                                'action': 'kitz_update_metabox',
                                'post_id': page_id,
                                "_nonce": bloxx.ajax_nonce,
                                'meta_type': meta_type
                            },
                            beforeSend: function () {
                               // $this.html('<i class="fa fa-spinner fa-spin"></i>');
                            },
                            success: function (resp) {
                                if (resp.code == 200) {
                                    window.location.href = $this.attr('href');
                                } else {
                                    alert(resp.message);
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Please try again later",
                                    confirmButtonColor: '#000',
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            </script>
            <?php
        }
    }

    function kitz_plugin_css_jsscripts() {
        global $wp_query;
        @$page_id = $wp_query->post->ID;
        wp_dequeue_script('utils');
        wp_dequeue_script('moxiejs');

        //Sweet Alret
        wp_enqueue_style('kitz_sweetalertcss', kitz_url . "css/sweetalert2.min.css");
        wp_enqueue_script('kitz_sweetalert', kitz_url . "js/sweetalert.min.js", array('jquery'), '', true);

        //Font Awesome
        wp_enqueue_style('kitz_fontawesome', kitz_url . "css/font-awesome.min.css");

        //Font Awesome
        wp_enqueue_style('kitz_allawesome', kitz_url . "css/all.css");

        //Style css
        wp_enqueue_style('kitz_stylecss', kitz_url . "css/style.css?v=" . time());

        // Pass ajax_url to script.js
        if (!isset($_GET['et_fb'])) {
            wp_enqueue_script(
                'kitz_script',
                kitz_url . 'js/script.js?v=' . time(),
                array( 'jquery', 'jquery-ui-sortable' ),
                time(),
                false
            );

            //wp_enqueue_script('kitz_script', kitz_url . 'js/script.js?v=' . time(), array(), '', true);
             
            $ajax_data=$this->basicApiext();

            // pre($ajax_data);
            wp_localize_script('kitz_script', 'bloxx', array('ajax_url' => admin_url('admin-ajax.php'), 'ajax_nonce'=> wp_create_nonce("divikitz") ) );
            wp_localize_script('kitz_script', 'bloxxapi', $ajax_data);
        }
    }


    function kitz_admin_css_jsscripts() {
        wp_enqueue_style('kitz_admin_fontawesome', kitz_url . "css/font-awesome.min.css");
        wp_enqueue_style('kitz_admin_style', kitz_url . "admin/assets/css/bloxx_admin.css");

        //Sweet Alret
        wp_enqueue_style('kitz_admin_sweetalertcss', kitz_url . "css/sweetalert2.min.css");
        wp_enqueue_script('kitz_admin_sweeralert', kitz_url . "js/sweetalert.min.js");

        //script.js
        wp_enqueue_script('kitz_admin_script', kitz_url . 'admin/assets/js/bloxxscript.js?v='.time());

        // Pass ajax_url to script.js
        if (!isset($_GET['et_fb'])) {
            $ajax_data=$this->basicApiext();
            wp_localize_script('kitz_admin_script', 'bloxx', array('ajax_url' => admin_url('admin-ajax.php')));
            wp_localize_script('kitz_admin_script', 'bloxxbuilder_admin', array('ajax_url' => admin_url('admin-ajax.php')));
            wp_localize_script('kitz_admin_script', 'bloxxapi', $ajax_data);
        }
    }

    
    
    function basicApiext(){
        include_once(ABSPATH.'wp-admin/includes/plugin.php');
        $builderapi_url = get_option('kitz_api_url', true);
        $builderapi_url_layouts = get_option('kitz_api_url_layouts', true);

        $theme = wp_get_theme();
        $er = 0;
        $divi_type="activate";
        if ('Divi' == $theme->name) {               
            $er = 1;
            $divi_type="theme_activated";
        } else if ('Divi Child Theme' == $theme->name) {
            $divi_type="theme_activated";
            $er = 1;        
        } else if (is_plugin_active( 'divi-builder/divi-builder.php' ) ) {
            $divi_type="plugin_activated";
            $er = 1;        
        }

        if(get_option('kitz_dropbox', true)!="disable" && get_option('kitz_dropbox', true)!=1){
            $dropbox="dropbox_enable";
        } else {
            $dropbox="dropbox_disable";
        }
        

        $ajax_data=array();
        
        if($er==0){
            $ajax_data= array(
                'kitz_builder'=> "no",
                'builder_key' => "activate",
                'api_token' => "activate",
                'ajax_url' => "activate",
                'ajax_url_layouts'=> "activate",
                'key_url'=> $builderapi_url,
                'key_url_layouts' => $builderapi_url_layouts,
                'imageurl'=> kitz_url,
                'enable'=> $divi_type,
                'dropbox'=> $dropbox,
                'ajax_nonce'=> wp_create_nonce("divikitz"),
                'siteurl'=>site_url()
            );
        } else {
            $builder_connect="no";
            if(get_option('kitz_connect')!=""){
                $builder_connect = get_option('kitz_connect', true);
            }

            
            $builder_key= get_option('kitz_key', true);
            $api_token= get_option('kitz_api_token', true);

            $kitz_unauth_section_api=get_option('kitz_unauth_section_api', true);
            $kitz_unauth_layout_api=get_option('kitz_unauth_layout_api', true);

            $ajax_data= array(
                'kitz_builder'=> $builder_connect,
                'ajax_url_layouts' => $builderapi_url_layouts,
                'ajax_url' => $builderapi_url,
                'kitz_unauth_section'=> $kitz_unauth_section_api,
                'kitz_unauth_layout'=> $kitz_unauth_layout_api,
                'builder_key' => $builder_key,
                'api_token' => $api_token,
                'key_url'=> $builderapi_url,
                'key_url_layouts' => $builderapi_url_layouts,
                'imageurl'=> kitz_url,
                'dropbox'=> $dropbox,
                'ajax_nonce'=> wp_create_nonce("divikitz"),
                'enable'=> $divi_type,
                'siteurl'=>site_url()
            );
            
        }
        return $ajax_data;
    }


    function check_divi_theme() {
        $theme = wp_get_theme();
        $er = 0;
        if ('Divi' == $theme->name) {
            $er = 0;
        } else if ('Divi Child Theme' == $theme->name) {
            $er = 0;            
        } else if ( is_plugin_active( 'divi-builder/divi-builder.php' ) ) {
            $er = 0;            
        } else {
            $er = 1;
        }

        if ($er == 1) {
            $error_message = "Please activate Divi theme or Divi Builder plugin to continue...  Kitz Pro Plugin";
            $this->error_message($error_message);
        }
    }

    function error_message($error_message) {
        ?>
        <div class="updated error divi_builder">
            <p>
                <?php 
                //echo esc_html__($error_message, 'kitz-builder'); 
                printf(
                    esc_html__( 'Error! %s.', 'kitz-builder' ),
                    esc_html($error_message)
                );
                ?>
            </p>
        </div>
        <?php
    }
    
    
    
    public function kitz_dropbox() {
        check_ajax_referer( 'divikitz', '_nonce' );

        $dropbox_key= sanitize_text_field($_REQUEST['dropbox_key']);
        $dropbox_secret= sanitize_text_field($_REQUEST['dropbox_secret']);
        $kitz_dropbox_url= sanitize_text_field($_REQUEST['kitz_dropbox_url']);
        $action= sanitize_text_field($_REQUEST['action']);

        $kitzdb_ar= array(
            "dropbox_key"=> $dropbox_key,
            "dropbox_secret"=> $dropbox_secret,
            "kitz_dropbox_url"=> $kitz_dropbox_url,
            "action"=> $action
        );

        update_option('kitz_dropbox_detail', $kitzdb_ar);
        $result=array(
            "code"=> 200,
            "message"=> "Dropbox setting saved successfully"
        );
        echo wp_json_encode($result);
        wp_die();
    }

    
    //Check API Connection
    public function kitz_siteblox_key_saved() {
        //extract($_REQUEST);
        check_ajax_referer( 'divikitz', '_nonce' );
        
        $website_url= sanitize_text_field($_POST['website_url']);
        $siteblox_username= sanitize_text_field($_POST['siteblox_username']);
        $siteblox_key= sanitize_text_field($_POST['siteblox_key']);
        $siteblox_status= sanitize_text_field($_POST['siteblox_status']);
        
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        update_option('kitz_key', sanitize_text_field($siteblox_key));
        $website_nm=get_option('blogname', true);
        $connect_data = array(
            'website_url' => $website_url,
            'server_userid' => $current_user_id,
            'siteblox_username' => trim($siteblox_username),
            'siteblox_key' => $siteblox_key,
            'website_nm' => $website_nm
        );


        if ($siteblox_status == "kitz_connect") {
            $connect_url=kitz_apiurl."wp-json/siteblox-api/connect";

            $connect_args= array(
                'body'        => $connect_data,
                'timeout'     => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array("cache-control: no-cache", 'Content-Type: application/json'),
                'cookies'     => array(),
            );

            $dropbox_query = wp_remote_post( $connect_url, $connect_args );
            $response     = wp_remote_retrieve_body( $dropbox_query );

            
            $siteblox_resp = json_decode($response, true);



            if ($siteblox_resp['code'] == 200) {
                update_option('kitz_api_url', sanitize_text_field($siteblox_resp['section_api']));
                update_option('kitz_api_url_layouts', sanitize_text_field($siteblox_resp['layout_api']));
                update_option('kitz_api_token', sanitize_text_field($siteblox_resp['api_token']));
                update_option('kitz_username', sanitize_text_field($siteblox_username));
                update_option('kitz_key', sanitize_text_field($siteblox_key));
                update_option('kitz_user_id', sanitize_text_field($siteblox_resp['user_id']));
                update_option('kitz_term_id', sanitize_text_field($siteblox_resp['term_id']));
                update_option('kitz_connect', sanitize_text_field('yes'));
                update_option('kitz_use_free_features', sanitize_text_field($siteblox_resp['bloxx_use_free_features']));
            }


            $kitz_result= array(
                "code" => sanitize_text_field($siteblox_resp['code']),
                "section_api" => sanitize_text_field($siteblox_resp['section_api']),
                "layout_api" => sanitize_text_field($siteblox_resp['layout_api']),
                "api_token" => sanitize_text_field($siteblox_resp['api_token']),
                "user_id" => sanitize_text_field($siteblox_resp['user_id']),
                "term_id" => sanitize_text_field($siteblox_resp['term_id']),
                "bloxxbuilder_use_free_features" => sanitize_text_field($siteblox_resp['bloxxbuilder_use_free_features']),
                "dropbox" => sanitize_text_field($siteblox_resp['dropbox']),
                "drop_api" => sanitize_text_field($siteblox_resp['drop_api']),
                "drop_secret" => sanitize_text_field($siteblox_resp['drop_secret']),
                "message" => sanitize_text_field($siteblox_resp['message'])
            );

            echo wp_json_encode($kitz_result);
            die();
            
        } else {
            $disconnect_url=kitz_apiurl."wp-json/siteblox-api/disconnect";

            $disconnect_args= array(
                'body'        => $connect_data,
                'timeout'     => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array("cache-control: no-cache", 'Content-Type: application/json'),
                'cookies'     => array(),
            );

            $dis_query = wp_remote_post( $disconnect_url, $disconnect_args );
            $response     = wp_remote_retrieve_body( $dis_query );
            $siteblox_resp = json_decode($response, true);

            // echo "<pre>";
            // print_r($siteblox_resp);
            // die();

            if ($siteblox_resp['code'] == 200) {
                update_option('kitz_use_free_features', sanitize_text_field($siteblox_resp['bloxx_use_free_features']));
                update_option('kitz_connect', sanitize_text_field('no'));
                update_option('kitz_api_url', sanitize_text_field("disconnect"));
                update_option('kitz_api_url_layouts', sanitize_text_field("disconnect"));
                update_option('kitz_api_token', sanitize_text_field("disconnect"));
                update_option('kitz_username', sanitize_text_field(''));
                update_option('kitz_key', sanitize_text_field(''));
            }
            

            $kitz_result= array(
                "code" => sanitize_text_field($siteblox_resp['code']),
                "bloxxbuilder_use_free_features" => sanitize_text_field($siteblox_resp['bloxxbuilder_use_free_features']),
                "message" => sanitize_text_field($siteblox_resp['message'])
            );
            echo wp_json_encode($kitz_result);
            die();
        }
        die();
    }


    function dropbox_return_params() {
        register_rest_route(
            'kitz', '/dropbox_params/', array(
                'methods' => 'GET',
                'callback' => array($this, 'kitz_dbparams'),
            )
        );
    }

    function kitz_dbparams($request) {
        global $wpdb;
        $drop_key= get_option('kitz_drop_api', true);
        $drop_secret= get_option('kitz_drop_secret', true);
        $code= sanitize_text_field($request['code']);
        $return_url= get_option('kitz_drop_returnuri', true);
        update_option('kitz_db_authcode', sanitize_text_field($code));

        //Dropbox Auth
        $args = array(
            'body'        => array( 
                "code"    => $code,  
                "grant_type"    => "authorization_code", 
                "redirect_uri" => $return_url, 
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



        $dropbox_auth_query = wp_remote_post( 'https://api.dropbox.com/oauth2/token', $args );
        $result     = wp_remote_retrieve_body( $dropbox_auth_query );
        $decode_drop_box= json_decode($result, true);

        $kitz_dp_result= array(
            "access_token" => esc_html($decode_drop_box['access_token']),
            "token_type" => esc_html($decode_drop_box['token_type']),
            "expires_in" => esc_html($decode_drop_box['expires_in']),
            "refresh_token" => esc_html($decode_drop_box['refresh_token']),
            "scope" => esc_html($decode_drop_box['scope']),
            "uid" => esc_html($decode_drop_box['uid']),
            "account_id" => esc_html($decode_drop_box['account_id'])
        );


        update_option('kitz_dropbox', sanitize_text_field("enable") );

        update_option('kitz_dropbox_token_detail', $kitz_dp_result );


        $dropbox_token_detail= get_option('kitz_dropbox_token_detail');
        $access_token= $dropbox_token_detail['access_token'];
        $refresh_token= $dropbox_token_detail['refresh_token'];

        update_option('kitz_db_refresh_token', sanitize_text_field($refresh_token));

        


        //Create Section Folder
        $headers_section = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$access_token
        ];

        $body_section = '{
            "autorename": false,
            "path": "/kitzbuilder/Sections"
        }';

        $args_section_folder = array(
            'body'        => $body_section,
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $headers_section,
            'cookies'     => array(),
        );

        $dropbox_section = wp_remote_post('https://api.dropboxapi.com/2/files/create_folder_v2', $args_section_folder);

        $response_section= wp_remote_retrieve_body( $dropbox_section );




        //Create Layout Folder
        $headers_layout = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$access_token
        ];

        $body_layout = '{
            "autorename": false,
            "path": "/kitzbuilder/Layouts"
        }';


        $args_section_folder = array(
            'body'        => $body_layout,
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $headers_layout,
            'cookies'     => array(),
        );

        $dropbox_layout = wp_remote_post('https://api.dropboxapi.com/2/files/create_folder_v2', $args_section_folder);

        $response_layout= wp_remote_retrieve_body( $dropbox_layout );


        $kitz_dropbox_url = esc_url( add_query_arg(
            'page',
            'kitz_settings',
            get_admin_url() . 'admin.php'
        ) );

        wp_safe_redirect($kitz_dropbox_url);
        exit();
        
    }

}

$kitz_core = new Kitz_core();