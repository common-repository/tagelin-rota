<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widget: Edit Schedule
 *
 * @class 		Tagelin_Rota_Widget_Edit_Schedule
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_Edit_Schedule' ) ) {

	/*
	 *	Edit Schedule class
	 */
	class Tagelin_Rota_Widget_Edit_Schedule extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'edit-schedule';


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
					'group' => '',
					'type'  => '',
					'title' => '',
					'edit'  => 'no',
					'range-from' => '-0f',
					'range-to' => '+0f',
					'range-selector' => '',
					'region' => 'en',
					'date-format' => 'D d M yy',
					'list-date-format' => 'd M yy'
				), $attrs, 'tagelin-rota' );


			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];


			ob_start();

			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-schedule">';


			echo '     <div class="tagelin-rota-config" >';

			echo '       <input type="hidden" class="tagelin-rota-range-from" value="'.$attrs['range-from'].'">';
			echo '       <input type="hidden" class="tagelin-rota-range-to" value="'.$attrs['range-to'].'">';
			echo '       <input type="hidden" class="tagelin-rota-range-selector"   value="'.$attrs['range-selector'].'">';
			echo '       <input type="hidden" class="tagelin-rota-region"  value="'.$attrs['region'].'">';

			echo '       <input type="hidden" class="tagelin-rota-list-date-format"   value="'.$attrs['list-date-format'].'">';

			echo '       <input type="hidden" class="tagelin-rota-date-format"   value="'.$attrs['date-format'].'">';

			echo '     </div>';

			echo '     <div class="tagelin-rota-month-picker" >';
			echo '     </div>';
		



			echo '    <div class="tagelin-rota-panel">';

			echo '      <div class="tagelin-rota-list">';

			echo '        <div class="tagelin-rota-list-content">';

			echo '          <table>';
			echo '            <tbody>';
			echo '            <tr><td>&nbsp</td></tr>';
			echo '            </tbody>';
			echo '          </table> ';
			
			echo '        </div>'; // tagelin-rota-list-content

			echo '      </div>'; // tagelin-rota-list

			echo '      <div class="tagelin-rota-edit">';
			echo '        <div class="tagelin-rota-edit-content">';
			echo '          <div class="tagelin-rota-detail">'; // begin tagelin-rota-detail

			echo '            <input class="tagelin-rota-group-id"  name="group-id" value="'.$group->id.'" type="text">';

								
			echo '            <input type="hidden" class="tagelin-rota-edit-id"  name="id" value="0">';



			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Name', 'tagelin-rota' );
			echo '              </label>';
			echo '                <input type="text" class="tagelin-rota-schedule-name" placeholder="--'.__("put name here",'tagelin-rota').'--"  name="name" value="">';
			echo '            </div>';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Event', 'tagelin-rota' );
			echo '              </label>';

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-event-missing">';
			echo '                    '.__( 'Must select an event', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-event-deleted">';
			echo '                    '.__( 'Event is deleted', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <select class="tagelin-rota-schedule-event">';
			echo '              </select>';
			echo '            </div>';


			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Location', 'tagelin-rota' );
			echo '              </label>';

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-location-missing">';
			echo '                    '.__( 'Must select a location', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-location-deleted">';
			echo '                    '.__( 'Location is deleted', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <select class="tagelin-rota-schedule-location">';
			echo '              </select>';
			echo '            </div>';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Date', 'tagelin-rota' );
			echo '              </label>';

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-date-missing">';
			echo '                    '.__( 'Must select a date', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-date-early">';
			echo '                    '.__( 'Too early', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-date-late">';
			echo '                    '.__( 'Too late', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-date-no-edit">';
			echo '                    '.__( 'Cannot change now', 'tagelin-rota' );
			echo '              </div> '; 

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-schedule-date-no-delete">';
			echo '                    '.__( 'Cannot delete now', 'tagelin-rota' );
			echo '              </div> '; 


			echo '                <input type="text" class="tagelin-rota-schedule-date" placeholder="--put date here--"  name="name" value="">';

			echo '                <input type="hidden" class="tagelin-rota-schedule-date-from" value="">';
			echo '                <input type="hidden" class="tagelin-rota-schedule-date-to" value="">';


//			echo '              </label>';
			echo '            </div>';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Time', 'tagelin-rota' );
			echo '              </label>';
			echo '                <div class="tagelin-rota-schedule-time">';
			echo '                  <select class="tagelin-rota-schedule-hours" >';
			echo '                  </select>';
			echo '              &nbsp:&nbsp';
			echo '                  <select  class="tagelin-rota-schedule-minutes" >';
			echo '                  </select>';
			echo '                </div>';
			echo '            </div>';


			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Until', 'tagelin-rota' );
			echo '              </label>';
			echo '                <div class="tagelin-rota-schedule-time">';
			echo '                  <select class="tagelin-rota-schedule-hours-end" >';
			echo '                  </select>';
			echo '              &nbsp:&nbsp';
			echo '                  <select class="tagelin-rota-schedule-minutes-end" >';
			echo '                  </select>';
			echo '                </div>';
			echo '            </div>';


					// description
			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Description', 'tagelin-rota' );
			echo '              </label>';
			echo '                <textarea class="tagelin-rota-schedule-description" placeholder="--'.__("put optional description here",'tagelin-rota').'--"  name="name" value="" ></textarea>';
			echo '            </div>';



			echo '          </div>';	// end tagelin-rota-detail

			echo '          <div class="tagelin-rota-attributes">';

			echo '            <input type="hidden" class="tagelin-rota-schedule-notes-label" value="'.__('Notes','tagelin-rota').'">';


			echo '            <div class="tagelin-rota-attributes-label">';
			echo '              '.__( 'Roles', 'tagelin-rota' ).'  ';
			echo '            </div>';

			echo '            <div class="tagelin-rota-attributes-content">'; 
			echo '            </div>';

			echo '          </div>';	// tagelin-rota-attributes

			echo '        </div>'; // tagelin-rota-edit-content

			echo '      </div>';	// tagelin-rota-edit

			echo '    </div>';	// tagelin-rota-panel

			echo '    <div class="tagelin-rota-button-band">';
			echo '      <div class="tagelin-rota-buttons">';
			echo '        <button class="tagelin-rota-button-new"  type="button">'.__( 'New', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-save"  type="button">'.__( 'Save', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-cancel"  type="button">'.__( 'Cancel', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-delete"  type="button">'.__( 'Delete', 'tagelin-rota' ).'</button>';
			echo '      </div>';	// end buttons
			echo '    </div>';	// end button band
			

			echo '  </div>'; 
			echo '</div>'; // widget

			$output = ob_get_clean();
			

			return $output;
		}

		public function action($request, $response){

			switch( $request['request'] ){

				case 'get_schedules':
					return $this->ajax_get_schedules( $request, $response );
				case 'get_schedule':
					return $this->ajax_get_schedule( $request, $response );
				case 'store_schedule':
					return $this->ajax_store_schedule( $request, $response );
				case 'store_schedule_details':
					return $this->ajax_store_schedule_detail( $request, $response );
				case 'delete_schedule':
					return $this->ajax_delete_schedule( $request, $response );
				case 'get_schedule_detail':
					return $this->ajax_get_schedule_detail( $request, $response );

			}
		}

		public function ajax_get_schedules($request, $response){
			$search_from = NULL;
			$search_to = NULL;
			$response['status']=true;
			$group_id=$request['group_id'];

			$view_from=$request['view_from'];
			if( $view_from != 0 ) {
				$search_from=date_create_from_format( 'U', $view_from )->format('Y-m-d H:i:s');
			}

			$view_to=$request['view_to'];
			if( $view_to != 0 ) {
				$search_to=date_create_from_format( 'U', $view_to )->format('Y-m-d H:i:s');
			}

			$schedules=$this->database->get_all_schedule( $group_id, $search_from, $search_to );

			$response['data']=$schedules;

			return $response;
		}

		public function ajax_get_schedule($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$schedule_id=$request['schedule_id'];

			$schedules=$this->database->get_schedule( $schedule_id );

			
			$schedule =  get_object_vars( $schedules[0] );

			// detect archive schedule based on current time
			if( $schedule['timestamp'] < time() ) {
				$schedule['archive'] = 1;
			}
			else {
				$schedule['archive'] = 0;
			}

			$detail=$this->database->get_schedule_detail( $schedule );

			$schedule['detail']=$detail;

			$response['data']=$schedule;

			return $response;
		}


		public function ajax_store_schedule($request, $response){
			$schedule_id = 0;
			$response['status']=true;

			$schedule = $request['schedule'];

			if( ! isset( $schedule['detail'] ) ) {
				$schedule['detail'] = array();
			}


			if ( isset($schedule['id']) && ($schedule['id'] > 0 ) )
			{

				// update
				$this->database->update_schedule( 
					$schedule['id'],
					array(
						'group_id' =>$schedule['group_id'],
						'name'  => $schedule['name'],
						'timestamp' => $schedule['timestamp'], 
						'until' => $schedule['until'], 
						'event_id' => $schedule['event_id'],
						'location_id' => $schedule['location_id'],
						'description' => $schedule['description']
					) 
				);
				$schedule_id = $schedule['id'];

			}
			else
			{			
				$schedule_id = $this->database->insert_schedule( 
					array(
						'group_id' => $schedule['group_id'],
						'name'  => $schedule['name'],
						'timestamp' => $schedule['timestamp'], 
						'until' => $schedule['until'], 
						'event_id' => $schedule['event_id'],
						'location_id' => $schedule['location_id'],
						'description' => $schedule['description']
					) 
				);
			}




			foreach( $schedule['detail'] as $schedule_details )
			{
				if( isset( $schedule_details['person_id'] ) )
				{
					$this->database->update_schedule_detail( 
						$schedule['group_id'], 
						$schedule_id, 
						$schedule['event_id'],
						$schedule_details );
				}
			}

			$response['data']=$schedule_id;


			return $response;
		}


		public function ajax_delete_schedule($request, $response){
			$schedule_id = 0;
			$response['status']=true;

			$schedule = $request['schedule'];
			if ( isset($schedule['id']) && ($schedule['id'] > 0 ) )
			{
				// delete
				$this->database->delete_schedule( 
					$schedule['id'],
					array(
						'group_id' =>$schedule['group_id']
					) 
				);
			}

			$response['data']=$schedule_id;

			return $response;
		}

		public function ajax_get_schedule_detail($request, $response){

			$response['status']=true;
			$group_id=$request['group_id'];
			$schedule_id=$request['schedule_id'];
			$event_id = $request['event_id'];

			$detail=$this->database->get_schedule_detail( 
						array( 
							'id'=> $request['schedule_id'],
							'event_id' => $request['event_id'],
							'group_id' => $request['group_id']
						) );


			$response['data']=$detail;

			return $response;
		}



		
		public function ajax_store_schedule_detail($request, $response){
			$schedule_id = 0;
			$response['status']=true;

			$schedule_id = $request['schedule_id'];
			$group_id = $request['group_id'];
			$event_id = $request['event_id'];
			$schedule_details_array = $request['schedule_details'];


			$this->database->clean_schedule_detail( $group_id, $schedule_id , $event_id );
	
	
			foreach( $schedule_details_array as $schedule_details )
			{
				if( isset( $schedule_details['person_id'] ) )
				{
					$this->database->update_schedule_detail( $group_id, $schedule_id, $event_id, $schedule_details );
				}
			}
	
			$response['data']=$schedule_id;

			return $response;
		}

		



	}	
}

?>