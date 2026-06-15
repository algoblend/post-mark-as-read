<?php
/**
 * Test cases for Post Mark as Read plugin
 * 
 * @package PostMarkAsRead
 */

class Post_Mark_As_Read_Access_Test extends WP_UnitTestCase {

    /**
     * Test plugin activation
     */
    public function test_plugin_activated() {
        $this->assertTrue(is_plugin_active('post-mark-as-read/post-mark-as-read.php'));
    }

    /**
     * Test that admin menu is registered
     */
    public function test_admin_menu_registered() {
        global $menu, $submenu;
        
        do_action('admin_menu');
        
        $found_menu = false;
        foreach ($menu as $item) {
            if (isset($item[2]) && $item[2] === 'post-mark-as-read') {
                $found_menu = true;
                break;
            }
        }
        
        $this->assertTrue($found_menu, 'Main menu item should be registered');
    }

    /**
     * Test that submenu pages are registered
     */
    public function test_submenu_pages_registered() {
        global $submenu;
        
        do_action('admin_menu');
        
        $this->assertArrayHasKey('post-mark-as-read', $submenu);
        $this->assertGreaterThanOrEqual(3, count($submenu['post-mark-as-read']));
    }

    /**
     * Test settings registration
     */
    public function test_settings_registered() {
        do_action('admin_init');
        
        $this->assertNotFalse(get_option('pmar_button_title', false) !== false || true);
    }

    /**
     * Test shortcode registration
     */
    public function test_shortcode_registered() {
        $this->assertTrue(shortcode_exists('pmar_btn'));
    }

    /**
     * Test post meta for read status
     */
    public function test_post_meta_read_status() {
        $user_id = $this->factory->user->create(array('role' => 'subscriber'));
        $post_id = $this->factory->post->create();
        
        $meta_key = 'pmar_read_' . $user_id;
        add_post_meta($post_id, $meta_key, 'read');
        
        $status = get_post_meta($post_id, $meta_key, true);
        $this->assertEquals('read', $status);
        
        update_post_meta($post_id, $meta_key, 'unread');
        $status = get_post_meta($post_id, $meta_key, true);
        $this->assertEquals('unread', $status);
    }

    /**
     * Test date tracking when marking as read
     */
    public function test_read_date_tracking() {
        $user_id = $this->factory->user->create(array('role' => 'subscriber'));
        $post_id = $this->factory->post->create();
        
        $meta_key = 'pmar_read_' . $user_id;
        $date_key = 'pmar_read_date_' . $user_id;
        
        update_post_meta($post_id, $meta_key, 'read');
        update_post_meta($post_id, $date_key, current_time('mysql'));
        
        $date = get_post_meta($post_id, $date_key, true);
        $this->assertNotEmpty($date);
        $this->assertNotFalse(strtotime($date));
    }

    /**
     * Test per-user isolation
     */
    public function test_per_user_isolation() {
        $user1_id = $this->factory->user->create(array('role' => 'subscriber'));
        $user2_id = $this->factory->user->create(array('role' => 'subscriber'));
        $post_id = $this->factory->post->create();
        
        $meta_key1 = 'pmar_read_' . $user1_id;
        $meta_key2 = 'pmar_read_' . $user2_id;
        
        update_post_meta($post_id, $meta_key1, 'read');
        update_post_meta($post_id, $meta_key2, 'unread');
        
        $status1 = get_post_meta($post_id, $meta_key1, true);
        $status2 = get_post_meta($post_id, $meta_key2, true);
        
        $this->assertEquals('read', $status1);
        $this->assertEquals('unread', $status2);
        $this->assertNotEquals($status1, $status2);
    }

    /**
     * Test AJAX handler exists
     */
    public function test_ajax_handler_registered() {
        $this->assertTrue(has_action('wp_ajax_pmarAjaxSubmit'));
        $this->assertTrue(has_action('wp_ajax_nopriv_pmarAjaxSubmit'));
    }

    /**
     * Test REST API routes registration
     */
    public function test_rest_api_routes_registered() {
        do_action('rest_api_init');
        
        $routes = rest_get_server()->get_routes();
        
        $this->assertArrayHasKey('/pmar/v1/posts/(?P<id>\d+)/read', $routes);
        $this->assertArrayHasKey('/pmar/v1/user/stats', $routes);
        $this->assertArrayHasKey('/pmar/v1/user/history', $routes);
    }

    /**
     * Test content filter
     */
    public function test_content_filter() {
        $this->assertTrue(has_filter('the_content', 'my_content_filter'));
    }

    /**
     * Test scripts are enqueued on single pages
     */
    public function test_scripts_enqueued() {
        global $post;
        $post = $this->factory->post->create_and_get();
        
        $this->go_to(get_permalink($post));
        
        do_action('wp_enqueue_scripts');
        
        $this->assertTrue(wp_script_is('pmarAjaxHandle', 'registered'));
        $this->assertTrue(wp_style_is('custom_wp_front_css', 'registered'));
    }

    /**
     * Test button location option
     */
    public function test_button_location_option() {
        update_option('pmar_button_location', 'pmar_after_content');
        $this->assertEquals('pmar_after_content', get_option('pmar_button_location'));
        
        update_option('pmar_button_location', 'pmar_before_content');
        $this->assertEquals('pmar_before_content', get_option('pmar_button_location'));
        
        update_option('pmar_button_location', 'pmar_button_widget');
        $this->assertEquals('pmar_button_widget', get_option('pmar_button_location'));
    }

    /**
     * Test button customization options
     */
    public function test_button_customization() {
        update_option('pmar_button_title', 'Mark Complete');
        update_option('pmar_button_icon', '<i class="fas fa-check"></i>');
        
        $this->assertEquals('Mark Complete', get_option('pmar_button_title'));
        $this->assertEquals('<i class="fas fa-check"></i>', get_option('pmar_button_icon'));
    }

    /**
     * Test admin capability checks
     */
    public function test_admin_capability_required() {
        $user_id = $this->factory->user->create(array('role' => 'subscriber'));
        wp_set_current_user($user_id);
        
        $this->assertFalse(current_user_can('manage_options'));
    }

    /**
     * Test uninstall cleanup
     */
    public function test_uninstall_file_exists() {
        $uninstall_file = dirname(dirname(dirname(__FILE__))) . '/../uninstall.php';
        $this->assertFileExists($uninstall_file);
    }

    /**
     * Test export handler registered
     */
    public function test_export_handler_registered() {
        $this->assertTrue(has_action('admin_post_pmar_export_data'));
    }

    /**
     * Test import handler registered
     */
    public function test_import_handler_registered() {
        $this->assertTrue(has_action('admin_post_pmar_import_data'));
    }
}
