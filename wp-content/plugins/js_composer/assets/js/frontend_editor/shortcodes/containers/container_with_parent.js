( function ( $ ) {
	'use strict';

	window.InlineShortcodeViewContainerWithParent = window.InlineShortcodeViewContainer.extend({
		controls_selector: '#vc_controls-template-container-with-parent',
		events: {
			'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
			'touchstart > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
			'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
			'touchstart > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
			'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
			'touchstart > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
			'click > .vc_controls .vc_element .vc_control-btn-copy': 'copy',
			'touchstart > .vc_controls .vc_element .vc_control-btn-copy': 'copy',
			'click > .vc_controls .vc_element .vc_control-btn-paste': 'paste',
			'touchstart > .vc_controls .vc_element .vc_control-btn-paste': 'paste',
			'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
			'touchstart > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
			'click > .vc_controls .vc_control-btn-append': 'appendElement',
			'touchstart > .vc_controls .vc_control-btn-append': 'appendElement',
			'click > .vc_controls .vc_parent .vc_control-btn-delete': 'destroyParent',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-delete': 'destroyParent',
			'click > .vc_controls .vc_parent .vc_control-btn-edit': 'editParent',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-edit': 'editParent',
			'click > .vc_controls .vc_parent .vc_control-btn-clone': 'cloneParent',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-clone': 'cloneParent',
			'click > .vc_controls .vc_parent .vc_control-btn-copy': 'copyParent',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-copy': 'copyParent',
			'click > .vc_controls .vc_parent .vc_control-btn-paste': 'pasteParent',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-paste': 'pasteParent',
			'click > .vc_controls .vc_parent .vc_control-btn-prepend': 'addSibling',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-prepend': 'addSibling',
			'click > .vc_controls .vc_parent .vc_control-btn-layout': 'changeLayout',
			'touchstart > .vc_controls .vc_parent .vc_control-btn-layout': 'changeLayout',
			'click > .vc_empty-element': 'appendElement',
			'touchstart > .vc_empty-element': 'appendElement',
			'click > .vc_controls .vc_control-btn-switcher': 'switchControls',
			'touchstart > .vc_controls .vc_control-btn-switcher': 'switchControls',
			'mouseenter': 'resetActive',
			'mouseleave': 'holdActive'
		},
		destroyParent: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.destroy( e );
		},
		cloneParent: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.clone( e );
		},
		copyParent: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.copy( e );
		},
		pasteParent: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.paste( e );
		},
		editParent: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.edit( e );
		},
		addSibling: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.addElement( e );
		},
		changeLayout: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.parent_view.changeLayout( e );
		},
		switchControls: function ( e ) {
			var $control, $parent, $current;
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			vc.unsetHoldActive();
			$control = $( e.currentTarget );
			$parent = $control.parent();
			// $parentAdvanced = $parent.find( '.vc_advanced' );
			// $parentAdvanced.width( 30 * $parentAdvanced.find( '.vc_control-btn' ).length );
			$parent.addClass( 'vc_active' );

			$current = $parent.siblings( '.vc_active' );
			// $current.find( '.vc_advanced' ).width( 0 );
			$current.removeClass( 'vc_active' );
			if ( !$current.hasClass( 'vc_element' ) ) {
				window.setTimeout( this.holdActive, 500 );
			}
		}
	});
})( window.jQuery );
