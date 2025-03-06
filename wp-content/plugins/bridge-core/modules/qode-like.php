<?php
if(!defined('ABSPATH')) exit;

/**
 * Class QodeLike
 *
 * Stores likes for posts in post meta table and retrieves it.
 * Also generates HTML for different use cases
 */
class QodeLike {
    public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_qode_like', array($this, 'ajax'));
		add_action('wp_ajax_nopriv_qode_like', array($this, 'ajax'));
    }
	
	public function is_like_enabled() {
		$blog_like = bridge_qode_options()->getOptionValue( 'qode_like' );
		$portfolio_like = bridge_qode_options()->getOptionValue( 'portfolio_qode_like' );
		
		return 'on' === $blog_like || 'on' === $portfolio_like;
	}


    /**
     * Loads all necessary script for like functionality
     */
    public function enqueueScripts() {
	    if( $this->is_like_enabled() ) {
		    wp_enqueue_script( 'qode-like', get_template_directory_uri() . '/js/plugins/qode-like.min.js', 'jquery', false, true );
		    
		    wp_localize_script( 'qode-like', 'qodeLike', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	    }
    }

    /**
     * Method that hooks to WP ajax functionality. Calss
     */
    public function ajax() {
        $type = isset($_POST['type']) ? $_POST['type'] : '';

        //update
        if(isset($_POST['likes_id'])) {
            $post_id = str_replace('qode-like-', '', $_POST['likes_id']);
            echo bridge_qode_get_module_part( $this->likePost($post_id, 'update', $type) );
        } //get
        else {
            $post_id = str_replace('qode-like-', '', $_POST['likes_id']);
            echo bridge_qode_get_module_part( $this->likePost($post_id, 'get', $type) );
        }
        exit;
    }

    /**
     * Returns inner HTML of like button / icon. Also updates like value if action param is update
     * @param $post_id ID of post for which to get likes
     * @param string $action could be update or get. If it's update increases number of likes, if it's get it only generats inner HTML
     * @param string $type type of output to generate. Could be icon or default (button)
     * @return int|mixed|string|void
     */
    public function likePost($post_id, $action = 'get', $type = '') {
        if(!is_numeric($post_id)) {
            return;
        }

        switch($action) {

            case 'get':
                $like_count = get_post_meta($post_id, '_qode-like', true);
                if(!$like_count) {
                    $like_count = 0;
                    add_post_meta($post_id, '_qode-like', $like_count, true);
                }

                if($type == 'icon') {
                    return;
                }

                $return_value = $like_count;

                if($like_count != 1) {
                    $return_value .= "<span> " . esc_html__(' Likes', 'bridge-core') . "</span>";
                } else {
                    $return_value .= "<span> " . esc_html__(' Like', 'bridge-core') . "</span>";
                }

                return $return_value;
                break;

            case 'update':
                $like_count = get_post_meta($post_id, '_qode-like', true);
                if(isset($_COOKIE['qode-like_' . $post_id])) {
                    return $like_count;
                }

                $like_count++;
                update_post_meta($post_id, '_qode-like', $like_count);
                setcookie('qode-like_' . $post_id, $post_id, time() * 20, '/');

                if($type == 'icon') {
                    return;
                }

                $return_value = $like_count;
                if($like_count != 1) {
                    $return_value .= "<span> " . esc_html__(' Likes', 'bridge-core') . "</span>";
                } else {
                    $return_value .= "<span> " . esc_html__(' Like', 'bridge-core') . "</span>";
                }

                return $return_value;
                break;
        }
    }

    /**
     * Generates HTML for like button / icon
     * @param string $type type of output to generate. It's passed to QodeLike::likePost method
     * @return string generated HTML
     *
     * @see QodeLike::likePost()
     */
    public function generateLikeHTML($type = '') {
        global $post;

        $output = $this->likePost($post->ID, 'get', $type);

        $class = 'qode-like';
        $title = esc_html__('Like this', 'bridge-core');
        if(isset($_COOKIE['qode-like_' . $post->ID])) {
            $class = 'qode-like liked';
            $title = esc_html__('You already like this!', 'bridge-core');
        }

        return '<a '.bridge_qode_get_inline_attr($type, 'data-type').' href="#" class="'.esc_attr($class).'" id="qode-like-'.esc_attr($post->ID).'" title="'.esc_attr($title).'">' . $output . '</a>';
    }
}

if(!function_exists('bridge_core_like')) {
    /**
     *
     */
    function bridge_core_like() {
        global $qode_like;
        echo bridge_qode_get_module_part($qode_like->generateLikeHTML());
    }
}

if(!function_exists('bridge_core_like_latest_posts')) {
    /**
     * @return string
     */
    function bridge_core_like_latest_posts() {
        global $qode_like;

        return $qode_like->generateLikeHTML();
    }
}

if(!function_exists('bridge_core_like_portfolio_list')) {
    /**
     * @return string
     */
    function bridge_core_like_portfolio_list($type = '') {
        global $qode_like;

        return $qode_like->generateLikeHTML($type);
    }
}

if( ! function_exists( 'bridge_qode_instantiate_likes' ) ) {
	function bridge_qode_instantiate_likes() {
		global $qode_like;
		
		$qode_like = new QodeLike();
	}
	
	add_action( 'after_setup_theme', 'bridge_qode_instantiate_likes' );
}