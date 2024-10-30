<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kitz_dropbox {

    public function __construct() {
        //When user clicks on Section then display all folder from dropbox
        add_action("wp_ajax_kitz_load_ajax_cats", array($this, "kitz_load_ajax_cats"));
        add_action("wp_ajax_nopriv_kitz_load_ajax_cats", array($this, "kitz_load_ajax_cats"));


        //When user dropbox folder and display all sections under selected folder
        add_action("wp_ajax_kitz_load_sections", array($this, "kitz_load_sections"));
        add_action("wp_ajax_nopriv_kitz_load_sections", array($this, "kitz_load_sections"));


        //When user clicks on Layout
        // add_action("wp_ajax_kitz_kitz_load_ajax_industries", array($this, "kitz_load_ajax_industries"));
        // add_action("wp_ajax_nopriv_kitz_load_ajax_industries", array($this, "kitz_load_ajax_industries"));
    }

    //Sections
    public function kitz_load_ajax_cats(){
        check_ajax_referer( 'divikitz', '_nonce' );
        $type= sanitize_text_field($_POST['type']);

        
        
        if(get_option('drop_api', true)!="" && get_option('drop_api', true)!=1){
            kitz_db_refresh_token_function();
            
            $dropbox= "enable";
            $dropbox_token_detail= get_option('kitz_dropbox_token_detail');

            $access_token= $dropbox_token_detail['access_token'];

            // echo $access_token;
            // die();

            $header_list= array(
                'Content-Type'=> 'application/json',
                'Authorization'=> 'Bearer '.$access_token
            );

            $curl_folder= array(
                "include_deleted"=> false,
                "path" => "/kitzbuilder/Sections/",
                "recursive"=> false
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
            $response     = wp_remote_retrieve_body( $dropbox_querylist );

            $get_directory= json_decode($response, true);

            if($get_directory['entries']){
                if(!empty($get_directory['entries'])){
                    foreach ($get_directory['entries'] as $folders) {
                        $folder_name= $folders['name'];
                        ?>
                        <li class="project_section" id="<?php echo esc_html($folder_name); ?>">
                            <a href="javascript:void(0)" class="builder_cats" id="<?php echo esc_html($folder_name); ?>"><?php echo esc_html($folder_name); ?></a>
                        </li>
                        <?php
                    }
                }
            } else {
                echo esc_html("Your Tokan has been expired");
            }
        } else {
            echo esc_html("Dropbox not connected");
        }
        die();
    }


    public function kitz_load_sections() {
        check_ajax_referer( 'divikitz', '_nonce' );
        $dropbox_folder= sanitize_text_field($_POST['dropbox_folder']);
        
        $post_field=array(
            "include_deleted"=> false,
            "path" => "/kitzbuilder/Sections/$dropbox_folder",
            "recursive"=> false
        );

        kitz_db_refresh_token_function();

        $dropbox_token_detail= get_option('kitz_dropbox_token_detail');
        $access_token= $dropbox_token_detail['access_token'];
        

        
        $header_list= array(
            'Content-Type'=> 'application/json',
            'Authorization'=> 'Bearer '.$access_token
        );

        $dropbox_sections= json_encode($post_field);

        $dropbox_sections_args = array(
            'body'        => $dropbox_sections,
            'timeout'     => '10',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $header_list,
            'cookies'     => array(),
        );

        $dropbox_query_sections = wp_remote_post( 'https://api.dropboxapi.com/2/files/list_folder', $dropbox_sections_args );
        $response     = wp_remote_retrieve_body( $dropbox_query_sections );

        $kitz_files= json_decode($response, true);
        

        $section_html="<section data-count_collection-section_ajax_load='$dropbox_folder' data-section_limit-section_ajax_load='5' class='builder_posts active_slide' id='cat_post_$dropbox_folder' style='display:none;'>";
        
        if(!empty($kitz_files['entries'])) {
            $j=0;
            foreach ($kitz_files['entries'] as $kdb_files) {
                $filename= $kdb_files['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $in_filepath="/kitzbuilder/Sections/$dropbox_folder/$filename";

                
                if($ext=="json") {
                    $path_lower= $folders['path_display'];

                    $headers = array();
                    $headers['Authorization'] = 'Bearer '.$access_token;
                    $headers['Content-Type'] = '';
                    $headers['Dropbox-API-Arg'] = '{"path":"' . $in_filepath . '"}';

                    $dropbox_download_args = array(
                        'body'        => array(),
                        'timeout'     => '10',
                        'redirection' => '5',
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => $headers,
                        'cookies'     => array(),
                    );

                    $dropbox_download_query = wp_remote_post( 'https://content.dropboxapi.com/2/files/download', $dropbox_download_args );
                    $json_data     = wp_remote_retrieve_body( $dropbox_download_query );

                    
                    $file_path= kitz_path."temp/".$filename;
                    
                    $fp = fopen($file_path ,"wb");
                    fwrite($fp,$json_data);
                    fclose($fp);
                    

                    $file_url= kitz_url."temp/".$filename;
                    $request = wp_remote_get( $file_url);
                    $remote_json = wp_remote_retrieve_body( $request, true );
                    $remote_data = json_decode( $remote_json, true, JSON_UNESCAPED_SLASHES);
                    $remote_data_renew = json_decode( $remote_json, true, JSON_UNESCAPED_SLASHES);
                    $remote_json_data= $remote_data['data'];

                    if($remote_json_data==""){
                        $remote_json_data= $json_data;
                    } else {
                        foreach($remote_json_data as $remote_data):
                            $remote_json_data= $remote_data;
                        endforeach;
                    }


                    $json_image=explode(".", $in_filepath);
                    $json_image_path=$json_image[0].".png";
                    
                    $path= array(
                        "path"=> $json_image_path
                    );

                    
                    $response= $this->dropboximg($path, $access_token);
                    $thumb_data= json_decode($response, true);
                    
                    

                    if(@$thumb_data['error']['.tag']=="path"){
                        $json_image_path=$json_image[0].".jpg";
                        $path= array(
                            "path"=> $json_image_path
                        );
                        $response= $this->dropboximg($path, $access_token);
                        $thumb_data= json_decode($response, true);
                        
                    }
                    
                    $thumb_image= $thumb_data['link'];


                    $section_html .='<div data-sectiontype="1" data-usertype="" data-useremail="" id="builder_inner_dragpost_104229" class="builder_inner_dragpost_sel builder_inner_dragpost connectedSortable">';
                    $section_html .='<div class="section_type is_free"><!-- <h3>Free</h3> --></div>';
                    $section_html .='<div class="card" data-sectiontype="1" data-usertype="">';
                    $section_html .='<div class="builder-dragpost builder-dragpost-sel builder-dragpost-sidebar" id="104229" data-id=""  data-sectiontype="1" data-usertype="">';
                    // $section_html .='<div class="action_btns" style="display:none;">';
                    // $section_html .='<a href="javascript:void(0)" class="builder_uparrow" id="104229">&#8593;</a>';
                    // $section_html .='<a href="javascript:void(0)" class="builder_downarrow" id="104229">&#8595;</a>';
                    // $section_html .='<a href="javascript:void(0)" class="builder_remove_layout" id="104229"><i class="far fa-trash-alt" aria-hidden="true"></i></a>';
                    // $section_html .='</div>';

                    $section_html .="<img class ='show_clone_img' src='$thumb_image' style='display:block;margin: auto;'>";
                    $section_html .="<input type='hidden' class='builder_layout' value='$remote_json_data'/>";
                    $section_html .="<div class='show_clone_html' style='display: none;'></div>";
                    $section_html .="</div>";
                    $section_html .="</div>";
                    $section_html .="</div>";

                }
            }
            
            $section_html .="</section>";
            echo str_replace("&#039;", "'", html_entity_decode(esc_html($section_html)));
        } else {
            echo esc_html("Dropbox_not_found");
        }
        die();
    }



    public function dropboximg($path, $access_token){
        $header_list= array(
            'Content-Type'=> 'application/json',
            'Authorization'=> 'Bearer '.$access_token
        );

        $body_path= json_encode($path);

        $list_args = array(
            'body'        => $body_path,
            'timeout'     => '10',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $header_list,
            'cookies'     => array(),
        );

        $dropbox_querylist = wp_remote_post( 'https://api.dropboxapi.com/2/files/get_temporary_link', $list_args );
        $response     = wp_remote_retrieve_body( $dropbox_querylist );
        return $response;
    }


    //Layouts
    // public function kitz_load_ajax_industries(){
    //     extract($_REQUEST);
    //     // echo "<pre>";
    //     // print_r($_REQUEST);
    //     echo "Dropbox_not_found";
    //     die();
    // }

    

}

$kitz_dropbox = new Kitz_dropbox();