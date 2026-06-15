<?php
/**
* Plugin Name: Post Mark as Read
* Plugin URI: https://www.algoblend.in/wordpress/plugin/post-mark-as-read/
* Description: To save user read post data with advanced tracking, statistics, and bulk actions.
* Version: 2.0
* Author: Alok Verma
* Author URI: http://algoblend.com/alok-verma
* License: GPL v2 or later
* Text Domain: post-mark-as-read
**/

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'post_mark_as_read_setup_menu');
function post_mark_as_read_setup_menu(){
    add_menu_page( 
        'Post Mark as Read Setting', 
        'Post Mark as Read', 
        'manage_options', 
        'post-mark-as-read', 
        'pmar_settings_page', 
        'dashicons-yes', 
        66 
    );
    
    add_submenu_page(
        'post-mark-as-read',
        'Statistics',
        'Statistics',
        'manage_options',
        'pmar-statistics',
        'pmar_statistics_page'
    );
    
    add_submenu_page(
        'post-mark-as-read',
        'Reading History',
        'Reading History',
        'manage_options',
        'pmar-history',
        'pmar_history_page'
    );
    
    add_submenu_page(
        'post-mark-as-read',
        'Bulk Actions',
        'Bulk Actions',
        'manage_options',
        'pmar-bulk',
        'pmar_bulk_actions_page'
    );
}

