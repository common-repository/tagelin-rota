<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widgets
 *
 * @class 		Tagelin_Rota_Widgets
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widgets' ) ) {


	/**
	 *	Widget collection
	 */
	class Tagelin_Rota_Widgets {

		private $settings;
		private $database;
		private $widgets;

		/**
		 * Constructor
		 */
		public function __construct( $settings, $database ) 	{
			$this->settings = $settings;
			$this->database = $database;
			add_shortcode( 'tagelin-rota', array( $this,'widget_shortcode' ) );
			$this->define_widgets();

		}

		public function define_widgets() {

			$this->widgets=array();

			$this->widgets[] = new Tagelin_Rota_Widget_Edit_Role( $this->settings, $this->database );
			$this->widgets[] = new Tagelin_Rota_Widget_Edit_Person( $this->settings, $this->database );
			$this->widgets[] = new Tagelin_Rota_Widget_Edit_Event( $this->settings, $this->database );
			$this->widgets[] = new Tagelin_Rota_Widget_Edit_Location( $this->settings, $this->database );
			$this->widgets[] = new Tagelin_Rota_Widget_Edit_Schedule( $this->settings, $this->database );
			$this->widgets[] = new Tagelin_Rota_Widget_View_Schedule( $this->settings, $this->database );
		}


		/**
		 *	Generate html view for shortcodes
		 */
		function widget_shortcode( $attrs ) {
			wp_enqueue_script( 'tagelin-rota-js' );

			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'tagelin-rota-style' );
			wp_enqueue_style( 'tagelin-rota-view-style' );
			wp_enqueue_style( 'tagelin-rota-view-site-style' );

			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_localize_jquery_ui_datepicker();

			// Configure defaults and extract the attributes into variables
			$generic_attrs = shortcode_atts(
				array(
					'group' => '__default',
					'type'  => '',
					'title' => '',
				), $attrs, 'tagelin-rota' );


			foreach( $this->widgets as $widget ){
				if( $widget->get_type() == $generic_attrs['type'] ) {
								
					// check group exists	
					$this->database->insert_group( array( 'name' => $generic_attrs['group'] ) );

					return $widget->view( $attrs );
				}
			}
			
			return '';

		}


		/**
		 *	AJAX handler. Pass to individual widgets for action.
		 */
		public function ajax_action($request, $response) {

			$widget_type = $request['widget'];

			foreach( $this->widgets as $widget ){
				if( $widget->get_type() == $widget_type ) {
					return $widget->action( $request, $response );
				}
			}

		}


	}	
}

?>