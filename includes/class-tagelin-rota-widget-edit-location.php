<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widget: Event Location
 *
 * @class 		Tagelin_Rota_Widget_Edit_Location
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_Edit_Location' ) ) {


	/**
	 *	Edit Location class
	 */
	class Tagelin_Rota_Widget_Edit_Location extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'edit-location';


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
					'title' => __('Edit Locations','tagelin-rota'),
				), $attrs, 'tagelin-rota' );


			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];

			
			ob_start();


			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-location">';

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

			echo '            <input type="hidden" class="tagelin-rota-group-id" name="group-id" value="'.$group->id.'">';
			echo '            <input type="hidden" class="tagelin-rota-edit-id" name="id" value="">';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Name', 'tagelin-rota' );
			echo '              </label>';

			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-location-name-missing">';
			echo '                    '.__( 'Must have a name', 'tagelin-rota' );
			echo '              </div> '; 


			echo '                <input type="text" class="tagelin-rota-location-name" placeholder="--'.__("put name here",'tagelin-rota').'--"  name="name" value="">';
			echo '            </div>';

			echo '          </div>';	// end tagelin-rota-detail


			echo '        </div>'; // tagelin-rota-edit-content

			echo '      </div>';	// tagelin-rota-edit

			echo '    </div>';	// tagelin-rota-panel

			echo '    <div class="tagelin-rota-button-band">';
			echo '      <div class="tagelin-rota-buttons">';
			echo '        <button class="tagelin-rota-button-new"  type="button">'.__( 'New', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-save"  type="button">'.__( 'Save', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-cancel" type="button">'.__( 'Cancel', 'tagelin-rota' ).'</button>';
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

				case 'get_locations':
					return $this->ajax_get_locations( $request, $response );
				case 'get_location':
					return $this->ajax_get_location( $request, $response );
				case 'store_location':
					return $this->ajax_store_location( $request, $response );
				case 'delete_location':
					return $this->ajax_delete_location( $request, $response );

			}
		}

		public function ajax_get_locations($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];

			$locations=$this->database->get_all_location( $group_id );

			$response['data']=$locations;

			return $response;
		}

		public function ajax_get_location( $request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$location_id=$request['location_id'];

			$locations=$this->database->get_location( $location_id );

			$location =  get_object_vars( $locations[0] );

			$response['data']=$location;

			return $response;
		}


		public function ajax_store_location($request, $response){
			$location_id = 0;
			$response['status']=true;

			$location = $request['location'];

			if ( isset($location['id']) && ($location['id'] > 0 ) )
			{
				// update
				$this->database->update_location( 
					array( 
						'id' => $location['id']
					),
					array(
						'group_id' =>$location['group_id'],
						'name'  => $this->decode_json( $location['name'] )
					) 
				);
				$location_id = $location['id'];
			}
			else
			{			
				$location_id = $this->database->insert_location( 
					array(
						'group_id' => $location['group_id'],
						'name'  => $this->decode_json( $location['name'] )
					) 
				);
			}

			$response['data']=$location_id;


			return $response;
		}


		public function ajax_delete_location($request, $response){
			$location_id = 0;
			$response['status']=true;
 
			$location = $request['location'];
			if ( isset($location['id']) && ($location['id'] > 0 ) )
			{
				// mark as deleted
				$this->database->update_location( 
					array( 
						'id' => $location['id'],
						'group_id' =>$location['group_id']
					),
					array(
						'deleted' => 1
					) 
				);

			}

			$response['data']=$location_id;

			return $response;
		}



	}	
}

?>