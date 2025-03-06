import 'jquery.repeater';
import 'flatpickr';
import FieldsInitialize from "./fields-init";

// Initialize app on jQuery Ready.
jQuery(() => {

    const Fields = new FieldsInitialize(jQuery(document));
});

// Re-init any fields within a container
window.rbmFHinitField = function( $root ) {
	
	new FieldsInitialize( $root );
	
}

// Grab Field Object from the RBM_FieldHelpers global in order to run methods on them
window.rbmFHgetFieldObject = function( name, instance = null, fieldType = null ) {
	
	if ( instance === null ) {
		
		instance = jQuery( '.fieldhelpers-field[data-fieldhelpers-name="' + name + '"]' ).data( 'fieldhelpers-instance' );
		
	}
	
	if ( fieldType === null ) {
		
		let classNames = jQuery( '.fieldhelpers-field[data-fieldhelpers-name="' + name + '"][data-fieldhelpers-instance="' + instance + '"]' ).first().attr( 'class' );
		
		let test = /fieldhelpers-field-(\S*)/.exec( classNames );
		
		if ( test !== null && 
		   test[1].length > 0 ) {
			
			fieldType = test[1];
			
		}
		
	}
	
	try {
	
		return RBM_FieldHelpers[ instance ]['fieldObjects'][ fieldType ][ name ];
		
	}
	catch ( error ) {
		
		if ( instance === null || 
		   fieldType === null || 
		   RBM_FieldHelpers[ instance ]['fieldObjects'][ fieldType ][ name ].length < 0 ) {
			console.error( 'Field Helpers Error: Field Object for "' + name + '" Not Found' );
		}
		
		return false;
	}
	
}