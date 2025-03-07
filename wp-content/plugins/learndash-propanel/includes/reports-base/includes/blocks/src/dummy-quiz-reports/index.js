// cspell:ignore eporting

import React, { Component, CSSProperties } from 'react';
import { __ } from '@wordpress/i18n';
import './style.scss';
import Select from 'react-select';

class DummyFilters extends Component {
	constructor( props ) {
		super( props );
	}

	render() {
		let body = '';
		body = (
			<div className="quiz-report-filters-wrapper wrld-dummy-filters">
				<div className="wrld-pro-note">
					<div className="wrld-pro-note-content">
						<span>
							<b>{ __( 'Note: ', 'learndash-reports-pro' ) }</b>
							{ __(
								'Below is the dummy representation of the Quiz Reports available in ProPanel.',
								'learndash-reports-pro'
							) }
						</span>
					</div>
				</div>
				<div className="select-view">
					<span>
						{ __( 'Select View', 'learndash-reports-pro' ) }
					</span>
				</div>
				<div className="quiz-report-types">
					<input
						id="dfr"
						type="radio"
						name="quiz-report-types"
						defaultValue="default-quiz-reports"
						defaultChecked=""
					/>
					<label htmlFor="dfr" className="">
						{ __(
							'Default Quiz Report View',
							'learndash-reports-pro'
						) }
					</label>
					<input
						id="cqr"
						type="radio"
						name="quiz-report-types"
						defaultValue="custom-quiz-reports"
						checked
					/>
					<label htmlFor="cqr" className="checked">
						{ ' ' }
						{ __(
							'Customized Quiz Report View',
							'learndash-reports-pro'
						) }
					</label>
				</div>
				<div>
					<div className="quiz-eporting-filter-section custom-filters">
						<div className="quiz-reporting-custom-filters">
							<div className="selector">
								<div className="selector-label">
									{ __( 'Courses', 'learndash-reports-pro' ) }
								</div>
								<div className="select-control">
									<Select
										isDisabled={ true }
										value={ {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
										} }
										isClearable={ true }
									/>
								</div>
							</div>
							<div className="selector">
								<div className="selector-label">
									{ __( 'Groups', 'learndash-reports-pro' ) }
								</div>
								<div className="select-control">
									<Select
										isDisabled={ true }
										value={ {
											value: null,
											label: __(
												'All',
												'learndash-reports-pro'
											),
										} }
										isClearable={ true }
									/>
								</div>
							</div>
							<div className="selector">
								<div className="selector-label">
									{ __( 'Quizzes', 'learndash-reports-pro' ) }
								</div>
								<Select
									isDisabled={ true }
									value={ {
										value: null,
										label: __(
											'All',
											'learndash-reports-pro'
										),
									} }
									isClearable={ true }
								/>
							</div>
						</div>
						<div className="filter-buttons">
							<div className="filter-button-container">
								<button className="button-customize-preview">
									{ __(
										'CUSTOMIZE REPORT',
										'learndash-reports-pro'
									) }
								</button>
								<button className="button-quiz-preview">
									{ __(
										'APPLY FILTERS',
										'learndash-reports-pro'
									) }
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
		return body;
	}
}

export default DummyFilters;
