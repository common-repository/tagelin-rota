<?php
/**
 * Rota Schedule View Templare
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 ?>

	<?php
	//		
	?>


	<div class="tagelin-rota-schedule-view-event">


		<div class="tagelin-rota-schedule-view-time">${format_time}</div>  
		<div class="tagelin-rota-schedule-view-name">${name}</div>
		<div class="tagelin-rota-schedule-view-location">${location}</div>  
		<div class="tagelin-rota-schedule-view-description">${description}</div>  
	

		<div class="tagelin-rota-schedule-view-details">

			{{tmpl(detail) '.tagelin-rota-view-detail-template'}}

			{{each detail}}

			{{/each}}

		</div>

	</div>

 
