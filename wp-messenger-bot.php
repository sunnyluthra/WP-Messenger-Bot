<?php
/*
Plugin Name: WP Messenger Bot
Description: Convert your WordPress site to an awesomely amazing bot.
Plugin URI: https://gruppo.io
Author: Sunny Luthra
Author URI: http://gruppo.io
Version: 1.0
License: GPL2
 */

/*

Copyright (C) 2106  Sunny  sunny@gruppo.io

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Gruppo_Messenger_Bot')):
	final class Gruppo_Messenger_Bot {
		private static $instance;
		public $namespace = 'gruppo_bot';
		public $prefix = 'gruppo_bot_';
		public $fb_message_url = "https://graph.facebook.com/v2.6/me/messages?access_token=";
		public $fb_user_url = "https://graph.facebook.com/v2.6/<USER_ID>?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=<PAGE_ACCESS_TOKEN>";
		public $bot;

		public static function instance() {
			if (!isset(self::$instance) && !(self::$instance instanceof Gruppo_Messenger_Bot)) {
				self::$instance = new Gruppo_Messenger_Bot;
				self::$instance->setup_constants();
				self::$instance->includes();
			}
			return self::$instance;
		}
		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'gruppo-messenger-bot'), '1.0');
		}
		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'gruppo-messenger-bot'), '1.0');
		}
		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if (!defined('GMB_VERSION')) {
				define('GMB_VERSION', '1');
			}
			// Plugin Folder Path.
			if (!defined('GMB_PLUGIN_DIR')) {
				define('GMB_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
			// Plugin Folder URL.
			if (!defined('GMB_PLUGIN_URL')) {
				define('GMB_PLUGIN_URL', plugin_dir_url(__FILE__));
			}
			// Plugin Root File.
			if (!defined('GMB_PLUGIN_FILE')) {
				define('GMB_PLUGIN_FILE', __FILE__);
			}

		}
		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			require_once GMB_PLUGIN_DIR . 'libs/cpt.php';
			require_once GMB_PLUGIN_DIR . 'libs/tgm/class-tgm-plugin-activation.php';
			// require_once GMB_PLUGIN_DIR . 'libs/carbon-fields/carbon-fields-plugin.php';

			require_once GMB_PLUGIN_DIR . 'includes/endpoints.php';
			require_once GMB_PLUGIN_DIR . 'includes/post-types.php';
			require_once GMB_PLUGIN_DIR . 'includes/fields.php';
			require_once GMB_PLUGIN_DIR . 'includes/messenger.php';
			require_once GMB_PLUGIN_DIR . 'includes/users.php';
			require_once GMB_PLUGIN_DIR . 'includes/templates.php';
			require_once GMB_PLUGIN_DIR . 'includes/data-source.php';

			GMB()->post_types = new GMB_Post_Types();
			GMB()->fields = new GMB_Fields();
			GMB()->endpoints = new GMB_Endpoints();
			GMB()->users = new GMB_Users();
			GMB()->messenger = new GMB_Messenger();
			GMB()->templates = new GMB_Templates();
			GMB()->data_source = new GMB_Data_Source();

			add_action('admin_enqueue_scripts', array($this, 'admin_style'));
			add_action('tgmpa_register', array($this, 'register_required_plugins'));

		}
		/**
		 * Include admin css.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		function admin_style() {
			wp_enqueue_style(GMB()->prefix . 'admin-style', GMB_PLUGIN_URL . 'assets/css/grid.css');
		}

		/**
		 * Required Plugins
		 */
		function register_required_plugins() {
			$plugins = array(
				array(
					'name' => 'Carbon Fields',
					'slug' => 'carbon-fields',
					'required' => true,
				),
			);
			$config = array(
				'id' => 'gruppo-bot', // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '', // Default absolute path to bundled plugins.
				'menu' => 'tgmpa-install-plugins', // Menu slug.
				'parent_slug' => 'plugins.php', // Parent menu slug.
				'capability' => 'manage_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices' => true, // Show admin notices or not.
				'dismissable' => true, // If false, a user cannot dismiss the nag message.
				'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => false, // Automatically activate plugins after installation or not.
				'message' => '', // Message to output right before the plugins table.
				'strings' => array(
					'page_title' => __('Install Required Plugins', 'gruppo-bot'),
					'menu_title' => __('Install Plugins', 'gruppo-bot'),
					'installing' => __('Installing Plugin: %s', 'gruppo-bot'),
					'updating' => __('Updating Plugin: %s', 'gruppo-bot'),
					'oops' => __('Something went wrong with the plugin API.', 'gruppo-bot'),
					'notice_can_install_required' => _n_noop(
						'This plugin requires the following plugin: %1$s.',
						'This plugin requires the following plugins: %1$s.',
						'gruppo-bot'
					),
				),
			);
			tgmpa($plugins, $config);
		}
	}
endif;

function GMB() {
	return Gruppo_Messenger_Bot::instance();
}

GMB();