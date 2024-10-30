<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$builder_connect="no";
if(get_option('kitz_connect')!=""){
    $builder_connect = get_option('kitz_connect');
}

$siteblox_username = get_option('kitz_username');
$builder_key = get_option('kitz_key');

$dropbox_key= $dropbox_secret ="";


$dropbox_token_detail= get_option('kitz_dropbox_token_detail');

// echo "<pre>";
// print_r($dropbox_token_detail);
// echo "</pre>";

$kitz_dropbox_url = esc_url( add_query_arg(
    'page',
    'kitz_dropbox',
    get_admin_url() . 'admin.php'
) );



if(get_option('kitz_dropbox_detail')!=""){
	$dropbox_details= get_option('kitz_dropbox_detail');
	$dropbox_key= $dropbox_details['dropbox_key'];
	$dropbox_secret= $dropbox_details['dropbox_secret'];
}

if(isset($_REQUEST['dropBox_connect'])){

	$encodesiteurl= base64_encode(site_url());


	if(get_option('kitz_dropbox_settings', true)=="enable"){
		$dropbox_key= get_option('kitz_drop_api', true);
		$dropbox_redirect= get_option('kitz_drop_returnuri', true);
		$dropbox_allow_url= "https://www.dropbox.com/oauth2/authorize?client_id=$dropbox_key&token_access_type=offline&redirect_uri=$dropbox_redirect&response_type=code";
		//$dropbox_allow_url= esc_url(sanitize_url("https://www.dropbox.com/oauth2/authorize?client_id=$dropbox_key&token_access_type=offline&response_type=code"));

		?>
		<script>
			window.location.href="<?php echo html_entity_decode( esc_url($dropbox_allow_url)); ?>";
		</script>
		<?php
		exit;
	} else {
		$resp_message="Please deactivate and activate plugin again for continue";
	}
}



if(isset($_REQUEST['dropBox_disconnect'])){
	update_option('kitz_dropbox', sanitize_text_field("disable"));
	update_option('kitz_db_refresh_token', sanitize_text_field("disable"));
}


if(!get_option('kitz_dropbox', true)){
	$dropbox= "disable";
} else {
	$dropbox= get_option('kitz_dropbox', true);
}

$kitz_logo= kitz_url."images/Divi-Webkitz-Logo.png";
$drop_logo= kitz_url."images/dropbox.jpg";
?>