function pmar_settings_page(){
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Post Mark as Read Settings', 'post-mark-as-read'); ?></h1>
        <h2><?php esc_html_e('Button Settings', 'post-mark-as-read'); ?></h2>
        <hr>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php 
            settings_fields('pmar-button-settings-group'); 
            do_settings_sections('pmar-button-settings-group'); 
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Button Title', 'post-mark-as-read'); ?></th>
                    <td>
                        <input type="text" name="pmar_button_title" 
                               value="<?php echo esc_attr(get_option('pmar_button_title', 'Complete')); ?>" 
                               class="regular-text" />
                        <p class="description"><?php esc_html_e('The text shown on the mark as read button.', 'post-mark-as-read'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Button Icon', 'post-mark-as-read'); ?></th>
                    <td>
                        <input type="text" name="pmar_button_icon" 
                               value='<?php echo esc_attr(get_option('pmar_button_icon', '<i class="fas fa-circle"></i>')); ?>' 
                               class="regular-text" />
                        <p class="description"><?php esc_html_e('HTML for the button icon (e.g., FontAwesome).', 'post-mark-as-read'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Button Location', 'post-mark-as-read'); ?></th>
                    <td>
                        <select name="pmar_button_location">
                            <option value="pmar_after_content" <?php selected(get_option('pmar_button_location'), 'pmar_after_content'); ?>>
                                <?php esc_html_e('After Content', 'post-mark-as-read'); ?>
                            </option>
                            <option value="pmar_before_content" <?php selected(get_option('pmar_button_location'), 'pmar_before_content'); ?>>
                                <?php esc_html_e('Before Content', 'post-mark-as-read'); ?>
                            </option>
                            <option value="pmar_button_widget" <?php selected(get_option('pmar_button_location'), 'pmar_button_widget'); ?>>
                                <?php esc_html_e('Enable Widget (use shortcode [pmar_btn])', 'post-mark-as-read'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('Where to display the button on single posts.', 'post-mark-as-read'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <hr>
        <h2><?php esc_html_e('Export/Import Data', 'post-mark-as-read'); ?></h2>
        <p><?php esc_html_e('Export or import reading data for backup or transfer.', 'post-mark-as-read'); ?></p>
        
        <h3><?php esc_html_e('Export', 'post-mark-as-read'); ?></h3>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('pmar_export_action', 'pmar_export_nonce'); ?>
            <input type="hidden" name="action" value="pmar_export_data" />
            <?php submit_button(__('Export Reading Data', 'post-mark-as-read'), 'secondary'); ?>
        </form>
        
        <h3><?php esc_html_e('Import', 'post-mark-as-read'); ?></h3>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field('pmar_import_action', 'pmar_import_nonce'); ?>
            <input type="hidden" name="action" value="pmar_import_data" />
            <input type="file" name="pmar_import_file" accept=".json" required />
            <?php submit_button(__('Import Reading Data', 'post-mark-as-read'), 'secondary'); ?>
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
	register_setting( 'pmar-button-settings-group', 'pmar_button_title', 'sanitize_text_field' );
	register_setting( 'pmar-button-settings-group', 'pmar_button_icon', 'wp_kses_post' );
	register_setting( 'pmar-button-settings-group', 'pmar_button_location', 'sanitize_text_field' );
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
		$pmar_button_title = esc_attr(get_option('pmar_button_title', 'Complete'));
		$pmar_button_icon = get_option('pmar_button_icon', '<i class="fas fa-circle"></i>') . " ";
		$pmar_button_location = esc_attr(get_option('pmar_button_location'));

		$post_id = get_the_ID();
		$user_id = get_current_user_id();
		$meta_key = 'pmar_read_' . $user_id;
		$get_post_meta = get_post_meta($post_id, $meta_key, true);
		$pmar_button_class = "";
		
		if($get_post_meta != '' && $get_post_meta == 'read'){
			$pmar_button_title = __('Completed', 'post-mark-as-read');
			$pmar_button_class = 'class="pmar_read"';
			$pmar_button_icon = '<i class="fas fa-check"></i> ';
		}

		if($pmar_button_location == 'pmar_before_content'){
			$before = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.esc_attr($post_id).'">'.$pmar_button_icon.esc_html($pmar_button_title).'</button></p>';
		}elseif($pmar_button_location == 'pmar_after_content'){
			$after = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.esc_attr($post_id).'">'.$pmar_button_icon.esc_html($pmar_button_title).'</button></p>';
		}
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
	    array('jquery'),
	    '2.0',
	    true
	  );
	  wp_enqueue_script( 'pmarAjaxHandle' );
	  wp_localize_script(
	    'pmarAjaxHandle',
	    'pmar_ajax_object',
	    array(
	    	'pmarAjaxURL' => admin_url( 'admin-ajax.php' ),
	    	'pmarAjaxAction' => 'pmarAjaxSubmit',
	    	'nonce' => wp_create_nonce('pmar_nonce')
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
	check_ajax_referer('pmar_nonce', 'nonce');
	
	if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
		wp_send_json_error(array('message' => 'Invalid post ID'));
	}
	
	$post_id = intval($_POST['post_id']);
	$user_id = get_current_user_id();
	
	if (!$user_id) {
		wp_send_json_error(array('message' => 'User not logged in'));
	}

	$meta_key = 'pmar_read_' . $user_id;
	$get_post_meta = get_post_meta($post_id, $meta_key, true);

	if($get_post_meta != ''){
		if($get_post_meta == 'read') {
			update_post_meta($post_id, $meta_key, 'unread');
			delete_post_meta($post_id, 'pmar_read_date_' . $user_id);
		} else {
			update_post_meta($post_id, $meta_key, 'read');
			update_post_meta($post_id, 'pmar_read_date_' . $user_id, current_time('mysql'));
		}
	} else {
		add_post_meta($post_id, $meta_key, 'read');
		add_post_meta($post_id, 'pmar_read_date_' . $user_id, current_time('mysql'));
	}
	
	$get_post_meta = get_post_meta($post_id, $meta_key, true);

	$response = array("status" => $get_post_meta);
	wp_send_json($response);
}

function pmar_widget($content=""){
	if(is_single() && is_main_query() && is_user_logged_in()){
		$before = $after = '';
		$pmar_button_title = esc_attr(get_option('pmar_button_title', 'Complete'));
		$pmar_button_icon = get_option('pmar_button_icon', '<i class="fas fa-circle"></i>') . " ";
		$pmar_button_location = esc_attr(get_option('pmar_button_location'));

		$post_id = get_the_ID();
		$user_id = get_current_user_id();
		$meta_key = 'pmar_read_' . $user_id;
		$get_post_meta = get_post_meta($post_id, $meta_key, true);
		$pmar_button_class = "";
		
		if($get_post_meta != '' && $get_post_meta == 'read'){
			$pmar_button_title = __('Completed', 'post-mark-as-read');
			$pmar_button_class = 'class="pmar_read"';
			$pmar_button_icon = '<i class="fas fa-check"></i> ';
		}

		if($pmar_button_location == 'pmar_button_widget'){
			$after = '<p><button '.$pmar_button_class.' id="pmarPostID" value="'.esc_attr($post_id).'">'.$pmar_button_icon.esc_html($pmar_button_title).'</button></p>';
		}
		$content = $before . $content . $after;
	}
	return $content; 
}

add_shortcode('pmar_btn', 'pmar_widget');

/*
==================================================
Statistics Page
==================================================
*/
function pmar_statistics_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    
    global $wpdb;
    $users = get_users(array('fields' => array('ID', 'display_name')));
    $total_posts = wp_count_posts('post')->publish;
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Reading Statistics', 'post-mark-as-read'); ?></h1>
        
        <div class="pmar-stats-container">
            <div class="pmar-stat-box">
                <h2><?php echo esc_html($total_posts); ?></h2>
                <p><?php esc_html_e('Total Posts', 'post-mark-as-read'); ?></p>
            </div>
            
            <?php foreach ($users as $user): 
                $user_id = $user->ID;
                $meta_key = 'pmar_read_' . $user_id;
                
                $read_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
                    WHERE meta_key = %s AND meta_value = 'read'",
                    $meta_key
                ));
                
                $percentage = $total_posts > 0 ? round(($read_count / $total_posts) * 100, 1) : 0;
            ?>
            <div class="pmar-stat-box">
                <h3><?php echo esc_html($user->display_name); ?></h3>
                <p><?php echo esc_html($read_count); ?> / <?php echo esc_html($total_posts); ?> <?php esc_html_e('posts read', 'post-mark-as-read'); ?></p>
                <div class="pmar-progress-bar">
                    <div class="pmar-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
                </div>
                <p><?php echo esc_html($percentage); ?>% <?php esc_html_e('Complete', 'post-mark-as-read'); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <style>
            .pmar-stats-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            .pmar-stat-box {
                background: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .pmar-stat-box h2 {
                margin: 0 0 10px 0;
                font-size: 36px;
                color: #2271b1;
            }
            .pmar-stat-box h3 {
                margin: 0 0 10px 0;
                font-size: 20px;
                color: #2271b1;
            }
            .pmar-progress-bar {
                width: 100%;
                height: 20px;
                background: #f0f0f0;
                border-radius: 10px;
                overflow: hidden;
                margin: 10px 0;
            }
            .pmar-progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #2271b1, #5cb85c);
                transition: width 0.3s ease;
            }
        </style>
    </div>
    <?php
}

/*
==================================================
Reading History Page
==================================================
*/
function pmar_history_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    
    global $wpdb;
    $selected_user = isset($_GET['user_id']) ? intval($_GET['user_id']) : get_current_user_id();
    $users = get_users(array('fields' => array('ID', 'display_name')));
    
    $meta_key = 'pmar_read_' . $selected_user;
    $date_key = 'pmar_read_date_' . $selected_user;
    
    $read_posts = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID, p.post_title, pm.meta_value as read_date 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        LEFT JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = %s
        WHERE pm.meta_key = %s AND pm.meta_value = 'read' AND p.post_status = 'publish'
        ORDER BY pm_date.meta_value DESC",
        $date_key, $meta_key
    ), ARRAY_A);
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Reading History', 'post-mark-as-read'); ?></h1>
        
        <form method="get">
            <input type="hidden" name="page" value="pmar-history" />
            <label for="user_id"><?php esc_html_e('Select User:', 'post-mark-as-read'); ?></label>
            <select name="user_id" id="user_id" onchange="this.form.submit()">
                <?php foreach ($users as $user): ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($selected_user, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
        
        <h2><?php esc_html_e('Posts Read', 'post-mark-as-read'); ?>: <?php echo count($read_posts); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Post Title', 'post-mark-as-read'); ?></th>
                    <th><?php esc_html_e('Date Marked Read', 'post-mark-as-read'); ?></th>
                    <th><?php esc_html_e('Actions', 'post-mark-as-read'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($read_posts)): ?>
                    <?php foreach ($read_posts as $post): ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url(get_permalink($post['ID'])); ?>" target="_blank">
                                <?php echo esc_html($post['post_title']); ?>
                            </a>
                        </td>
                        <td><?php echo $post['read_date'] ? esc_html(date_i18n(get_option('date_format'), strtotime($post['read_date']))) : __('N/A', 'post-mark-as-read'); ?></td>
                        <td>
                            <a href="<?php echo esc_url(get_edit_post_link($post['ID'])); ?>" class="button button-small">
                                <?php esc_html_e('Edit', 'post-mark-as-read'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3"><?php esc_html_e('No posts marked as read yet.', 'post-mark-as-read'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/*
==================================================
Bulk Actions Page
==================================================
*/
function pmar_bulk_actions_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    
    if (isset($_POST['pmar_bulk_submit'])) {
        check_admin_referer('pmar_bulk_action', 'pmar_bulk_nonce');
        
        $action = sanitize_text_field($_POST['pmar_bulk_action']);
        $user_id = intval($_POST['pmar_user_id']);
        $category = intval($_POST['pmar_category']);
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        
        if ($category > 0) {
            $args['cat'] = $category;
        }
        
        $posts = get_posts($args);
        $meta_key = 'pmar_read_' . $user_id;
        $date_key = 'pmar_read_date_' . $user_id;
        $count = 0;
        
        foreach ($posts as $post) {
            if ($action === 'mark_read') {
                update_post_meta($post->ID, $meta_key, 'read');
                update_post_meta($post->ID, $date_key, current_time('mysql'));
                $count++;
            } elseif ($action === 'mark_unread') {
                update_post_meta($post->ID, $meta_key, 'unread');
                delete_post_meta($post->ID, $date_key);
                $count++;
            }
        }
        
        echo '<div class="notice notice-success"><p>' . 
             sprintf(__('%d posts updated successfully.', 'post-mark-as-read'), $count) . 
             '</p></div>';
    }
    
    $users = get_users(array('fields' => array('ID', 'display_name')));
    $categories = get_categories(array('hide_empty' => false));
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Bulk Actions', 'post-mark-as-read'); ?></h1>
        <p><?php esc_html_e('Mark multiple posts as read or unread in bulk.', 'post-mark-as-read'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('pmar_bulk_action', 'pmar_bulk_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="pmar_user_id"><?php esc_html_e('User', 'post-mark-as-read'); ?></label></th>
                    <td>
                        <select name="pmar_user_id" id="pmar_user_id" required>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo esc_attr($user->ID); ?>">
                                <?php echo esc_html($user->display_name); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="pmar_category"><?php esc_html_e('Category', 'post-mark-as-read'); ?></label></th>
                    <td>
                        <select name="pmar_category" id="pmar_category">
                            <option value="0"><?php esc_html_e('All Categories', 'post-mark-as-read'); ?></option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>">
                                <?php echo esc_html($category->name); ?> (<?php echo esc_html($category->count); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="pmar_bulk_action"><?php esc_html_e('Action', 'post-mark-as-read'); ?></label></th>
                    <td>
                        <select name="pmar_bulk_action" id="pmar_bulk_action" required>
                            <option value="mark_read"><?php esc_html_e('Mark as Read', 'post-mark-as-read'); ?></option>
                            <option value="mark_unread"><?php esc_html_e('Mark as Unread', 'post-mark-as-read'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Apply Bulk Action', 'post-mark-as-read'), 'primary', 'pmar_bulk_submit'); ?>
        </form>
    </div>
    <?php
}

/*
==================================================
Export/Import Handlers
==================================================
*/
add_action('admin_post_pmar_export_data', 'pmar_export_data');
function pmar_export_data() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    
    check_admin_referer('pmar_export_action', 'pmar_export_nonce');
    
    global $wpdb;
    $data = $wpdb->get_results(
        "SELECT * FROM {$wpdb->postmeta} 
        WHERE meta_key LIKE 'pmar_read_%' OR meta_key LIKE 'pmar_read_date_%'",
        ARRAY_A
    );
    
    $filename = 'pmar-export-' . date('Y-m-d-His') . '.json';
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

add_action('admin_post_pmar_import_data', 'pmar_import_data');
function pmar_import_data() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'post-mark-as-read'));
    }
    
    check_admin_referer('pmar_import_action', 'pmar_import_nonce');
    
    if (!isset($_FILES['pmar_import_file']) || $_FILES['pmar_import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_redirect(add_query_arg('error', 'upload_failed', admin_url('admin.php?page=post-mark-as-read')));
        exit;
    }
    
    $json_data = file_get_contents($_FILES['pmar_import_file']['tmp_name']);
    $data = json_decode($json_data, true);
    
    if (!is_array($data)) {
        wp_redirect(add_query_arg('error', 'invalid_json', admin_url('admin.php?page=post-mark-as-read')));
        exit;
    }
    
    foreach ($data as $row) {
        if (isset($row['post_id']) && isset($row['meta_key']) && isset($row['meta_value'])) {
            update_post_meta($row['post_id'], $row['meta_key'], $row['meta_value']);
        }
    }
    
    wp_redirect(add_query_arg('success', 'imported', admin_url('admin.php?page=post-mark-as-read')));
    exit;
}

/*
==================================================
REST API Endpoints
==================================================
*/
add_action('rest_api_init', 'pmar_register_rest_routes');
function pmar_register_rest_routes() {
    register_rest_route('pmar/v1', '/posts/(?P<id>\d+)/read', array(
        'methods' => 'GET',
        'callback' => 'pmar_get_read_status',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args' => array(
            'id' => array(
                'validate_callback' => function($param) {
                    return is_numeric($param);
                }
            ),
        ),
    ));
    
    register_rest_route('pmar/v1', '/posts/(?P<id>\d+)/read', array(
        'methods' => 'POST',
        'callback' => 'pmar_set_read_status',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args' => array(
            'id' => array(
                'validate_callback' => function($param) {
                    return is_numeric($param);
                }
            ),
            'status' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return in_array($param, array('read', 'unread'));
                }
            ),
        ),
    ));
    
    register_rest_route('pmar/v1', '/user/stats', array(
        'methods' => 'GET',
        'callback' => 'pmar_get_user_stats',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ));
    
    register_rest_route('pmar/v1', '/user/history', array(
        'methods' => 'GET',
        'callback' => 'pmar_get_user_history',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ));
}

