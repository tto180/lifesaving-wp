if ( ! HTMLFormElement.prototype.reportValidity ) {
    
    if ( jQuery( '.rbp-support-form' ).length > 0 ) {

        /**
         * Wait, people use IE and Safari outside of downloading Chrome?
         * 
         * @since	  1.0.0
         * @return	  void
         */
        HTMLFormElement.prototype.reportValidity = function () {
            
            var $form;
            
            // This allows us to properly find our form whether it is in a <form> or a <div>
            if ( jQuery( this ).hasClass( 'rbp-support-form' ) ) {
                $form = jQuery( this );
            }
            else {
                $form = jQuery( this ).find( '.rbp-support-form' );
            }

            var prefix = $form.data( 'prefix' ),
                i18n = window[ prefix + '_support_form' ],
                error = i18n.validationError,
                valid = true;

            // Remove all old Validation Errors
            jQuery( this ).find( '.validation-error' ).remove();

            jQuery( this ).find( '.required' ).each( function( index, element ) {

                // Reset Custom Validity Message
                element.setCustomValidity( '' );

                if ( ! jQuery( element ).closest( 'td' ).hasClass( 'hidden') && 
                    ( jQuery( element ).val() === null || jQuery( element ).val() == '' ) ) {

                    element.setCustomValidity( error );
                    jQuery( element ).before( '<span class="validation-error">' + error + '</span>' );

                    valid = false;

                }

            } );

            if ( ! valid ) {

                jQuery( this ).closest( 'body' ).scrollTop( jQuery( this ).find( '.validation-error:first-of-type' ) );
                return valid;

            }

            return valid;

        };
        
    }
    
};