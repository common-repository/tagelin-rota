<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Admin page, abstract Tab
 *
 * @class 		Tagelin_Rota_Admin_Tab
 * @author 		Julian Dean


 */

if ( !class_exists( 'Tagelin_Rota_Admin_Tab' ) ) {

	abstract class Tagelin_Rota_Admin_Tab {

		protected $title;
		protected $page;
		protected $section;
		protected $settings;
		protected $check_callback;

		/**
		 * Constructor
		 */
		public function __construct( $title, $settings, $page, $section, $check_callback ) 	{
			$this->title =$title;
			$this->settings = $settings;
			$this->page = $page;
			$this->section = $section;
			

			$this->register_setting( $check_callback );
			$this->define_settings();
		}


		public function get_title() {
			return $this->title;
		}

		public function get_section() {
			return $this->section;
		}

		public function get_page() {
			return $this->page;
		}


		abstract public function define_settings();


		/**
		 * Register settings
		 */
		private function register_setting($check_callback) {

			register_setting(
				$this->page, // Option group
				$this->settings->key, // Option name
				$check_callback//array( $this, 'check_options' ) 
			);
		}


		/**
		 * Sanitize each setting field as needed.
		 * Update the $options fields from $input map
		 *
		 * @param array $options Current options map
		 * @param array $input Contains all settings fields as array keys
		 */
		abstract public function process_post_options( $options, $input );
		

	}
}