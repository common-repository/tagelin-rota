
// Called on document ready
jQuery(document).ready( function( $ ) {

	// Check page for widgets
	if( $( '.tagelin-rota-widget' ).length == 0 ) {
		return;	// Nothing to do
	}

	// Determine media capability
	var media = window.matchMedia( "screen and (min-width: 500px)" );
	screenLayout( media );
	media.addListener( function( media ) {
		screenLayout( media );
	} );


	// Save placeholder texts
	$( '.tagelin-rota-widget .tagelin-rota-detail .tagelin-rota-entry input' ).each( function() {
		$(this).attr('placeholdertext', $(this).attr('placeholder') );
	});

	$( '.tagelin-rota-widget .tagelin-rota-detail .tagelin-rota-entry textarea' ).each( function() {
		$(this).attr('placeholdertext', $(this).attr('placeholder') );
	});


/*
 *	R O L E    W I D G E T
 */

	// add event handlers
	addEventHandlers( { target:'.tagelin-rota-role', 
			onSelect: selectRole, 
			onEdit: editRole,
			onSave: saveRole, 
			onDelete: deleteRole, 
			onCancel: cancelRole, 
			onNew: newRole,
			formatList: defaultListFormat });




	$( '.tagelin-rota-widget .tagelin-rota-role' ).each( function () {
		getAllRole( $(this) );
	});


	/*
	 *	Display list of all Roles
	 */
	function getAllRole( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-role',
			request: 'get_roles',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				roles=response.data;
				buildList( owner, roles );
			}
		});

	}



	/*
	 *	Called when a Role is selected in the list
	 */
	function selectRole( owner, row ) {
	}

	/*
	 *	called when a new Role is created
	 */
	function newRole( owner ) {
	}

	/*
	 *	Edit this Role, get details from the server
	 */
	function editRole( owner, role ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-role',
			request: 'get_role',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			role_id: role.id
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				role=response.data;
				
				setGroupId( owner, role.group_id );
				setEditId( owner, role.id );
				setDetailText( owner, '.tagelin-rota-role-name', role.name  );

				stopWaiting( owner );

				
			}
		});
	}

	/*
	 *	Save this Role
	 */
	function saveRole( owner ) {
		var role = {};
		role.id = getEditId( owner );
		role.group_id = getGroupId( owner );
		role.name = getDetailText( owner, '.tagelin-rota-role-name'  );

		if( validateRole( owner, role ) ) {

			startWaiting( owner );

			// do write
			var request = {
				nonce: ajax_params.tagelin_rota_nonce,
				widget: 'edit-role',
				request: 'store_role',
				action: ajax_params.action,
				role: role
			}

			$.ajax({
				url: ajax_params.ajax_url,
				type: 'post',
				dataType: 'json',
				data: request,
				success: function( response ) {

					if( role.id == 0 ) {

						/* Add new Role */
						role.id = response.data;
						setEditId( owner, role.id );

						appendList( owner, role );

						$( '.tagelin-rota-widget .tagelin-rota-person'  ).each( function() {
							addPersonRole( $(this), role );
						});

						$( '.tagelin-rota-widget .tagelin-rota-event'  ).each( function() {
							addEventRole( $(this), role );
						});
					}
					else {
						updateList( owner, role );

						/* find any other role lists and update them: */
	
						// Person edit
						$( '.tagelin-rota-widget .tagelin-rota-person'  ).each( function() {
	
							if( role.group_id == getGroupId( $(this) ) ) {
								updatePersonRole( $(this), role );
							}
						});
	
						// Event Edit:
						$( '.tagelin-rota-widget .tagelin-rota-event'  ).each( function() {
	
							if( role.group_id == getGroupId( $(this) ) ) {
								updateEventRole( $(this), role );
							}
						});

					}

					selectList( owner, role.id );

					stopWaiting( owner );
				}
			});



		}
		
	}


	/*
	 *	Validation check before saving
	 */
	function validateRole( owner, role ) {

		// clear error messages
		hideDetailError( owner );

		if( ! role )
			return false;

		if( ! role['name' ] ) {
			showDetailError( owner, '.tagelin-rota-role-name-missing' );
			return false;
		}

		return true;
	}

	/*
	 *	called when a Role is cancelled
	 */
	function cancelRole( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );
		clearAttributes( owner );
		setStateIdle( owner );
	}

	/*
	 *	called when a Role is deleted
	 */
	function deleteRole( owner ) {
		var role = {};
		role.id = getEditId( owner );
		role.group_id = getGroupId( owner );

		startWaiting( owner );

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-role',
			request: 'delete_role',
			action: ajax_params.action,
			role:role
		};

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {

			},
			success: function( response ) {

				removeList( owner, role.id );
				cancelRole( owner );				



				/* Find any other role lists and remove this role: */

				// Person
				$( '.tagelin-rota-widget .tagelin-rota-person'  ).each( function() {
					if( role.group_id == getGroupId( $(this) ) ) {
						removePersonRole( $(this), role );
					}
				});


				// Events
				$( '.tagelin-rota-widget .tagelin-rota-event'  ).each( function() {
					if( role.group_id == getGroupId( $(this) ) ) {
						removeEventRole( $(this), role );
					}
				});
		


				stopWaiting( owner );

			}
		});


	}



/*
 *	P E R S O N   W I D G E T
 */

	// add event handlers
	addEventHandlers( { target:'.tagelin-rota-person', 
			onSelect: selectPerson, 
			onEdit: editPerson,
			onSave: savePerson, 
			onDelete: deletePerson, 
			onCancel: cancelPerson, 
			onNew: newPerson,
			formatList: defaultListFormat });

	// initialise list of available roles
	$( '.tagelin-rota-widget .tagelin-rota-person' ).each( function () {
		getAllPersonRole( $(this) );
	});

	// populate person list
	$( '.tagelin-rota-widget .tagelin-rota-person' ).each( function () {
		getAllPerson( $(this) );
	});


	/*
	 *	Display list of all Role valid for a Person
	 */
	function getAllPersonRole( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-role',
			request: 'get_roles',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				roles=response.data;

				// clear existing
				clearAttributes( owner );

				// add rows
				for( var i = 0; i < roles.length; i++ ) {
					addPersonRole( owner, roles[i] );
				}
			}
		});

	}


	/*
	 *	Add Person-Role to list when a role is created
	 */
	function addPersonRole( owner, role )
	{
		if( role.name.length ) {	


			var attribute = $('<div class="tagelin-rota-attribute"></div>' );
			var item = $('<div class="tagelin-rota-attribute-item"></div>' );
			var name = $('<div class="tagelin-rota-attribute-item-name"></div>' );
			var value = $('<div class="tagelin-rota-attribute-item-value"></div>' );

			name.append( escapeHtml( role.name ) );

			value.append( '<input type="checkbox" name="' 
				+ role.id
				+ '" value=""/>' );


			item.append( name );
			item.append( value );

			attribute.append( item );
			owner.find( '.tagelin-rota-attributes-content' ).append( attribute );
		}
	}


	/*
	 *	Update Person-Role list when a role is changed
	 */
	function updatePersonRole( owner, role )
	{
		owner.find( '.tagelin-rota-attribute input[name='
							+role.id
							+']').each( function() {
			var attribute = $(this).parentsUntil('.tagelin-rota-attributes-content').last();
			attribute.find( '.tagelin-rota-attribute-item-name' ).text( escapeHtml( role.name ) );

		});
	}

	/*
	 *	Remove Person-Role list when a role is deleted
	 */
	function removePersonRole( owner, role )
	{
		owner.find( '.tagelin-rota-attribute input[name='
							+role.id
							+']').each( function() {
			var attribute = $(this).parent();
			attribute.remove();

		});
	}



	/*
	 *	Display a list of all Person
	 */
	function getAllPerson( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-person',
			request: 'get_persons',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				persons=response.data;

				buildList( owner, persons );

			}
		});

	}

	/*
	 *	Called when a Person is selected in the list
	 */
	function selectPerson( owner, row ) {
	}
	
	/*
	 *	Called when a new Person is created
	 */
	function newPerson( owner ) {

		// clear all checks
		owner.find( '.tagelin-rota-attributes-content input' ).each( function () {
			this.checked = false;
		});

	}

	
	/*
	 *	Edit this Person, get details from the server
	 */
	function editPerson( owner, person ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-person',
			request: 'get_person',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			person_id: person.id
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				person=response.data;

				setGroupId( owner, person.group_id );
				setEditId( owner, person.id );
				setDetailText( owner, '.tagelin-rota-person-name',  person.name );
				setDetailText( owner, '.tagelin-rota-person-email',  person.email );
				setDetailText( owner, '.tagelin-rota-person-telephone',  person.telephone );

				// clear all checks
				owner.find( '.tagelin-rota-attributes-content input' ).each( function () {
					this.checked = false;
				});

				// for each person
				person.roles.forEach( function( role ){

					// add checks for role
                			owner.find( '.tagelin-rota-attributes-content input[name='
							+role.id
							+']')
						.each( function () {
							this.checked = true;
						});
				});
				
				stopWaiting( owner );

			}
		});
	}

	/*
	 *	Save this Person
	 */
	function savePerson( owner ) {

		var person = {};
		person.id = getEditId( owner );
		person.group_id = getGroupId( owner );
		person.name = getDetailText( owner, '.tagelin-rota-person-name'  );
		person.email = getDetailText( owner, '.tagelin-rota-person-email'  );
		person.telephone = getDetailText( owner, '.tagelin-rota-person-telephone'  );

		person.roles =  [];

		owner.find( '.tagelin-rota-attributes-content input:checked' ).each( function() {
			var role = {
				id:$(this).attr('name')		
			};
			person.roles.push( role );
		});

		if( validatePerson( owner, person ) ) {

			startWaiting( owner );

			// do write
			var request = {
				nonce: ajax_params.tagelin_rota_nonce,
				widget: 'edit-person',
				request: 'store_person',
				action: ajax_params.action,
				person: person
			}

			$.ajax({
				url: ajax_params.ajax_url,
				type: 'post',
				dataType: 'json',
				data: request,
				success: function( response ) {

					if( person.id == 0 ) {
						person.id = response.data;
						setEditId( owner, person.id );

						appendList( owner, person );
					}
					else {
						updateList( owner, person );
					}

					selectList( owner, person.id );

					// find any other person lists and update them
					$( '.tagelin-rota-widget .tagelin-rota-schedule'  ).each( function() {

						if( person.group_id == getGroupId( $(this) ) ) {
							updateSchedulePerson( $(this), person );
						}
					});

					stopWaiting( owner );
				}
			});



		}
		
	}


	/*
	 *	Validation check prior to saving
	 */
	function validatePerson( owner, person ) {

		// clear error messages
		hideDetailError( owner );

		if( ! person )
			return false;

		if( ! person['name' ] ) {
			showDetailError( owner, '.tagelin-rota-person-name-missing' );
			return false;
		}

		return true;
	}

	/*
	 *	Called when a Person is cancelled
	 */
	function cancelPerson( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );
//		clearAttributes( owner );

		// clear all checks
		owner.find( '.tagelin-rota-attributes-content input' ).each( function () {
			this.checked = false;
		});

		setStateIdle( owner );
	}

	/*
	 *	Called when a Person is deleted
	 */
	function deletePerson( owner ) {
		var person = {};
		person.id = getEditId( owner );
		person.group_id = getGroupId( owner );

		startWaiting( owner );

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-person',
			request: 'delete_person',
			action: ajax_params.action,
			person:person
		};

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {

			},
			success: function( response ) {

				removeList( owner, person.id );
				cancelPerson( owner );				
				stopWaiting( owner );

			}
		});


	}



