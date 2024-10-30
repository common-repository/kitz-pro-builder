<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!get_option('kitz_dropbox', true)){
	$dropbox= "disable";
} else {
	kitz_db_refresh_token_function();
	$dropbox= "enable";
    $dropbox_token_detail= get_option('kitz_dropbox_token_detail');

    $access_token= $dropbox_token_detail['access_token'];

    $list_header= array(
	    'Content-Type'=> 'application/json',
	    'Authorization'=> 'Bearer '.$access_token
	);
    
	$list_args= array(
		"include_deleted"  					 	=> false,
	    "include_has_explicit_shared_members"	=> false,
	    "include_media_info"					=> false,
	    "include_mounted_folders"				=> true,
	    "include_non_downloadable_files"		=> true,
	    "path"									=> "/kitzbuilder",
	    "recursive"								=> false
	);

	$encode_list_args= json_encode($list_args);

    $args_listfolder = array(
        'body'        => $encode_list_args,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $list_header,
        'cookies'     => array(),
    );

    $dropbox_query = wp_remote_post( 'https://api.dropboxapi.com/2/files/list_folder', $args_listfolder );
    $response     = wp_remote_retrieve_body( $dropbox_query );

	$get_directory= json_decode($response, true);

	

	if($get_directory['entries']){
		$entry_folder= "/kitzbuilder/".$get_directory['entries'][0]['name']."/";

		$listf_header= array(
		    'Content-Type'=> 'application/json',
		    'Authorization'=> 'Bearer '.$access_token
		);

		$curl_folder= array(
		    "include_deleted"=> false,
		    "include_has_explicit_shared_members"=> false,
		    "include_media_info"=> false,
		    "include_mounted_folders"=> true,
		    "include_non_downloadable_files"=> true,
		    "path"=> $entry_folder,
		    "recursive"=> false
		);

		$encode_listf_args= json_encode($curl_folder);

		$args_listfolder = array(
	        'body'        => $encode_listf_args,
	        'timeout'     => '5',
	        'redirection' => '5',
	        'httpversion' => '1.0',
	        'blocking'    => true,
	        'headers'     => $listf_header,
	        'cookies'     => array(),
	    );

	    $dropbox_folder_query = wp_remote_post( 'https://api.dropboxapi.com/2/files/list_folder', $args_listfolder );
	    $resp     = wp_remote_retrieve_body( $dropbox_folder_query );
	    $get_direct= json_decode($resp, true);
	} 
}


$dropbox_logo= kitz_url."/images/dropbox.jpg?v=".time();
?>

<div id="maincontent" class="main-content">
	<div class="page-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="main-title">
						<h1>Hello from Team Divikitz! </h1>
						<p>Welcome to Dropbox functionality</p>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-6 col-md-6 col-lg-5 col-4">
					<div class="bloxx_box blox_box_form">
						<div class="blx_logo">
							<img src="<?php echo esc_url($dropbox_logo); ?>" style="max-width: 175px;" />
							<h3>Access Drop Box</h3>
							<p>Here you can upload your .json file into Dropbox</p>

							<form class="kitz_form" method="POST" id="dropbox_import" enctype="multipart/form-data">
							    <section class="object-meta-data taxonomy-meta-data">
							        <table class="widefat fixed siteblox-table" cellspacing="0">
							            <tbody>
							            	<tr class="alternate">
												<td>
													<input type="text" name="section_title" id="section_title" placeholder="Section Title">
												</td>
											</tr>

											<tr class="alternate">
												<td>
													<select name="kitz" class="select-box" id="kitz_directory">
														<?php if(!empty($get_directory['entries'])){ ?>
															<?php foreach ($get_directory['entries'] as $folders) { ?>
																<option value="<?php echo esc_html($folders['name']); ?>"><?php echo esc_html($folders['name']) ?></option>	
															<?php } ?>
														<?php } ?>
													</select>	
												</td>
											</tr>

											<tr class="alternate">
												<td>

													<select name="kitz_subdirectory" id="kitz_subdirectory" class="select-box">
														<?php if(!empty($get_direct['entries'])){ ?>
															<?php foreach ($get_direct['entries'] as $sub_folders) { ?>
																<option value="<?php echo esc_html($sub_folders['name']); ?>"><?php echo esc_html($sub_folders['name']); ?></option>	
															<?php } ?>
														<?php } ?>
													</select>	
													
													<a class="kitz_button kitz_dropbox_directory" href="javascript:void(0)">Create Directory</a>
												</td>
											</tr>

							                <tr class="alternate">
												<td>
													<div class="wrapper">
														<div class="drop">
															<div class="cont" style="color: rgb(142, 153, 165);">
																	<i class="fa fa-cloud-upload"></i>
																	<div class="tit">
																		Drag &amp; Drop
																	</div>

																	<div class="desc">
																		your files to Assets, or 
																	</div>

																	<div class="browse">
																		click here to browse
																	</div>
															</div>
															<output id="list"></output>
															<input id="json_files" multiple="true" name="json_url" type="file">
														</div>
													</div>
													
													<input type="hidden" name="action" value="<?php echo esc_html('kitzdropbox_upload'); ?>">

													<input type="hidden" name="_nonce" value="<?php echo esc_html(sanitize_text_field(wp_create_nonce("divikitz"))); ?>">

													<button type="submit" id="kitz_import_json" style="visibility: hidden; position:absolute;">Upload Json</button>
												</td>
											</tr>

											
							            </tbody>
							        </table>
							    </section>

							    <div class="submit-buttons">
						            <button type="submit" class="kitz_btn button">Submit</button>
							    </div>
							</form>

							
						</div>
					</div>
				</div>
			</div>
			<div class="height40px"></div>
		</div>
	</div>
</div>