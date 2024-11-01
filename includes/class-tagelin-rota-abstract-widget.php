<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Abstract Widget
 *
 * @class 		Tagelin_Rota_Abstract_Widget
 * @author 		Julian Dean
 */

if ( !class_exists( 'Tagelin_Rota_Abstract_Widget' ) ) {


	/**
	 *	Abstract Widget class
	 */
	abstract class Tagelin_Rota_Abstract_Widget {

		/**
		 *	Get class identifier
		 */
		abstract function get_type();


		/**
		 *	Get the html layout of the widget
		 */
		abstract function view( $attrs );


		/**
		 *	AJAX actions
		 */
		abstract function action( $request, $response );

		/**
		 *	Utility function to decode JSON escaped strings
		 */
		function decode_json( $text ) {

			$blob = '{ "value":"'.str_replace("\'", "'",  $text).'"}';
			$decoded = json_decode( $blob );

			return $decoded->value;
		}

	}	
}

?>