/*
 *	L O C A T I O N    W I D G E T
 */

	// add event handlers
	addEventHandlers( { target:'.tagelin-rota-location', 
			onSelect: selectLocation, 
			onEdit: editLocation,
			onSave: saveLocation, 
			onDelete: deleteLocation, 
			onCancel: cancelLocation, 
			onNew: newLocation,
			formatList: defaultListFormat });


	$( '.tagelin-rota-widget .tagelin-rota-location' ).each( function () {
		getAllLocation( $(this) );
	});


	/*
	 *	Display list of all Location
	 */
	function getAllLocation( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-location',
			request: 'get_locations',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				locations=response.data;
				buildList( owner, locations );
			}
		});

	}

	/*
	 *	Called on selecting Location
	 */
	function selectLocation( owner, row ) {
	}

	/*
	 *	Called when creating new Location 
	 */
	function newLocation( owner ) {
	}

	
	/*
	 *	Edit this Location, get details from the server
	 */
	function editLocation( owner, location ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-location',
			request: 'get_location',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			location_id: location.id
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				location=response.data;

				setGroupId( owner, location.group_id );
				setEditId( owner, location.id );
				setDetailText( owner, '.tagelin-rota-location-name',  location.name );

				stopWaiting( owner );
			}
		});
	}

	/*
	 *	Save this Location
	 */
	function saveLocation( owner ) {
		var location = {};
		location.id = getEditId( owner );
		location.group_id = getGroupId( owner );
		location.name = getDetailText( owner, '.tagelin-rota-location-name'  );

		if( validateLocation( owner, location ) ) {

			startWaiting( owner );

			// do write
			var request = {
				nonce: ajax_params.tagelin_rota_nonce,
				widget: 'edit-location',
				request: 'store_location',
				action: ajax_params.action,
				location: location
			}

			$.ajax({
				url: ajax_params.ajax_url,
				type: 'post',
				dataType: 'json',
				data: request,
				success: function( response ) {

					if( location.id == 0 ) {
						location.id = response.data;

						setEditId( owner, location.id );
						appendList( owner, location );
					}
					else {
						updateList( owner, location );
					}

					selectList( owner, location.id );

					// find any other location references and update them
					$( '.tagelin-rota-widget .tagelin-rota-schedule'  ).each( function() {

						if( location.group_id == getGroupId( $(this) ) ) {
							updateScheduleLocation( $(this), location );
						}
					});

					stopWaiting( owner );
				}
			});



		}
		
	}


	/*
	 *	Validation check prior to saving
	 */
	function validateLocation( owner, location ) {

		// clear error messages
		hideDetailError( owner );

		if( ! location )
			return false;

		if( ! location['name' ] ) {
			showDetailError( owner, '.tagelin-rota-location-name-missing' );
			return false;
		}

		return true;
	}

	/*
	 *	Called when a Location is cancelled
	 */
	function cancelLocation( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );
		clearAttributes( owner );
		setStateIdle( owner );
	}

	/*
	 *	Called when a Location is cancelled
	 */
	function deleteLocation( owner ) {
		var location = {};
		location.id = getEditId( owner );
		location.group_id = getGroupId( owner );

		startWaiting( owner );

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-location',
			request: 'delete_location',
			action: ajax_params.action,
			location: location
		};

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {

			},
			success: function( response ) {

				removeList( owner, location.id );
				cancelLocation( owner );				
				stopWaiting( owner );

			}
		});

	}



/*
 *	E V E N T   W I D G E T
 */

	// add event handlers
	addEventHandlers( { target:'.tagelin-rota-event', 
			onSelect: selectEvent, 
			onEdit: editEvent,
			onSave: saveEvent, 
			onDelete: deleteEvent, 
			onCancel: cancelEvent, 
			onNew: newEvent,
			formatList: defaultListFormat });

	// button to update event-role
	$( '.tagelin-rota-widget .tagelin-rota-event .tagelin-rota-button-add' ).click( function( event ) {
		event.preventDefault();
		var button = event.currentTarget;
		var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
		var edit = owner.find( '.tagelin-rota-edit-event-role' );
		var eventRole = {};


		eventRole.id = edit.find( '.tagelin-rota-edit-event-role-id' ).val();
		eventRole.role_id = edit.find( '.tagelin-rota-event-role-list' ).val();
		eventRole.name = edit.find( '.tagelin-rota-event-role-list option:selected' ).text();
		eventRole.localname = edit.find( '.tagelin-rota-edit-event-role-name' ).val();

		if( eventRole.role_id != 0 )
			storeEventRole( owner, eventRole );

		edit.find( '.tagelin-rota-edit-event-role-id' ).val(0);
		edit.find( '.tagelin-rota-event-role-list' ).val(0).change();
		edit.find( '.tagelin-rota-edit-event-role-name' ).val("");

		// clear selected attribute
		clearSelectedAttribute( owner.find( '.tagelin-rota-attributes-content .tagelin-rota-list-selected' ) );

	});


	// button to move event role UP
	$( '.tagelin-rota-widget .tagelin-rota-event .tagelin-rota-edit-up' ).click( function( event ) {
		event.preventDefault();
		var button = event.currentTarget;
		var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
		var edit = owner.find( '.tagelin-rota-edit-event-role' );

		var id = edit.find( '.tagelin-rota-edit-event-role-id' ).val();
		if( id == 0 ) {
			id = edit.find( '.tagelin-rota-edit-event-role-local-id' ).val();
		}

		if( id == 0 ) {
			return;
		}

		owner.find( '.tagelin-rota-attributes .tagelin-rota-event-role-id' ).each( function() {
			if( $(this).text() == id ) {
				var attribute = $(this).parent();
				var attributes = attribute.parent();
				var above = attribute.prev();
				if( above.length > 0) {
					attribute.detach();
					attribute.appendTo( attributes );
					attribute.insertBefore( above );
				}
			}

		});
	});

	// button to move event role DOWN
	$( '.tagelin-rota-widget .tagelin-rota-event .tagelin-rota-edit-down' ).click( function( event ) {
		event.preventDefault();
		var button = event.currentTarget;
		var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
		var edit = owner.find( '.tagelin-rota-edit-event-role' );

		var id = edit.find( '.tagelin-rota-edit-event-role-id' ).val();
		if( id === 0 ) {
			id = edit.find( '.tagelin-rota-edit-event-role-local-id' ).val();
		}

		if( id == 0 ) {
			return;
		}

		owner.find( '.tagelin-rota-attributes  .tagelin-rota-event-role-id' ).each( function() {
			if( $(this).text() == id ) {
				var attribute = $(this).parent();
				var attributes = attribute.parent();
				var below = attribute.next();
				if( below.length > 0 ) {
					attribute.detach();
					attribute.appendTo( attributes );
					attribute.insertAfter( below );
				}
			}

		});
	});

	$( '.tagelin-rota-widget .tagelin-rota-event' ).each( function () {
		getEventRoles( $(this) )
		getAllEvent( $(this) );
	});


	/*
	 *	Display list of all Event
	 */
	function getAllEvent( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-event',
			request: 'get_events',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				events=response.data;
				buildList( owner, events );
			}
		});

	}

	/*
	 *	Get list of Roles for an event
	 */
	function getEventRoles( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-role',
			request: 'get_roles',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				eventRoles=response.data;

				clearAttributes( owner );
				appendOption( owner.find( '.tagelin-rota-event-role-list' ), { id:0, name:"" } );

				for( var i = 0; i < eventRoles.length; i++ ) {
					addEventRole( owner, eventRoles[i] );
				}
			}
		});

	}


	/*
	 *	Add a new Role to event editor
	 */
	function addEventRole( owner, role ) {
		appendOption( owner.find( '.tagelin-rota-event-role-list' ), role );
	}


	/*
	 *	Edit a Role in an Event
	 */
	function editEventRole( owner, eventRole ) {

		var edit = owner.find( '.tagelin-rota-edit-event-role' );
		edit.find( '.tagelin-rota-edit-event-role-id' ).val(eventRole.id);
 		edit.find( '.tagelin-rota-event-role-list' ).val(eventRole.role_id).change();
 		edit.find( '.tagelin-rota-edit-event-role-name' ).val(eventRole.localname);

	}

	/*
	 *	Store a Role in this Event
	 */
	function storeEventRole( owner, eventRole ) {

		var attribute; 
		if( eventRole.id == 0 ) {
			// The event id zero denotes a new entry that is not yet 
			// stored in the database.
			// We use negative numberas as temporary local id
			// to distinguish these entries.
			// The negatiove id is replaced by zero when we store it.
			eventRole.id = owner.find( '.tagelin-rota-edit-event-role-local-id' ).val();
			eventRole.id = - eventRole.id;
			owner.find( '.tagelin-rota-edit-event-role-local-id' ).val(eventRole.id );
			attribute = eventRoleAttribute( eventRole );
			appendAttribute( owner, attribute );


			
		}
		else {

			owner.find( '.tagelin-rota-attributes .tagelin-rota-attribute .tagelin-rota-event-role-id' )
				.each( function() {
				if( $(this).text() == eventRole.id ) {
					attribute = $(this).parentsUntil('.tagelin-rota-attributes-content').last();
					attribute.find('.tagelin-rota-event-role-id' ).text( eventRole.id );
					attribute.find('.tagelin-rota-role-id' ).text( eventRole.role_id );
					attribute.find('.tagelin-rota-event-role-name' ).text( eventRole.name );
					attribute.find('.tagelin-rota-event-role-local-name' ).text( eventRole.localname );

					if(
						( eventRole.localname == null ) 
						|| (eventRole.localname == "" )
						|| (eventRole.localname == eventRole.name ) ) {

						attribute.find('.tagelin-rota-event-role-display-name' ).text( eventRole.name );
					}else {
						attribute.find('.tagelin-rota-event-role-display-name' ).text( eventRole.localname+'['+eventRole.name+']' );
					}

					

					
				}
			});

		}
			

		attribute.find( '.tagelin-rota-event-role-display-name' ).click( function(event) {
			event.preventDefault();
			var attribute = $(event.currentTarget).parentsUntil('.tagelin-rota-attributes-content').last();
			var eventRole = getEventRoleAttribute( attribute );
			editEventRole( owner, eventRole );
		});

		// scroll to place
		owner.find( '.tagelin-rota-attributes-content' )[0].scrollTop = attribute[0].offsetTop;


	}

	/*
	 *	Update the display of Roles
	 */
	function updateEventRole( owner, role ) {

		var select = owner.find( '.tagelin-rota-event-role-list' );
		var options =owner.find( '.tagelin-rota-event-role-list option[value='+role.id+']' );

		// update select widget in edit section
		if( options.length > 0 ) {
			// rename
			options.text( role.name );
		}
		else {
			// append
			select.append( 
				$('<option value="' + role.id + '">'
							+role.name
				+'</option>' )
			)
		}
		// find attributes using this role:
		owner.find( '.tagelin-rota-attributes .tagelin-rota-attribute .tagelin-rota-role-id' ).each( function() {
			if( $(this).text() == role.id ) {
				var attribute = $(this).parentsUntil('.tagelin-rota-attributes-content').last();

				var oldName = attribute.find('.tagelin-rota-event-role-name' ).text();
				var localName = attribute.find('.tagelin-rota-event-role-local-name' ).text();

				// set role name
				attribute.find('.tagelin-rota-event-role-name' ).text( role.name );

				if(( localName.length == 0 ) || (localName == role.name )) {
					// set local name as well
					attribute.find('.tagelin-rota-event-role-local-name' ).text( role.name );
					// and update display name
					attribute.find('.tagelin-rota-event-role-display-name' ).text( role.name );
				} 
				else {
					// update display name to show new base role
					attribute.find('.tagelin-rota-event-role-display-name' ).text( localName + '[' +role.name +']' );

				}

				
			}
			
		});




	}


	/*
	 *	EventRole has been deleted, remove from this event edit
	 */
	function removeEventRole( owner, role ) {

		var select = owner.find( '.tagelin-rota-event-role-list' );
		var options =owner.find( '.tagelin-rota-event-role-list option[value='+role.id+']' );

		// update select widget in edit section
		if( options.length > 0 ) {
			// remove
			options.remove();
		}
		

		// find attributes using this role:
		owner.find( '.tagelin-rota-attributes .tagelin-rota-attribute .tagelin-rota-role-id' ).each( function() {
			if( $(this).text() == role.id ) {
				var attribute = $(this).parentsUntil('.tagelin-rota-attributes-content').last();
				attribute.remove();
			}
			
		});

	}


	/*
	 *	Called when an Event is selected
	 */
	function selectEvent( owner, row ) {
	}
	
	/*
	 *	Called when creating new Event
	 */
	function newEvent( owner ) {
		var edit = owner.find( '.tagelin-rota-edit-event-role' );
		edit.find( '.tagelin-rota-edit-event-role-id' ).val(0);
		edit.find( '.tagelin-rota-event-role-list' ).val(0).change();
		edit.find( '.tagelin-rota-edit-event-role-name' ).val("");

		clearAttributes( owner );

	}

	/*
	 *	Edit this Event, get details from the server
	 */
	function editEvent( owner, event ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-event',
			request: 'get_event',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			event_id: event.id
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				event=response.data;

				setGroupId( owner, event.group_id );
				setEditId( owner, event.id );
				setDetailText( owner, '.tagelin-rota-event-name',  event.name );

				// clear edit attribute
				var edit = owner.find( '.tagelin-rota-edit-event-role' );
				edit.find( '.tagelin-rota-edit-event-role-id' ).val(0);
				edit.find( '.tagelin-rota-edit-event-role-local-id' ).val(0);
				edit.find( '.tagelin-rota-event-role-list' ).val(0).change();
				edit.find( '.tagelin-rota-edit-event-role-name' ).val("");
		
				clearAttributes( owner );

				for( var i = 0; i < event.roles.length; i++ ) {
					var attribute = eventRoleAttribute( event.roles[i] );
					appendAttribute( owner, attribute );

					attribute.find( '.tagelin-rota-event-role-display-name' ).click( function(event) {
						event.preventDefault();
						var attribute = $(event.currentTarget).parentsUntil('.tagelin-rota-attributes-content').last();
						var eventRole = getEventRoleAttribute( attribute );

						// Mark selected Attribute
						markSelectedAttribute( attribute );
						editEventRole( owner, eventRole );
					});

				}

				stopWaiting( owner );

			}
		});
	}


	/*
	 *	Save this Event
	 */
	function saveEvent( owner ) {
		var event = {};
		event.id = getEditId( owner );
		event.group_id = getGroupId( owner );
		event.name = getDetailText( owner, '.tagelin-rota-event-name'  );
		event.roles =  [];


		var sequence = 1;
		owner.find( '.tagelin-rota-attributes-content .tagelin-rota-attribute' ).each( function() {
			var eventRole = getEventRoleAttribute( $(this) );
			if( eventRole.id < 0 )
				eventRole.id = 0;// replace local id with 0, db will allocate real id
			eventRole.sequence = sequence ++;
			event.roles.push( eventRole );
		});


		if( validateEvent( owner, event ) ) {

			startWaiting( owner );

			// do write
			var request = {
				nonce: ajax_params.tagelin_rota_nonce,
				widget: 'edit-event',
				request: 'store_event',
				action: ajax_params.action,
				event: event
			}

			$.ajax({
				url: ajax_params.ajax_url,
				type: 'post',
				dataType: 'json',
				data: request,
				success: function( response ) {

					if( event.id == 0 ) {
						event.id = response.data;
						setEditId( owner, event.id );

						appendList( owner, event );
					}
					else {
						updateList( owner, event );
					}

					selectList( owner, event.id );

					// remove unselected attributes
					owner.find( '.tagelin-rota-attributes-content '
							+'.tagelin-rota-attribute '
							+'.tagelin-rota-event-role-delete '
							+'input:not(:checked)'
							 ).each( function () {

						var attribute = $(this).parentsUntil( '.tagelin-rota-attributes-content' ).last();
						attribute.remove();

					});

					// find any other  references and update them
					$( '.tagelin-rota-widget .tagelin-rota-schedule'  ).each( function() {

						if( event.group_id == getGroupId( $(this) ) ) {
							updateScheduleEvent( $(this), event );
						}
					});




					stopWaiting( owner );
				}
			});



		}
		
	}


	/*
	 *	Validation prior to saving
	 */
	function validateEvent( owner, event ) {

		// clear error messages
		hideDetailError( owner );

		if( ! event )
			return false;

		if( ! event['name' ] ) {
			showDetailError( owner, '.tagelin-rota-event-name' );
			return false;
		}
		return true;
	}


	/*
	 *	Called when an Event is cancelled
	 */
	function cancelEvent( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );

		var edit = owner.find( '.tagelin-rota-edit-event-role' );
		edit.find( '.tagelin-rota-edit-event-role-id' ).val(0);
		edit.find( '.tagelin-rota-event-role-list' ).val(0).change();
		edit.find( '.tagelin-rota-edit-event-role-name' ).val("");

		clearAttributes( owner );
		setStateIdle( owner );
	}


	/*
	 *	Called when an Event is deleted
	 */
	function deleteEvent( owner ) {
		var event = {};
		event.id = getEditId( owner );
		event.group_id = getGroupId( owner );

		startWaiting( owner );

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-event',
			request: 'delete_event',
			action: ajax_params.action,
			event: event
		};

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {

			},
			success: function( response ) {

				removeList( owner, event.id );
				cancelEvent( owner );				
				stopWaiting( owner );

			}
		});

	}


	/*
	 *	Render the Role for this event
	 */
	function eventRoleAttribute( eventRole ) {
				
		var attribute = $( '<div class="tagelin-rota-attribute" />' );

		attribute.append($('<div class="tagelin-rota-event-role-id">' + eventRole.id + '</div>'));
		attribute.append($('<div class="tagelin-rota-role-id">' + eventRole.role_id + '</div>'));

		attribute.append(
			$('<div class="tagelin-rota-event-role-name">' + eventRole.name + '</div>'));

		attribute.append(
			$('<div class="tagelin-rota-event-role-local-name">' + eventRole.localname + '</div>'));


		var item = $('<div class="tagelin-rota-attribute-item"></div>' );
		var name = $('<div class="tagelin-rota-attribute-item-name"></div>' );
		var value = $('<div class="tagelin-rota-attribute-item-value"></div>' );

		if( (!eventRole.localname) || (eventRole.localname.length<=0) ) {
			name.append(
			$('<div class="tagelin-rota-event-role-display-name">' + eventRole.name + '</div>')); 
		}
		else
		if( eventRole.localname == eventRole.name ) {
			name.append(
			$('<div class="tagelin-rota-event-role-display-name">' + eventRole.name + '</div>')); 
		}
		else {
			name.append(
			$('<div class="tagelin-rota-event-role-display-name">' 
				+ eventRole.localname 
				+ '[' + eventRole.name + ']'
			+ '</div>'));
		}

		value.append(
		$('<div class="tagelin-rota-event-role-delete">' 
			+ '<input type="checkbox"  />' 
		+ '</div>'));


		item.append( name );
		item.append( value );
		attribute.append( item );


		attribute.find( '.tagelin-rota-event-role-delete input' ).each( function () {
					this.checked = true;
				});

		return attribute;
	}


	/*
	 *	Get the Event Role from the attribute fields
	 */
	function getEventRoleAttribute( attribute )
	{
		var id = attribute.find( '.tagelin-rota-event-role-id' ).text();
		var role_id = attribute.find( '.tagelin-rota-role-id' ).text();
		var localname = attribute.find( '.tagelin-rota-event-role-local-name' ).text();
		var name = attribute.find( '.tagelin-rota-event-role-name' ).text();
		var active = attribute.find( '.tagelin-rota-event-role-delete input:checked' );

		var eventRole = {
			id:id,			
			role_id:role_id,	
			localname:localname,
			name: name,
			active: (active.length>0)?true:false	
		};
		return eventRole;
	}

