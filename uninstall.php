<?php
 
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

include_once( 'tagelin-rota.php' );
$instance = Tagelin_Rota_Instance();
$instance->delete();

?>
