<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Admin page, General Tab
 *
 * @class 		Tagelin_Rota_Admin_General
 * @author 		Julian Dean


 */

if ( !class_exists( 'Tagelin_Rota_Admin_General' ) ) {

	class Tagelin_Rota_Admin_General extends Tagelin_Rota_Admin_Tab {


		public function define_settings() {

			// general settings section
			add_settings_section(
				$this->section, // defines this section
				__( 'General', 'tagelin-rota' ), // Title
				array( $this, 'print_section_info' ), // Callback
				$this->page // Page for the section
			);  
	
	
			add_settings_field(
				'title', 	// field
				'MyTitle', 	// Title
				array( $this, 'title_callback' ), 	// callback
				$this->page, 
				$this->section
			);

	

		}



		
		/**
		 * Sanitize each setting field as needed.
		 * Update the $options fields from $input map
		 *
		 * @param array $options Current options map
		 * @param array $input Contains all settings fields as array keys
	 	 * @return $options updated
		 */
		public function process_post_options( $options, $input )
		{
			if( isset( $input['title'] ) )
				$options['title'] = sanitize_text_field( $input['title'] );

			return $options;
		}	

		

		/** 
		 * Print the section text				
		 */
		public function print_section_info()
		{
			print __( 'Enter your setttings:', 'tagelin-rota' );
		}
	
	
	
		/** 
		 * Title Callback
		 */
		public function title_callback()
		{
			$value = $this->settings->get_title();

			printf(
				'<input type="text" id="title" name="%s[title]" value="%s" />',
				$this->settings->key,
				isset( $value ) ? esc_attr( $value ) : ''
			);
		}
	
	}
	
}

?>