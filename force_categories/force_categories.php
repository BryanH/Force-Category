<?php
/*
Plugin Name: Force Categories
Plugin URI: http://github.com/BryanH/Force_Categories
Description: Force posts by a user to include one or more specified categories (custom taxonomies) and/or prevent that user from assigning some categories (custom taxonomies) to her posts.
Version: 0.945
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
		var $defaults = array (
			'use_taxonomy' => 'subsite'
		); // default values
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
		/**
		 * hook for update_wpmu_options to save any sitewide settings for this plugin
		 * TODO: update for blog-level instead of MU
		 */
		function update_sitewide_options() {
			if ($_POST['taxonomy_to_use']) {
				update_site_option('force_categories', $_POST['taxonomy_to_use']);
			}
		}
		/*
		 * Plugin options
		 */
		function add_sitewide_options() {
			$taxonomy_to_use = get_site_option('force_categories', esc_attr($defaults['use_taxonomy']));
			$taxonomies = get_taxonomies('', 'objects');
?>
<?php include(WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) . "plugin_options.php"); ?>
<?php

		}
		/*
		 * User profile screen
		 */
		function fc_user_profile($user) {
			//			check_edit_authorization($user);
			if (!current_user_can('edit_user', $user)) {
				wp_die(__('You do not have sufficient permissions to edit this user.'));
			}
			/* Form Setup */
			$musthaves = get_the_author_meta('musthave_categories', $user->ID);
			if (1 > count($musthaves)) {
				$musthaves = array ();
			}
			$musthaves_flat = implode(',', $musthaves);
			$canthaves = get_the_author_meta('canthave_categories', $user->ID);
			if (1 > count($canthaves)) {
				$canthaves = array ();
			}
			$canthaves_flat = implode(',', $canthaves);
			// only get cats that aren't already assigned
			$categories_in_use = array_merge($musthaves, $canthaves);
			$taxonomy_to_use = get_site_option('force_categories', 'not set');
			if ('not set' == $taxonomy_to_use) {
				wp_die(__("<h2>Taxonomy/Category to use has not been set. You must go to the super-admin options page to do this first.</h2>"));
			}
			$categories = array_diff(get_terms($taxonomy_to_use, 'fields=names'), $categories_in_use);
?>
<?php include(WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) . "options.php"); ?>
<?php

		}
		function save_fc_options($user_id) {
			/*check_edit_authorization($user_id);*/
			if (!current_user_can('edit_user', $user_id)) {
				wp_die(__('You do not have sufficient permissions to edit this user.'));
			}
			$canthaveval = array ();
			$cant_explode = explode(',', $_POST['canthaveval']);
			$musthaveval = array ();
			$must_explode = explode(',', $_POST['musthaveval']);
			if (false != $cant_explode) {
				$canthaveval = $cant_explode;
			}
			if (false != $must_explode) {
				$musthaveval = $must_explode;
			}
			update_user_meta($user_id, 'canthave_categories', $canthaveval);
			update_user_meta($user_id, 'musthave_categories', $musthaveval);
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
		/*
		 * Force or prohibit
		 * TODO - finish
		 */
		function save_post($post_id, $post) {
			//			$taxonomy = get_site_option('force_categories', 'not set');
			$taxonomy = 'subsite';
			$author_id = $post->author;
			$must_haves = get_user_meta($author_id, 'musthave_categories');
			$cant_haves = get_user_meta($author_id, 'canthave_categories');
			/////wp_set_post_terms($post_id, $must_haves, $taxonomy, true);
			//			wp_set_post_terms( $post_id, $cant_haves, $taxonomy, false);
		}
		/*
		 * Show only main well posts
		 */
		function show_only_main_well_posts($query) {
			global $query_comment;
						global $wp_query;
			//$wp_query->set('orderby', 'cat');
			//if( is_home() ) { /* and not something else - featured? */
			// query_vars + taxonomy = FAIL: http://bit.ly/cYHneG
							$wp_query->query_vars['taxonomy'] = 'mainwell';
							$wp_query->query_vars['subsite'] = 'mainwell';
			if (false == $query->query_vars['suppress_filters']) {
				$query->set('taxonomy', 'subsite');
				$query->set('term', 'mainwell');/*
				$query->set('subsite', 'mainwell');*/
				// NOPE: $query->set( array ('subsite' =>'mainwell'));
				/* NOPE:
								$query->set('meta_key', 'subsite');
								$query->set('meta_value', 'mainwell');
								*/
				$query->set('posts_per_page', 5);
				//			//	$query->set('posts_per_page', 3);
			}
			$query_comment = $query;
			return $query;
			//}
		}
		function echo_query() { //print it into the footer
			//			wp_die("AHAHAHAHAHA");
			global $query_comment;
			echo "<h1>Query!</h1><pre>";
			print_r($query_comment);
			echo "</pre>";
		}
	}
}
if (class_exists("ForceCategories")) {
	$force_cats = new ForceCategories();
}
if (isset ($force_cats)) {
	// TODO: Actions here
	if (is_admin()) { // admin page actions
		add_action('edit_user_profile', array (
			& $force_cats,
			'fc_user_profile'
		));
		add_action('show_user_profile', array (
			& $force_cats,
			'fc_user_profile'
		));
		add_action('admin_head', array (
			& $force_cats,
			'fc_style_enqueue'
		));
		add_action('personal_options_update', array (
			& $force_cats,
			'save_fc_options'
		));
		add_action('edit_user_profile_update', array (
			& $force_cats,
			'save_fc_options'
		));
		// todo: MAKE for single blog only
		add_action('wpmu_options', array (
			& $force_cats,
			'add_sitewide_options'
		));
		add_action('update_wpmu_options', array (
			& $force_cats,
			'update_sitewide_options'
		));
		add_action('save_post', array (
			& $force_cats,
			'save_post'
		));
		/*add_action('pre_get_posts', array (
			& $force_cats,
			'show_only_main_well_posts'
		));*/
	} else {
		// Non-Admin enqueues, actions, and filters
		// Actions
		add_action('wp_footer', array (
			& $force_cats,
			'echo_query'
		));
		// Filters
		add_filter('pre_get_posts', array (
			& $force_cats,
			'show_only_main_well_posts'
		));
	}
}
?>
