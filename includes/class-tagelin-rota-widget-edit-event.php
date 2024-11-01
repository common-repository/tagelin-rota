<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widget: Edit Event
 *
 * @class 		Tagelin_Rota_Widget_Edit_Event
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_Edit_Event' ) ) {

	/**
	 *	Edit Event class
	 */
	class Tagelin_Rota_Widget_Edit_Event extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'edit-event';


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
					'title' => __('Edit Events','tagelin-rota'),
				), $attrs, 'tagelin-rota' );


			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];
			
			ob_start();




			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-event">';

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

			echo '            <input type="hidden" class="tagelin-rota-group-id"  name="group-id" value="'.$group->id.'">';
			echo '            <input type="hidden" class="tagelin-rota-edit-id"  name="id" value="">';


			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Name', 'tagelin-rota' );
			echo '              </label>';
			echo '                <input type="text" class="tagelin-rota-event-name" placeholder="--'.__("put name here",'tagelin-rota').'--"  name="name" value="">';
			echo '            </div>';

			echo '            <div class="tagelin-rota-edit-event-role"> ';
			echo '              <label >'.__( "Edit Event Role",'tagelin-rota' ).':</label>';
			echo '              </label>';

			echo '              <div>';
			
			echo '                <input type="hidden" class="tagelin-rota-edit-event-role-id" value="0"/>';
			echo '                <input type="hidden" class="tagelin-rota-edit-event-role-local-id" value="0"/>';


			echo '                <div class="tagelin-rota-entry">';
			echo '                  <label class="tagelin-rota-entry-label" >'.__( 'Role', 'tagelin-rota' );
			echo '                  </label>';

			echo '                    <select class="tagelin-rota-event-role-list">';
			echo '                    </select>';
//			echo '                  </label>';
			echo '                </div>';

			echo '                <div class="tagelin-rota-entry">';
			echo '                  <label class="tagelin-rota-entry-label" >'.__( 'RoleName', 'tagelin-rota' );
			echo '                  </label>';

			echo '                    <input type="text" class="tagelin-rota-edit-event-role-name" placeholder="--optional name--"  name="name" value="" />';
//			echo '                  </label>';
			echo '                </div>';


			echo '                <div class="tagelin-rota-attribute-move">';
			echo '                  <button class="tagelin-rota-edit-up">&#x25B2</button>';
			echo '                  <button class="tagelin-rota-edit-down">&#x25BC</button>';


			echo '                  <button class="tagelin-rota-button-add" type="button">'.__( 'Add', 'tagelin-rota' ).'</button>';

			echo '                </div>';



			echo '              </div>';
			echo '            </div>';

			echo '          </div>';	// end tagelin-rota-detail

			echo '          <div class="tagelin-rota-attributes">';

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

				case 'get_events':
					return $this->ajax_get_events( $request, $response );
				case 'get_event':
					return $this->ajax_get_event( $request, $response );
				case 'store_event':
					return $this->ajax_store_event( $request, $response );
				case 'delete_event':
					return $this->ajax_delete_event( $request, $response );
			}
		}

		public function ajax_get_events($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];

			$events=$this->database->get_all_event( $group_id );

			$response['data']=$events;

			return $response;
		}

		public function ajax_get_event($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$event_id=$request['event_id'];

			$events=$this->database->get_event( $event_id );

			$roles=$this->database->get_event_roles( $event_id );
			
			$event =  get_object_vars( $events[0] );
			$event['roles']=$roles;

			$response['data']=$event;

			return $response;
		}


		public function ajax_store_event($request, $response){
			$event_id = 0;
			$response['status']=true;

			$event = $request['event'];

			if( ! isset( $event['roles'] ) ) {
				$event['roles'] = array();
			}


			if ( isset($event['id']) && ($event['id'] > 0 ) )
			{
				// update
				$this->database->update_event( 
					array( 
						'id' => $event['id']
					),
					array(
						'group_id' =>$event['group_id'],
						'name'  => $this->decode_json( $event['name'] )
					) 
				);
				$event_id = $event['id'];
			}
			else
			{			
				$event_id = $this->database->insert_event( 
					array(
						'group_id' => $event['group_id'],
						'name'  => $this->decode_json( $event['name'] )
					) 
				);
			}

			$response['data']=$event_id;


			foreach ( $event['roles'] as $role ) {
				$role['localname'] = $this->decode_json( $role['localname'] );
			}

			// wipe old roles and set new ones		
 			$this->database->set_event_roles(
					array(
						'id' => $event_id,
						'group_id' => $event['group_id'],
						'roles'  => $event['roles']
					) 
			);

			return $response;
		}



		public function ajax_delete_event($request, $response){
			$event_id = 0;
			$response['status']=true;

			$event = $request['event'];
			if ( isset($event['id']) && ($event['id'] > 0 ) )
			{
				// mark as deleted
				$this->database->update_event( 
					array( 
						'id' => $event['id'],
						'group_id' =>$event['group_id']
					),
					array(
						'deleted' => 1
					) 
				);

			}

			$response['data']=$event_id;

			return $response;
		}


	}	
}

?>