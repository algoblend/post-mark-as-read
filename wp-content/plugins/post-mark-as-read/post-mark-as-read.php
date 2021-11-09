<?php
/**
* Plugin Name: Post Mark as Read
* Plugin URI: https://www.algoblend.in/wordpress/plugin/post-mark-as-read/
* Description: To save user read post data.
* Version: 1.0
* Author: Alok Verma
* Author URI: http://algoblend.com/alok-verma
**/

add_action('admin_menu', 'post_mark_as_read_setup_menu');
function post_mark_as_read_setup_menu(){
    add_menu_page( 'Post Mark as Read Setting', 'Post Mark as Read', 'manage_options', 'post-mark-as-read', 'init', 'dashicons-yes', 66 );
}

function init(){
    ?>
    <h1> <?php esc_html_e( 'Welcome to Post Mark as Read', 'post-mark-as-read-textdomain' ); ?> </h1>
	<div class="wrap">
	<h2> Button Setting </h2>
	<hr>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">
	    <?php settings_fields( 'pmar-button-settings-group' ); ?>
	    <?php do_settings_sections( 'pmar-button-settings-group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Title</th>
	        <td><input type="text" name="pmar_button_title" value="<?php echo esc_attr( get_option('pmar_button_title') ) != esc_attr( get_option('pmar_button_title') ) ? : 'Complete' ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row">Icon</th>
	        <td><input type="text" name="pmar_button_icon" value='<?php echo esc_attr( get_option('pmar_button_icon') ) != esc_attr( get_option('pmar_button_icon') ) ? : '<i class="fas fa-circle"></i>' ?>' /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row">Location</th>
	        <td>
	        	<select name="pmar_button_location">
	        		<option value="pmar_after_content" <?php if( get_option('pmar_button_location') == 'pmar_after_content') { echo 'selected'; }  ?>>After Content</option>
	        		<option value="pmar_before_content" <?php if( get_option('pmar_button_location') == 'pmar_before_content') { echo 'selected'; }  ?>>Before Content</option>
	        		<option value="pmar_button_widget" <?php if( get_option('pmar_button_location') == 'pmar_button_widget') { echo 'selected'; }  ?>>Enable Widget</option>
	        	</select>
	        </td>
	        </tr>
	    </table>
	    
	    <?php submit_button(); ?>

	</form>
	</div>
    <?php
}

/*
	==================================================
	Register Setting into WP
	==================================================
*/
add_action( 'admin_init', 'pmar_button_settings' );

function pmar_button_settings() {
	register_setting( 'pmar-button-settings-group', 'pmar_button_title' );
	register_setting( 'pmar-button-settings-group', 'pmar_button_icon' );
	register_setting( 'pmar-button-settings-group', 'pmar_button_location' );
}

/*
==========================================================
Name	:	Add CSS Style and JavaScript File in footer
==========================================================
*/

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
function load_custom_wp_admin_style($hook) {
	// Load only on ?page=post-mark-as-read
	if( $hook != 'toplevel_page_post-mark-as-read' ) {
		 return;
	}
	wp_enqueue_style( 'custom_wp_admin_css', plugins_url('admin/css/admin-style.css', __FILE__) );
}

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_script' );
function load_custom_wp_admin_script($hook) {
	// Load only on ?page=post-mark-as-read
	if( $hook != 'toplevel_page_post-mark-as-read' ) {
		 return;
	}
	wp_enqueue_script( 'custom_wp_admin_script', plugins_url('admin/js/admin-script.js', __FILE__) );
}

/* Modified Wordpress Article */

function my_content_filter($content){
	if(is_single() && is_main_query() && is_user_logged_in()){
		$before = $after = '';
		$pmar_button_title = esc_attr(get_option('pmar_button_title'));
		$pmar_button_icon = get_option('pmar_button_icon')." ";
		$pmar_button_location = esc_attr(get_option('pmar_button_location'));

		// Get Post meta data
		$post_id = get_the_ID();
		$get_post_meta = get_post_meta($post_id, 'pmar_read', true );
		$pmar_button_class = "";
		if($get_post_meta != '' && $get_post_meta == 'read'){
			$pmar_button_title = 'Completed';
			$pmar_button_class = 'class="pmar_read"';
			$pmar_button_icon = '<i class="fas fa-check"></i> ';
		}

		if($pmar_button_location == 'pmar_before_content'){
			$before = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.get_the_ID().'">'.$pmar_button_icon.$pmar_button_title.'</button></p>';
		}elseif($pmar_button_location == 'pmar_after_content'){
			$after = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.get_the_ID().'">'.$pmar_button_icon.$pmar_button_title.'</button></p>';
		}
		//modify the incoming content 
		$content = $before . $content . $after;
	}
	return $content; 
} 

add_filter( 'the_content', 'my_content_filter' );

/* Front Side Code */

/*
==================================================
Name	:	Add JavaScript File in footer
==================================================
*/

add_action( 'wp_enqueue_scripts', 'so_enqueue_scripts' );
function so_enqueue_scripts(){
	if( is_page() || is_single() ){
		wp_enqueue_style( 'custom_wp_front_css', plugins_url('front/css/front-style.css', __FILE__) );
	  wp_register_script(
	    'pmarAjaxHandle',
	    plugins_url('front/js/front-script.js', __FILE__),
	    array(),
	    false,
	    true
	  );
	  wp_enqueue_script( 'pmarAjaxHandle' );
	  wp_localize_script(
	    'pmarAjaxHandle',
	    'pmar_ajax_object',
	    array(
	    	'pmarAjaxURL' => admin_url( 'admin-ajax.php' ),
	    	'pmarAjaxAction' => 'pmarAjaxSubmit'
	    )
	  );
	}
}

/*
==================================================
Name	:	Ajax Function
==================================================
*/
add_action( "wp_ajax_pmarAjaxSubmit", "pmarAjaxSubmit" );
add_action( "wp_ajax_nopriv_pmarAjaxSubmit", "pmarAjaxSubmit" );
function pmarAjaxSubmit(){
	//DO whatever you want with data posted
	//To send back a response you have to echo the result!
	$post_id = $_POST['post_id'];

	$get_post_meta = get_post_meta($post_id, 'pmar_read', true );

	if($get_post_meta != ''){
		if($get_post_meta == 'read')
			update_post_meta ( $post_id, 'pmar_read', 'unread' );
		else
			update_post_meta ( $post_id, 'pmar_read', 'read' );
	}else{
		add_post_meta($post_id, 'pmar_read', 'read');
	}
	// print_r($get_post_meta);die;
	$get_post_meta = get_post_meta($post_id, 'pmar_read', true );

	$response = array("status"=>$get_post_meta);
	wp_send_json( $response );
	wp_die(); // ajax call must die to avoid trailing 0 in your response
}

/* Create Short Code */
function pmar_widget($content=""){
	if(is_single() && is_main_query() && is_user_logged_in()){
		$before = $after = '';
		$pmar_button_title = esc_attr(get_option('pmar_button_title'));
		$pmar_button_icon = get_option('pmar_button_icon')." ";
		$pmar_button_location = esc_attr(get_option('pmar_button_location'));

		// Get Post meta data
		$post_id = get_the_ID();
		$get_post_meta = get_post_meta($post_id, 'pmar_read', true );
		$pmar_button_class = "";
		if($get_post_meta != '' && $get_post_meta == 'read'){
			$pmar_button_title = 'Completed';
			$pmar_button_class = 'class="pmar_read"';
			$pmar_button_icon = '<i class="fas fa-check"></i> ';
		}

		if($pmar_button_location == 'pmar_button_widget'){
			$after = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.get_the_ID().'">'.$pmar_button_icon.$pmar_button_title.'</button></p>';
		}
		//modify the incoming content 
		$content = $before . $content . $after;
	}
	return $content; 
}

add_shortcode('pmar_btn', 'pmar_widget');