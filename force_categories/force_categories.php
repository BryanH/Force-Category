<?php
/*
Plugin Name: Force Categories
Plugin URI: http://github.com/BryanH/Force_Categories
Description: Force posts by a user to include one or more specified categories (custom taxonomies) and/or prevent that user from assigning some categories (custom taxonomies) to her posts.
Version: 0.930
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
		function ForceCategories() { //constructor
			$this->register_plugin();
			/* Widget settings. */
			$widget_ops = array (
				'classname' => 'force_categories',
				'description' => __('Forces user posts to include and/or exclude specified categories', 'force_categories')
			);
			add_action('plugins_loaded', array (
				$this,
				'register_plugin'
			));
			/*
			 * Set up taxonomy for niche crap - jam the round peg into the octagonal hole.
			 * 'cause nothing says "Success" like forcing a blog platform to be a
			 */
			 /*
			$taxonomy_name = $this->plugin->com . 'subsite';
						register_taxonomy( "chubb", 'post', array (
							'hierarchical' => true,
							'label' => 'SubSite',
							'query_var' => true,
							'show_tagcloud' => false,
							'rewrite' => true
						));
			*/
			/*
			 * POUND that
			 */
			/* wp_insert_term( "Featured", $taxonomy_name, array(
			  'description' => 'Featured posts that will display in the "Featured"/"Spotlight" area',
			  'slug' => 'featured',
			  )
			 );

			 wp_insert_term( "0 - Home Page", $taxonomy_name, array(
			  'description' => 'Posts that should display on the home page',
			  'slug' => 'home',
			  )
			 );

			  * Kill some kittens

			 wp_insert_term( "Voices", $taxonomy_name, array(
			  'description' => 'Voices posts',
			  'slug' => 'voices',
			  )
			 );*/
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
		/*
		 * Verify the current user can edit the user record
		 * Parameter: user_id - id of user
		 * Returns: (nothing) or exception if fail
		 */
		function check_edit_authorization($user_id) {
			if (!current_user_can('edit_user', $user_id)) {
				wp_die(__('You do not have sufficient permissions to edit this user.'));
			}
		}
		/*
			* Retrieves the value of the key from a form's values
			* Updates the datastore with that key:value
			* Parameter: 		$key - form and datastore key (must be identical)
			* Returns: 		value passed so the intermediate variable can be updated
			*/
		function update_usermeta_from_post($key) {
			if (true == empty ($key)) {
				wp_die(__('Invalid key passed to fc_update_from_post'));
			}
			update_usermeta($key, $_POST[$key]);
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
		 * Get the styles installed
		 */
		function fc_style_enqueue() {
			wp_enqueue_script('jquery'); // Ensure jQ is active.
			$siteurl = get_option('siteurl');
			$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/stylesheets/force_cat.css';
			echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
		}
		/*
		 * Option screen
		 */
		function fc_options($user) {
			//			check_edit_authorization($user);
			if (!current_user_can('edit_user', $user)) {
				wp_die(__('You do not have sufficient permissions to edit this user.'));
			}
?>
<?php include(WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) . "options.php"); ?>
<?php

			add_action('personal_options_update', 'save_fc_options');
			add_action('edit_user_profile_update', 'save_fc_options');
		}
		function save_fc_options($user_id) {
			check_edit_authorization($user_id);
			update_user_meta($user_id, 'canthave_categories', $_POST['canthaveval']);
			update_user_meta($user_id, 'musthave_categories', $_POST['musthaveval']);
		}
		/*
		 * Obtains the url and file location of a given CSS
		 * Parameter: css filename (assumes it lives in the 'stylesheets' directory under the plugin)
		 * Returns: array of file, url to stylesheet.
		 * Use:  list($cssfile, $cssurl) = get_css_location('somecss.css');
		 */
		function get_css_location($css) {
			$admin_css = '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) . 'stylesheets/' . $css;
			return array (
				$this->plugin->dir . $admin_css,
				$this->plugin->url . $admin_css
			);
		}
	}
}
if (class_exists("ForceCategories")) {
	$force_cats = new ForceCategories();
}
if (isset ($force_cats)) {
	// TODO: Actions here
	if (is_admin()) { // admin actions
		add_action('edit_user_profile', array (
			& $force_cats,
			'fc_options'
		));
		add_action('show_user_profile', array (
			& $force_cats,
			'fc_options'
		));
		add_action('admin_head', array (
			& $force_cats,
			'fc_style_enqueue'
		));
		//		add_action('admin_init', array (
		//			& $force_cats,
		//			'register_mysettings'
		//		));
	} else {
		// non-admin enqueues, actions, and filters
	}
	// TODO: Filters here
}
?>
