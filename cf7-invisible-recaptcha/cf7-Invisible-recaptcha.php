<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link
 * @since             1.0.0
 * @package           cf7-Invisible-recaptcha
 *
 * @wordpress-plugin
 * Plugin Name:       CF7 Invisible reCAPTCHA
 * Plugin URI:        https://wordpress.org/plugins/cf7-invisible-recaptcha/
 * Description:       Effective solution that secure your Contact form 7.
 * Version:           1.3.5
 * Author:            Vsourz Digital
 * Author URI:        https://www.vsourz.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-invisible-recaptcha
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
define( 'CF7_CAPTCHA_VERSION', '1.3.5' );
///// Adding custom menu page
add_action( 'admin_menu' ,'vsz_cf7_invisible_recaptcha',10);

function vsz_cf7_invisible_recaptcha(){
	global $_wp_last_object_menu;

	$_wp_last_object_menu++;

	add_menu_page(
		__( 'Invisible reCAPTCHA', 'cf7-invisible-recaptcha' ),
		'Invisible reCAPTCHA',
		'manage_options',
		'cf7-Invisible-recaptcha',
		'vsz_cf7_invisible_recaptcha_page',
		'dashicons-admin-site',
		$_wp_last_object_menu
	);

}

////// Callback function for custom menu
function vsz_cf7_invisible_recaptcha_page(){
	
	// SECURITY: Verify admin capability
	if (!current_user_can('manage_options')) {
		wp_die(esc_html__('You do not have permission to access this page.', 'cf7-invisible-recaptcha'));
	}
	
	
	$tab = isset($_GET["tab"]) ? sanitize_text_field($_GET["tab"]) : "settings";
	$tab_id = isset($_POST['tab_id'])&& !empty($_POST['tab_id']) ? sanitize_text_field($_POST['tab_id']) : "";
	$successMsg = "";

	// General Settings submit
	if(isset($_POST["submit-settings"])){
		if( ! wp_verify_nonce($tab_id, 'submit-settings')){

            wp_die(esc_html__("You don't have permission to view this page", 'cf7-invisible-recaptcha'));
            exit;
        }
		$sitekey = sanitize_text_field($_POST['sitekey']);
		$secretkey = sanitize_text_field($_POST['secretkey']);
		$badge = sanitize_text_field($_POST['badge']);
		$badge_position = sanitize_text_field($_POST['badge_position']);
		$button_class = sanitize_text_field($_POST['button_class']);
		
		if(isset($sitekey)){
			update_option('invisible_recaptcha_sitekey',$sitekey);
		}
		if(isset($secretkey)){
			update_option('invisible_recaptcha_secretkey',$secretkey);
		}
		if(isset($badge) && !empty($badge)){
			update_option('invisible_recaptcha_badge',$badge);
		}
		if(isset($badge_position) && !empty($badge_position)){
			update_option('invisible_recaptcha_badge_position',$badge_position);
		}
		if(isset($button_class) && !empty($button_class)){
			update_option('invisible_recaptcha_button_class',$button_class);
		}

		$successMsg = 'General settings updated successfully!';
	}

	// Contact Form submit
	else if(isset($_POST["submit-cf7"])){
		if( ! wp_verify_nonce($tab_id, 'submit-cf7')){
            wp_die(esc_html__("You don't have permission to view this page", 'cf7-invisible-recaptcha'));
            exit;
        }
		if(isset($_POST['enable'])){
			$enable = sanitize_text_field($_POST['enable']);
		}
		$exclude = sanitize_text_field($_POST['exclude']);


		if(isset($exclude)){
			update_option('invisible_recaptcha_badge_exclude',$exclude);
		}
		if(isset($enable) && !empty($enable)){
			update_option('invisible_recaptcha_enable',$enable);
		}
		else{
			update_option('invisible_recaptcha_enable','0');
		}

		$successMsg = 'Contact form protection settings updated successfully!';
	}

	// This hook can be used to save custom fields
	$successMsg = apply_filters('cf7-Invisible-recaptcha-admin-submit-form',$successMsg);

	wp_enqueue_style("admin_css",plugin_dir_url(__FILE__)."css/admin.css", array(), CF7_CAPTCHA_VERSION, 'all');
	wp_enqueue_style("font_awesome_css",plugin_dir_url(__FILE__)."css/font-awesome.css", array(), CF7_CAPTCHA_VERSION, 'all');

	?><div class="wrap">
		<!--
		<div class=" notice inline notice-warning notice-alt">
			<p>
				It is possible that some or all functions may not work proper if you are using some other invisible recaptcha functionality providing plugin.
			</p>
		</div>
		<div class=" notice inline notice-warning notice-alt">
			<p>
				It is advisable to validate your key before saving.
			</p>
		</div>--><?php
		if(isset($successMsg) && !empty($successMsg)){
			?><div class="updated notice notice-success is-dismissible">
				<p><?php
					echo esc_html($successMsg);
				?></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">Dismiss this notice.</span>
				</button>
			</div><?php
		}

		// Check if invisible recaptcha is active
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'invisible-recaptcha/invisible-recaptcha.php' ) ) {
			?><div class="error">
				<p>
					It seems that <b>"Invisible reCaptcha"</b> plugin is active and conflicts with this plugin functionality. <br/>
					Kindly remove it, to get better performance. You can deactivate it from :<a href="<?php echo esc_url(admin_url('plugins.php')); ?>">here</a>.
				</p>
			</div><?php
		}
		if ( is_plugin_active( 'wp-cerber/wp-cerber.php' ) ) {
			?><div class="error">
				<p>
					It seems that <b>"WP Cerber Security & Antispam"</b> plugin is active and conflicts with this plugin functionality. <br/>
					Kindly remove it, to get better performance. You can deactivate it from :<a href="<?php echo esc_url(admin_url('plugins.php')); ?>">here</a>.
				</p>
			</div><?php
		}
		if ( is_plugin_active( 'google-captcha/google-captcha.php' ) ) {
			?><div class="error">
				<p>
					It seems that <b>"Google Captcha (reCAPTCHA) by BestWebSoft"</b> plugin is active and conflicts with this plugin functionality. <br/>
					Kindly remove it, to get better performance. You can deactivate it from :<a href="<?php echo esc_url(admin_url('plugins.php')); ?>">here</a>.
				</p>
			</div><?php
		}
		
		$generalSettingsTabLink = admin_url("admin.php?page=cf7-Invisible-recaptcha&tab=settings");
		$contactFormsTabLink = admin_url("admin.php?page=cf7-Invisible-recaptcha&tab=cf7");
		
		?><div id="" class="mch-module-tabs cf7-head">
			<a class="tablinks <?php if($tab == 'settings'){ echo 'active';}?>" href="<?php echo esc_url($generalSettingsTabLink); ?>"><?php echo esc_html__("General Settings", 'cf7-invisible-recaptcha'); ?></a>
			<a class="tablinks <?php if($tab == 'cf7'){ echo 'active';}?>" href="<?php echo esc_url($contactFormsTabLink); ?>"><?php echo esc_html__("Contact Forms", 'cf7-invisible-recaptcha'); ?></a><?php
			//This hook is used to add custom tabs
			do_action('cf7-Invisible-recaptcha-admin-page-tabs',$tab);
		?></div>

		<!-- General Settings -->
		<form class="cf7-settings-form woocommerce-settings tabbed" id="settings" method="post"><?php

			$site_key = get_option('invisible_recaptcha_sitekey');
			$secretkey= get_option('invisible_recaptcha_secretkey');
			$badge = get_option('invisible_recaptcha_badge');
			$badge_position = get_option('invisible_recaptcha_badge_position');
			$button_class= get_option('invisible_recaptcha_button_class');
			
			?><div class="mch-settings-section-header">
				<h3><?php echo esc_html__("General Settings", 'cf7-invisible-recaptcha'); ?></h3>
			</div>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="sitekey" ><?php echo esc_html__("Site Key", 'cf7-invisible-recaptcha'); ?></label></th>
						<td><input name="sitekey" id="sitekey" class="regular-text vsz_captcha_site_key" type="text" value="<?php if(isset($site_key) && !empty($site_key)){ echo esc_attr($site_key);}?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="secretkey" ><?php echo esc_html__("Secret Key", 'cf7-invisible-recaptcha'); ?></label></th>
						<td><input name="secretkey" id="secretkey" class="regular-text" type="text" value="<?php if(isset($secretkey) && !empty($secretkey)){ echo esc_attr($secretkey);}?>"></td>
					</tr>
					<tr>
						<th scope="row"><label></label></th>
						<td>
							<div class="vsz_recaptcha_setup">
								<div class="vsz_recaptcha_setup_msg"></div>
								<input type="button" class="button button-primary vsz_recaptcha_test" id="recaptcha-holder-1" value="Validate Credentials"/>
								<p class="spinner" style="float:none;"></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="badge" ><?php echo esc_html__("Display Badge", 'cf7-invisible-recaptcha'); ?></th>
						<td>
							<select name="badge" id="badge">
								<option value="yes" <?php if(isset($badge) && $badge== 'yes'){ ?>selected="selected"<?php } ?>>Yes</option>
								<option value="no" <?php if(isset($badge) && $badge== 'no'){ ?>selected="selected"<?php } ?>>No</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="badge-position" > <?php echo esc_html__("Badge Position", 'cf7-invisible-recaptcha'); ?></label></th>
						<td>
							<select name="badge_position" id="badge-position">
								<option value="bottomright" <?php if(isset($badge_position) && $badge_position== 'bottomright'){ ?>selected="selected"<?php } ?>><?php echo esc_html__("Bottom Right", 'cf7-invisible-recaptcha'); ?></option>
								<option value="bottomleft" <?php if(isset($badge_position) && $badge_position== 'bottomleft'){ ?>selected="selected"<?php } ?>><?php echo esc_html__("Bottom Left", 'cf7-invisible-recaptcha'); ?></option>
								<option value="inline" <?php if(isset($badge_position) && $badge_position== 'inline'){ ?>selected="selected"<?php } ?>> <?php echo esc_html__("Inline", 'cf7-invisible-recaptcha'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="badge-position" ><?php echo esc_html__("Button Class", 'cf7-invisible-recaptcha'); ?></label></th>
						<td>
							<input name="button_class" id="button_class" class="regular-text vsz_captcha_button_class" type="text" value="<?php if(isset($button_class) && !empty($button_class)){ echo esc_attr($button_class);}?>">
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="tab_id" value="<?php echo esc_attr(wp_create_nonce('submit-settings')); ?>">
				<input name="submit-settings" id="submit" class="button button-primary" value="<?php echo esc_html__("Save Changes", 'cf7-invisible-recaptcha'); ?>" type="submit">
			</p>
			<div class="cf7_captcha_notice_outer notice">
				<p class="note"><?php echo esc_html__("Note:", 'cf7-invisible-recaptcha'); ?></p>
				<ul class="cf7_captcha_notice">
					<li>
						<i class="fa fa-hand-o-right"></i><?php echo esc_html__("It is possible that some or all functions may not work proper if you are using some other invisible recaptcha functionality providing plugin.", 'cf7-invisible-recaptcha'); ?>
						
					</li>
					<li>
						<i class="fa fa-hand-o-right"></i>
						<?php echo esc_html__("It is advisable to validate your key before saving.", 'cf7-invisible-recaptcha'); ?>
						
					</li><?php
						//This hook is used to add custom notes
						do_action('cf7-Invisible-recaptcha-admin-page-gs-notes');
				?></ul>
			</div>
		</form>

		<!-- Contact Form 7 -->
		<form class="cf7-settings-form woocommerce-settings tabbed" id="cf7" method="post"><?php

			$enable = get_option('invisible_recaptcha_enable');
			$exclude = get_option('invisible_recaptcha_badge_exclude');

			?><div class="mch-settings-section-header">
				<h3><?php echo esc_html__("Contact Forms", 'cf7-invisible-recaptcha'); ?></h3>
			</div>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable" ><?php echo esc_html__("Enable Protection for Contact Form 7", 'cf7-invisible-recaptcha'); ?></label></th>
						<td><input name="enable" id="enable" class="regular-text vsz_captcha_active" type="checkbox" value="1" <?php if(isset($enable) && $enable == 1){ ?>checked <?php } ?> /></td>
					</tr>
					<tr>
						<th scope="row"><label for="exclude" ><?php echo esc_html__("Excluded Forms IDs", 'cf7-invisible-recaptcha'); ?></label></th>
						<td>
							<input name="exclude" id="exclude" class="regular-text" type="text" value="<?php if(isset($exclude) && !empty($exclude)){ echo esc_attr($exclude);}?>">
							<p class="description"><?php echo esc_html__("A list of comma separated  Forms IDs which should not be protected by Invisible reCaptcha", 'cf7-invisible-recaptcha'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="tab_id" value="<?php echo esc_attr(wp_create_nonce('submit-cf7')); ?>">
				<input name="submit-cf7" id="submit" class="button button-primary" value="<?php echo esc_html__("Save Changes", 'cf7-invisible-recaptcha'); ?>" type="submit">
			</p>
			<!-- Changed dated 17-04-2023 -->
			<div class="recaptcha-holder-section">
				<div class="recaptcha-holder" id="recaptcha-holder-101"></div>
			</div>
		</form><?php

		//This hook is used to add custom html
		do_action('cf7-Invisible-recaptcha-admin-page-tabs-html');

	?></div>
	<script>
		jQuery(document).ready(function(){
			jQuery("form.cf7-settings-form").hide();
			jQuery("#<?php echo esc_attr($tab); ?>").show();

			jQuery(".noquote").keypress(function (event) {
				var charCode = (event.which) ? event.which : event.keyCode;
				if (charCode == 34){
					alert("Character quotation mark (\") is not allowed.");
					return false;
				}
				else{
					return true;
				}
			});

			jQuery('.cf7-head').after('<div class="cf7-recaptcha-error"></div>');
			jQuery('form').submit(function(){
				var formType = this.id;
				if(formType == "settings"){
					jQuery('.cf7-recaptcha-error').html('');

					var checkForm = true;
					if(jQuery("#sitekey").val() == '' ){
						jQuery('.cf7-recaptcha-error').append('<div id="message" class="notice error is-dismissible"><p>Enter Site Key.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
						jQuery("#sitekey").css("border","1px solid red");
						checkForm = false;
					}
					else{
						jQuery("#sitekey").css("border","");
					}
					if(jQuery("#secretkey").val() == '' ){
						jQuery('.cf7-recaptcha-error').append('<div id="message" class="notice error is-dismissible"><p>Enter Secret Key.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
						jQuery("#secretkey").css("border","1px solid red");
						checkForm = false;
					}
					else{
						jQuery("#secretkey").css("border","");
					}
					if(!checkForm){
						return false;
					}

				}
			});

			jQuery('.vsz_recaptcha_test').click(function(){

				jQuery('.vsz_recaptcha_setup_msg').html('');
				jQuery('.vsz_recaptcha_setup div:not(.vsz_recaptcha_setup_msg)').remove();
				var checkForm = true;
					if(jQuery("#sitekey").val() == '' ){
						jQuery('.vsz_recaptcha_setup_msg').append('<div id="message" class="notice error is-dismissible"><p>Enter Site Key.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');

						checkForm = false;
					}
					if(jQuery("#secretkey").val() == '' ){
						jQuery('.vsz_recaptcha_setup_msg').append('<div id="message" class="notice error is-dismissible"><p>Enter Secret Key.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');

						checkForm = false;
					}
					if(checkForm){
						renderGoogleInvisibleRecaptcha();
					}

			});
			jQuery(document).on('click','.notice-dismiss',function(){
				jQuery(this).parent().remove();
			});

		});

		var ajax_nonce = "<?php echo esc_js(wp_create_nonce( "checksecretkey" ));?>";
		var renderGoogleInvisibleRecaptcha = function() {
			//Changed dated 17-04-2023
			if(jQuery(".recaptcha-holder").length == 0){
				jQuery(".recaptcha-holder-section").html('<div class="recaptcha-holder" id="recaptcha-holder-101"></div>')
			}
			
			jQuery(".spinner").css("visibility","visible");
			//Changed dated 17-04-2023
			var index  = 101;
			
			var sitekey = jQuery('#sitekey').val();
			var holderId = grecaptcha.render('recaptcha-holder-'+index,{
					'sitekey': sitekey,
					'size': 'invisible',
					'badge' : 'bottomright', // possible values: bottomright, bottomleft, inline
					'callback' : function (recaptchaToken) {
						var test = 1;

						jQuery('.vsz_recaptcha_setup_msg').html('<div id="message" class="notice  is-dismissible"><p>Your Site Key is Valid.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
						// Call ajax for get contact form messages
						var secretkey = jQuery("#secretkey").val();
						jQuery.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
									'token':recaptchaToken,
									'action':"vsz_cf7_secret_key",
									'secretkey' : secretkey,
									'ajax_nonce':ajax_nonce,
									},
							success: function(data){
								jQuery(".spinner").css("visibility","hidden");
								jQuery('.vsz_recaptcha_setup_msg').append('<div id="message" class="notice  is-dismissible"><p>Your Secret Key is '+data+'.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');

							}
						});
						grecaptcha.reset(holderId);
						
						//Changed dated 17-04-2023
						jQuery(".recaptcha-holder").remove();

					},
					'error-callback' : function(){
						jQuery(".spinner").css("visibility","hidden");
						jQuery('.vsz_recaptcha_setup_msg').html('<div id="message" class="notice  is-dismissible"><p>Your Site Key is Invalid.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
						
						//Changed dated 17-04-2023
						jQuery(".recaptcha-holder").remove();
					},
			},false);

			if(grecaptcha.getResponse(holderId) != ''){
				grecaptcha.reset(holderId);
			}
			else{

				// execute the recaptcha challenge
				grecaptcha.execute(holderId);
			}
		}
	</script>
	<script  src="https://www.google.com/recaptcha/api.js?render=explicit" async defer></script><?php
}

//for display invisible recaptcha on contact form
$enable = get_option('invisible_recaptcha_enable');

if(isset($enable) && $enable == 1){
	add_action( 'wp_enqueue_scripts', 'vsz_cf7_invisible_recaptcha_page_scripts' );
}
function vsz_cf7_invisible_recaptcha_page_scripts(){
	
	// Enqueue default functions
	wp_enqueue_script("cf7_invisible_recaptcha_functions", plugin_dir_url( __FILE__ )."js/cf7_invisible_recaptcha.js" ,array( 'jquery' ), CF7_CAPTCHA_VERSION, false);
	
	$site_key = get_option('invisible_recaptcha_sitekey');
	$secretkey= get_option('invisible_recaptcha_secretkey');
	$badge = get_option('invisible_recaptcha_badge');
	$badge_position = get_option('invisible_recaptcha_badge_position');
	$exclude = get_option('invisible_recaptcha_badge_exclude');
	$button_class= get_option('invisible_recaptcha_button_class');
	//get comma separated id
	$exclude = explode(',',$exclude);

	//added in 1.3.5
	foreach($exclude as $key=>$val){		
		if(defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, '5.8', '>=')){
			$objForm = wpcf7_get_contact_form_by_hash(trim($val));

			if(empty($objForm) || $objForm == NULL) {
				$exclude[$key] = $val;
			} else {
				$exclude[$key] = (string) $objForm->id;
				
			}
		}
		else{
			$exclude[$key] = $val;
		}
	}

	?>

	<style>
		.wpcf7-submit{
			display:none;
		}
		.recaptcha-btn{
			display:block;
		}
		<?php
			if(isset($badge) && $badge== 'no'){
				?>.grecaptcha-badge {display: none;} <?php
			}
			else{
				?>.grecaptcha-badge { margin: 10px 0; }<?php
			}
		?>

	</style>
	<script type="text/javascript">
		var contactform = [];
		var checkIfCalled = true;
		var renderGoogleInvisibleRecaptchaFront = function() {
			// prevent form submit from enter key
			jQuery("input[name=_wpcf7]").attr("class","formid");
				jQuery('.wpcf7-form').on('keyup keypress', "input", function(e) {
				  var keyCode = e.keyCode || e.which;
				  if (keyCode === 13) {
					e.preventDefault();
					return false;
				  }
				});

			jQuery('.wpcf7-submit').each(function(index){

				var checkexclude = 0;
				var form = jQuery(this).closest('.wpcf7-form');
				var value = jQuery(form).find(".formid").val();
				// check form exclude from invisible recaptcha
				<?php
					if(isset($exclude) && !empty($exclude) && $exclude[0] != ''){
						foreach($exclude as $data){ ?>
							if(value == <?php echo esc_js($data);?>){
								checkexclude = 1;
								form.find('.wpcf7-submit').show();
							}
				<?php  }
					 }
				?>
				if(checkexclude == 0){
					// Hide the form orig submit button
					form.find('.wpcf7-submit').hide();

					// Fetch class and value of orig submit button
					btnClasses = form.find('.wpcf7-submit').attr('class');
					btnValue = form.find('.wpcf7-submit').attr('value');

					// Add custom button and recaptcha holder

					form.find('.wpcf7-submit').after('<input type="button" id="wpcf-custom-btn-'+index+'" class="'+btnClasses+' <?php echo esc_attr($button_class); ?> recaptcha-btn recaptcha-btn-type-css" value="'+btnValue+'" title="'+btnValue+'" >');
					form.append('<div class="recaptcha-holder" id="recaptcha-holder-'+index+'"></div>');
					// Recaptcha rendenr from here
					var holderId = grecaptcha.render('recaptcha-holder-'+index,{
								'sitekey':'<?php $site_key = get_option('invisible_recaptcha_sitekey'); if(isset($site_key) && !empty($site_key)){ echo esc_js($site_key);}?>',
								'size': 'invisible',
								'badge' : '<?php echo esc_js($badge_position);?>', // possible values: bottomright, bottomleft, inline
								'callback' : function (recaptchaToken) {
									//console.log(recaptchaToken);
									var response=jQuery('#recaptcha-holder-'+index).find('.g-recaptcha-response').val();
									//console.log(response);
									//Remove old response and store new respone
									jQuery('#recaptcha-holder-'+index).parent().find(".respose_post").remove();
									jQuery('#recaptcha-holder-'+index).after('<input type="hidden" name="g-recaptcha-response"  value="'+response+'" class="respose_post">')
									grecaptcha.reset(holderId);

									if(typeof customCF7Validator !== 'undefined'){
										if(!customCF7Validator(form)){
											return;
										}
									}
									// Call default Validator function
									else if(contactFormDefaultValidator(form)){
										return;
									}
									else{
										// hide the custom button and show orig submit button again and submit the form
										jQuery('#wpcf-custom-btn-'+index).hide();
										form.find('input[type=submit]').show();
										form.find("input[type=submit]").click();
										form.find('input[type=submit]').hide();
										jQuery('#wpcf-custom-btn-'+index).attr('style','');
									}
								}
						},false);

					// action call when click on custom button
					jQuery('#wpcf-custom-btn-'+index).click(function(event){
						event.preventDefault();
						// Call custom validator function
						if(typeof customCF7Validator == 'function'){
							if(!customCF7Validator(form)){
								return false;
							}
						}
						// Call default Validator function
						else if(contactFormDefaultValidator(form)){
							return false;
						}
						else if(grecaptcha.getResponse(holderId) != ''){
							grecaptcha.reset(holderId);
						}
						else{
							// execute the recaptcha challenge
							grecaptcha.execute(holderId);
						}
					});
				}
			});
		}
	</script><?php
	$add_js = true;
	$add_js = apply_filters('cf7-Invisible-recaptcha-add-js',$add_js);

	if($add_js){
		?><script  src="https://www.google.com/recaptcha/api.js?onload=renderGoogleInvisibleRecaptchaFront&render=explicit" async defer></script><?php
	}
}

// Check user secret key
add_action('wp_ajax_vsz_cf7_secret_key','vsz_cf7_vsz_cf7_secret_key_callback');
function vsz_cf7_vsz_cf7_secret_key_callback(){

	// SECURITY: Verify user is authenticated and has admin capability
	if (!is_user_logged_in() || !current_user_can('manage_options')) {
		wp_die(esc_html__('Unauthorized access', 'cf7-invisible-recaptcha'));
	}

	if(isset($_POST['ajax_nonce']) && !empty($_POST['ajax_nonce'])){
		////// checking for nonce
		if( ! wp_verify_nonce($_POST['ajax_nonce'], 'checksecretkey')){

			wp_die(esc_html__("You don't have permission to view this page", 'cf7-invisible-recaptcha'));
			exit;
		}
		if(isset($_POST["token"]) && !empty($_POST["token"]) && isset($_POST["secretkey"]) && !empty($_POST["secretkey"])){
			$secret= sanitize_text_field($_POST["secretkey"]);
			$remoteip = sanitize_text_field($_SERVER["REMOTE_ADDR"]);
			$response = sanitize_text_field($_POST["token"]);
			if(isset($response)){
				$api_response = wp_remote_get( add_query_arg( array(
					'secret'   => $secret,
					'response' => $response,
					'remoteip' => $remoteip
				), 'https://www.google.com/recaptcha/api/siteverify' ) );
				
				
				// SECURITY: Check for WP_Error before processing
				if( is_wp_error( $api_response ) ) {
					
					echo esc_html__("Invalid", 'cf7-invisible-recaptcha');
					exit;
				}
				
				$recaptcha = wp_remote_retrieve_body( $api_response );
				$recaptcha = json_decode($recaptcha, true);
				
				// SECURITY: Validate JSON decode
				if($recaptcha === null) {
					echo esc_html__("Invalid", 'cf7-invisible-recaptcha');
					exit;
				}

				//varify response
				if (isset($recaptcha["success"]) && $recaptcha["success"] == 'true'){
					echo esc_html__("Valid", 'cf7-invisible-recaptcha');
					exit;
				}else{
					echo esc_html__("Invalid", 'cf7-invisible-recaptcha');
					exit;
				}
			}
		}
	}
}


//++++++++++ SPAM CHECKER ++++++++++++//
//Add No Script
if(isset($enable) && $enable == 1){
	// Add no script tag in Every CF7 form
	add_filter( 'wpcf7_form_elements', 'vsz_wpcf7_form_elements', 10, 1 );

	// Validate recaptcha field at form submission and display message accordingly.
	add_filter( 'wpcf7_validate', 'vsz_filter_wpcf7_validate', 10, 2 );
	add_filter( 'wpcf7_display_message', 'cf7ic_filter_wpcf7_validation_error', 10, 2 );
}

function vsz_wpcf7_form_elements( $this_replace_all_form_tags ) {
	// Display message when browser script
	$this_replace_all_form_tags .= '<noscript>
			<div class="wpcf7-response-output wpcf7-spam-blocked">Your browser does not support JavaScript!. Please enable javascript in your browser in order to get form work properly.</div>
	</noscript>';

	// make filter magic happen here...
    return $this_replace_all_form_tags;
}

// Validate form field
function vsz_filter_wpcf7_validate( $result, $tags ) {

		global $cf7ic_spam_entry;
		$cf7ic_spam_entry = false;
	

		$form_id = 	absint($_POST['_wpcf7']);
		if(!isset($form_id) || empty($form_id)){
			return $result;
		}

		$exclude = get_option('invisible_recaptcha_badge_exclude');
		$exclude = !empty($exclude) ? array_map('absint', array_filter(array_map('trim', explode(',', $exclude)))) : array();
		
		if(isset($exclude) && !empty($exclude) && $exclude[0] != ''){
			foreach($exclude as $data){
				if($data == $form_id){
					return $result;
				}
			}
		}

		if(isset($_POST["g-recaptcha-response"])){

			$remoteip = sanitize_text_field($_SERVER["REMOTE_ADDR"]);
			$response = sanitize_textarea_field($_POST["g-recaptcha-response"]);
			$secretkey= get_option('invisible_recaptcha_secretkey');
			if(isset($response)){
				
				$api_response = wp_remote_get( add_query_arg( array(
					'secret'   => $secretkey,
					'response' => $response,
					'remoteip' => $remoteip
				), 'https://www.google.com/recaptcha/api/siteverify' ) );
				
				
				// SECURITY: Check for WP_Error before processing
				if ( is_wp_error( $api_response ) ) {
					$cf7ic_spam_entry = true;
					$result->invalidate($tags[0], '');
					return $result;
				}
				
				$recaptcha = wp_remote_retrieve_body( $api_response );
				$recaptcha = json_decode($recaptcha, true);
				
				// SECURITY: Validate JSON decode
				if ($recaptcha === null) {
					$cf7ic_spam_entry = true;
					$result->invalidate($tags[0], '');
					return $result;
				}

				if (isset($recaptcha["success"]) && $recaptcha["success"] == 'true'){
					$cf7ic_spam_entry = false;
				}
				else{
					$cf7ic_spam_entry = true;
					$result->invalidate( $tags[0],"");
				}
			}
		}
		else{
			$cf7ic_spam_entry = true;
			$result->invalidate( $tags[0],"");
		}
		return $result;
}

function cf7ic_filter_wpcf7_validation_error($error, $name){
	global $cf7ic_spam_entry;

	if(isset($cf7ic_spam_entry) && $cf7ic_spam_entry){
		$error = 'Robot verification failed, please try again.';
	}
    return $error;
}


/*
 *  This function is to get all the messages related to  contact form
 *	All the messages will be defined in a javascript object
 *  @ since 1.3.0
 *	Called when rendering the contact form elements
 */
add_filter('wpcf7_form_elements','additional_cf7_form_elements');
function additional_cf7_form_elements($elements){

	$objCF7Form = WPCF7_ContactForm::get_current();
	$formId = $objCF7Form->id();

	$formMsgs = get_post_meta($formId,'_messages',true);
	$gdpr_msg = get_option("wpgdprc_integrations_contact-form-7_error_message");

	if(!empty($formMsgs)){
		$elements .= "<script type='text/javascript'>
						if(contactform === undefined){
							var contactform = [];
						}";
		$i = 0;
		foreach($formMsgs as $key => $val){
			$val = wp_json_encode($val);
			$elements .= "var innerVal = [".$formId.",'".$key."','".$val."'];
						contactform.push(innerVal);
						";
			$i++;
		}

		// For GDPR related message
		$gdpr_msg_safe = isset($gdpr_msg[$formId]) ? wp_json_encode($gdpr_msg[$formId]) : wp_json_encode('');
		$elements .= "var innerVal = [".$formId.",'gdpr',".$gdpr_msg_safe."];
						contactform.push(innerVal);
						";
	
		$elements .= "</script>";
	}

	return $elements;
}