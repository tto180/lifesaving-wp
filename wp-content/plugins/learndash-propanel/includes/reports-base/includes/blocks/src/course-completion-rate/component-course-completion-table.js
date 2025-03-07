import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import React, { Component, CSSProperties } from 'react';

class CourseCompletionTable extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			tableData: props.tableData,
			sort: props.sort,
		};
		this.changeSort = this.changeSort.bind( this );
		this.changeDirection = this.changeDirection.bind( this );
	}

	componentDidMount() {
		//Patch logic for react state update on browser refresh bug.
		document.addEventListener(
			'completion-parent-sort-changed',
			this.changeDirection
		);
		jQuery(
			'.wisdm-learndash-reports-course-completion-table progress, .wisdm-learndash-reports-course-completion-table .progress-percentage'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );

		jQuery(
			'.wisdm-learndash-reports-course-completion-table span.toggle'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents(
						'.wisdm-learndash-reports-course-completion-table'
					)
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );
	}

	componentDidUpdate() {
		//Patch logic for react state update on browser refresh bug.
		jQuery(
			'.wisdm-learndash-reports-course-completion-table progress, .wisdm-learndash-reports-course-completion-table .progress-percentage'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents( '.graph-card-container' )
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );

		jQuery(
			'.wisdm-learndash-reports-course-completion-table span.toggle'
		)
			.on( 'mouseenter', function () {
				jQuery( this ).parent().css( { position: 'relative' } );
				const $div = jQuery( '<div/>' )
					.addClass( 'wrld-tooltip' )
					.css( {
						position: 'absolute',
						zIndex: 999,
						display: 'none',
					} )
					.appendTo( jQuery( this ).parent() );
				$div.text( jQuery( this ).attr( 'data-title' ) );
				const $font = jQuery( this )
					.parents(
						'.wisdm-learndash-reports-course-completion-table'
					)
					.css( 'font-family' );
				$div.css( 'font-family', $font );
				$div.show();
			} )
			.on( 'mouseleave', function () {
				jQuery( this ).parent().find( '.wrld-tooltip' ).remove();
			} );
	}

	static getDerivedStateFromProps( props, state ) {
		if ( props.sort !== state.sort ) {
			//Change in props
			return {
				sort: props.sort,
			};
		}
		if ( props.tableData !== state.tableData ) {
			//Change in props
			return {
				tableData: props.tableData,
			};
		}
		return null; // No change to state
	}

	changeDirection( event ) {
		this.setState( { sort: event.detail.value } );
	}

	changeSort() {
		let sort = '';
		if ( this.state.sort == 'ASC' ) {
			sort = 'DESC';
		} else {
			sort = 'ASC';
		}
		const durationEvent = new CustomEvent( 'local_sort_change_completion', {
			detail: { value: sort },
		} );
		document.dispatchEvent( durationEvent );
		this.setState( { sort } );
	}

	// componentDidUpdate() {
	// 	jQuery( ".wisdm-learndash-reports-course-completion-table progress, .wisdm-learndash-reports-course-completion-table .progress-percentage" ).hover(
	//     function() {
	//     	console.log('ss');
	//       var $div = jQuery('<div/>').addClass('wdm-tooltip').css({
	//           position: 'absolute',
	//           zIndex: 999,
	//           display: 'none'
	//       }).appendTo(jQuery(this));
	//       console.log($div);
	//       $div.text(jQuery(this).attr('data-title'));
	//       var $font = jQuery(this).parents('.graph-card-container').css('font-family');
	//       $div.css('font-family', $font);
	//       $div.show();
	//     }, function() {
	//       jQuery( this ).find( ".wdm-tooltip" ).remove();
	//     }
	//   );
	// }

	render() {
		return (
			<div className="wisdm-learndash-reports-course-completion-table">
				<table>
					<tbody>
						<tr>
							<th>
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Course'
								) }
							</th>
							<th>
								{ /* cspell:disable-next-line */ }
								{ wisdm_reports_get_ld_custom_lebel_if_avaiable(
									'Course'
								) +
									' ' +
									__(
										'Completion Rate',
										'learndash-reports-pro'
									) }
							</th>
						</tr>
						{ Object.keys( this.props.tableData ).map(
							( key, index ) => (
								<tr key={ index }>
									<td width="45%">
										<span className="course-name">
											{ key }
										</span>
									</td>
									<td width="55%" className="right-side">
										<progress
											className="progress"
											max="100"
											value={
												this.props.tableData[ key ]
													.percentage
											}
											data-title={
												this.props.tableData[ key ]
													.completed +
												__(
													' out of ',
													'learndash-reports-pro'
												) +
												this.props.tableData[ key ]
													.total +
												__(
													' learners completed',
													'learndash-reports-pro'
												)
											}
										></progress>
										<span
											className="progress-percentage"
											data-title={
												this.props.tableData[ key ]
													.completed +
												__(
													' out of ',
													'learndash-reports-pro'
												) +
												this.props.tableData[ key ]
													.total +
												__(
													' learners completed',
													'learndash-reports-pro'
												)
											}
										>
											{
												this.props.tableData[ key ]
													.percentage
											}
											%
										</span>
									</td>
								</tr>
							)
						) }
					</tbody>
				</table>
			</div>
		);
	}
}

export default CourseCompletionTable;