/*
 *	S C H E D U L E    W I D G E T
 */

	// add event handlers
	addEventHandlers( { target:'.tagelin-rota-schedule', 
			onSelect: selectSchedule, 
			onEdit: editSchedule,
			onSave: saveSchedule, 
			onDelete: deleteSchedule, 
			onCancel: cancelSchedule, 
			onNew: newSchedule,
			formatList: scheduleListFormat });


	/*
	 *	Handle dates and show list of Schedule in range
	 */
	$( '.tagelin-rota-widget .tagelin-rota-schedule' ).each( function () {

		var owner = $(this);

		var rangeModifiers = {};
		
		// default range modifiers
		rangeModifiers.from ="-0y";	// current year
		rangeModifiers.to="+0f";	// forever

		var monthPicker = owner.find('.tagelin-rota-month-picker' );
		var region='en';
		var dateFormat='d M yy';
		var listDateFormat = 'd M yy';

		var startDate = new Date();	// today

		var config = owner.find('.tagelin-rota-config' );
		if( config.length > 0 ) {
			var configValue;
			configValue = config.find( '.tagelin-rota-range-from' ).val();
			if( configValue != "" ) {
				rangeModifiers.from = configValue;
			}

			configValue = config.find( '.tagelin-rota-range-to' ).val();
			if( configValue != "" ) {
				rangeModifiers.to = configValue;
			}

			configValue = config.find( '.tagelin-rota-range-selector' ).val();

			var theirMonthPickerClass = config.find( '.tagelin-rota-range-selector' ).val();
			if( theirMonthPickerClass != "" ) {
				if( theirMonthPickerClass == 'none' ){
					monthPicker = null;
				}
				else {
					var theirMonthPicker = $( '.'+theirMonthPickerClass );
					monthPicker = theirMonthPicker;
				}
			}

			configValue = config.find( '.tagelin-rota-region' ).val();
			if( configValue != "" ) {
				region = configValue;
			}

			configValue = config.find( '.tagelin-rota-date-format' ).val();
			if( configValue != "" ) {
				dateFormat = configValue;
			}

			configValue = config.find( '.tagelin-rota-list-date-format' ).val();
			if( configValue != "" ) {
				listDateFormat = configValue;
			}

		}

		// save locale information in attrs
		owner.attr('region', region );
		owner.attr('date-format',dateFormat );
		owner.attr('list-date-format', listDateFormat );


		// schedule datepicker
//		$.datepicker.setDefaults($.datepicker.regional['en']);


//		owner.find( '.tagelin-rota-schedule-date').datepicker(
//				$.datepicker.regional[ region ] );
		owner.find( '.tagelin-rota-schedule-date').datepicker();

		//owner.find( '.tagelin-rota-schedule-date').datepicker.addClass('.tagelin-rota-widget');

		// dateformat matches date needed at server
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "altFormat",'d-MM-YY' );

		// user selected dateformat for display eg "D d M yy" 
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "dateFormat",dateFormat );

		// other options
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "showOn",'focus' );
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "changeDay", true );
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "changeMonth", true );
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "changeYear", true );
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "buttonText", '' );
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "showButtonPanel", false );


		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "onSelect", 
				function(dateText, inst) {
						$(this).val( dateText );
				});


		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "beforeShow", 
				function() {
					// add our datepicker style
					var wrapper = $('<div class="tagelin-rota-widget">' );
					$('#ui-datepicker-div').parent().append( wrapper );
					wrapper.append( $('#ui-datepicker-div') );
				});

		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "onClose", 
				function(dateText, inst ) {
					// remove our datepicker style
					var wrapper = $('#ui-datepicker-div').parent();
					if( wrapper.length > 0 ) {
						wrapper.parent().append( $('#ui-datepicker-div') );
						wrapper.remove();
					}

				});




 		getScheduleLocations( owner );
 		getScheduleEvents( owner );

		// initialise hour:minute selectors
		init_hours( owner.find( '.tagelin-rota-schedule-hours' ) );
		init_minutes( owner.find( '.tagelin-rota-schedule-minutes' ) );
		appendOption(  owner.find( '.tagelin-rota-schedule-hours-end' ), { id:-1, name: '' } );
		appendOption(  owner.find( '.tagelin-rota-schedule-minutes-end' ), { id:-1, name: '' } );
		init_hours( owner.find( '.tagelin-rota-schedule-hours-end' ) );
		init_minutes( owner.find( '.tagelin-rota-schedule-minutes-end' ) );


		if( (  monthPicker != null )&& (monthPicker != "" ) ) {

			//monthPicker.datepicker($.datepicker.regional[ region ]); 
			monthPicker.datepicker(); 

			monthPicker.datepicker( "option", "dateFormat", 'MM yy' );
			monthPicker.datepicker( "option", "changeDay", false );
			monthPicker.datepicker( "option", "changeMonth", true );
			monthPicker.datepicker( "option", "changeYear", true );
			monthPicker.datepicker( "option", "showButtonPanel", false );

			monthPicker.datepicker( "option", "onChangeMonthYear",
				function (year,month,inst) { 
					var startDate = new Date(year, month-1, 1);
					getAllSchedule( owner, startDate, rangeModifiers );

				} );


			monthPicker.addClass('tagelin-rota-month-picker' );

			monthPicker.focus(function () {
					$(".ui-datepicker-calendar").hide();
					$("#ui-datepicker-div").position({
						my: "center top",
						at: "center bottom",
						of: $(this)
					});
			});

					
			

		}

		getAllSchedule( owner, startDate, rangeModifiers );

	});


	/*
	 *	Update display for Schedule when its Event changes
	 */
	$( '.tagelin-rota-widget .tagelin-rota-schedule .tagelin-rota-schedule-event').change( function () {
		var owner = $(this).parentsUntil( '.tagelin-rota-widget' ).last();

		var scheduleId = getEditId( owner );


		scheduleEventChanged( owner, { id:scheduleId, event_id:$(this).val() } );
	});



	/*
	 *	Show datepicker on focus
	 */
	$('.tagelin-rota-widget .tagelin-rota-schedule .tagelin-rota-schedule-date').focus(function () {

		// clear error msg
		$( '.tagelin-rota-entry-msg' ).css( { 'display': 'none' } );


		$('#ui-datepicker-div').position({
			my: 'center top',
			at: 'center bottom',
			of: $(this).parent()
		});
	});

	/*
	 *	Regain datepicker focus by clicking on it after scrolling
	 */
	$('.tagelin-rota-widget .tagelin-rota-schedule .tagelin-rota-schedule-date').click(function () {
		$(this).focus();

		// clear error msg
		$( '.tagelin-rota-entry-msg' ).css( { 'display': 'none' } );

		$('#ui-datepicker-div').position({
			my: 'center top',
			at: 'center bottom',
			of: $(this).parent()
		});
	});


	/*
	 *	Show list of all Schedule in date range
	 */
	function getAllSchedule( owner, startDate, rangeModifiers) {

		var timestamp =0;


		var viewFrom = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() ); 

		if( rangeModifiers.from )
		{
			viewFrom = applyDateModifiers( viewFrom, rangeModifiers.from );
		}

		var viewTo= new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() ); 

		if( rangeModifiers.to )
		{
			viewTo = applyDateModifiers( viewTo, rangeModifiers.to );
		}



		cancelSchedule( owner );

		// default to start date
		owner.find('.tagelin-rota-schedule-date').datepicker( "option", "defaultDate", startDate);


		// save search range timestamp in page
		setDetailText( owner, '.tagelin-rota-schedule-date-from', (viewFrom == null )?0:viewFrom.getTime()/1000 );
		setDetailText( owner, '.tagelin-rota-schedule-date-to', (viewTo == null )?0:viewTo.getTime()/1000 );

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-schedule',
			request: 'get_schedules',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			view_from: (viewFrom == null )?0:viewFrom.getTime()/1000,
			view_to: (viewTo == null )?0:viewTo.getTime()/1000
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				schedule=response.data;

				buildList( owner, schedule );
			}
		});

	}


	/*	
	 *	Build display name for Schedule event in list
	 *	return key:value pair for list display
	 */
	function scheduleListFormat( owner, schedule ) {

		var eventTime = new Date( schedule.timestamp * 1000 );
		var region = owner.attr('region');
		var listDateFormat= owner.attr('list-date-format' );

		var displayDate=$.datepicker.formatDate( 
				listDateFormat, //"DD, MM d, yy",
				eventTime,
				$.datepicker.regional[region] );


		var name = schedule.name.trim();
		if( name.length == 0 ) {
			if( schedule.event ) {
				name = schedule.event.trim();
			}
			else {
				name = "NONAME";
			}
		}

		return {
			key: eventTime,
			value: escapeHtml( displayDate + " - "+ name )
		};
	}	

	

	/*	
	 *	Get list of all Location for Schedule event
	 */
	function getScheduleLocations( owner ) {
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,	
			widget: 'edit-location',
			request: 'get_locations',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				locations=response.data;

				//clearList( owner );
				for( var i = 0; i < locations.length; i++ ) {
					appendOption( owner.find( '.tagelin-rota-schedule-location' ), locations[i] );
				}

				setDetailSelect( owner, '.tagelin-rota-schedule-location', 0 );

			}
		});



	}


	/*
	 *	Get list of Event types for Schedule event
	 */
	function getScheduleEvents( owner ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,	
			widget: 'edit-event',
			request: 'get_events',
			action: ajax_params.action,
			group_id: getGroupId( owner )
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {

				events=response.data;
				for( var i = 0; i < events.length; i++ ) {
					appendOption( owner.find( '.tagelin-rota-schedule-event' ), events[i] );
				}

				setDetailSelect( owner, '.tagelin-rota-schedule-event', 0);

			}
		});


	}


	/*
	 *	Get list of matching Person for this Role
	 */
	function getPersonForRole( owner, attribute, tag, schedule_detail ) { 

		var select = attribute.find( tag );
		select.empty();
		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-person',
			request: 'get_persons_for_role',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			role_id: schedule_detail.role_id
		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				persons=response.data;

				select.append( 
						$('<option value="' + 0 + '">'
							+"&nbsp;"
						+'</option>' )
					);

				for( var i = 0; i < persons.length; i++ ) {
					select.append( 
						$('<option value="' + persons[i].id + '">'
							+persons[i].name
						+'</option>' )
					);
				}
				select.val( schedule_detail.person_id ).change();


				if( select.val() != schedule_detail.person_id ) {

					select.append( 
						$('<option value="' + (-(schedule_detail.person_id)) + '">'
							+schedule_detail.person
						+'</option>' )
					);
					select.val( -(schedule_detail.person_id) ).change();
				}

			}
		});
	}

	/*
	 *	Update Schedule when Event changes
	 */
	function updateScheduleEvent( owner, event ) {

		var select = owner.find( '.tagelin-rota-schedule-event' );
		var options =owner.find( '.tagelin-rota-schedule-event option[value='+event.id+']' );

		if( options.length > 0 ) {
			// Currently editing a schedule based on this event...

			// Rename in listbox
			options.text( event.name );


			// Update role names in schedule_detail attributes
			event.roles.forEach( function( event_role ) {
				var event_role_id = event_role.id;
				owner.find( '.tagelin-rota-attribute .tagelin-rota-event-role-id' ).each( function() {
					if( $(this).text() == event_role_id ) {
						var attribute = $(this).parentsUntil('.tagelin-rota-attributes-content').last();

						if( event_role.localname.length > 0 ) {

							attribute.find(
								'.tagelin-rota-event-role-display-name').text(
									event_role.localname
								);
						}
						else {

							attribute.find(
								'.tagelin-rota-event-role-display-name').text(
									event_role.role_name
								);
						}

						var current_role_id = attribute.find('.tagelin-rota-role-id').text();
						if( current_role_id != event_role.role_id ) {

							attribute.find( '.tagelin-rota-role-id').text(
									event_role.role_id
								);
					

							getPersonForRole( owner, 
								attribute,
								'.tagelin-rota-person-list', 
								{
									role_id:event_role.role_id,
									person_id:0,
									person:""
								}
							);




						}
						

					}
				});			

			});

		}
		else {
			// append
			select.append( 
				$('<option value="' + event.id + '">'
							+event.name
				+'</option>' )
			)
		}

	}


	/*
	 *	Update Schedule when Location changes
	 */
	function updateScheduleLocation( owner, location ) {

		var select = owner.find( '.tagelin-rota-schedule-location' );
		var options =owner.find( '.tagelin-rota-schedule-location option[value='+location.id+']' );

		if( options.length > 0 ) {
			// rename
			options.text( location.name );
		}
		else {
			// append
			select.append( 
				$('<option value="' + location.id + '">'
							+location.name
				+'</option>' )
			)
		}

	}


	/*
	 *	Update Schedule when Person changes
	 */
	function updateSchedulePerson( owner, person ) {

		owner.find( '.tagelin-rota-attributes .tagelin-rota-attribute' ).each( function () {
			var role_id = $(this).find( '.tagelin-rota-role-id' ).text();
			var select = $(this).find( '.tagelin-rota-person-list' );
			var options =$(this).find( '.tagelin-rota-person-list option[value='+person.id+']' );


			// is this role valid for the person ?
			var valid = false;
			person.roles.forEach( function( role ){
				if( role.id == role_id )
					valid = true;
			});

			if( valid ) {

				// ensure they are in the list
				if( options.length > 0 ) {

					// Present, can rename to current name
					options.text( person.name );
				}
				else {
					// add person for this role
					select.append( 
						$('<option value="' + person.id + '">'
							+person.name
						+'</option>' )
					);
				}
			}
			else {
				// if present...
				if( options.length > 0 ) {

					// are they selected ?	
					if( options.selected ) {

						// select empty option
						select.val(0).change();
					}

					options.remove();
				}
			}

		});
		

	}

	
	/*
	 *	Update Schedule display when its base Event is changed
	 */
	function scheduleEventChanged( owner, schedule ) {

		if( schedule.event_id < 0 )
			schedule.event_id = - schedule.event_id;	// this is a deleted event, show as readonly

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-schedule',
			request: 'get_schedule_detail',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			schedule_id: schedule.id,
			event_id: schedule.event_id,
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {

				var detail = response.data;

				clearAttributes( owner );

				for( var i = 0; i < detail.length; i++ ) {
					var schedule_detail= detail[i];

					var attribute = $( '<div class="tagelin-rota-attribute" />' );

					attribute.append($('<div class="tagelin-rota-schedule-detail-id">' + schedule_detail.id + '</div>'));

					attribute.append($('<div class="tagelin-rota-role-id">' + schedule_detail.role_id + '</div>'));

					attribute.append($('<div class="tagelin-rota-event-role-id">' + schedule_detail.event_role_id + '</div>'));

					var item = $( '<div class="tagelin-rota-attribute-item" />' );
		
					var name = $( '<div class="tagelin-rota-attribute-item-name" />' );
					var value = $( '<div class="tagelin-rota-attribute-item-value" />' );
		
					item.append( name );
					item.append( value );
		

					if( schedule_detail.event_role_name !== "" ){
						name.append(
							$('<div class="tagelin-rota-event-role-display-name">' 
							+ schedule_detail.event_role_name + ':'
							+ '</div>'));
					}
					else {
						name.append(
							$('<div class="tagelin-rota-event-role-display-name">' 
							+ schedule_detail.role_name  + ':'
							+ '</div>'));
					}


					value.append( $( '<select  class="tagelin-rota-person-list">' ) );
					attribute.append( item);

					// aux notes
					item = $( '<div class="tagelin-rota-attribute-item" />' );
					name = $( '<div class="tagelin-rota-attribute-item-name" />' );
					value = $( '<div class="tagelin-rota-attribute-item-value" />' );


					item.append( name );
					item.append( value );

					if( schedule_detail.aux == null )
						schedule_detail.aux = "";
		
					var notesLabel = owner.find( '.tagelin-rota-schedule-notes-label' ).val();
					name.append( notesLabel+':' );

					value.append( '<input size="12" class="tagelin-rota-event-role-aux" value="'
							+schedule_detail.aux
							+'">' );
					attribute.append( item );
/*
					attribute.append(
							$('<div class="tagelin-rota-attribute-A-XXX">Notes:<input class="tagelin-rota-event-role-aux" value="'
							+schedule_detail.aux
							+'"></div>' ));
*/

					if( schedule_detail.person_id == null )
						schedule_detail.person_id = 0;

					appendAttribute( owner, attribute );


					getPersonForRole( owner, 
						attribute,
						'.tagelin-rota-person-list', 
						schedule_detail
						);

				}

				stopWaiting( owner );
			}
		});	

	}


	/*
	 *	Get Schedule detail from attribute list
	 */
	function getScheduleAttribute( attribute )
	{
		var id = attribute.find( '.tagelin-rota-schedule-detail-id' ).text();
		var role_id = attribute.find( '.tagelin-rota-role-id' ).text();
		var event_role_id = attribute.find( '.tagelin-rota-event-role-id' ).text();
		var event_role_name = attribute.find( '.tagelin-rota-event-role-display-name' ).text();
		var person_id = attribute.find( '.tagelin-rota-person-list' ).val();
		var aux = attribute.find( '.tagelin-rota-event-role-aux' ).val(); 

		var detail = {
			id:id,			
			role_id:role_id,	
			event_role_id: event_role_id,
			event_role_name: event_role_name,
			person_id: person_id,
			aux: aux
		};
		return detail;
	}

	/*
	 *	Called when a Schedule is selected
	 */
	function selectSchedule( owner, row ) {
	}

	/*
	 *	Called when a new Schedule is selected
	 */
	function newSchedule( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );
		purgeDetailSelect( owner, '.tagelin-rota-schedule-location' );
		purgeDetailSelect( owner, '.tagelin-rota-schedule-event' );
		setDetailSelect( owner, '.tagelin-rota-schedule-event', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-location', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-hours', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-minutes', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-hours-end', -1 );
		setDetailSelect( owner, '.tagelin-rota-schedule-minutes-end', -1 );
		setDetailTextArea( owner, '.tagelin-rota-schedule-description', "" );
		clearAttributes( owner );
	}


	/*
	 *	Edit this Schedule, get details from server
	 */
	function editSchedule( owner, schedule ) {

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-schedule',
			request: 'get_schedule',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			schedule_id: schedule.id
		}

		startWaiting( owner );
		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			success: function( response ) {
				schedule=response.data;
				var eventTime = new Date( schedule.timestamp * 1000 );

				eventTime.setMinutes(eventTime.getMinutes() + eventTime.getTimezoneOffset());

				var hours = eventTime.getHours();
				var minutes = eventTime.getMinutes();
				var seconds = eventTime.getSeconds();

				setGroupId( owner, schedule.group_id );
				setEditId( owner, schedule.id );
				setDetailText( owner, '.tagelin-rota-schedule-name',  schedule.name );

				setDetailSelect( owner, '.tagelin-rota-schedule-hours', hours );
				setDetailSelect( owner, '.tagelin-rota-schedule-minutes', minutes);


				var endHours = -1;
				var endMinutes = -1;
				var endSeconds = -1;

				if( schedule.until != 0 ) {
					// we have an end specified
				
				
					var eventEndTime = new Date( schedule.until * 1000 );

					eventEndTime.setMinutes(
						eventEndTime.getMinutes() 
						+ 
						eventEndTime.getTimezoneOffset()
					);

	
					endHours = eventEndTime.getHours();
					endMinutes = eventEndTime.getMinutes();
					endSeconds = eventEndTime.getSeconds();

				}

				setDetailSelect( owner, '.tagelin-rota-schedule-hours-end', endHours );
				setDetailSelect( owner, '.tagelin-rota-schedule-minutes-end', endMinutes );


				var displayDate = new Date( (schedule.timestamp -  ((((hours*60)+minutes)*60)+seconds)  ) * 1000 );


				setDetailDate( owner, '.tagelin-rota-schedule-date', displayDate );

				purgeDetailSelect( owner, '.tagelin-rota-schedule-location' );
				purgeDetailSelect( owner, '.tagelin-rota-schedule-event' );


				setDetailSelect( owner, '.tagelin-rota-schedule-event', schedule.event_id );

				if( getDetailSelect( owner, '.tagelin-rota-schedule-event' ) !=  schedule.event_id ) {
					// This is a deleted event, add with negative id
					var deletedEvent = { 
						id: -schedule.event_id,
						name: schedule.event 
					};
					appendOption( owner.find( '.tagelin-rota-schedule-event' ), deletedEvent );
					setDetailSelect( owner, '.tagelin-rota-schedule-event', -schedule.event_id );
				}


				setDetailSelect( owner, '.tagelin-rota-schedule-location', schedule.location_id );

				if( getDetailSelect( owner, '.tagelin-rota-schedule-location' ) !=  schedule.location_id ) {
					// This is a deleted location, add with negative id
					var deletedLocation = { 
						id: -schedule.location_id,
						name: schedule.location 
					};
					appendOption( owner.find( '.tagelin-rota-schedule-location' ), deletedLocation );
					setDetailSelect( owner, '.tagelin-rota-schedule-location', -schedule.location_id );
				}

				setDetailTextArea( owner, '.tagelin-rota-schedule-description', schedule.description );


				clearAttributes( owner );

				for( var i = 0; i < schedule.detail.length; i++ ) {
					var schedule_detail=schedule.detail[i];
					var attribute = $( '<div class="tagelin-rota-attribute" />' );

					attribute.append($('<div class="tagelin-rota-schedule-detail-id">' + schedule_detail.id + '</div>'));

					attribute.append($('<div class="tagelin-rota-role-id">' + schedule_detail.role_id + '</div>'));

					attribute.append($('<div class="tagelin-rota-event-role-id">' + schedule_detail.event_role_id + '</div>'));

					var role = $( '<div class="tagelin-rota-attribute-item" />' );
		
					var name = $( '<div class="tagelin-rota-attribute-item-name" />' );
					var value = $( '<div class="tagelin-rota-attribute-item-value" />' );
		
					role.append( name );
					role.append( value );
		

					if( schedule_detail.event_role_name !== "" ){
						name.append(
							$('<div class="tagelin-rota-event-role-display-name">' 
							+ schedule_detail.event_role_name + ':'
							+ '</div>'));
					}
					else {
						name.append(
							$('<div class="tagelin-rota-event-role-display-name">' 
							+ schedule_detail.role_name  + ':'
							+ '</div>'));
					}


					value.append( $( '<select  class="tagelin-rota-person-list">' ) );
					attribute.append( role);


					// aux notes
					item = $( '<div class="tagelin-rota-attribute-item" />' );
					name = $( '<div class="tagelin-rota-attribute-item-name" />' );
					value = $( '<div class="tagelin-rota-attribute-item-value" />' );
		
					item.append( name );
					item.append( value );
		
					if( schedule_detail.aux == null )
						schedule_detail.aux = "";

					var notesLabel = owner.find( '.tagelin-rota-schedule-notes-label' ).val();
					name.append( notesLabel+':' );

					value.append( '<input size = "12" class="tagelin-rota-event-role-aux" value="'
							+schedule_detail.aux
							+'">' );
					attribute.append( item );


					if( schedule_detail.person_id == null )
						schedule_detail.person_id = 0;

					appendAttribute( owner, attribute );


					getPersonForRole( owner, 
						attribute,
						'.tagelin-rota-person-list', 
						schedule_detail
						);


				}

				stopWaiting( owner );

			}
		});
	}


	/*
	 *	Save this Schedule
	 */
	function saveSchedule( owner ) {
		var schedule = {};
		schedule.id = getEditId( owner );
		schedule.group_id = getGroupId( owner );
		schedule.name = getDetailText( owner, '.tagelin-rota-schedule-name'  );
		schedule.event_id = getDetailSelect( owner, '.tagelin-rota-schedule-event' );
		schedule.event = getDetailSelectText( owner, '.tagelin-rota-schedule-event' );
		schedule.location_id = getDetailSelect( owner, '.tagelin-rota-schedule-location' );
		schedule.description = getDetailTextArea( owner, '.tagelin-rota-schedule-description' );

		schedule.detail = [];


		owner.find( '.tagelin-rota-attributes-content .tagelin-rota-attribute' ).each( function() {
			var detail = getScheduleAttribute( $(this) );
			schedule.detail.push( detail );
		});

		if( validateSchedule( owner, schedule ) ) {
			var hours = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-hours' );
			var minutes = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-minutes' );
	
			var eventTime = parseInt(hours,10);
			eventTime *= 60;	// to minutes
			eventTime += parseInt(minutes,10);
			eventTime *= 60;	// to seconds

			var date = getDetailDate( owner, '.tagelin-rota-schedule-date' );

			// Add timezone offset to convert the local event time to UTC
			date.setMinutes(date.getMinutes() - date.getTimezoneOffset());

			eventTime += (date.getTime()/1000);
			schedule.timestamp = eventTime;		// Store UTC timestamp epoch 1970

			// get finish time
			schedule.until = 0;
			hours = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-hours-end' );
			minutes = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-minutes-end' );

			if(( hours != -1 ) || ( minutes != -1 ) ) {
				// finish has been given
				if( hours == -1 ) {
					// use same as event time
 					hours = getDetailSelect( owner,
						 '.tagelin-rota-schedule-time .tagelin-rota-schedule-hours' 
						);
				}
				if( minutes == -1 )
					minutes = 0;

				eventTime = parseInt(hours,10);
				eventTime *= 60;	// to minutes
				eventTime += parseInt(minutes,10);
				eventTime *= 60;	// to seconds

				eventTime += (date.getTime()/1000);
				schedule.until = eventTime;		// Store UTC timestamp epoch 1970


			}



			startWaiting( owner );

			// do write
			var request = {
				nonce: ajax_params.tagelin_rota_nonce,
				widget: 'edit-schedule',
				request: 'store_schedule',
				action: ajax_params.action,
				schedule: schedule
			}

			$.ajax({
				url: ajax_params.ajax_url,
				type: 'post',
				dataType: 'json',
				data: request,
				success: function( response ) {

					if( schedule.id == 0 ) {
						schedule.id = response.data;
						setEditId( owner, schedule.id );

						appendList( owner, schedule );
					}
					else {
						updateList( owner, schedule );
					}

					selectList( owner, schedule.id );
					stopWaiting( owner );
				}
			});



		}
		
	}


	/*
	 *	Validation prior to saving
	 */
	function validateSchedule( owner, schedule ) {

		// clear error messages
		hideDetailError( owner );

		if( ! schedule )
			return false;


		if( getDetailSelect( owner, '.tagelin-rota-schedule-event' ) == 0 ) {
			showDetailError( owner, '.tagelin-rota-schedule-event-missing' );
			return false;
		}


		if( getDetailSelect( owner, '.tagelin-rota-schedule-location' ) == 0 ) {
			showDetailError( owner, '.tagelin-rota-schedule-location-missing' );
		}


		// get the proposed date
		var hours = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-hours' );
		var minutes = getDetailSelect( owner, '.tagelin-rota-schedule-time .tagelin-rota-schedule-minutes' );

		var eventTime = parseInt(hours,10);
		eventTime *= 60;	// to minutes
		eventTime += parseInt(minutes,10);
		eventTime *= 60;	// to seconds

		var date = getDetailDate( owner, '.tagelin-rota-schedule-date' );
		if( date == null ) {
			showDetailError( owner, '.tagelin-rota-schedule-date-missing' );
			return false;
		}

		eventTime += (date.getTime()/1000);

		// check this is in date range
		var timestampFrom = getDetailText( owner, '.tagelin-rota-schedule-date-from');
		var timestampTo=getDetailText( owner, '.tagelin-rota-schedule-date-to' );

		if( timestampFrom != 0 ) {
			if( eventTime < (timestampFrom - 60*60*24) ) {
				showDetailError( owner, '.tagelin-rota-schedule-date-early' );
				return false;
			}
		}

		if( timestampTo != 0 ) {
			if( eventTime > (timestampTo + 60*60*24) ) {
				showDetailError( owner, '.tagelin-rota-schedule-date-late' );
				return false;
			}
		}

		// check if this is historical
		var now = new Date().getTime()/1000;


		if( 1 ) {
			if( eventTime < now - 60*60*24 ) {
				showDetailError( owner, '.tagelin-rota-schedule-date-no-edit' );
				return false;
			}
		}

		if( getDetailSelect( owner, '.tagelin-rota-schedule-event' ) < 0 ) {
			showDetailError( owner, '.tagelin-rota-schedule-event-deleted' );
			return false;
		}

		if( getDetailSelect( owner, '.tagelin-rota-schedule-location' ) == 0 ) {
			showDetailError( owner, '.tagelin-rota-schedule-location-deleted' );
		}


		var validPerson = true;
		schedule.detail.forEach( function ( detail ) {
			if( detail.person_id < 0 ) {
				validPerson = false;
			}
		});

		if( !validPerson ) {
//			console.log("Using deleted person " );
			return false;
		}


		return true;
	}

	/*
	 *	Called when a Schedule is cancelled
	 */
	function cancelSchedule( owner ) {
		deselectList( owner );
		setEditId( owner, 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-event', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-location', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-hours', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-minutes', 0 );
		setDetailSelect( owner, '.tagelin-rota-schedule-hours-end', -1 );
		setDetailSelect( owner, '.tagelin-rota-schedule-minutes-end', -1 );
		setDetailTextArea( owner, '.tagelin-rota-schedule-description', "" );
		clearAttributes( owner );
		setStateIdle( owner );
	}

	/*
	 *	Called when a Schedule is deleted
	 */
	function deleteSchedule( owner ) {
		var schedule = {};
		schedule.id = getEditId( owner );
		schedule.group_id = getGroupId( owner );

		startWaiting( owner );


		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'edit-schedule',
			request: 'delete_schedule',
			action: ajax_params.action,
			schedule: schedule
		};

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {
			},
			success: function( response ) {
				removeList( owner, schedule.id );
				cancelSchedule( owner );				
				stopWaiting( owner );

			}
		});


	}

