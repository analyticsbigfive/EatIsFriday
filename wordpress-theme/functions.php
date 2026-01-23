<?php
/**
 * Eat Is Family Theme Functions
 * 
 * @package EatIsFamily
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function eatisfamily_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    
    // Register menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'eatisfamily'),
        'footer' => __('Footer Menu', 'eatisfamily'),
    ));
}
add_action('after_setup_theme', 'eatisfamily_theme_setup');

/**
 * Register Custom Post Types
 */
function eatisfamily_register_post_types() {
    // Activities Post Type
    register_post_type('activity', array(
        'labels' => array(
            'name' => __('Activities', 'eatisfamily'),
            'singular_name' => __('Activity', 'eatisfamily'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite' => array('slug' => 'activities'),
    ));
    
    // Events Post Type
    register_post_type('event', array(
        'labels' => array(
            'name' => __('Events', 'eatisfamily'),
            'singular_name' => __('Event', 'eatisfamily'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'events'),
    ));
    
    // Jobs Post Type
    register_post_type('job', array(
        'labels' => array(
            'name' => __('Jobs', 'eatisfamily'),
            'singular_name' => __('Job', 'eatisfamily'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite' => array('slug' => 'jobs'),
    ));
    
    // Venues Post Type
    register_post_type('venue', array(
        'labels' => array(
            'name' => __('Venues', 'eatisfamily'),
            'singular_name' => __('Venue', 'eatisfamily'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'venues'),
    ));
}
add_action('init', 'eatisfamily_register_post_types');

/**
 * Register REST API Routes
 */
function eatisfamily_register_api_routes() {
    $namespace = 'eatisfamily/v1';
    
    // Activities endpoints
    register_rest_route($namespace, '/activities', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_activities',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route($namespace, '/activities/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_activity_by_slug',
        'permission_callback' => '__return_true',
        'args' => array(
            'slug' => array(
                'required' => true,
                'type' => 'string',
            ),
        ),
    ));
    
    // Blog posts endpoints
    register_rest_route($namespace, '/blog-posts', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_blog_posts',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route($namespace, '/blog-posts/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_blog_post_by_slug',
        'permission_callback' => '__return_true',
        'args' => array(
            'slug' => array(
                'required' => true,
                'type' => 'string',
            ),
        ),
    ));
    
    // Events endpoints
    register_rest_route($namespace, '/events', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_events',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route($namespace, '/events/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_event_by_id',
        'permission_callback' => '__return_true',
        'args' => array(
            'id' => array(
                'required' => true,
                'type' => 'integer',
            ),
        ),
    ));
    
    // Jobs endpoints
    register_rest_route($namespace, '/jobs', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_jobs',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route($namespace, '/jobs/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_job_by_slug',
        'permission_callback' => '__return_true',
        'args' => array(
            'slug' => array(
                'required' => true,
                'type' => 'string',
            ),
        ),
    ));
    
    // Venues endpoints
    register_rest_route($namespace, '/venues', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_venues',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route($namespace, '/venues/(?P<id>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_venue_by_id',
        'permission_callback' => '__return_true',
        'args' => array(
            'id' => array(
                'required' => true,
                'type' => 'string',
            ),
        ),
    ));
    
    // Site content endpoint
    register_rest_route($namespace, '/site-content', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_site_content',
        'permission_callback' => '__return_true',
    ));
    
    // Pages content endpoint
    register_rest_route($namespace, '/pages-content', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_pages_content',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'eatisfamily_register_api_routes');

/**
 * API Callback Functions - Activities
 */
function eatisfamily_get_activities($request) {
    $args = array(
        'post_type' => 'activity',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $query = new WP_Query($args);
    $activities = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $activities[] = eatisfamily_format_activity(get_post());
        }
    }
    
    wp_reset_postdata();
    return rest_ensure_response($activities);
}

function eatisfamily_get_activity_by_slug($request) {
    $slug = $request->get_param('slug');
    
    $args = array(
        'post_type' => 'activity',
        'name' => $slug,
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $activity = eatisfamily_format_activity(get_post());
        wp_reset_postdata();
        return rest_ensure_response($activity);
    }
    
    wp_reset_postdata();
    return new WP_Error('not_found', 'Activity not found', array('status' => 404));
}

function eatisfamily_format_activity($post) {
    return array(
        'id' => $post->ID,
        'slug' => $post->post_name,
        'title' => array(
            'rendered' => get_the_title($post->ID),
        ),
        'description' => get_post_meta($post->ID, 'description', true),
        'content' => array(
            'rendered' => apply_filters('the_content', $post->post_content),
        ),
        'date' => get_post_meta($post->ID, 'activity_date', true),
        'location' => get_post_meta($post->ID, 'location', true),
        'capacity' => (int) get_post_meta($post->ID, 'capacity', true),
        'available_spots' => (int) get_post_meta($post->ID, 'available_spots', true),
        'category' => get_post_meta($post->ID, 'category', true),
        'price' => get_post_meta($post->ID, 'price', true),
        'duration' => get_post_meta($post->ID, 'duration', true),
        'featured_media' => get_the_post_thumbnail_url($post->ID, 'large'),
        'status' => get_post_meta($post->ID, 'status', true) ?: 'open',
    );
}

/**
 * API Callback Functions - Blog Posts
 */
function eatisfamily_get_blog_posts($request) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $query = new WP_Query($args);
    $posts = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = eatisfamily_format_blog_post(get_post());
        }
    }
    
    wp_reset_postdata();
    return rest_ensure_response($posts);
}

