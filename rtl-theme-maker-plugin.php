<?php
/*
Plugin Name: RTL Theme Maker Plugin
Plugin URI: http://www.primal.co.il
Description: The RTL Theme Maker can transform any theme into a right-to-left theme
Version: 1.0
Author: Uri Goren
Author URI: http://www.primal.co.il
*/


/*  Copyright 2012  Uri Goren

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


define( 'RTL_THEME_MAKER_DIR', WP_PLUGIN_DIR . '/rtl-theme-maker-plugin' );
define( 'RTL_THEME_MAKER_URL', WP_PLUGIN_URL . '/rtl-theme-maker-plugin' );


if (!class_exists("RTL_Theme_Maker")) :

class RTL_Theme_Maker {
	var $addpage;
	
	function RTL_Theme_Maker() {	
		add_action('admin_init', array(&$this,'init_admin') );
		add_action('init', array(&$this,'init') );
		add_action('admin_menu', array(&$this,'add_pages') );
		
		register_activation_hook( __FILE__, array(&$this,'activate') );
		register_deactivation_hook( __FILE__, array(&$this,'deactivate') );
	}
	

	function activate($networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_activate();
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_activate();		
	}

	function deactivate($networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_deactivate();
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_deactivate();		
	}	
	
	function _activate() {}
	
	function _deactivate() {}
	
	function init_admin() {
	}

	function init() {
		load_plugin_textdomain( 'rtl_theme_maker', RTL_THEME_MAKER_DIR . '/lang', basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function add_pages() {
	
		// Add a new submenu
		$this->addpage = add_options_page(	__('RTL Theme Maker', 'rtl_theme_maker'), __('RTL Theme Maker', 'rtl_theme_maker'), 
											'administrator', 'rtl_theme_maker', 
											array(&$this,'add_rtl_theme_maker_page') );
		add_action("admin_head-$this->addpage", array(&$this,'add_rtl_theme_maker_admin_head'));
		add_action("load-$this->addpage", array(&$this, 'on_load_rtl_theme_maker_page'));
		add_action("admin_print_styles-$this->addpage", array(&$this,'add_rtl_theme_maker_admin_styles'));
		add_action("admin_print_scripts-$this->addpage", array(&$this,'add_rtl_theme_maker_admin_scripts'));
	}

	function add_rtl_theme_maker_admin_head() {
	}
	
	
	function add_rtl_theme_maker_admin_styles() {
	}
	
	function add_rtl_theme_maker_admin_scripts() {
	}
	
	function on_load_rtl_theme_maker_page() {	
	}
	
	
	function add_rtl_theme_maker_page() {
		include('rtl-theme-maker-page.php');
	
	}

	function print_example($str, $print_info=TRUE) {
		if (!$print_info) return;
		__($str . "<br/><br/>\n", 'rtl_theme_maker' );
	}

	function javascript_redirect($location) {
		// redirect after header here can't use wp_redirect($location);
		?>
		  <script type="text/javascript">
		  <!--
		  window.location= <?php echo "'" . $location . "'"; ?>;
		  //-->
		  </script>
		<?php
		exit;
	}

} // end class
endif;

global $rtl_theme_maker;
if (class_exists("RTL_Theme_Maker") && !$rtl_theme_maker) {
    $rtl_theme_maker = new RTL_Theme_Maker();	
}	
?>