/*	
 *	S C H E D U L E    V I E W    W I D G E T
 */


	/*
	 *	Calculate date range and show Schedule view
	 */
	$( '.tagelin-rota-widget .tagelin-rota-schedule-view' ).each( function () {

		var owner = $(this);

		var region='en';
		var dateFormat='d M yy';
		var timeFormat = 'HHs:MM';

		var rangeModifiers = {};
		
		// default range modifiers
		rangeModifiers.from ="";	// current time
		rangeModifiers.to="+1m";	// to end of month

		var monthPicker = owner.find('.tagelin-rota-month-picker' );

		var startDate = new Date();

		var config = owner.find('.tagelin-rota-config' );
		if( config.length > 0 ) {
			var configValue;
			configValue = config.find( '.tagelin-rota-range-from' ).val();
			if( configValue != "" ) {
				rangeModifiers.from = configValue;
			}
			configValue = config.find( '.tagelin-rota-range-to' ).val();
			if( configValue != "" ) {
				rangeModifiers.to = configValue;
			}
			configValue = config.find( '.tagelin-rota-range-selector' ).val();

			var theirMonthPickerClass = config.find( '.tagelin-rota-range-selector' ).val();
			if( theirMonthPickerClass != "" ) {
				if( theirMonthPickerClass == 'none' ){
					monthPicker = null;
				}
				else {
					var theirMonthPicker = $( '.'+theirMonthPickerClass );
					monthPicker = theirMonthPicker;
				}
			}

			configValue = config.find( '.tagelin-rota-region' ).val();
			if( configValue != "" ) {
				region = configValue;
			}

			configValue = config.find( '.tagelin-rota-date-format' ).val();
			if( configValue != "" ) {
				dateFormat = configValue;
			}

			configValue = config.find( '.tagelin-rota-time-format' ).val();
			if( configValue != "" ) {
				timeFormat = configValue;
			}


		}

		// save locale information in attrs
		owner.attr('region', region );
		owner.attr('date-format',dateFormat );
		owner.attr('time-format',timeFormat );

		// default locale for datepicker
//		$.datepicker.setDefaults($.datepicker.regional['en']);


		if( (  monthPicker != null )&& (monthPicker != "" ) ) {

			//monthPicker.datepicker($.datepicker.regional[ region ]); 
			monthPicker.datepicker(); 


			monthPicker.datepicker( "option", "dateFormat", 'MM yy' );
			monthPicker.datepicker( "option", "changeDay", false );
			monthPicker.datepicker( "option", "changeMonth", true );
			monthPicker.datepicker( "option", "changeYear", true );
			monthPicker.datepicker( "option", "showButtonPanel", false );

			monthPicker.datepicker( "option", "onChangeMonthYear",
				function (year,month,inst) { 
					var startDate = new Date(year, month-1, 1);
					showScheduleView( owner, startDate, rangeModifiers );
				} );



			monthPicker.addClass('tagelin-rota-month-picker' );

			monthPicker.focus(function () {
					$(".ui-datepicker-calendar").hide();
					$("#ui-datepicker-div").position({
						my: "center top",
						at: "center bottom",
						of: $(this)
					});
			});

/*
			monthPicker.datepicker({
					dateFormat: 'MM yy',
					changeDay: false,
					changeMonth: true,
					changeYear: true,
					showButtonPanel: false,

					onChangeMonthYear: function (year,month,inst) { 


						var startDate = new Date(year, month-1, 1);


						showScheduleView( owner, startDate, rangeModifiers );



					 }

					
			});
*/							
			
				
		}

		showScheduleView( owner, startDate, rangeModifiers );

	} );

	/*
		Apply date modifiers from shortcode to date.
		+1y	next year
		-1y 	last year
		+0y	to end of year
		-0y	to start of year
		+0m	to end of month
		-0m	to start of month
		+1d	to tomorrow
		+0f	to forever
		-0f	from forever
	 */
	function applyDateModifiers( date, dateModifiers ) {
		var modifiedDate =  date;
		var offset = 0;

		while(( dateModifiers != null )&& ( dateModifiers.substr(offset).length >= 3 )) {

			dateModifier = dateModifiers.substr(offset);

			var direction = dateModifier.charAt(0);
			if( ( direction == '-' ) || ( direction == '+' ) ) {

				var delta = parseInt( dateModifier.substr(1), 10 );


				if( delta == NaN ) {
					break;
				}
				else {
					var temp = direction+delta+'?';
					offset += temp.length;
					
					var scale = dateModifier.charAt( temp.length - 1 );
					if( scale == 'd' ) {

						if( delta == 0 ) {
							// no change
						}
						else
						if( direction == '+' ) {	
							modifiedDate = new Date(
								date.getFullYear(), 
								date.getMonth(), 
								date.getDate() + delta ); 
						}
						else
						if( direction == '-' ) {	
							modifiedDate = new Date(
								date.getFullYear(), 
								date.getMonth(), 
								date.getDate() - delta ); 
						}
					} 
					else 
					if( scale == 'm' ) {

						if( delta == 0 ) {
							if( direction == '+' ) {
								// end of month
								modifiedDate = new Date(
									date.getFullYear(), 
									date.getMonth() + 1, 
									1  ); 
							}

							if( direction == '-' ) {
								// start of month
								modifiedDate = new Date(
									date.getFullYear(), 
									date.getMonth(), 
									1  ); 
							}
						}
						else
						if( direction == '+' ) {	
							modifiedDate = new Date(
								date.getFullYear(), 
								date.getMonth() + delta, 
								date.getDate()  ); 
						}
						else
						if( direction == '-' ) {	
							modifiedDate = new Date(
								date.getFullYear(), 
								date.getMonth() - delta, 
								date.getDate()  ); 
						}


					}
					else
					if( scale == 'y' ) {
						if( delta == 0 ) {
							if( direction == '+' ) {
								// end of year
								modifiedDate = new Date(
									date.getFullYear()+1, 
									0, 
									1  ); 
							}

							if( direction == '-' ) {
								// start of year
								modifiedDate = new Date(
									date.getFullYear(), 
									0, 
									1  ); 
							}
						}
						else
						if( direction == '+' ) {	
							modifiedDate = new Date(
								date.getFullYear() + delta, 
								date.getMonth() , 
								date.getDate()  ); 
						}
						else
						if( direction == '-' ) {	
							modifiedDate = new Date(
								date.getFullYear() - delta, 
								date.getMonth() , 
								date.getDate()  ); 
						}

					}
					else
					if( scale == 'f' ) {
						if( direction == '-' ) {
							// from forever
							modifiedDate = new Date(0);
						}
						if( direction == '+' ) {
							// to forever
							modifiedDate = null;	
						}
					}
				}

			}
		}
		return modifiedDate;
	}

	/*
	 *	Show all Schedule in this date range
	 */
	function showScheduleView( owner, startDate, rangeModifiers ) {

		var viewFrom = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() ); 

		if( rangeModifiers.from )
		{
			viewFrom = applyDateModifiers( viewFrom, rangeModifiers.from );
		}

		var viewTo= new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() ); 

		if( rangeModifiers.to )
		{
			viewTo = applyDateModifiers( viewTo, rangeModifiers.to );
		}

 		getScheduleView( owner, viewFrom, viewTo );
	}

	/*
	 *	Display view of Schedule details
	 */
	function getScheduleView( owner, viewFrom, viewTo ) {



		var timeFormat = owner.attr( 'date-format' );
	

		var request = {
			nonce: ajax_params.tagelin_rota_nonce,
			widget: 'view-schedule',
			request: 'get_schedules',
			action: ajax_params.action,
			group_id: getGroupId( owner ),
			view_from: (viewFrom == null )? 0: viewFrom.getTime()/1000,
			view_to: (viewTo == null )? 0:viewTo.getTime()/1000

		}

		$.ajax({
			url: ajax_params.ajax_url,
			type: 'post',
			dataType: 'json',
			data: request,
			failure: function ( response ) {
			},
			success: function( response ) {
				schedule=response.data;
				var region = owner.attr( 'region');
				var dateFormat= owner.attr( 'date-format' );

				owner.find( '.tagelin-rota-schedule-view-list' ) .empty();

				for (var i = 0; i < schedule.length; i++) {
					var scheduled_event = schedule[i];

					// get UTC Date
					var event_time = new Date(  scheduled_event['timestamp'] * 1000 );

					// convert to local timezone
					event_time.setMinutes(
						event_time.getMinutes() 
						+ event_time.getTimezoneOffset()
					);

					var hours = ("0"+event_time.getHours() ).slice(-2);
					var minutes = ("0"+event_time.getMinutes() ).slice(-2);

					var time = hours + ':' + minutes;




					// get UTC End Date
					var until = "";
					if( scheduled_event['until'] != 0 ) {
						event_time = new Date(  scheduled_event['until'] * 1000 );

						// convert to local timezone
						event_time.setMinutes(
							event_time.getMinutes() 
							+ event_time.getTimezoneOffset()
						);

						hours = ("0"+event_time.getHours() ).slice(-2);
						minutes = ("0"+event_time.getMinutes() ).slice(-2);

						until = ' - ' + hours + ':' + minutes;
					}

					scheduled_event['format_time']
						= $.datepicker.formatDate( 
							dateFormat,//"DD, MM d, yy", 
							event_time,
							 $.datepicker.regional[region] )
						+ ' '
						+ time
						+ until;


					for( var d=0; d<scheduled_event.detail.length; d ++ ){
						var detail = scheduled_event.detail[d];
						if( detail['person']==null )
							detail.person="";
					}

//					$( '.tagelin-rota-view-template').tmpl( scheduled_event ).appendTo(
//						owner.find( '.tagelin-rota-schedule-view-list' ) ); 

					var template=$( '.tagelin-rota-view-template');
					var output = render_template( template.html(), scheduled_event );
					owner.find( '.tagelin-rota-schedule-view-list' ).append( output );


				}

			}
		});

	}


	