function eatisfamily_get_blog_post_by_slug($request) {
    $slug = $request->get_param('slug');
    
    $args = array(
        'post_type' => 'post',
        'name' => $slug,
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $post = eatisfamily_format_blog_post(get_post());
        wp_reset_postdata();
        return rest_ensure_response($post);
    }
    
    wp_reset_postdata();
    return new WP_Error('not_found', 'Blog post not found', array('status' => 404));
}

function eatisfamily_format_blog_post($post) {
    return array(
        'id' => $post->ID,
        'slug' => $post->post_name,
        'title' => array(
            'rendered' => get_the_title($post->ID),
        ),
        'excerpt' => array(
            'rendered' => get_the_excerpt($post->ID),
        ),
        'content' => array(
            'rendered' => apply_filters('the_content', $post->post_content),
        ),
        'date' => get_the_date('c', $post->ID),
        'featured_media' => get_the_post_thumbnail_url($post->ID, 'large'),
    );
}

/**
 * API Callback Functions - Events
 */
function eatisfamily_get_events($request) {
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num',
        'meta_key' => 'event_order',
        'order' => 'ASC',
    );
    
    $query = new WP_Query($args);
    $events = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $events[] = eatisfamily_format_event(get_post());
        }
    }
    
    wp_reset_postdata();
    return rest_ensure_response($events);
}

function eatisfamily_get_event_by_id($request) {
    $id = $request->get_param('id');
    $post = get_post($id);
    
    if ($post && $post->post_type === 'event') {
        return rest_ensure_response(eatisfamily_format_event($post));
    }
    
    return new WP_Error('not_found', 'Event not found', array('status' => 404));
}

function eatisfamily_format_event($post) {
    return array(
        'id' => $post->ID,
        'title' => get_the_title($post->ID),
        'image' => get_the_post_thumbnail_url($post->ID, 'large') ?: get_post_meta($post->ID, 'image', true),
        'description' => apply_filters('the_content', $post->post_content),
        'event_type' => get_post_meta($post->ID, 'event_type', true),
    );
}

/**
 * API Callback Functions - Jobs
 */
function eatisfamily_get_jobs($request) {
    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Handle filters
    $department = $request->get_param('department');
    $venue_id = $request->get_param('venue_id');
    
    if ($department) {
        $args['meta_query'][] = array(
            'key' => 'department',
            'value' => $department,
            'compare' => '=',
        );
    }
    
    if ($venue_id) {
        $args['meta_query'][] = array(
            'key' => 'venue_id',
            'value' => $venue_id,
            'compare' => '=',
        );
    }
    
    $query = new WP_Query($args);
    $jobs = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $jobs[] = eatisfamily_format_job(get_post());
        }
    }
    
    wp_reset_postdata();
    return rest_ensure_response($jobs);
}

function eatisfamily_get_job_by_slug($request) {
    $slug = $request->get_param('slug');
    
    $args = array(
        'post_type' => 'job',
        'name' => $slug,
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $job = eatisfamily_format_job(get_post());
        wp_reset_postdata();
        return rest_ensure_response($job);
    }
    
    wp_reset_postdata();
    return new WP_Error('not_found', 'Job not found', array('status' => 404));
}

