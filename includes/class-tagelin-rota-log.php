<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Logger
 *
 * @class 		Tagelin_Rota_Log
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Log' ) ) {

	interface LogLevel
	{
		const Debug = 0;
		const Warn = 1;
		const Info = 2;
		const Error = 3;
		const Critical = 4;
	}

	/**
	 *	Log class
	 */
	class Tagelin_Rota_Log {


		private static $logLevel = LogLevel::Debug;
		private static $prefix = "Tagelin_Rota_Log: ";

		/**
		 * Log init
		 */
		public static function init() {
			self::set_level( LogLevel::Debug );
		}

		/**
		 * Log level
		 */
		public static function set_level( $level ) {
			self::$logLevel = $level;
		}

	
		/**
		 * Log writer
		 */
		private static function log( $level, $msg ) {
			if( $level >= self::$logLevel )
			{
				ob_start();
				echo self::$prefix; 
				switch( $level)
				{
					case LogLevel::Debug: echo "[DEBUG]"; break;
					case LogLevel::Info: echo "[INFO]"; break;
					case LogLevel::Warn: echo "[WARN]"; break;
					case LogLevel::Error: echo "[ERROR]"; break;
					case LogLevel::Critical: echo "[CRITICAL]"; break;
				}
				
				if( is_object( $msg ) || is_array( $msg ) )
					print_r( $msg );
				else
 					echo $msg;
				error_log( ob_get_clean() );
			}
		}


		/**
		 * Log writer
		 */
		public static function error( $msg ) {
			self::log( LogLevel::Error, $msg );
		}

		/**
		 * Log writer
		 */
		public static function warn( $msg ) {
			self::log( LogLevel::Warn, $msg );
		}
		/**
		 * Log writer
		 */
		public static function info( $msg ) {
			self::log( LogLevel::Info, $msg );
		}

		/**
		 * Log writer
		 */
		public static function debug( $msg ) {
			self::log( LogLevel::Debug, $msg );
		}

		/**
		 * Log writer
		 */
		public static function critical( $msg ) {
			self::log( LogLevel::Critical, $msg );
		}
	}	
}

?>