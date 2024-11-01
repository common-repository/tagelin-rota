<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

if ( !class_exists( 'Tagelin_Rota_Admin' ) ) {

include_once( dirname(__FILE__).'/class-tagelin-rota-admin-tab.php' );
include_once( dirname(__FILE__).'/class-tagelin-rota-admin-general.php' );

	class Tagelin_Rota_Admin {

		private $options;
		private $settings;

		private $tabs;

		/**
		 * Constructor
		 */
		public function __construct( $settings ) 	{
			$this->settings = $settings;
		}


		/**
		 *	Init admin page
		 */
		public function init() {

			add_action( 'current_screen', array( $this, 'load_screen_hooks' ) );


			$menu_title = __( 'Rota', 'tagelin-rota' );

			$menu_settings_label= __( "Settings" , 'tagelin-rota' );
			$menu_faq_label= __(  'FAQ' , 'tagelin-rota' );
			$page_title = __( 'Rota Admin' , 'tagelin-rota' );
			$page_settings_title = __( 'Rota - Settings' , 'tagelin-rota' );
			$page_faq_title = __( 'Rota - FAQ' , 'tagelin-rota' );


			// Add new Menu Item
			add_menu_page( $page_title, $menu_title, 
				'manage_options', 
				'tagelin-rota-admin',
				array( $this,'page_admin' ) 
			);

			// Submenu for Settings
			add_submenu_page( 'tagelin-rota-admin', $page_settings_title, $menu_settings_label, 
				'manage_options', 
				'tagelin-rota-admin',
				array( $this,'page_settings' ) 
			);	

			// Submenu for FAQ
			add_submenu_page( 'tagelin-rota-admin', $page_faq_title, $menu_faq_label, 
				'manage_options', 
				'tagelin-rota-faq',
				array( $this,'page_faq' ) 
			);

			// Tabbed Panels:
			$this->tabs = array();

			$this->tabs['general_options' ] = new Tagelin_Rota_Admin_General( 
								'General',
								$this->settings,
								'tagelin-rota-general',  
								'general-setting-section',
								array( $this,'check_options' )   );
		}



		/**
		 * Sanitize setting fields
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function check_options( $input ){
			foreach ( $this->tabs as $tab_id=>$tab )
			{
				$options = $this->settings->get_options();
				$options = $tab -> process_post_options( $options, $input );
				$this->settings->set_options( $options );
			}
			return $options;
		}


		function page_admin(){
		}

		function page_settings(){
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
	
			
			<?php

				$active_tab = 'general_options';
				$current_tab=null;
				if( isset( $_GET[ 'tab' ] ) ) {
					$active_tab = $_GET[ 'tab' ];
				}

			?>
	
	
			<h2 class="nav-tab-wrapper">

			<?php 
				foreach ( $this->tabs as $tab_id=>$tab ){

					printf( '<a href="?page=tagelin-rota-admin&tab=%s" ' , $tab_id );
					printf( 'class="nav-tab %s">', ($active_tab == $tab_id) ? 'nav-tab-active' : '' );
					printf( '%s', __( $tab->get_title(), 'tagelin-rota' ) );
					printf( '</a>');

					if( $active_tab == $tab_id ){
						$current_tab = $tab;
					}

				}
				?>
			</h2>

	
			<form method="post" action="options.php">

			<?php
				settings_fields( $current_tab->get_page() );   
				do_settings_sections( $current_tab->get_page() );
			?>

			<?php submit_button(); ?>

			</form>
		</div>

		<?php
		}

		function page_faq(){
			echo '<h2>FAQ</h2>';
			echo '<h3>FAQ</h3>';
			echo '<ul>';
			echo '<li> </li>\r\n';
			echo '</ul>';
		}
	

	
		/**
		 * Add the hooks for installing scripts and styles
		 */
		public function load_screen_hooks(){
			$screen = get_current_screen();
			add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_styles' ) );
		}

		/**
		 * Add the styles here
		 */
		public function add_styles(){
		}
		
		/**
		 * Add the scripts here
		 */
		public function add_scripts(){		
		}

	}


}


?>