<?php
/**
 * This file contains a class that is used to setup the LearnDash endpoints.
 *
 * @package LearnDash\Reports
 *
 * cspell:ignore coursewise userroles wpapi
 */

defined( 'ABSPATH' ) || exit;

require_once 'class-wrld-common-functions.php';
/**
 * Class that sets up all the LearnDash endpoints
 *
 * @author LearnDash
 * @since 1.0.0
 * @subpackage LearnDash API
 */
class WRLD_LearnDash_Endpoints extends WRLD_Common_Functions {
	/**
	 * This static contains the number of points being assigned on course completion
	 *
	 * @var Instance of WRLD_LearnDash_Endpoints class
	 * @since 1.0.0
	 * @access private
	 */
	private static $instance = null;

	/**
	 * API version
	 *
	 * @var string
	 */

	private static $version = 'v1';
	/**
	 * API namespace.
	 *
	 * @var string
	 */

	private static $namespace = 'rp';

	/**
	 * This static method is used to return a single instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 * @return Object
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * This getter method returns the API version.
	 *
	 * @return string
	 */
	public static function get_api_version() {
		return self::$version;
	}

	/**
	 * This is a constructor which will be used to initialize required hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 * @see register_hook static method.
	 */
	private function __construct() {
		$this->include_api_files();
		// Set API endpoint version.
		self::register_hook(
			'init',
			'set_endpoint_version',
			$this,
			array(
				'type'     => 'action',
				'priority' => 100,
				'num_args' => 0,
			)
		);
		// Register custom endpoints.
		self::register_hook(
			'rest_api_init',
			'register_custom_endpoints',
			$this,
			array(
				'type'     => 'action',
				'priority' => 10,
				'num_args' => 0,
			)
		);

		self::register_hook(
			'wrld_user_accessibility_for_reports',
			'filter_excluded_users',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 11,
				'num_args' => 1,
			)
		);

