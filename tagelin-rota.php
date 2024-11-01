<?php
/*
Plugin Name: Tagelin Rota
Plugin URI: http://www.tagelin.com/project/wordpress/tagelin-rota
Description: Rota duty roster. Allocate people to roles at scheduled events.
Version: 1.0.2
Author: Tagelin Ltd
Author URI: http://www.tagelin.com
License: GPLv2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tagelin-rota
Domain Path: /languages
*/

/*
Copyright 2018 Tagelin Ltd, United Kingdom
Author: Julian Dean (j.dean@tagelin.com)

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



/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}


if ( !class_exists( 'Tagelin_Rota' ) ) {
	final class Tagelin_Rota {
		protected static $_instance = null;

		public static $plugin_version;
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $plugin_basefile_path;
		public static $template_path;

		public $settings;
//		public $admin;
		public $database;
		public $widgets;

		public function __construct() 	{
			self::$plugin_version = '1.0';
			self::$plugin_prefix = 'tagelin_rota_';
			self::$plugin_basefile_path = __FILE__;
			self::$plugin_basefile = plugin_basename( self::$plugin_basefile_path );
			self::$plugin_url = plugin_dir_url( self::$plugin_basefile );
			self::$plugin_path = trailingslashit( dirname( self::$plugin_basefile_path ) );	
			self::$template_path  = trailingslashit( self::$plugin_path . 'templates' );

			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, "deactivate") );

			add_action( 'init', array( $this, 'load' ) );

		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}


		public function activate() {
			$this->load();
			$this->database->create_tables();
		}

		public function deactivate() {
		}

		public function delete() {
			$this->include_classes();
			$this->database = new Tagelin_Rota_Database();
			$this->database->delete_tables();
		}


		public function load() {

			$this->include_classes();

			$this->settings = new Tagelin_Rota_Settings();
			$this->database = new Tagelin_Rota_Database();
			$this->widgets = new Tagelin_Rota_Widgets( $this->settings, $this->database );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_tagelin_rota', array( $this, 'ajax_action' ) );
			add_action( 'wp_ajax_nopriv_tagelin_rota', array( $this,'ajax_action' ) ); // need this to serve non 

		}


		public function enqueue_scripts() {

			load_plugin_textdomain( 'tagelin-rota', 
					false, 
					dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


			wp_register_script( 'tagelin-rota-js', 
				self::$plugin_url.'js/tagelin-rota.js',
				array( 'jquery' ),
				'',
				true );


			$script_data = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'tagelin_rota_nonce' => wp_create_nonce('tagelin-rota-nonce'),
				'action' => 'tagelin_rota'
			);

			wp_localize_script( 'tagelin-rota-js', 'ajax_params', $script_data );


			//Base style, structural elements
			wp_register_style( 
				'tagelin-rota-style', 
				self::$plugin_url.'css/tagelin-rota.css',
				false, 
				self::$plugin_version,
				'all');


			//View Colours
			wp_register_style( 	//First look for local one in theme directory
				'tagelin-rota-view-site-style', 
				get_template_directory_uri().'/'.'tagelin-rota-view.css', 
				array('tagelin-rota-style'), 
				self::$plugin_version,
				'all');



			wp_register_style( 	// then look for ours in the plugin bundle
				'tagelin-rota-view-style', 
				self::$plugin_url.'css/tagelin-rota-view.css',
				array('tagelin-rota-style'), 
				self::$plugin_version,
				'all');




		}


 		public function ajax_action() {

			$request = $_POST;

 			$nonce = $request['nonce'];
 			if( ! wp_verify_nonce( $nonce, 'tagelin-rota-nonce')) die ('Busted!');
 

			$response = array();
			$response['status'] = false;


 			$response = $this->widgets->ajax_action( $request, $response );

			exit( json_encode( $response ) ); 

 		}


		public function include_classes() {
			include_once( 'includes/class-tagelin-rota-settings.php' );
			include_once( 'includes/class-tagelin-rota-database.php' );
			include_once( 'includes/class-tagelin-rota-log.php' );
			include_once( 'includes/class-tagelin-rota-abstract-widget.php' );
			include_once( 'includes/class-tagelin-rota-widgets.php' );

			include_once( 'includes/class-tagelin-rota-widget-edit-role.php' );
			include_once( 'includes/class-tagelin-rota-widget-edit-person.php' );
			include_once( 'includes/class-tagelin-rota-widget-edit-event.php' );
			include_once( 'includes/class-tagelin-rota-widget-edit-location.php' );
			include_once( 'includes/class-tagelin-rota-widget-edit-schedule.php' );
			include_once( 'includes/class-tagelin-rota-widget-view-schedule.php' );


		}

		public function locate_template( $located, $template_name ) {

			$template_path = self::$template_path;
			$located = locate_template(
					array(
						'tagelin-rota/'.$template_name,
						trailingslashit( $template_path ) . $template_name,
						$template_name	
					)
				);

			if( ! $located ) {
				$located = $template_path . $template_name;
			}

			return $located;
		}


		public function get_template( $template_name, $args=array() ) {

			$located = $this->locate_template( '' , $template_name );
		
			if ( ! file_exists( $located ) ) {
				Tagelin_Rota_Log::error( "no such file at ". $located );
				return;
			}
		
			extract( $args );
			include( $located );
	
		}

	}

	function Tagelin_Rota_Instance() {
		return Tagelin_Rota::instance();



	}


	Tagelin_Rota_Instance();

}


?>
