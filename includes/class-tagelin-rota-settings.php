<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Settings	
 *
 * @class 		Tagelin_Rota_Settings
 * @author 		Julian Dean
 */


/**
 * Settings class
 */
if ( !class_exists( 'Tagelin_Rota_Settings' ) ) {

	class Tagelin_Rota_Settings {

		public  $key='tagelin-rota-settings';

		public function __construct() 	{
			if ( get_option( $this->key ) == false ) {
			
				// default options
				add_option( $this->key, 
					array(),  
					null , $autoload = 'yes' 
				);
			}

 			$this->options = get_option( $this->key );

			// default settings:
			if ( ! isset( $this->options[ 'title' ] ) )
 				$this->options[ 'title' ] = 'my title';

			if ( ! isset( $this->options[ 'database-host' ] ) )
				$this->options[ 'database-host' ] = 'localhost';

			if ( ! isset( $this->options[ 'database-user' ] ) )
				$this->options[ 'database-user' ] = 'tagelin-rota';

			if ( ! isset( $this->options[ 'database-passwd' ] ) )
				$this->options[ 'database-passwd' ] = '';

			if ( ! isset( $this->options[ 'database-db' ] ) )
				$this->options[ 'database-db' ] = 'tagelinrota';



			// store options
			update_option( $this->key, $this->options );

		}



		/** 
		 *	Get the current options array. 
		 */
		public function get_options() {
			return $this->options;
		}

		/** 
		 *	Set the current options array. 
		 */
		public function set_options($options) {
			$this->options = $options;
		}


		//	G E N E R A L 

		/**
		 *	Get the title.
		 */
		public function get_title()
		{
			return $this->options[ 'title' ];
		}


	}
}

?>