		self::register_hook(
			'wrld_user_accessibility_for_reports',
			'filter_excluded_userroles',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 10,
				'num_args' => 1,
			)
		);

		self::register_hook(
			'wrld_course_accessibility_for_reports',
			'filter_excluded_courses',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 11,
				'num_args' => 1,
			)
		);

		self::register_hook(
			'rest_user_query',
			'filter_excluded_users_wpapi',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 10,
				'num_args' => 1,
			)
		);

		self::register_hook(
			'learndash_get_assignments_pending_count_query_args',
			'filter_pending_assignments',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 10,
				'num_args' => 1,
			)
		);

		self::register_hook(
			'rest_request_after_callbacks',
			'allow_gl_ld_api_access',
			$this,
			array(
				'type'     => 'filter',
				'priority' => 10,
				'num_args' => 3,
			)
		);
	}

	public function filter_pending_assignments( $query_args ) {
		if ( ! isset( $query_args['reports_api'] ) ) {
			return $query_args;
		}
		$excluded_courses = get_option( 'exclude_courses', false );
		if ( ! empty( $excluded_courses ) ) {
			$query_args['meta_query'][] = array(
				'key'     => 'course_id',
				'value'   => $excluded_courses,
				'compare' => 'NOT IN',
			);
		}

		$excluded_users = get_option( 'exclude_users', array() );
		if ( ! empty( $excluded_users ) && defined( 'LDRP_PLUGIN_VERSION' ) ) {
			$query_args['author__not_in'] = $excluded_users;
		}

		$excluded_ur = get_option( 'exclude_ur', false );
		if ( ! empty( $excluded_ur ) && defined( 'LDRP_PLUGIN_VERSION' ) ) {
			$args = array(
				'number'   => -1,
				'fields'   => array(
					'ID',
				),
				'role__in' => $excluded_ur,
			);

			$users    = get_users( $args );
			$user_ids = wp_list_pluck( $users, 'ID' );
			if ( isset( $query_args['author__not_in'] ) ) {
				$query_args['author__not_in'] = array_merge( $query_args['author__not_in'], $user_ids );
			} else {
				$query_args['author__not_in'] = $user_ids;
			}
		}
		return $query_args;
	}

	public function filter_excluded_users_wpapi( $prepared_args ) {
		if ( ! isset( $_GET['reports'] ) || empty( $_GET['reports'] ) ) {
			return $prepared_args;
		}
		$excluded_users = get_option( 'exclude_users', false );
		$excluded_ur    = get_option( 'exclude_ur', false );

		if ( ! empty( $excluded_users ) ) {
			$prepared_args['exclude'] = $excluded_users;
		}

		if ( ! empty( $excluded_ur ) ) {
			$prepared_args['role__not_in'] = $excluded_ur;
		}
		return $prepared_args;
	}

	/**
	 * This method is used to set endpoint version and namespace.
	 */
	public function set_endpoint_version() {
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			return;
		}
		self::$version   = 'v1';
		self::$namespace = 'rp';
	}

	/**
	 * This method includes all the classes related to our endpoints.
	 */
	public function include_api_files() {
		include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-revenue-api.php';
		include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-course-progress-info.php';
		include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-course-time-tracking.php';
		include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-quiz-reporting-tools.php';
		include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-quiz-export-db.php';

		WRLD_Course_Time_Tracking::get_instance()->init_hooks();
	}

	/**
	 * Add all the WordPress actions/filters
	 *
	 * @since 1.0.0
	 * @access private
	 * @param [string] $hook     [action-hook].
	 * @param [string] $callback [class method].
	 * @param [object] $scope    [class name or object instance].
	 * @param [array]  $args     [type of hook, it's priority and number of arguments to the callback].
	 */
	private static function register_hook( $hook, $callback, $scope, $args ) {
		call_user_func_array( 'add_' . $args['type'], array( $hook, array( $scope, $callback ), $args['priority'], $args['num_args'] ) );
	}

	/**
	 * Returns whether the user has access to the API.
	 *
	 * By managers, we mean admin users, group leaders, and instructors.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function restrict_access_to_managers(): bool {
		$user_id = get_current_user_id();

		return learndash_is_admin_user( $user_id )
			|| learndash_is_group_leader_user( $user_id )
			|| (
				function_exists( 'wdm_is_instructor' )
				&& wdm_is_instructor( $user_id )
			);
	}

	/**
	 * Provides the ability to force API calls to fail based on the output of a filter.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request object.
	 *
	 * @return bool Whether this API call should be allowed or not.
	 */
	public static function permission_callback_filterable( $request ): bool {
		/**
		 * Filters whether the user has access to the API.
		 *
		 * @since 3.0.0
		 *
		 * @param bool            $can_access Whether the user has access to the API. Default is true.
		 * @param int             $user_id    Current user ID. It can be 0 if the user is not logged in.
		 * @param WP_REST_Request $request    WP_REST_Request object.
		 *
		 * @return bool
		 */
		return apply_filters( 'learndash_propanel_api_user_has_access', true, get_current_user_id(), $request );
	}

	/**
	 * This is a method used to register the My Courses Endpoint
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_custom_endpoints() {
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/total-revenue-earned/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Revenue_API::get_instance(),
					'get_total_revenue',
				),
				'permission_callback' => function ( $request ) {
					/**
					 * Filters whether the user has access to the total revenue earned endpoint.
					 *
					 * @deprecated 3.0.0 Use the `learndash_propanel_api_user_has_access` filter instead.
					 *
					 * @param bool $has_access Whether the user has access to the total revenue endpoint. Default is the return value of the `restrict_access_to_managers` method.
					 *
					 * @return bool
					 */
					$has_access = apply_filters_deprecated(
						'wrld_filter_total_revenue_access_permission',
						[ self::restrict_access_to_managers() ],
						'3.0.0',
						'learndash_propanel_api_user_has_access'
					);

					return $has_access && self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/total-courses/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Revenue_API::get_instance(),
					'get_total_courses',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/total-learners/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Revenue_API::get_instance(),
					'get_total_learners',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/revenue-from-courses/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Revenue_API::get_instance(),
					'get_coursewise_revenue',
				),
				'permission_callback' => function ( $request ) {
					/**
					 * Filters whether the user has access to the revenue from courses endpoint.
					 *
					 * @deprecated 3.0.0 Use `learndash_propanel_api_user_has_access` filter instead.
					 *
					 * @param bool $has_access Whether the user has access to the revenue from courses endpoint. Default is the return value of the `restrict_access_to_managers` method.
					 *
					 * @return bool
					 */
					$has_access = apply_filters_deprecated(
						'wrld_filter_coursewise_revenue_access_permission',
						[ self::restrict_access_to_managers() ],
						'3.0.0',
						'learndash_propanel_api_user_has_access'
					);

					return $has_access && self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/daily-enrollments/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_daily_enrollments',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/time-spent-on-a-course/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'get_course_time_spent',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/time-spent-on-a-course-csv/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'get_course_time_spent_csv',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/time-spent-on-a-course-filter/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'set_course_time_spent_filter',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-time-details/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'get_course_time_details',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/time-spent-on-a-lesson-topic/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'get_lesson_topic_time_spent',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-completion-rate/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_completion_rate',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-completion-rate-csv/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_completion_rate_csv',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-completion-average/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_completion_rate_average',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-progress-rate/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_progress_rate',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-progress-rate-csv/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_progress_rate_csv',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-progress-details/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_progress_details',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/quiz-completion-time-per-course/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Time_Tracking::get_instance(),
					'get_quiz_completion_time',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/quiz-completion-rate/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_quiz_completion_rate',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/learner-pass-fail-rate-per-course/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_quiz_passing_rate',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/average-quiz-attempts/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_average_quiz_attempts',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/course-list-info/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_course_list_info',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/qre-live-search/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Quiz_Reporting_Tools::get_instance(),
					'qre_live_search_results',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/pending-assignments/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_pending_assignments_info',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/inactive-users/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_inactive_users_info',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/learner-activity-log/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					WRLD_Course_Progress_Info::get_instance(),
					'get_learner_activity_log',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/student-dashboard-info/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					\WRLD_Quiz_Reporting_Tools::get_instance(),
					'wrld_get_student_dashboard_results',
				),
				'permission_callback' => function ( $request ) {
					return is_user_logged_in()
						&& self::permission_callback_filterable( $request );
				},
			)
		);
		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/question-details/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					\WRLD_Quiz_Reporting_Tools::get_instance(),
					'wrld_get_question_details',
				),
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/report-filters-data/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array(
					__CLASS__,
					'wrld_get_report_filters_data',
				),
				'permission_callback' => function ( $request ) {
					return is_user_logged_in()
						&& self::permission_callback_filterable( $request );
				},
			)
		);

		register_rest_route(
			self::$namespace . '/' . self::$version,
			'/learners/',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [
					WRLD_Course_Progress_Info::get_instance(),
					'get_learners',
				],
				'permission_callback' => function ( $request ) {
					return self::restrict_access_to_managers()
						&& self::permission_callback_filterable( $request );
				},
			]
		);
	}

	public function filter_excluded_users( $accessible_users ) {
		$excluded_users = get_option( 'exclude_users', false );
		if ( empty( $excluded_users ) || ! defined( 'LDRP_PLUGIN_VERSION' ) ) {
			return $accessible_users;
		}
		if ( is_null( $accessible_users ) || -1 === intval( $accessible_users ) ) {
			// $args = array(
			// 'number'  => -1,
			// 'fields'  => array(
			// 'ID',
			// ),
			// 'exclude' => $excluded_users,
			// );

			// $users    = get_users( $args );
			// $user_ids = wp_list_pluck( $users, 'ID' );
			return $accessible_users;
		}
		return array_diff( $accessible_users, $excluded_users );
	}

	public function filter_excluded_courses( $accessible_courses ) {
		$excluded_courses = get_option( 'exclude_courses', false );
		if ( empty( $excluded_courses ) ) {
			return $accessible_courses;
		}
		if ( empty( $accessible_courses ) || -1 === intval( $accessible_courses ) ) {
			$args = array(
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'post__not_in'     => $excluded_courses,
				'post_type'        => 'sfwd-courses',
				'suppress_filters' => 0,
			);

			$courses = get_posts( $args );
			return $courses;
		}
		return array_diff( $accessible_courses, $excluded_courses );
	}

	public function filter_excluded_userroles( $accessible_users ) {
		$excluded_users = get_option( 'exclude_ur', false );
		if ( empty( $excluded_users ) || ! defined( 'LDRP_PLUGIN_VERSION' ) ) {
			return $accessible_users;
		}
		if ( is_null( $accessible_users ) || -1 === intval( $accessible_users ) ) {
			$args = array(
				'number'       => -1,
				'fields'       => array(
					'ID',
				),
				'role__not_in' => $excluded_users,
			);

			$users    = get_users( $args );
			$user_ids = wp_list_pluck( $users, 'ID' );
			return $user_ids;
		}
		$args = array(
			'number'       => -1,
			'fields'       => array(
				'ID',
			),
			'role__not_in' => $excluded_users,
		);

		$users = get_users( $args );
		if ( empty( $users ) ) {
			return array();
		}
		$user_ids = wp_list_pluck( $users, 'ID' );
		return array_intersect( $accessible_users, $user_ids );
	}

	/**
	 * Determines wether the user have proper permission to access the data from the api callback.
	 *
	 * @deprecated 3.0.0 This method is no longer used.
	 *
	 * @return bool true when the data is accessible. False when data is not accessible.
	 */
	public static function total_revenue_data_permission_callback() {
		_deprecated_function( __METHOD__, '3.0.0' );

		/** This filter is documented in includes/reports-base/includes/apis/class-wrld-learndash-endpoints.php */
		return apply_filters_deprecated(
			'wrld_filter_total_revenue_access_permission',
			[ self::restrict_access_to_managers() ],
			'3.0.0',
			'learndash_propanel_api_user_has_access'
		);
	}

	/**
	 * Determines wether the user have proper permission to access the data from the api callback.
	 *
	 * @deprecated 3.0.0 This method is no longer used.
	 *
	 * @return bool true when the data is accessible. False when data is not accessible.
	 */
	public static function coursewise_revenue_data_permission_callback() {
		_deprecated_function( __METHOD__, '3.0.0' );

		/** This filter is documented in includes/reports-base/includes/apis/class-wrld-learndash-endpoints.php */
		return apply_filters_deprecated(
			'wrld_filter_coursewise_revenue_access_permission',
			[ self::restrict_access_to_managers() ],
			'3.0.0',
			'learndash_propanel_api_user_has_access'
		);
	}

	/**
	 * Allow group leaders LD Rest API Access.
	 *
	 * @since 1.8.1
	 *
	 * @param WP_REST_Response $response   Result to send to the client.
	 * @param array            $handler    Route handler used for the request.
	 * @param WP_REST_Request  $request    Request used to generate the response.
	 *
	 * @return WP_REST_Response           Updated result to send for the request.
	 */
	public function allow_gl_ld_api_access( $response, $handler, $request ) {
		// Check whether user is logged in user.
		$user_id = get_current_user_id();
		if ( ! is_user_logged_in() ) {
			return $response;
		}

		// Check if LD API request.
		$route = $request->get_route();

		if ( false !== strpos( $route, LEARNDASH_REST_API_NAMESPACE ) ) {
			$course_namespace = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-courses' );
			$course_namespace = empty( $course_namespace ) ? 'sfwd-courses' : $course_namespace;
			$group_namespace  = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'groups ' );
			$group_namespace  = empty( $group_namespace ) ? 'groups' : $group_namespace;

			// Course Requests.
			if ( false !== strpos( $route, $course_namespace ) ) {
				// If get single course request, verify is course accessible.
				if ( array_key_exists( 'id', $request->get_params() ) ) {
					$course_id = intval( $request['id'] );

					if ( ! in_array( $course_id, learndash_get_group_leader_groups_courses( $user_id ), true ) ) {
						return $response;
					}
				}
				$response = call_user_func( $handler['callback'], $request );
			}

			// Group Requests.
			if ( false !== strpos( $route, $group_namespace ) ) {
				// Get group ID and check if accessible.
				if ( array_key_exists( 'id', $request->get_params() ) ) {
					$group_id = intval( $request['id'] );

					if ( in_array( $group_id, learndash_get_administrators_group_ids( $user_id ) ) ) {
						$response = call_user_func( $handler['callback'], $request );
					}
				}
			}
		}

		return $response;
	}
}
