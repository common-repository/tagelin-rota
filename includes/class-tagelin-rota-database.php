<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Database
 *
 * @class 		Tagelin_Rota_Database
 * @author 		Julian Dean
 */


if ( !class_exists( 'Tagelin_Rota_Database' ) ) {


	/**
	 *	Database class
	 */
	class Tagelin_Rota_Database {

		private $wpdb;

		const tagelin_rota_database_version = '1.0';
		const table_prefix = 'tagelin_rota_';
		const tablename_group = 'group';
		const tablename_person = 'person';
		const tablename_role = 'role';
		const tablename_person_role = 'person_role';
		const tablename_event = 'event';
		const tablename_event_role = 'event_role';
		const tablename_location = 'location';
		const tablename_schedule = 'schedule';
		const tablename_schedule_detail = 'schedule_detail';


		/**
		 * Constructor
		 */
		public function __construct(  ) {
			global $wpdb;
			$this->wpdb = $wpdb;
		}


		/**
		 *	Create tables
		 */
		public function create_tables(){
			$this->create_group_table();
			$this->create_person_table();
			$this->create_role_table();
			$this->create_person_role_table();
			$this->create_event_table();
			$this->create_event_role_table();
			$this->create_location_table();
			$this->create_schedule_table();
			$this->create_schedule_detail_table();
		}



		public function create_group_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_group;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					name varchar(60) NOT NULL UNIQUE,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			

				$index = "CREATE INDEX `$table_name.index.name` ON `$table_name` (`name`); ";
				$this->wpdb->query( $index );
			}

		}

		function search_group( $where ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_group;

			$query = "SELECT * FROM `$table_name` WHERE `name` = %s;";

			$this->wpdb->prepare( $query, $where['name'] );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $where['name']  ) );



			return $results;
		}

		public function create_person_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					deleted tinyint DEFAULT 0,
					name varchar(60) DEFAULT '' NOT NULL,
					email varchar(60) DEFAULT '',
					telephone varchar(60) DEFAULT '',
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );

			
				$index = "CREATE INDEX `$table_name.index.name` ON `$table_name` (`name`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.deleted` ON `$table_name` (`deleted`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );


			}
		}

		public function create_role_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					deleted tinyint DEFAULT 0,
					name varchar(60) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			

				$index = "CREATE INDEX `$table_name.index.deleted` ON `$table_name` (`deleted`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.name` ON `$table_name` (`name`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

			}

		}

		public function create_person_role_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person_role;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					person_id mediumint(9) NOT NULL,
					role_id mediumint(9) NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			

				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.person` ON `$table_name` (`person_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.role` ON `$table_name` (`role_id`); ";
				$this->wpdb->query( $index );
			}

		}

		public function create_event_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					deleted tinyint DEFAULT 0,
					name varchar(60) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			

				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.deleted` ON `$table_name` (`deleted`); ";
				$this->wpdb->query( $index );

			}
		}

		public function create_event_role_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event_role;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					event_id mediumint(9) NOT NULL,
					role_id mediumint(9) NOT NULL,
					name varchar(60) DEFAULT '',
					sequence mediumint(9) NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			
				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.event` ON `$table_name` (`event_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.role` ON `$table_name` (`role_id`); ";
				$this->wpdb->query( $index );
			}

		}

		public function create_location_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					deleted tinyint DEFAULT 0,
					name varchar(60) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			}
		}



		public function create_schedule_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
					// create table
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					name varchar(60) DEFAULT '',
					event_id mediumint(9) NOT NULL,
					location_id mediumint(9) NOT NULL,
					description varchar(max)  DEFAULT '',
					UNIQUE KEY id (id)
				) $charset_collate;";
	
				$this->wpdb->query( $sql );
			

				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.time` ON `$table_name` (`time`); ";
				$this->wpdb->query( $index );
			}
		}

		public function create_schedule_detail_table(){
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule_detail;
			$charset_collate = $this->wpdb->get_charset_collate();

			$db_check="SELECT 1 FROM `$table_name`;";
			$db_result=$this->wpdb->query( $db_check );

			if( $db_result !== FALSE) {
				// already exists
			}
			else {
				// create table

				$sql = "CREATE TABLE `$table_name` (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					group_id mediumint(9) NOT NULL,
					schedule_id mediumint(9) NOT NULL,
					event_id mediumint(9) NOT NULL,
					role_id mediumint(9) NOT NULL,
					person_id mediumint(9) DEFAULT 0,
					aux varchar(60) DEFAULT '',
					UNIQUE KEY id (id)
				) $charset_collate;";

				$this->wpdb->query( $sql );

			
				$index = "CREATE INDEX `$table_name.index.group` ON `$table_name` (`group_id`); ";
				$this->wpdb->query( $index );

				$index = "CREATE INDEX `$table_name.index.schedule` ON `$table_name` (`schedule_id`); ";
				$this->wpdb->query( $index );

			}
		}


		function insert_group( $group_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_group;

			$query = "SELECT * FROM `$table_name` WHERE `name` = %s;";
			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_data['name'] ) );

			if( count($results) > 0 )
				return 0;	// already present

			$this->wpdb->insert( $table_name,
								$group_data );

			return $this->wpdb->insert_id;
		}


		function insert_role( $role_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;

			$query = "INSERT into $table_name ( "
				." `group_id`, "
				." `name` "
				." ) VALUES ( %d, %s ) ";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$role_data['group_id'],
				$role_data['name']
				) );


			return $this->wpdb->insert_id;
		}

		function update_role( $where, $role_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;


			$query = "UPDATE $table_name SET "
				." `group_id` =%d, "
				." `name`= %s "
				." WHERE $table_name.id = %d";

		
			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$role_data['group_id'],
				$role_data['name'],
				$where['id']
				) );


		}

			
		function delete_role( $where  ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;


			$query = "UPDATE $table_name SET "
				." `deleted`= 1 "
				." WHERE $table_name.id = %d AND `group_id` =%d";

		
			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$where['id'],
				$where['group_id']				
				) );


		}

			




		function get_role( $group_id, $role_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;

			$query = "SELECT * FROM `$table_name` WHERE `group_id` = %d and `id` = %d;";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id, $role_id ) );


			return $results;
		}

		function get_all_role( $group_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;

			$query = "SELECT * FROM `$table_name` WHERE `group_id` = %d AND `deleted` = 0;";
			$this->wpdb->prepare( $query, $group_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id ) );

			return $results;
		}


		function insert_person( $person_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;

			$this->wpdb->insert( $table_name,
						$person_data );

			return $this->wpdb->insert_id;
		}

		function update_person( $where, $person_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;

			$this->wpdb->update( $table_name,
						$person_data,
						$where );
		}


		function get_person( $person_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;

			$query = "SELECT * FROM `$table_name` WHERE `id` = %d AND `deleted` = 0;";

			$this->wpdb->prepare( $query, $person_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $person_id ) );

			return $results;
		}

		function get_all_person( $group_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;

			$query = "SELECT * FROM `$table_name` WHERE `group_id` = %d AND `deleted` = 0;";

			$this->wpdb->prepare( $query, $group_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id ) );

			return $results;
		}


		function get_person_for_role( $group_id, $role_id ) {
			$person_table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;
			$person_role_table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person_role;


			$query = "SELECT $person_table_name.id as `id`, $person_table_name.name as `name`, $person_role_table_name.role_id as `role_id` "
				."FROM $person_role_table_name "
				."INNER JOIN  $person_table_name "
				."ON $person_table_name.id = $person_role_table_name.person_id "
				."WHERE "
					."$person_role_table_name.group_id = %d "
					." AND $person_role_table_name.role_id = %d"
					." AND $person_table_name.deleted = 0;";

			$this->wpdb->prepare( $query, $group_id, $role_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id, $role_id ) );

			return $results;
		}


		function get_person_roles( $person_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person_role;

			$query = "SELECT role_id as id FROM `$table_name` WHERE `person_id` = %d;";

			$this->wpdb->prepare( $query, $person_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $person_id ) );

			return $results;

		}

		function set_person_roles( $person_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person_role;


			$this->wpdb->delete( $table_name, array( 'person_id' => $person_data['id'] ) );

			foreach( $person_data['roles'] as $role) {

				$this->wpdb->insert( 
					$table_name, 
					array( 
						'person_id' => $person_data['id'], 
						'group_id' => $person_data['group_id'],
						'role_id' => $role['id'] 
					), 
					array( 
						'%d', 
						'%d',
						'%d' 
					) 
				);
			}
	

		}


		function insert_event( $event_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;


			$this->wpdb->insert( $table_name,
								$event_data );

			return $this->wpdb->insert_id;
		}

		function update_event( $where, $event_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;

			$res= $this->wpdb->update( $table_name,
								$event_data,
								$where );

		}


		function get_event( $event_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;

			$query = "SELECT * FROM `$table_name` WHERE `id` = %d AND `deleted` = 0;";

			$this->wpdb->prepare( $query, $event_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $event_id ) );

			return $results;
		}

		function get_all_event( $group_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;

			$query = "SELECT * FROM `$table_name` WHERE `group_id` = %d AND `deleted` = 0;";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id ) );

			return $results;
		}


		function get_event_roles( $event_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event_role;
			$role_table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;



			$query = "SELECT $table_name.id as id, role_id, sequence, $role_table_name.name as name, $table_name.name as localname FROM $table_name "
				."INNER JOIN  $role_table_name "
				."ON $role_table_name.id = $table_name.role_id "
				."WHERE `event_id` = %d  AND $role_table_name.deleted = 0  ORDER BY `sequence`;";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $event_id ) );

			return $results;

		}

		function set_event_roles( $event_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event_role;

			// get current result set:
			$previous_resultset = [];
			$query = "SELECT id,name, role_id FROM `$table_name` WHERE `event_id` = %d;";
			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $event_data['id'] ) );
			foreach ( $results as $result ){
				$previous_resultset[$result->id]=$result->role_id;
			}

			foreach( $event_data['roles'] as $role) {


				if(( $role['active'] == "true" ) && ( $role['role_id'] != 0 ))	{
				
					if( $role['id'] == 0 ) {

						// insert new
						$this->wpdb->insert( 
							$table_name, 
							array( 
								'event_id' => $event_data['id'], 
								'group_id' => $event_data['group_id'],
								'role_id' => $role['role_id'],
								'name' => $role['localname'],
								'sequence' => $role['sequence'],
							), 
						
							array( 
								'%d', 
								'%d',
								'%d',
								'%s',
								'%d'
							) 
						);
					} else {

						// update existing
						$this->wpdb->update( 
							$table_name, 
							array( 
								'event_id' => $event_data['id'], 
								'group_id' => $event_data['group_id'],
								'role_id' => $role['role_id'],
								'name' => $role['localname'],
								'sequence' => $role['sequence'],
							), 
							array( 'id' => $role['id'] ),
							array( 
								'%d', 
								'%d',
								'%d',
								'%s',
								'%d'
							) 
						);

						// remove from previous set list
						unset(	$previous_resultset[$role['id']] );
					}

				}
			}
	
			// anything remaining in previous_resultset has been removed:
			foreach ($previous_resultset as $id => $result) {
				$this->wpdb->delete( $table_name, array( 'id' => $id ) );
			}


		}



		function insert_location( $location_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;

			$this->wpdb->insert( $table_name,
							$location_data );

			return $this->wpdb->insert_id;
		}

		function update_location( $where, $location_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;

			$this->wpdb->update( $table_name,
								$location_data,
								$where );

		}


		function get_location( $location_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;

			$query = "SELECT * FROM `$table_name` WHERE `id` = %d AND `deleted` = 0;";
			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $location_id ) );

			return $results;
		}

		function get_all_location( $group_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;

			$query = "SELECT * FROM `$table_name` WHERE `group_id` = %d AND `deleted` = 0 ;";
			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id ) );

			return $results;
		}



		function get_schedule( $schedule_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;
			$location_table = $this->wpdb->prefix . self::table_prefix . self::tablename_location;
			$event_table = $this->wpdb->prefix . self::table_prefix . self::tablename_event;


			$query = "SELECT $table_name.id as `id`,"
				." $table_name.group_id as `group_id`,"
				." UNIX_TIMESTAMP($table_name.time) as `timestamp`,"
				." UNIX_TIMESTAMP($table_name.until) as `until`,"
				." $table_name.name as `name`,"
				." $table_name.description as `description`,"
				." $table_name.location_id as `location_id`,"
				." $location_table.name as `location`,"
				." $table_name.event_id as `event_id`,"
				." $event_table.name as `event`"
				." FROM $table_name "
				. " LEFT JOIN $location_table ON $location_table.id = $table_name.location_id "
				. " LEFT JOIN $event_table ON $event_table.id = $table_name.event_id "
				. " WHERE $table_name.id = %d ;";

			$this->wpdb->prepare( $query, $schedule_id );

			$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $schedule_id ) );

			return $results;
		}

		function get_all_schedule( $group_id, $search_from = NULL, $search_to = NULL ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;
			$location_table = $this->wpdb->prefix . self::table_prefix . self::tablename_location;
			$event_table = $this->wpdb->prefix . self::table_prefix . self::tablename_event;

			$query = "SELECT $table_name.id as `id`,"
				." $table_name.group_id as `group_id`,"
				." UNIX_TIMESTAMP($table_name.time) as `timestamp`,"
				." UNIX_TIMESTAMP($table_name.until) as `until`,"
				." $table_name.name as `name`,"
				." $table_name.location_id as `location_id`,"
				." $table_name.description as `description`,"
				." $location_table.name as `location`,"
				." $table_name.event_id as `event_id`,"
				." $event_table.name as `event`"
				." FROM $table_name "
				." LEFT JOIN $location_table ON $location_table.id = $table_name.location_id "
				." LEFT JOIN $event_table ON $event_table.id = $table_name.event_id ";
				
			$sort = " ORDER BY $table_name.time ";


			if( $search_from ) {
				if( $search_to ) {
					$query = $query." WHERE $table_name.group_id = %d AND $table_name.time >= %s AND $table_name.time <= %s ".$sort.";";
;
					$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id, $search_from, $search_to ) );
				}else {
					$query = $query."  WHERE $table_name.group_id = %d AND $table_name.time >= %s ".$sort.";";
					$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id, $search_from ) );
				}
			} else {
				$query = $query."  WHERE $table_name.group_id = %d ".$sort." ;";

				$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $group_id ) );
 			}


			return $results;
		}


		function insert_schedule( $schedule_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;
	
			$query = "INSERT INTO $table_name "
				." (`group_id`, `time`, `until`, `name`, `event_id`, `location_id`,`description` ) "
				." VALUES ( %d, FROM_UNIXTIME(%d),FROM_UNIXTIME(%d), %s, %d, %d, %s  )";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$schedule_data['group_id'],
				$schedule_data['timestamp'],
				$schedule_data['until'],
				$schedule_data['name'], 
				$schedule_data['event_id'],
				$schedule_data['location_id'],
				$schedule_data['description']
				) );

			return $this->wpdb->insert_id;
		}


		function update_schedule( $id, $schedule_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;


			$query = "UPDATE $table_name SET "
				." `group_id` =%d, "
				." `time` = FROM_UNIXTIME(%d), "
				." `until` = FROM_UNIXTIME(%d), "
				." `name`= %s, "
				." `event_id` = %d, "
				." `location_id` = %d,"
				." `description` = %s "
				." WHERE $table_name.id = %d";

		
			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$schedule_data['group_id'],
				$schedule_data['timestamp'],
				$schedule_data['until'],
				$schedule_data['name'],
				$schedule_data['event_id'],
				$schedule_data['location_id'],
				$schedule_data['description'],
				$id
				) );


		}


		function delete_schedule( $id, $schedule_data ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;

			$query = "DELETE FROM $table_name "
				." WHERE $table_name.id = %d AND $table_name.group_id = %d";
		
			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$id,
				$schedule_data['group_id']
				) );


		}



		function get_schedule_detail( $schedule ) {
			$schedule_detail_table = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule_detail;
			$event_role_table = $this->wpdb->prefix . self::table_prefix . self::tablename_event_role;
			$role_table = $this->wpdb->prefix . self::table_prefix . self::tablename_role;
			$person_table = $this->wpdb->prefix . self::table_prefix . self::tablename_person;

			$query = "SELECT $schedule_detail_table.id as id,"
				." $event_role_table.sequence as sequence,"
				." $schedule_detail_table.event_id as event_id,"
				." $schedule_detail_table.aux as aux,"
				." $event_role_table.role_id as role_id,"
				." $role_table.name as role_name,"
				." $role_table.deleted as role_deleted,"
				." $event_role_table.id as event_role_id,"
				." $event_role_table.name as event_role_name,"
				." $person_table.id as person_id,"
				." $person_table.name as person"
				." FROM $event_role_table "
				. " LEFT JOIN $role_table ON $role_table.id = $event_role_table.role_id "
				. " LEFT JOIN $schedule_detail_table "
				. " ON $schedule_detail_table.role_id = $event_role_table.id "
				. " AND $schedule_detail_table.schedule_id = %d   " /*schedule.id */

				. " AND $schedule_detail_table.event_id = %d " /* event_id */


				. " LEFT JOIN $person_table "
				. " ON $person_table.id =  $schedule_detail_table.person_id "
				. " WHERE $event_role_table.event_id = %d "
				. " ORDER by `sequence` ";


			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
					$query, 
					$schedule['id'], 
					$schedule['event_id'],
					$schedule['event_id']
					) );

	
			return $results;

		}


		function clean_schedule_detail( $group_id, $schedule_id , $event_id, $event_role_id ) {
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule_detail;

			// wipe old settings
			$erase_query = "DELETE from $table_name WHERE `schedule_id` = %d AND `event_id` = %d AND `role_id` = %d";
			$erase_results=$this->wpdb->get_results( 
					$this->wpdb->prepare( $erase_query, $schedule_id, $event_id, $event_role_id ) );

		}



		function update_schedule_detail( $group_id, $schedule_id, $event_id, $schedule_detail_data ) {

			$this->clean_schedule_detail( $group_id, 
					$schedule_id , 
					$event_id, 
					$schedule_detail_data['event_role_id'] ) ;


			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule_detail;

			$query = "INSERT into $table_name ( "
				." `group_id`, "
				." `schedule_id`, "
				." `event_id`, "
				." `role_id`, "
				." `person_id`, "
				." `aux` "
				." ) VALUES ( %d, %d, %d, %d, %d, %s ) ";

			$results = $this->wpdb->get_results( $this->wpdb->prepare( 
				$query, 
				$group_id,
				$schedule_id,
				$event_id,
				$schedule_detail_data['event_role_id'],
				$schedule_detail_data['person_id'],
				$schedule_detail_data['aux']
				) );

		}

		/**
		 *	Delete tables
		 */
		public function delete_tables(){

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_group;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );
			
			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_role;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_person_role;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_event_role;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_location;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

			$table_name = $this->wpdb->prefix . self::table_prefix . self::tablename_schedule_detail;
			$sql = "DROP TABLE IF EXISTS $table_name ";
			$this->wpdb->query( $sql );

		}



	}



}

?>