<div id="maincontent" class="main-content">
	<div class="page-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="main-title">
						<h1>Hello from Team Divikitz! </h1>
						<p>If you have a premium plan then you can connect your account below here, if you do not have you can get one <a target="_blank" href="<?php echo esc_url(kitz_apiurl."plans/"); ?>">here</a>. Other wise you can enjoy our free services.</p>
						<p><strong>To manually upload a JSON file and an image into Dropbox, follow these step-by-step instructions:</strong> </p>
						<ul>
							<li style="list-style: inside;">Rename both files to have similar names, for example, "hero12.json" and "hero12.jpg".</li>
							<li style="list-style: inside;">Open your Dropbox account and navigate to the desired location. Create a folder structure following the format: "Section/Banner" (replace "Banner" with the desired names). This structure will help organize your files effectively.</li>
							<li style="list-style: inside;">Upload the renamed "hero12.json" and "hero12.jpg" files to the designated folder in Dropbox. You can do this by either dragging and dropping the files into Dropbox or using the "Upload" button.</li>
							<li style="list-style: inside;">Once the files are successfully uploaded, you can activate the Kitz Builder under the corresponding section in your frontend. The JSON file and image will automatically be displayed in the frontend when the Kitz Builder is activated.</li>
						</ul>
					</div>
				</div>
			</div>


			<div class="row">
			  	<div class="box-drop-section">
					<div class="col-sm-6 col-md-6 col-lg-4 col-4 first-sec">
						<div class="bloxx_box">
							<div class="blx_logo">
								<img src="<?php echo esc_url($drop_logo); ?>" style="max-width: 175px;" />
								<h3>Access Drop Box</h3>
								<?php if(get_option('kitz_drop_api', true)!="" && get_option('kitz_drop_api', true)!=1){ ?>
									<p>You can connect/Disconnect DropBox with one click on "Activate Dropbox" Button.</p>
									<form class="kitz_form" method="POST" id="dropbox_auth_process">
										<?php if($dropbox=="enable"){ ?>
										    <div class="submit-buttons">
										    	<button type="submit" name="dropBox_disconnect" class="btn button kitz_btn">Account Linked</button>
								            	<a href="<?php echo esc_url($kitz_dropbox_url); ?>" class="btn button button-success">Upload Json Files</a>
										    </div>
										<?php } else { ?>
										    <div class="submit-buttons">
									            <button type="submit" name="dropBox_connect" class="btn button button-danger">Activate Dropbox</button>
									            <p style="color:red;"><?php echo esc_html($resp_message); ?></p>
										    </div>
										<?php } ?>
									</form>
								<?php } else { ?>
									<p style="color:red;">Please deactivate and activate plugin again for continue</p>
								<?php } ?>
							</div>
						</div>
					</div>


					<div class="col-sm-6 col-md-6 col-lg-4 col-4 second-sec">
						<div class="bloxx_box">
							<div class="blx_logo">
								<img src="<?php echo esc_url($kitz_logo); ?>" style="max-width: 175px;" />
								<button type="submit" name="premium" class="button kitz_btn premium">Premium</button>
								<h3>Access Premium Features</h3>
								<p>Each of our plugins makes the Divi experience better and faster for developers.</p>

								<form class="kitz_form" method="POST" id="siteblox_connectivity">
								    <section class="object-meta-data taxonomy-meta-data">
								        <table class="widefat fixed siteblox-table" cellspacing="0">
								            <tbody>

								               <?php 
								               $readonly="";
								               if ($builder_connect == "yes" &&  isset($siteblox_username) && $siteblox_username!='') {
								               		$readonly="readonly='readonly'";
								               }
								               ?>
								                <tr class="alternate">
								                    <td>
								                        <input name="website_url" type="hidden" value="<?php echo esc_url(site_url()); ?>">
								                        <input name="siteblox_username" type="text"  id="siteblox_username" value="<?php
								                        if (isset($siteblox_username)) {
								                            echo esc_html($siteblox_username);
								                        }
								                        ?>" placeholder="Enter Account Email" <?php echo esc_attr( $readonly); ?>>
								                        <input name="siteblox_key" id="siteblox_key" type="text" value="<?php
								                        if (isset($builder_key)) {
								                            echo esc_html($builder_key);
								                        }
								                        ?>" placeholder="Enter API Key" <?php echo esc_attr($readonly); ?> required>
								                        <input type="hidden" name="action" value="<?php echo esc_html('kitz_siteblox_key_saved'); ?>">

								                        <input type="hidden" name="_nonce" value="<?php echo esc_html(sanitize_text_field(wp_create_nonce("divikitz"))); ?>">
								                    </td>
								                </tr>
								            </tbody>
								        </table>
								    </section>

								    <div class="submit-buttons">

								        <?php if ($builder_connect == "yes" &&  isset($siteblox_username) && $siteblox_username!='') { ?>
								            <input type="hidden"  id="siteblox_status" name="siteblox_status" value="<?php echo esc_html('kitz_disconnect'); ?>">
								            <button type="submit" id="save_connectivity" class="button kitz_btn">Disconnect</button>
								            
								        <?php } else { ?>
								            <input type="hidden" id="siteblox_status" name="siteblox_status" value="<?php echo esc_html('kitz_connect'); ?>">
								            <button type="submit" id="save_connectivity" class="button kitz_btn button-pro">Connect</button>
										<?php } ?>
								    </div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="height40px"></div>
		</div>
	</div>
</div>