function eatisfamily_format_job($post) {
    return array(
        'id' => $post->ID,
        'slug' => $post->post_name,
        'title' => array(
            'rendered' => get_the_title($post->ID),
        ),
        'excerpt' => array(
            'rendered' => get_the_excerpt($post->ID),
        ),
        'content' => array(
            'rendered' => apply_filters('the_content', $post->post_content),
        ),
        'venue_id' => get_post_meta($post->ID, 'venue_id', true),
        'department' => get_post_meta($post->ID, 'department', true),
        'job_type' => get_post_meta($post->ID, 'job_type', true),
        'salary' => get_post_meta($post->ID, 'salary', true),
        'requirements' => json_decode(get_post_meta($post->ID, 'requirements', true), true) ?: array(),
        'benefits' => json_decode(get_post_meta($post->ID, 'benefits', true), true) ?: array(),
        'featured_media' => get_the_post_thumbnail_url($post->ID, 'large'),
    );
}

/**
 * API Callback Functions - Venues
 */
function eatisfamily_get_venues($request) {
    // Get metadata
    $metadata = get_option('eatisfamily_venues_metadata', array(
        'title' => 'Explore Where We Operate',
        'description' => 'Tap any marker on the map to discover the story behind that event.',
        'filter_label' => 'Click to filter by an event type',
    ));
    
    // Get event types
    $event_types = get_option('eatisfamily_event_types', array());
    
    // Get stats
    $stats = get_option('eatisfamily_stats', array());
    
    // Get venues
    $args = array(
        'post_type' => 'venue',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    
    $query = new WP_Query($args);
    $venues = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $venues[] = eatisfamily_format_venue(get_post());
        }
    }
    
    wp_reset_postdata();
    
    return rest_ensure_response(array(
        'metadata' => $metadata,
        'event_types' => $event_types,
        'stats' => $stats,
        'venues' => $venues,
    ));
}

function eatisfamily_get_venue_by_id($request) {
    $venue_id = $request->get_param('id');
    
    $args = array(
        'post_type' => 'venue',
        'name' => $venue_id,
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $venue = eatisfamily_format_venue(get_post());
        wp_reset_postdata();
        return rest_ensure_response($venue);
    }
    
    wp_reset_postdata();
    return new WP_Error('not_found', 'Venue not found', array('status' => 404));
}

function eatisfamily_format_venue($post) {
    return array(
        'id' => $post->post_name,
        'name' => get_the_title($post->ID),
        'location' => get_post_meta($post->ID, 'location', true),
        'city' => get_post_meta($post->ID, 'city', true),
        'country' => get_post_meta($post->ID, 'country', true),
        'type' => get_post_meta($post->ID, 'venue_type', true),
        'lat' => (float) get_post_meta($post->ID, 'latitude', true),
        'lng' => (float) get_post_meta($post->ID, 'longitude', true),
        'description' => apply_filters('the_content', $post->post_content),
        'capacity' => (int) get_post_meta($post->ID, 'capacity', true),
        'amenities' => json_decode(get_post_meta($post->ID, 'amenities', true), true) ?: array(),
        'image' => get_the_post_thumbnail_url($post->ID, 'large'),
    );
}

/**
 * API Callback Functions - Site Content
 */
function eatisfamily_get_site_content($request) {
    $site_content = get_option('eatisfamily_site_content', array());
    
    if (empty($site_content)) {
        // Default structure
        $site_content = array(
            'site' => array(
                'name' => get_bloginfo('name'),
                'tagline' => get_bloginfo('description'),
                'description' => '',
                'seo' => array(),
                'contact' => array(),
                'social' => array(),
            ),
            'about' => array(),
            'homepage' => array(),
        );
    }
    
    return rest_ensure_response($site_content);
}

/**
 * API Callback Functions - Pages Content
 */
function eatisfamily_get_pages_content($request) {
    $pages_content = get_option('eatisfamily_pages_content', array());
    return rest_ensure_response($pages_content);
}

/**
 * Add CORS headers for API requests
 */
function eatisfamily_add_cors_headers() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}
add_action('rest_api_init', 'eatisfamily_add_cors_headers');

/**
 * Enqueue Scripts and Styles
 */
function eatisfamily_enqueue_scripts() {
    wp_enqueue_style('eatisfamily-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'eatisfamily_enqueue_scripts');

/**
 * Include additional files
 */
require_once get_template_directory() . '/inc/admin.php';
