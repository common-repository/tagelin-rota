<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}


/**
 * Widget: View Schedule
 *
 * @class 		Tagelin_Rota_Widget_View_Schedule
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_View_Schedule' ) ) {


	/**
	 *	View Schedule Class
	 */
	class Tagelin_Rota_Widget_View_Schedule extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'view-schedule';


		/**
		 * Constructor
		 */
		public function __construct( $settings, $database ) 	{
			$this->settings = $settings;
			$this->database = $database;
		}

		function get_type() {
			return self::widget_type;
		}

		function view( $attrs ) {

			// Configure defaults and extract the attributes into variables
			$attrs = shortcode_atts(
				array(
					'group' => '_default',
					'type'  => '',
					'title' => '',
					'edit'  => 'no',
					'range-from' => '-0m',
					'range-to' => '+0f',
					'range-selector' => '',
					'region' => 'en',
					'date-format' => 'D d M yy',
					'time-format' => 'HHs:MM'
				), $attrs, 'tagelin-rota' );


			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];


			ob_start();

			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-schedule-view" >';
			echo '    <div class="tagelin-rota-config" >';
			echo '      <input type="hidden" class="tagelin-rota-range-from" value="'.$attrs['range-from'].'">';
			echo '      <input type="hidden" class="tagelin-rota-range-to" value="'.$attrs['range-to'].'">';
			echo '      <input type="hidden" class="tagelin-rota-range-selector"   value="'.$attrs['range-selector'].'">';
			echo '      <input type="hidden" class="tagelin-rota-region" value="'.$attrs['region'].'">';
			echo '      <input type="hidden" class="tagelin-rota-date-format"  value="'.$attrs['date-format'].'">';
			echo '      <input type="hidden" class="tagelin-rota-time-format"  value="'.$attrs['time-format'].'">';
			echo '    </div>';


			echo '    <div class="tagelin-rota-month-picker" >';
			echo '    </div>';

			echo '    <div class="tagelin-rota-detail">'; 

			echo '      <input class="tagelin-rota-group-id"  name="group-id" value="'.$group->id.'" type="text">';

			echo '      <input type="hidden" class="tagelin-rota-edit-id"  name="id" value="0">';
			echo '    </div>'; //detail


			echo '    <div class="tagelin-rota-schedule-view-list">';
			echo '    </div>';
			echo '  </div>';

			echo '  <script class="tagelin-rota-view-template" type="text/x-jquery-tmpl">';
		 	Tagelin_Rota_Instance()->get_template( 'template_rota_schedule_view.php', array() );
			echo '  </script>';

			echo '  <script class="tagelin-rota-view-detail-template" type="text/x-jquery-tmpl">';
		 	Tagelin_Rota_Instance()->get_template( 'template_rota_schedule_detail_view.php', array() );
			echo '  </script>';


			echo '</div>';  
//			echo '</div>';

			$output = ob_get_clean();
			
			return $output;
		}

		public function action($request, $response){

			switch( $request['request'] ){

				case 'get_schedules':
					return $this->ajax_get_schedules( $request, $response );
				case 'get_schedule':
					return $this->ajax_get_schedule( $request, $response );
			}
		}

		public function ajax_get_schedules($request, $response){


			$response['status']=true;
			$group_id=$request['group_id'];

			$search_from = NULL;
			$search_to = NULL;


			$view_from=$request['view_from'];
			if( $view_from != 0 ) {
				$search_from=date_create_from_format( 'U', $view_from )->format('Y-m-d H:i:s');
			}

			$view_to=$request['view_to'];
			if( $view_to != 0 ) {
				$search_to=date_create_from_format( 'U', $view_to )->format('Y-m-d H:i:s');
			}

			$schedules=$this->database->get_all_schedule( $group_id , $search_from, $search_to );

			$response['data']= [];

			foreach( $schedules as $schedule ) {
				$schedule = get_object_vars( $schedule );


				$detail=$this->database->get_schedule_detail( $schedule );
//Tagelin_Rota_Log::debug( $detail );
//				if(( ! isset( $detail['person'] ) ) ||( $detail['person'] == NULL ) ) {
//Tagelin_Rota_Log::debug( $detail );
//					$detail['person']="";
//				}
				$schedule['detail'] = $detail;
/*
				$schedule[ 'format_time' ] 
					= date_i18n( 
						get_option( 'date_format' ), 
						$schedule['timestamp'] )
						.' '.
					date_i18n( 
						get_option( 'time_format' ), 
						$schedule['timestamp'] );
*/

/*				$schedule[ 'format_time' ] 
					= date_i18n( 
						'l j F Y H:i',
						$schedule['timestamp'] );
*/
//Tagelin_Rota_Log::debug( "SCHEDULE VIEW" ); 
//Tagelin_Rota_Log::debug( $schedule );



			    $response['data'][]=$schedule;


			}

/*			$response['months']= array(
					__('Jan','tagelin-rota'), 
					__('Feb','tagelin-rota'),
					__('Mar','tagelin-rota'),
					__('Apr','tagelin-rota'),
					__('May','tagelin-rota'),
					__('Jun','tagelin-rota'),
					__('Jul','tagelin-rota'),
					__('Aug','tagelin-rota'),
					__('Sep','tagelin-rota'),
					__('Oct','tagelin-rota'),
					__('Nov','tagelin-rota'),
					__('Dec','tagelin-rota')
				);
			$response['days']= array(
					__( "Sunday","tagelin-rota"),
					__( "Monday","tagelin-rota"),
					__( "Tuesday","tagelin-rota"),
					__( "Wednesday","tagelin-rota"),
					__( "Thursday","tagelin-rota"),
					__( "Friday","tagelin-rota"),
					__( "Saturday","tagelin-rota" )
				);
*/
			return $response;
		}

		public function ajax_get_schedule($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$schedule_id=$request['schedule_id'];

			$schedules=$this->database->get_schedule( $schedule_id );
			if( count( $schedules ) > 0 ) {
				$schedule =  get_object_vars( $schedules[0] );
				$detail=$this->database->get_schedule_detail( $schedule );
				$schedule['detail']=$detail;
				$response['data']=$schedule;
			}else {
				$response['data']= null;
			}

			return $response;
		}




	}	
}

?>