<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Widget: Edit Role
 *
 * @class 		Tagelin_Rota_Widget_Edit_Role
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Widget_Edit_Role' ) ) {

	/**
	 *	Edit Role Class
	 */
	class Tagelin_Rota_Widget_Edit_Role extends Tagelin_Rota_Abstract_Widget {

		private $settings;
		private $database;
		const widget_type = 'edit-role';


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
					'title' => __('Edit Roles','tagelin-rota'),
				), $attrs, 'tagelin-rota' );

			
			$groups=$this->database->search_group( array( 'name' => $attrs['group'] ) );
			$group=$groups[0];

			ob_start();

			echo '<h2 class="tagelin-rota-heading">'.$attrs['title'].'</h2>';

			echo '<div class="tagelin-rota-widget">';
			echo '  <div class="tagelin-rota-role">';

			echo '    <div class="tagelin-rota-panel">';

			echo '      <div class="tagelin-rota-list">';

			echo '      <div class="tagelin-rota-list-content">';

			echo '        <table>';
			echo '          <tbody>';
			echo '          <tr><td>&nbsp</td></tr>';
			echo '          </tbody>';
			echo '        </table> ';
			
			echo '      </div>'; // tagelin-rota-list-content

			echo '      </div>'; // tagelin-rota-list


			echo '      <div class="tagelin-rota-edit">';

			echo '        <div class="tagelin-rota-edit-content">';
			echo '          <div class="tagelin-rota-detail">';


			echo '            <input type="hidden" class="tagelin-rota-group-id"  name="group-id" value="'.$group->id.'">';
			echo '            <input type="hidden" class="tagelin-rota-edit-id" name="id" value="">';


			echo '            <div class="tagelin-rota-entry">';
			echo '              <label class="tagelin-rota-entry-label" >'.__( 'Name', 'tagelin-rota' );
			echo '              </label>';

			echo '                <div class="tagelin-rota-entry-msg tagelin-rota-role-name-missing">';
			echo '                    '.__( 'Must have a name', 'tagelin-rota' );
			echo '                </div> '; 

			echo '                <input type="text" class="tagelin-rota-role-name" placeholder="--'.__( 'put name here', 'tagelin-rota' ).'--"  name="name" value="">';
			echo '            </div>';
			echo '          </div>';	// tagelin-rota-detail
			echo '        </div>'; 
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
			
			echo '  </div>'; // role
			echo '</div>'; // widget

			$output = ob_get_clean();
			
			return $output;
		}

		public function action($request, $response){

			switch( $request['request'] ){

				case 'get_role':
					return $this->ajax_get_role( $request, $response );
				case 'get_roles':
					return $this->ajax_get_roles( $request, $response );
				case 'store_role':
					return $this->ajax_store_role( $request, $response );
				case 'delete_role':
					return $this->ajax_delete_role( $request, $response );

			}
		}

		public function ajax_get_role($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];
			$role_id=$request['role_id'];

			$roles=$this->database->get_role( $group_id, $role_id );

			$role =  get_object_vars( $roles[0] );
		
			$response['data']=$role;

			return $response;
		}

		public function ajax_get_roles($request, $response){
			$response['status']=true;
			$group_id=$request['group_id'];

			$roles=$this->database->get_all_role( $group_id );
			
			$response['data']=$roles;

			return $response;
		}


		public function ajax_store_role($request, $response){
			$role_id = 0;
			$response['status']=true;

			$role = $request['role'];

			if ( isset($role['id']) && ($role['id'] > 0 ) )
			{
				// update
				$this->database->update_role( 
					array( 
						'id' => $role['id']
					),
					array(
						'group_id' =>$role['group_id'],
						'name'  => $this->decode_json( $role['name'] )
					) 
				);
				$role_id = $role['id'];
			}
			else
			{			
				$role_id = $this->database->insert_role( 
					array(
						'group_id' => $role['group_id'],
//						'name'  => $role['name']
						'name'  => $this->decode_json( $role['name'] )
					) 
				);
			}

			$response['data']=$role_id;

			return $response;
		}



		public function ajax_delete_role($request, $response){
			$role_id = 0;
			$response['status']=true;
 
			$role = $request['role'];
			if ( isset($role['id']) && ($role['id'] > 0 ) )
			{
				// mark as deleted
				$this->database->delete_role( 
					array( 
						'id' => $role['id'],
						'group_id' =>$role['group_id']
					)
				);

			}

			$response['data']=$role_id;

			return $response;
		}

	}	
}

?>