function pmar_get_read_status($request) {
    $post_id = $request['id'];
    $user_id = get_current_user_id();
    $meta_key = 'pmar_read_' . $user_id;
    $status = get_post_meta($post_id, $meta_key, true);
    
    return array(
        'post_id' => $post_id,
        'status' => $status ? $status : 'unread',
        'user_id' => $user_id,
    );
}

function pmar_set_read_status($request) {
    $post_id = $request['id'];
    $status = $request['status'];
    $user_id = get_current_user_id();
    
    $meta_key = 'pmar_read_' . $user_id;
    $date_key = 'pmar_read_date_' . $user_id;
    
    update_post_meta($post_id, $meta_key, $status);
    
    if ($status === 'read') {
        update_post_meta($post_id, $date_key, current_time('mysql'));
    } else {
        delete_post_meta($post_id, $date_key);
    }
    
    return array(
        'success' => true,
        'post_id' => $post_id,
        'status' => $status,
        'user_id' => $user_id,
    );
}

function pmar_get_user_stats($request) {
    global $wpdb;
    $user_id = get_current_user_id();
    $meta_key = 'pmar_read_' . $user_id;
    
    $read_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
        WHERE meta_key = %s AND meta_value = 'read'",
        $meta_key
    ));
    
    $total_posts = wp_count_posts('post')->publish;
    $percentage = $total_posts > 0 ? round(($read_count / $total_posts) * 100, 1) : 0;
    
    return array(
        'user_id' => $user_id,
        'read_count' => (int)$read_count,
        'total_posts' => (int)$total_posts,
        'percentage' => $percentage,
    );
}

function pmar_get_user_history($request) {
    global $wpdb;
    $user_id = get_current_user_id();
    $meta_key = 'pmar_read_' . $user_id;
    $date_key = 'pmar_read_date_' . $user_id;
    
    $read_posts = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID, p.post_title, p.post_date, pm_date.meta_value as read_date 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        LEFT JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = %s
        WHERE pm.meta_key = %s AND pm.meta_value = 'read' AND p.post_status = 'publish'
        ORDER BY pm_date.meta_value DESC
        LIMIT 100",
        $date_key, $meta_key
    ), ARRAY_A);
    
    return array(
        'user_id' => $user_id,
        'count' => count($read_posts),
        'posts' => $read_posts,
    );
}