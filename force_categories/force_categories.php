<?php
/*
Plugin Name: Force Categories
Plugin URI: http://github.com/BryanH/Force_Categories
Description: Force posts by a user to include one or more specified categories and/or prevent that user from assigning some categories to her posts.
Version: 0.9
Author: Bryan Hanks, PMP
Author URI: http://www.chron.com/apps/adbxh0/
License: GPLv3
*/
/*
  Copyright 2010 Houston Chronicle, Inc.

  Force Categories is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Force Categories is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
if (!class_exists("ForceCategories")) {
	class ForceCategories {
		var $meta_sm = 'force_cats';
		var $default_meta_sm = array (); // default values
		function FollowSocialMedia() { //constructor
			$this->register_plugin();
			/* Widget settings. */
			$widget_ops = array (
				'classname' => 'force_categories',
				'description' => __('Forces user posts to include and/or exclude specified categories', 'force_categories')
			);
			$control_ops = array (
				'width' => 300,
				'height' => 350,
				'id_base' => 'force_categories-plugin'
			);
			add_action('plugins_loaded', array (
				$this,
				'register_plugin'
			));
		}
		// Generic plugin functionality by John Blackbourn
		function register_plugin() {
			$this->plugin = (object) array (
				'dom' => strtolower(get_class($this)),
				'url' => WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)),
				'dir' => WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__))
			);
			$this->settings = get_option($this->plugin->dom);
			if (!$this->settings) {
				add_option($this->plugin->dom, $this->defaults, true, true);
				$this->settings = $this->defaults;
			}
			load_plugin_textdomain($this->plugin->dom, false, $this->plugin->dom);
			add_action('admin_init', array (
				$this,
				'register_setting'
			));
		}
		function register_setting() {
			if ($callback = method_exists($this, 'sanitize'))
				$callback = array (
					$this,
					'sanitize'
				);
			register_setting($this->plugin->dom, $this->plugin->dom, $callback);
		}
	}
	/*
		* Retrieves the value of the key from a form's values
		* Updates the datastore with that key:value
		* Parameter: 		$key - form and datastore key (must be identical)
		* Returns: 		value passed so the intermediate variable can be updated
		*/
	function update_from_post($key) {
		if (true == empty ($key)) {
			wp_die(__('Invalid key passed to fc_update_from_post'));
		}
		update_option($key, $_POST[$key]);
		return $_POST[$key];
	}
	/*
	* Retrieves value from datastore. If nothing returned,
	* then it returns the default (or null if no default)
	* (equivalent to $foo = $bar || $default)
	* Parameters:	$key - datastore key
	*				$default - default value
	* Returns: either datastore's value, or default if former is empty
	*/
	function get_value_or_default($key, $default = null) {
		$the_data = get_option($key);
		if (true == empty ($the_data)) {
			$the_data = $default;
		}
		return $the_data;
	}
	/*
	 * Admin Option menu
	 */
	function fc_plugin_menu() {
		add_options_page(__('"Force Categories" Options', 'menu-fc'), __('Force Categories', 'menu-fc'), 'manage_options', 'forcecatsettings', array (
			& $this,
			'fc_options'
		));
	}
	/*
	 * Option screen
	 */
	function fc_options() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		$social_media = $this->get_value_or_default($this->meta_fc, $this->default_meta_fc);
		$hidden_field_name = 'fc_submit_hidden';
		if (isset ($_POST[$hidden_field_name]) && 'Y' == $_POST[$hidden_field_name]) {
			/* variables for the field and option names */
			// TODO: update using array
			$opt_max_words = $this->update_from_post($this->meta_fc);
?>
<div class="updated"><p><strong><?php _e('settings saved', 'menu-fc' ); ?></strong></p></div>
<?php

		}
?>
<?php include("options.php"); ?>
<?php

	}
	/*
	 * Obtains the url and file location of a given CSS
	 * Parameter:	css filename (assumes it lives in the 'stylesheets' directory under the plugin)
	 * Output: 		file, url to stylesheet.
	 * Use:			list($cssfile, $cssurl) = get_css_location('somecss.css');
	 */
	function get_css_location($css) {
		$admin_css = '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) . 'stylesheets/' . $css;
		$css_location = array (
			WP_PLUGIN_DIR . $admin_css,
			WP_PLUGIN_URL . $admin_css
		);
		return $css_location;
	}
}
if (class_exists("ForceCategories")) {
	$force_cats = new ForceCategories();
}
if (isset ($force_cats)) {
	// TODO: Actions here
	if (is_admin()) { // admin actions
		add_action('admin_menu', array (
			& $force_cats,
			'force_cats_menu'
		));
		add_action('admin_init', array (
			& $force_cats,
			'register_mysettings'
		));
	} else {
		// non-admin enqueues, actions, and filters
	}
	// TODO: Filters here
}
?>