/*
 *	U T I L I T Y   H E L P E R S
 */	

	/*
	 *	Set event handlers for a widget
	 */
	function addEventHandlers(handler) {
		$( '.tagelin-rota-widget' +' '+handler.target ).each( function () {
			setStateIdle( $(this) );


			$(this).data( 'tagelin-eventhandlers', handler );

			// Button 'New'
			$(this).find( '.tagelin-rota-button-band .tagelin-rota-button-new'  ).each( function() {
				$(this).click( function( event ) {
					event.preventDefault();
					var button = event.currentTarget;
					var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
					handler.onNew( owner );
					setStateNew( $(owner) );
				});
			});

			// Button 'Save'
			$(this).find( '.tagelin-rota-button-band .tagelin-rota-button-save' ).each( function() {
				$(this).click( function( event ) {
					event.preventDefault();
					var button = event.currentTarget;
					var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
					handler.onSave( owner );
					setStateEdit( owner ) 
				});
			});

			// Button 'Cancel'
			$(this).find( '.tagelin-rota-button-band .tagelin-rota-button-cancel' ).each( function() {
				$(this).click( function( event ) {

					event.preventDefault();
					var button = event.currentTarget;
					var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
					handler.onCancel( owner );

					hideDetailError( owner );

					var entry = {};
					entry.id = getEditId( owner );
			
			
					if( entry.id != 0 ) {
						handler.onEdit( owner,entry );
						setStateEdit( owner );
					}
					else {
						setStateIdle( owner );
					}

				});
			});

			// Button 'Delete'
			$(this).find( '.tagelin-rota-button-band .tagelin-rota-button-delete' ).each( function() {
				$(this).click( function( event ) {
					event.preventDefault();
					var button = event.currentTarget;
					var owner = $(button).parentsUntil( '.tagelin-rota-widget' ).last();
					handler.onDelete( owner );
					setStateIdle( owner ) 
				});
			});
		});
	}


	/*
	 *	Set buttons etc
	 *	Empty and disabled form, blank feilds
	 */
	function setStateIdle( owner ) {

		// clear error messages
		hideDetailError( owner );

		setEditId( owner, 0 );

		// empty and disabled form, blank feilds
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry input' ).attr("disabled", true);
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry input' ).val('');
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry input' ).attr('placeholder','');

		// empty and disabled form, blank feilds
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry textarea' ).attr("disabled", true);
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry textarea' ).val('');
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry textarea' ).attr('placeholder','');


		// disable buttons & selectors
		owner.find( '.tagelin-rota-detail  select' ).each( function() {
			$(this).attr('disabled', true );
		});	

		// deselect list
		owner.find( '.tagelin-rota-list-content tbody tr' ).removeClass( "tagelin-rota-list-selected" );

		owner.find ( '.tagelin-rota-button-new' ).attr("disabled", false);
		owner.find ( '.tagelin-rota-button-save' ).attr("disabled", true);
		owner.find ( '.tagelin-rota-button-cancel' ).attr("disabled", true);
		owner.find ( '.tagelin-rota-button-delete' ).attr("disabled", true);
	}


	/*
	 *	New and enabled form, show placeholders
	 */
	function setStateNew( owner ) {

		// clear error messages
		hideDetailError( owner );


		setEditId( owner, 0 );

		// enable fields and show placeholders
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry input' ).each( function() {
			$(this).attr('disabled', false );
			$(this).val( '' );
			$(this).attr('placeholder', $(this).attr('placeholdertext') );
		});


		// enable textarea
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry textarea' ).each( function() {
			$(this).attr('disabled', false );
			$(this).val( '' );
			$(this).attr('placeholder', $(this).attr('placeholdertext') );
		});


		// enable all dropdown
		owner.find( '.tagelin-rota-detail  select' ).each( function() {
			$(this).attr('disabled',false);
		});

		// enable buttons
		owner.find( '.tagelin-rota-detail  input:button' ).each( function() {
			$(this).attr('disabled',false);
		});

		// deselect list
		owner.find( '.tagelin-rota-list-content tbody tr' ).removeClass( "tagelin-rota-list-selected" );


		owner.find ( '.tagelin-rota-button-new' ).attr("disabled", true);
		owner.find ( '.tagelin-rota-button-save' ).attr("disabled", false);
		owner.find ( '.tagelin-rota-button-cancel' ).attr("disabled", false);
		owner.find ( '.tagelin-rota-button-delete' ).attr("disabled", true);
	}

	/*
	 *	Display and enable inputs, allow save or cancel
	 */
	function setStateEdit( owner ) {

		// display and enable inputs, allow save or cancel
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry input' ).attr("disabled", false);

		owner.find( '.tagelin-rota-detail .tagelin-rota-entry textarea' ).attr("disabled", false);

		// enable all dropdown
		owner.find( '.tagelin-rota-detail .tagelin-rota-entry select' ).each( function() {
			$(this).attr('disabled',false);
		});

		owner.find ( '.tagelin-rota-button-new' ).attr("disabled", false);
		owner.find ( '.tagelin-rota-button-save' ).attr("disabled", false );
		owner.find ( '.tagelin-rota-button-cancel' ).attr("disabled", false );
		owner.find ( '.tagelin-rota-button-delete' ).attr("disabled", false );
	}


	/*
	 *	Render name in escaped html
	 *	return key:value pair for list entry
	 */
	function defaultListFormat( owner, item ) {
		return  { 
			key:escapeHtml( item.name ).toLowerCase(),
			value: escapeHtml( item.name ) 
		};
	}	


	/**
	 *	Helper for render template
	 */
	function render_template_parts( parts, start, stop, object ) {
		var output =  "";
		for (var i = start; i <= stop; i++) {
			part = parts[i].trim();
			if( part.length == 0 ) {
				continue;
			}
			// strip trailing $ before variable
			if( part[part.length-1] == '$' ) {
				part = part.substr( 0, part.length-1);
			}

			var previous = parts[i-1];

			if( previous.length == 0 ) {
				// keyword

				if( part.substr(0,2) == 'if' ) {
					var tail = part.substr(2).split('}');
					var condition = tail[0].trim();
					
					var first;
					var second;
					var test;

					if( condition [0] == '"' ) {
						var end = condition.indexOf( '"' , 1 );
						first = condition.substr(1, end-1 );
						condition = condition.substr(end+1).trim();
					}
					else
					if( condition [0] == "'" ) {
						var end = condition.indexOf( "'" , 1 );
						first = condition.substr(1, end-1 );
						condition = condition.substr(end+1).trim();
					}
					else
					if( (condition[0] == '-' ) || ( !isNaN(parseInt( condition[0], 10)) ) ) {
						for( var ii = 1; !isNaN(parseInt( condition[ii], 10)); ii ++ ) {
						}
						first = parseInt( condition.substr(0,ii ) );
						condition=condition.substr(ii).trim();
					}
					else
					if( condition.substr(0,4) === "true" ) {
						first = true;
						condition=condition.substr(4).trim();
					}
					else
					if( condition.substr(0,5) === "false" ) {
						first = false;
						condition=condition.substr(5).trim();
					}
					else
					{
						var ii = 0;
						while( /^\w+$/i.test(condition.charAt(ii) )) {
							ii ++;
						}

						first = object [ condition.substr(0,ii) ];
						condition=condition.substr(ii).trim();

					}


					{
						// get test
						var ii = 0;
						while( /^[!=<>]/i.test(condition.charAt(ii) )) {
							ii ++;
						}
						test = condition.substr(0,ii);
						condition=condition.substr(ii).trim();
					}


					if( condition [0] == '"' ) {
						var end = condition.indexOf( '"' , 1 );
						second = condition.substr(1, end-1 );
						condition = condition.substr(end+1).trim();
					}
					else
					if( condition [0] == "'" ) {
						var end = condition.indexOf( "'" , 1 );
						second = condition.substr(1, end-1 );
						condition = condition.substr(end+1).trim();
					}
					else
					if( (condition[0] == '-' ) || ( !isNaN(parseInt( condition[0], 10)) ) ) {
						for( var ii = 1; !isNaN(parseInt( condition[ii], 10)); ii ++ ) {
						}
						second = parseInt( condition.substr(0,ii ) );
						condition=condition.substr(ii).trim();
					}
					else
					if( condition.substr(0,4) === "null" ) {
						second = null;
						condition=condition.substr(4).trim();
					}
					else
					if( condition.substr(0,4) === "true" ) {
						second = true;
						condition=condition.substr(4).trim();
					}
					else
					if( condition.substr(0,5) === "false" ) {
						second = false;
						condition=condition.substr(5).trim();
					}
					else
					{
						var ii = 0;
						while( /^\w+$/i.test(condition.charAt(ii) )) {
							ii ++;
						}

						second = object [ condition.substr(0,ii) ];
						condition=condition.substr(ii).trim();;

					}

					// Evaluation
					var expr = false;


					if(( test == "===" ) || ( test == "==" ) ) {
						expr = first === second;
					}
					if(( test == "!==" ) || ( test == "!=" ) ) {
						expr = first !== second;
					}

					var conditional_start = -1;
					var conditional_stop = -1;
					var level = 0;
					
					if( expr ) {
					
						conditional_start = i+1;	
						if( tail.length > 2 )
							output += tail[2];
					}
					for( var index = i+1; index <= stop; index ++ ) {
						var line = parts[index];
						if( line.substr(0,2) == 'if' ) {
							level ++;
						}
						else
						if( line.substr(0,3) == '/if' ) {
							
							if( level == 0 ) {
								if( expr ) {
		
									if( conditional_stop == -1 ) {
										conditional_stop = index -1;
									}
								}
								else {
									if( conditional_start != -1 ) {
										conditional_stop = index -1;
									}
								}
								// set continuation point in outer loop
								i = index;

								break;						
							}
							level --;
						}
						else
						if( line.substr(0,4) == 'else' ) {
							if( level == 0 ) {
								if( !expr ) {
									conditional_start = index + 1;

									// strip trailing $ before variable
									if( line[line.length-1] == '$' ) {
										line 
										= line.substr( 0,line.length-1);
									}


									// trim remainder of line
									tail = line.substr(4).split('}');
						
									if( tail.length > 2 ) {
										output += tail[2];
									}
									

								}
								else {
									conditional_stop = index - 1;
								}
							}
						}

					}

					if( conditional_start != -1 ) {
						output += render_template_parts( parts, conditional_start, conditional_stop, object );
					}
					var line = parts[index];
					if( line.substr(0,3) == '/if' ) {
						// strip trailing $ before variable
						if( line[line.length-1] == '$' ) {
							line = line.substr( 0,line.length-1);
						}
						// trim remainder of line
						tail = line.substr(3).split('}');
						
						if( tail.length > 2 ) {
							output += tail[2];
						}
					}
					

				}
				else
				if( part.substr(0,4) == 'tmpl' ) {
					var tail = part.substr(4).split('}');
					
					var ii = tail[0].indexOf( ')' );
					var name = tail[0].substr(1, ii-1 ).trim();
					var selector = tail[0].substr( ii +1).trim();
					selector=selector.substr(1,selector.length-2).trim();

					var innerDestination = $(  selector  ); //selector );
					for( var k =0; k < object[name].length; k ++ )
					{
						var str = render_template(  innerDestination.html(), object[ name ][k] );
						output +=  str;
					}
				}

			}	
			else {
				// variable substitution,
				var variable = part.split( '}' );
				output += object [ variable[0] ];

				// append remainder of line
				for( var j = 1; j < variable.length; j ++ ) {
					output += variable[j];
				}
			}


		}
		return output;
	}	
	
	function render_template( template, object ) {
	
		var parts = template.split( '{' );
		if( parts.length ) {

			var part = parts[0];

			// strip trailing $ before variable
			if( part[part.length-1] == '$' ) {
				part = part.substr( 0, part.length-1);
			}

			var output = part;
			output +=  render_template_parts( parts, 1, parts.length-1, object );

			return output;
		}
		return "";
	}




	/*
	 *	Get the GroupId from hidden input in widget
	 */
	function getGroupId( owner ) {
		return owner.find( '.tagelin-rota-detail .tagelin-rota-group-id' ).val();
	}

	/*
	 *	Set the GroupId in hidden input in widget
	 */
	function setGroupId( owner, groupId ) {
		owner.find( '.tagelin-rota-detail .tagelin-rota-group-id' ).val( groupId );
	}

	/*
	 *	Get the EditId from hidden input in widget
	 */
	function getEditId( owner ) {
		return owner.find( '.tagelin-rota-detail .tagelin-rota-edit-id' ).val();
	}

	/*
	 *	Set the EditId in hidden input in widget
	 */
	function setEditId( owner, editId ) {
		owner.find( '.tagelin-rota-detail .tagelin-rota-edit-id' ).val( editId );
	}

	/*
	 *	Set the text value of an input field
	 */
	function setDetailText( owner, tag, value ) {
		owner.find( '.tagelin-rota-detail ' + tag ).val( value );
	}

	/*
	 *	Enable showing this error text
	 */
	function showDetailError( owner, tag ) {

		owner.find( tag ).css(
			{ "display": "inline-block"  } 
		);
	}

	/*
	 *	Disable showing all error texts in this widget
	 */
	function hideDetailError( owner ) {

		owner.find( '.tagelin-rota-entry-msg' ).css(
			{ "display": "none"  } 
		);

	}


	/*
	 *	Get the text value of an input field
	 */
	function getDetailText( owner, tag ) {
		return	owner.find( '.tagelin-rota-detail ' + tag ).val();
	}

	/*
	 *	Set the text value of a textarea
	 */
	function setDetailTextArea( owner, tag, value ) {
		owner.find( '.tagelin-rota-detail ' + tag ).val( value );
	}

	/*
	 *	Get the text value of a textarea field
	 */
	function getDetailTextArea( owner, tag ) {
		return	owner.find( '.tagelin-rota-detail ' + tag ).val();
	}

	/*
	 *	Set the selected value of a selection box
	 */
	function setDetailSelect( owner, tag, value ) {
		owner.find( '.tagelin-rota-detail ' + tag ).val( value ).change();
	}

	/*
	 *	Get the selected value of a selection box
	 */
	function getDetailSelect( owner, tag) {
		var value = owner.find( '.tagelin-rota-detail ' + tag ).val();
		if( value == null ) {
			value = 0;	// use 0 for unselected 
		}
		return value;
	}

	/*
	 *	Get the selected text of a selection box
	 */
	function getDetailSelectText( owner, tag) {
		var text = owner.find( '.tagelin-rota-detail ' + tag + ' option:selected ' ).text();
		if ( text == null ) {
			text = "";
		}
		return text;
	}


	/*
	 *	Remove deleted entries (with negative id)
	 */
	function purgeDetailSelect( owner, tag ) {
		owner.find( '.tagelin-rota-detail ' + tag +' option' ).each( function (){
			if( $(this).val() < 0 ) {
				// expired entry not required now
				$(this).remove();
			}
		});
	}

	/*
	 *	Set the selected value of a date field
	 */
	function setDetailDate( owner, tag, value ) {
		owner.find('.tagelin-rota-detail '+tag ).datepicker('option', 'defaultDate' , value );
		owner.find('.tagelin-rota-detail '+tag ).datepicker('setDate',  value );
	}

	/*
	 *	Get the selected value of a date field
	 */
	function getDetailDate( owner, tag ) {
		return owner.find('.tagelin-rota-detail '+tag ).datepicker('getDate' );
	}

	/*
 	 *	Add option to a selection box
	 */
	function appendOption(selector, item) {
		var option = $( '<option value="' + item.id + '" >' +item.name+'</option>');
		selector.append(option); 
	}

	/*
	 *	Add an attribute div to the list
	 */
	function appendAttribute( owner, attribute ) {
		owner.find( '.tagelin-rota-attributes-content' ).append( attribute );
	}

	/*
	 *	Clear a list table
	 */
	function clearList( owner ) {
		owner.find( '.tagelin-rota-list-content tbody tr' ).remove();
	} 

	/*
	 *	Remove all attribute div from the list
	 */
	function clearAttributes( owner ) {
		owner.find( '.tagelin-rota-attributes-content' ).empty();
	}

	/*
	 *	Regenerate the list display name for this item
	 */
	function updateList( owner, rowData ) {

		var handler = owner.data( 'tagelin-eventhandlers' );

		var listEntry = handler.formatList( owner, rowData );

//		owner.find( '.tagelin-rota-list-content [ref="'+rowData.id+'"] td' ).each( function () {
//			$(this).html( listEntry.value );
//		});

		var element = owner.find( '.tagelin-rota-list-content [ref="'+rowData.id+'"] td' );

		var row = element.parent();

		element.html( listEntry.value );
		row.attr( 'key', listEntry.key );



		// check position
		var needMove = false;
		var prev = row.prev();
		if(( prev.length > 0 ) && (  listEntry.key < prev.attr('key') ) ) {
			needMove = true;
		}

		var next = row.next();
		if(( next.length > 0 ) && (  listEntry.key > next.attr('key') ) ) {
			needMove = true;
		}

		if( needMove ) { 

			// move to new place
			var existingRow = owner.find( '.tagelin-rota-list-content tr' ).first();
			while( existingRow.length ) {
				if( listEntry.key < existingRow.attr('key' ) ) {
					// put it here
					existingRow.before( row );
					break;
				} 

				if( existingRow.next().length == 0 ) {
					// we have reached the end
					existingRow.after( row );
					break;
				}

				existingRow = existingRow.next();
				
			};

		}




	}

	/*
	 *	Add new element to the list display
	 */
	function appendList( owner, rowData ) {

		var handler = owner.data( 'tagelin-eventhandlers' );

		var row = $( '<tr />' );
		var element = $('<td />' );

		var listEntry = handler.formatList( owner, rowData );

		//element.html( escapeHtml(rowData.name) );
		element.html( listEntry.value );

		row.append( element );
		row.attr( 'ref', rowData.id );
		row.attr( 'key', listEntry.key );

		// Insert in list, order by ascending key:
		// First scan table to find index..
		var index = 0;
		var size = 0;
		owner.find( '.tagelin-rota-list-content tr' ).each( function () {
			var existingRow = $(this);
			size ++;
			if( listEntry.key < existingRow.attr('key' ) ) {
				// put it here
			} else {
				index ++;
			}
		} );

		if( size == 0 ) {
			// empty list. append
			owner.find( '.tagelin-rota-list-content tbody' ).append( row ); 
		}
		else {
			if( index == 0 ) {
				// before first entry
				owner.find( '.tagelin-rota-list-content tbody > tr').eq(index).before( row );
			}
			else {
				// after the last smaller 
				owner.find( '.tagelin-rota-list-content tbody > tr').eq(index-1).after( row );
			}
		}

		// list selection
		row.click( function( event ) {
				event.preventDefault();
				var row = event.currentTarget;

				owner.find( '.tagelin-rota-list-content tbody tr' ).removeClass( "tagelin-rota-list-selected" );
				$(row).addClass( "tagelin-rota-list-selected" );

				hideDetailError( owner );

				handler.onSelect( owner, row );
				handler.onEdit( owner, { id: $(row).attr( "ref" ) } );

				setStateEdit( owner );

		});		
	}

	/*
	 *	Build a display list from these items
	 */
	function buildList( owner, list_items ) {
		clearList( owner );

		if( list_items.length == 0 ) {
			var row = $( '<tr />' );
			row.append( $( '<td>&nbsp;</td>') );
			row.attr( "ref", 0 );
			owner.find( '.tagelin-rota-list-content tbody' ).append( row ); 
		}

		for( var i = 0; i < list_items.length; i++ ) {
			appendList( owner, list_items[i] );
		}

	}

	/*	
	 *	Called wwhen item is selected in list
	 */
	function selectList( owner, ref ) {

		owner.find( '.tagelin-rota-list-content tbody tr' ).removeClass( "tagelin-rota-list-selected" );
		owner.find( '.tagelin-rota-list-content [ref="'+ref+'"]' ).each( function () {
			// mark selected
			$(this).addClass( "tagelin-rota-list-selected" );
			// scroll to place
			owner.find( '.tagelin-rota-list-content' )[0].scrollTop = this.offsetTop;
		});

	}

	/*	
	 *	Called when item is deselected in list
	 */
	function deselectList( owner ) {
		owner.find( '.tagelin-rota-list-content tbody tr' ).removeClass( "tagelin-rota-list-selected" );
	}

	/*	
	 *	Remove item from list
	 */
	function removeList( owner, ref ) {
		owner.find( '.tagelin-rota-list-content [ref="'+ref+'"]' ).each( function () {
			$(this).remove();
		});

	}

	/**
	 *	Mark an attribute as selected by changing background
	 */
	function markSelectedAttribute( attribute ) {
		clearSelectedAttribute( attribute );
		attribute.addClass( 'tagelin-rota-list-selected');
	}


	function clearSelectedAttribute( attribute ) {

		// find attributes list
		var attributes = attribute.parent();

		// unmark any selected attributes
		attributes.find( '.tagelin-rota-list-selected' ).removeClass( 'tagelin-rota-list-selected');

	}

	/*
	 *	Build a selection list of hour values
	 */
	function init_hours(selector) {

		selector.each( function() {
			for (var i = 0; i < 24; i++) {
				var option = $( '<option value="' + i + '" >' + i +'</option>');
				$(this).append(option); 
			}

		} );
	}

	/*
	 *	Build a selection list of minute values
	 */
	function init_minutes(selector) {
		selector.each( function() {
			for (var i = 0; i < 60; i+=5) {
				var option = $( '<option value="' + i + '" >' + i +'</option>');
				$(this).append(option); 
			}

		} );		

	}


	/*
	 *	Change cursor to a 'waiting' state
	 */
	function startWaiting( owner ) {
		owner.addClass( "wait" );
	}

	/*
	 *	Change cursor back from a 'waiting' state
	 */
	function stopWaiting( owner ) {
		owner.removeClass( "wait" );
	}

	/*
	 *	Remove html escaping from a text
	 */
	function unescapeHtml( value ) {
		return  $("<p/>").html( value ).text(); 

	}

	/*
	 *	Apply html escaping to a text
	 */
	function escapeHtml( value ) {
		return $("<p/>").text( value ).html();

	}

	/*
	 *	Apply html escaping to a text
	 *
	function _escapeHtml( value ) {
		
		var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;',
		'`': '&#x60;',
		'=': '&#x3D;',
		"\n": '<br>'
		};

 		var mapped =  String(value).replace( /[&<>"'\/]|[\n]/g , function (c) {
			return map[c];
		});

		return mapped;
		
	}	*/


	/**
	 *	Two layouts. 
	 *	For desktop we use list + panel side by side.
	 *	For small screens we adopt a linear senquence
	 */
	function screenLayout( media ) {

		// Role widgets
		$( '.tagelin-rota-widget' ).each( function () {
			var rotaWidget = $(this);
	
			var list = rotaWidget.find( '.tagelin-rota-list-content' );
	
			var details = rotaWidget.find( '.tagelin-rota-detail' );

			var attributes = rotaWidget.find( '.tagelin-rota-attributes-content' );
			var attributesLabel = rotaWidget.find( '.tagelin-rota-attributes-label' );

			if( list.length > 0 ) {
				// If large screen set list height to match the detail panel
				if( media.matches )
				{
					list.removeClass("short-list");

					if( details.height() ) {
				
						//list.height( details.height());
						list[0].style.height =  details[0].clientHeight+"px";
					


						if( attributes.length>0 ) {
							if( attributesLabel.length > 0 ) {
						//		attributes.height( details.height()-attributesLabel.height() );
								attributes[0].style.height 
									=  (details[0].clientHeight - attributesLabel[0].clientHeight) 
									+"px";
						
							}
							else {
							//	attributes.height( details.height() );
						
								attributes[0].style.height =  list[0].style.height;
						
							}
						}
								
					}
				}
				else
				{
					// use list.addClass( "class with height 6em" )
					list.addClass("short-list");
				}
			}
		} );

	}




});