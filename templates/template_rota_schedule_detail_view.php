<?php
/**
 * Rota Schedule Detail View Templare
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 ?>

	<?php
	//		
	?>

{{if person != "" }}
	<div class="tagelin-rota-schedule-view-detail">

		{{if event_role_name != ""}}
			<div class="tagelin-rota-schedule-view-role">${event_role_name}</div> 
		{{else}}
			<div class="tagelin-rota-schedule-view-role">${role_name}</div> 
		{{/if}}
	
		<div class="tagelin-rota-schedule-view-person">${person}</div>
		<div class="tagelin-rota-schedule-view-aux">${aux}</div>

	</div>
{{/if}}

	


 
