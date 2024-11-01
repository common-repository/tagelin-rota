<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widget: Edit Person
 *
 * @class 		Tagelin_Rota_Widget_Edit_Person
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_Edit_Person' ) ) {

	/**
	 *	Edit Person class
	 */
	class Tagelin_Rota_Widget_Edit_Person extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'edit-person';


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
					'title' => __('Edit Staff','tagelin-rota'),
				), $attrs, 'tagelin-rota' );


			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];

			ob_start();

			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-person">';

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
			echo '              <div class="tagelin-rota-entry-msg tagelin-rota-person-name-missing">';
			echo '                    '.__( 'Must have a name', 'tagelin-rota' );
			echo '              </div> '; 
			echo '                <input type="text" class="tagelin-rota-person-name" placeholder="--'.__( "put name here", 'tagelin-rota' ).'--"  name="name" value="">';
			echo '            </div>';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Email Address', 'tagelin-rota' );
			echo '              </label>';
			echo '                <input type="text" class="tagelin-rota-person-email" placeholder="--'.__("put email address here", 'tagelin-rota').'--"  name="name" value="">';
			echo '            </div>';

			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Telephone', 'tagelin-rota' );
			echo '              </label>';
			echo '                <input type="text" class="tagelin-rota-person-telephone" placeholder="--'.__("put telephone number here",'tagelin-rota').'--"  name="name" value="">';
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
			echo '        <button class="tagelin-rota-button-new" id="new-role-button" type="button">'.__( 'New', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-save" id="save-role-button" type="button">'.__( 'Save', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-cancel" id="cancel-role-button" type="button">'.__( 'Cancel', 'tagelin-rota' ).'</button>';
			echo '        <button class="tagelin-rota-button-delete" id="delete-role-button" type="button">'.__( 'Delete', 'tagelin-rota' ).'</button>';
			echo '      </div>';	// end buttons
			echo '    </div>';	// end button band
			

			echo '  </div>'; 
			echo '</div>'; // widget



			$output = ob_get_clean();
			
			return $output;
		}

		public function action($request, $response){

			switch( $request['request'] ){

				case 'get_persons':
					return $this->ajax_get_persons( $request, $response );
				case 'get_persons_for_role':
					return $this->ajax_get_persons_for_role( $request, $response );
				case 'get_person':
					return $this->ajax_get_person( $request, $response );
				case 'store_person':
					return $this->ajax_store_person( $request, $response );
				case 'delete_person':
					return $this->ajax_delete_person( $request, $response );

			}
		}

		public function ajax_get_persons($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];

			$persons=$this->database->get_all_person( $group_id );

			$response['data']=$persons;

			return $response;
		}

		public function ajax_get_persons_for_role($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$role_id=$request['role_id'];

			$persons=$this->database->get_person_for_role( $group_id, $role_id );

			$response['data']=$persons;

			return $response;
		}


		public function ajax_get_person($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$person_id=$request['person_id'];

			$persons=$this->database->get_person( $person_id );

			$roles=$this->database->get_person_roles( $person_id );
			
			$person =  get_object_vars( $persons[0] );
			$person['roles']=$roles;

			$response['data']=$person;

			return $response;
		}


		public function ajax_store_person($request, $response){
			$person_id = 0;
			$response['status']=true;

			$person = $request['person'];


			if( ! isset( $person['roles'] ) ) {
				$person['roles'] = array();
			}

			if ( isset($person['id']) && ($person['id'] > 0 ) )
			{
				// update
				$this->database->update_person( 
					array( 
						'id' => $person['id']
					),
					array(
						'group_id' =>$person['group_id'],
						'name'  => $this->decode_json($person['name']),
						'email'  => $this->decode_json($person['email']),
						'telephone'  => $this->decode_json($person['telephone'])
					) 
				);
				$person_id = $person['id'];
			}
			else
			{			
				$person_id = $this->database->insert_person( 
					array(
						'group_id' => $person['group_id'],
						'name'  => $this->decode_json($person['name']),
						'email'  => $this->decode_json($person['email']),
						'telephone'  => $this->decode_json($person['telephone'])
					) 
				);
			}

			$response['data']=$person_id;


			// wipe old roles and set new ones
 			$this->database->set_person_roles(
					array(
						'id' => $person_id,
						'group_id' => $person['group_id'],
						'roles'  => $person['roles']
					) 
			);
			

			return $response;
		}

		
		public function ajax_delete_person($request, $response){
			$person_id = 0;
			$response['status']=true;
 
			$person = $request['person'];
			if ( isset($person['id']) && ($person['id'] > 0 ) )
			{
				// mark as deleted
				$this->database->update_person( 
					array( 
						'id' => $person['id'],
						'group_id' =>$person['group_id']
					),
					array(
						'deleted' => 1
					) 
				);

			}

			$response['data']=$person_id;

			return $response;
		}



	}	
}

?>