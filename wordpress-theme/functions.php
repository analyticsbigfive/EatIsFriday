<?php
/**
 * EIF Backend Theme Functions
 *
 * @package EIFBackend
 * @version 5.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Include Meta Boxes (WYSIWYG editors & dynamic dropdowns)
 */
require_once get_template_directory() . '/inc/meta-boxes.php';

/**
 * Include Admin Pages (Site Content & Pages Content editors)
 */
require_once get_template_directory() . '/inc/admin-pages.php';

/**
 * Include Extended Admin Pages (Partners, Services, Gallery, etc.)
 */
require_once get_template_directory() . '/inc/admin-pages-extended.php';

/**
 * Include Admin Pages V5 (Unified interface with Forms & Components)
 * This adds the new unified admin interface with all fields administrable
 */
require_once get_template_directory() . '/inc/admin-pages-v5.php';

/**
 * Include Theme Customizer (Logo, Header, Markers, SEO, etc.)
 */
require_once get_template_directory() . '/inc/customizer.php';

/**
 * Helper function to parse array fields (supports PHP array, JSON string, or comma-separated)
 * Used by format functions for requirements, benefits, services, amenities, etc.
 */
function eatisfamily_parse_array_field($value) {
    if (empty($value)) return array();
    
    // If already an array, return it
    if (is_array($value)) {
        return array_values(array_filter($value));
    }
    
    // Try JSON decode first (for imported data)
    $decoded = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return array_values(array_filter($decoded));
    }
    
    // Try newline-separated (for WYSIWYG lists)
    if (strpos($value, "\n") !== false) {
        return array_values(array_filter(array_map('trim', explode("\n", $value))));
    }
    
    // Try comma-separated
    if (strpos($value, ',') !== false) {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
    
    // Single value
    return array(trim($value));
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
    
    // Timeline Events Post Type (for About page timeline)
    register_post_type('timeline_event', array(
        'labels' => array(
            'name' => __('Timeline Events', 'eatisfamily'),
            'singular_name' => __('Timeline Event', 'eatisfamily'),
            'add_new' => __('Add Timeline Event', 'eatisfamily'),
            'add_new_item' => __('Add New Timeline Event', 'eatisfamily'),
            'edit_item' => __('Edit Timeline Event', 'eatisfamily'),
            'new_item' => __('New Timeline Event', 'eatisfamily'),
            'view_item' => __('View Timeline Event', 'eatisfamily'),
            'search_items' => __('Search Timeline Events', 'eatisfamily'),
            'not_found' => __('No timeline events found', 'eatisfamily'),
            'not_found_in_trash' => __('No timeline events found in trash', 'eatisfamily'),
            'menu_name' => __('Timeline', 'eatisfamily'),
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-backup',
        'supports' => array('title', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'timeline'),
    ));
    
    // Blog Posts Custom Taxonomy (optional)
    register_taxonomy('blog_category', 'post', array(
        'labels' => array(
            'name' => __('Blog Categories', 'eatisfamily'),
            'singular_name' => __('Blog Category', 'eatisfamily'),
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'blog-category'),
    ));
}
add_action('init', 'eatisfamily_register_post_types');

/**
 * ============================================================================
 * THEME ACTIVATION - Import JSON Data
 * ============================================================================
 */

/**
 * Hook on theme activation
 * NOTE: Auto-import is DISABLED in v2.0 to prevent overwriting admin changes
 * Use the admin page "Data Management" to manually import if needed
 */
function eatisfamily_theme_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Initialize empty options if they don't exist
    if (!get_option('eatisfamily_site_content')) {
        update_option('eatisfamily_site_content', array(
            'site' => array(
                'name' => get_bloginfo('name'),
                'tagline' => get_bloginfo('description'),
                'description' => '',
                'seo' => array(
                    'default_title' => get_bloginfo('name'),
                    'default_description' => get_bloginfo('description'),
                    'keywords' => '',
                    'og_image' => '',
                ),
                'contact' => array(
                    'email' => get_option('admin_email'),
                    'phone' => '',
                ),
                'social' => array(
                    'facebook' => '',
                    'instagram' => '',
                    'twitter' => '',
                    'linkedin' => '',
                    'youtube' => '',
                ),
            ),
        ));
    }
    
    if (!get_option('eatisfamily_pages_content')) {
        update_option('eatisfamily_pages_content', array(
            'homepage' => array('hero' => array('title' => '', 'subtitle' => '', 'cta_text' => '', 'cta_link' => '', 'background_image' => '')),
            'about' => array('hero' => array('title' => '', 'subtitle' => '', 'background_image' => ''), 'intro_section' => array('title' => '', 'content' => ''), 'timeline_title' => 'Our History'),
            'contact' => array('hero' => array('title' => '', 'subtitle' => ''), 'form_title' => '', 'form_subtitle' => ''),
            'careers' => array('hero' => array('title' => '', 'subtitle' => ''), 'benefits_title' => '', 'benefits' => array()),
            'events' => array('hero' => array('title' => '', 'subtitle' => '')),
        ));
    }
}
add_action('after_switch_theme', 'eatisfamily_theme_activation');

/**
 * Import all JSON data
 */
function eatisfamily_import_all_json_data() {
    $json_dir = get_template_directory() . '/data/';
    
    // Create data directory if not exists
    if (!file_exists($json_dir)) {
        wp_mkdir_p($json_dir);
    }
    
    // Import each data type
    eatisfamily_import_activities($json_dir . 'activities.json');
    eatisfamily_import_events($json_dir . 'events.json');
    eatisfamily_import_jobs($json_dir . 'jobs.json');
    eatisfamily_import_venues($json_dir . 'venues.json');
    eatisfamily_import_blog_posts($json_dir . 'blog-posts.json');
    eatisfamily_import_site_content($json_dir . 'site-content.json');
    eatisfamily_import_pages_content($json_dir . 'pages-content.json');
    
    // Log import
    error_log('EatIsFamily: All JSON data imported successfully on ' . current_time('mysql'));
}

/**
 * Import Activities from JSON
 */
function eatisfamily_import_activities($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: activities.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $activities = json_decode($json_data, true);
    
    if (!$activities || !is_array($activities)) {
        error_log('EatIsFamily: Invalid activities.json format');
        return false;
    }
    
    $imported = 0;
    foreach ($activities as $activity) {
        // Check if activity already exists
        $existing = get_posts(array(
            'post_type' => 'activity',
            'meta_key' => 'original_id',
            'meta_value' => $activity['id'],
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            continue;
        }
        
        // Create new activity
        $post_data = array(
            'post_title' => $activity['title']['rendered'] ?? $activity['title'],
            'post_content' => $activity['content']['rendered'] ?? '',
            'post_excerpt' => $activity['description'] ?? '',
            'post_status' => 'publish',
            'post_type' => 'activity',
            'post_name' => $activity['slug'] ?? sanitize_title($activity['title']['rendered'] ?? ''),
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            // Add meta fields
            update_post_meta($post_id, 'original_id', $activity['id']);
            update_post_meta($post_id, 'activity_date', $activity['date'] ?? '');
            update_post_meta($post_id, 'location', $activity['location'] ?? '');
            update_post_meta($post_id, 'capacity', $activity['capacity'] ?? 0);
            update_post_meta($post_id, 'available_spots', $activity['available_spots'] ?? 0);
            update_post_meta($post_id, 'category', $activity['category'] ?? '');
            update_post_meta($post_id, 'price', $activity['price'] ?? '');
            update_post_meta($post_id, 'duration', $activity['duration'] ?? '');
            update_post_meta($post_id, 'status', $activity['status'] ?? 'open');
            
            // Handle featured image
            if (!empty($activity['featured_media'])) {
                eatisfamily_set_featured_image_from_url($post_id, $activity['featured_media']);
            }
            
            $imported++;
        }
    }
    
    error_log("EatIsFamily: Imported {$imported} activities");
    return $imported;
}

/**
 * Import Events from JSON
 */
function eatisfamily_import_events($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: events.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $events = json_decode($json_data, true);
    
    if (!$events || !is_array($events)) {
        error_log('EatIsFamily: Invalid events.json format');
        return false;
    }
    
    $imported = 0;
    foreach ($events as $event) {
        // Check if event already exists
        $existing = get_posts(array(
            'post_type' => 'event',
            'meta_key' => 'original_id',
            'meta_value' => $event['id'],
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            continue;
        }
        
        // Create new event
        $post_data = array(
            'post_title' => $event['title'] ?? '',
            'post_content' => $event['description'] ?? '',
            'post_status' => 'publish',
            'post_type' => 'event',
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'original_id', $event['id']);
            update_post_meta($post_id, 'event_type', $event['event_type'] ?? '');
            
            // Handle featured image
            if (!empty($event['image'])) {
                eatisfamily_set_featured_image_from_url($post_id, $event['image']);
            }
            
            $imported++;
        }
    }
    
    error_log("EatIsFamily: Imported {$imported} events");
    return $imported;
}

/**
 * Import Jobs from JSON
 */
function eatisfamily_import_jobs($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: jobs.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $jobs = json_decode($json_data, true);
    
    if (!$jobs || !is_array($jobs)) {
        error_log('EatIsFamily: Invalid jobs.json format');
        return false;
    }
    
    $imported = 0;
    foreach ($jobs as $job) {
        // Check if job already exists
        $existing = get_posts(array(
            'post_type' => 'job',
            'meta_key' => 'original_id',
            'meta_value' => $job['id'],
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            continue;
        }
        
        // Create new job
        $post_data = array(
            'post_title' => $job['title']['rendered'] ?? $job['title'],
            'post_content' => $job['content']['rendered'] ?? '',
            'post_excerpt' => $job['excerpt']['rendered'] ?? '',
            'post_status' => 'publish',
            'post_type' => 'job',
            'post_name' => $job['slug'] ?? sanitize_title($job['title']['rendered'] ?? ''),
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'original_id', $job['id']);
            update_post_meta($post_id, 'venue_id', $job['venue_id'] ?? '');
            update_post_meta($post_id, 'department', $job['department'] ?? '');
            update_post_meta($post_id, 'job_type', $job['job_type'] ?? '');
            update_post_meta($post_id, 'salary', $job['salary'] ?? '');
            update_post_meta($post_id, 'requirements', json_encode($job['requirements'] ?? array()));
            update_post_meta($post_id, 'benefits', json_encode($job['benefits'] ?? array()));
            
            // Handle featured image
            if (!empty($job['featured_media'])) {
                eatisfamily_set_featured_image_from_url($post_id, $job['featured_media']);
            }
            
            $imported++;
        }
    }
    
    error_log("EatIsFamily: Imported {$imported} jobs");
    return $imported;
}

/**
 * Import Venues from JSON
 */
function eatisfamily_import_venues($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: venues.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $data = json_decode($json_data, true);
    
    if (!$data) {
        error_log('EatIsFamily: Invalid venues.json format');
        return false;
    }
    
    // Store metadata and event_types as options
    if (isset($data['metadata'])) {
        update_option('eatisfamily_venues_metadata', $data['metadata']);
    }
    if (isset($data['event_types'])) {
        update_option('eatisfamily_event_types', $data['event_types']);
    }
    if (isset($data['stats'])) {
        update_option('eatisfamily_stats', $data['stats']);
    }
    
    $venues = $data['venues'] ?? array();
    $imported = 0;
    
    foreach ($venues as $venue) {
        // Check if venue already exists
        $existing = get_posts(array(
            'post_type' => 'venue',
            'meta_key' => 'venue_slug',
            'meta_value' => $venue['id'],
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            continue;
        }
        
        // Create new venue
        $post_data = array(
            'post_title' => $venue['name'] ?? '',
            'post_content' => $venue['description'] ?? '',
            'post_status' => 'publish',
            'post_type' => 'venue',
            'post_name' => $venue['id'],
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'venue_slug', $venue['id']);
            update_post_meta($post_id, 'location', $venue['location'] ?? '');
            update_post_meta($post_id, 'city', $venue['city'] ?? '');
            update_post_meta($post_id, 'country', $venue['country'] ?? '');
            update_post_meta($post_id, 'venue_type', $venue['type'] ?? '');
            update_post_meta($post_id, 'latitude', $venue['lat'] ?? 0);
            update_post_meta($post_id, 'longitude', $venue['lng'] ?? 0);
            update_post_meta($post_id, 'capacity', $venue['capacity'] ?? '');
            update_post_meta($post_id, 'staff_members', $venue['staff_members'] ?? 0);
            update_post_meta($post_id, 'recent_event', $venue['recent_event'] ?? '');
            update_post_meta($post_id, 'guests_served', $venue['guests_served'] ?? '');
            update_post_meta($post_id, 'services', json_encode($venue['services'] ?? array()));
            update_post_meta($post_id, 'shops', json_encode($venue['shops'] ?? array()));
            update_post_meta($post_id, 'menu_items', json_encode($venue['menu_items'] ?? array()));
            
            // Handle images
            if (!empty($venue['image'])) {
                eatisfamily_set_featured_image_from_url($post_id, $venue['image']);
            }
            if (!empty($venue['logo'])) {
                update_post_meta($post_id, 'logo_url', $venue['logo']);
            }
            
            $imported++;
        }
    }
    
    error_log("EatIsFamily: Imported {$imported} venues");
    return $imported;
}

/**
 * Import Blog Posts from JSON
 */
function eatisfamily_import_blog_posts($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: blog-posts.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $posts = json_decode($json_data, true);
    
    if (!$posts || !is_array($posts)) {
        error_log('EatIsFamily: Invalid blog-posts.json format');
        return false;
    }
    
    $imported = 0;
    foreach ($posts as $post) {
        // Check if post already exists
        $existing = get_posts(array(
            'post_type' => 'post',
            'meta_key' => 'original_id',
            'meta_value' => $post['id'],
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            continue;
        }
        
        // Create new post
        $post_data = array(
            'post_title' => $post['title']['rendered'] ?? $post['title'],
            'post_content' => $post['content']['rendered'] ?? '',
            'post_excerpt' => $post['excerpt']['rendered'] ?? '',
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_name' => $post['slug'] ?? sanitize_title($post['title']['rendered'] ?? ''),
            'post_date' => isset($post['date']) ? date('Y-m-d H:i:s', strtotime($post['date'])) : current_time('mysql'),
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'original_id', $post['id']);
            
            // Optional fields (may not exist in JSON)
            if (!empty($post['author'])) {
                update_post_meta($post_id, 'author_name', $post['author']['name'] ?? '');
                update_post_meta($post_id, 'author_avatar', $post['author']['avatar'] ?? '');
            }
            if (!empty($post['reading_time'])) {
                update_post_meta($post_id, 'reading_time', $post['reading_time']);
            }
            
            // Handle featured image
            if (!empty($post['featured_media'])) {
                eatisfamily_set_featured_image_from_url($post_id, $post['featured_media']);
            }
            
            $imported++;
        }
    }
    
    error_log("EatIsFamily: Imported {$imported} blog posts");
    return $imported;
}

/**
 * Import Site Content from JSON
 */
function eatisfamily_import_site_content($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: site-content.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $content = json_decode($json_data, true);
    
    if (!$content) {
        error_log('EatIsFamily: Invalid site-content.json format');
        return false;
    }
    
    update_option('eatisfamily_site_content', $content);
    error_log('EatIsFamily: Site content imported successfully');
    return true;
}

/**
 * Import Pages Content from JSON
 */
function eatisfamily_import_pages_content($file_path) {
    if (!file_exists($file_path)) {
        error_log('EatIsFamily: pages-content.json not found at ' . $file_path);
        return false;
    }
    
    $json_data = file_get_contents($file_path);
    $content = json_decode($json_data, true);
    
    if (!$content) {
        error_log('EatIsFamily: Invalid pages-content.json format');
        return false;
    }
    
    update_option('eatisfamily_pages_content', $content);
    error_log('EatIsFamily: Pages content imported successfully');
    return true;
}

/**
 * Helper: Set featured image from URL
 */
function eatisfamily_set_featured_image_from_url($post_id, $image_url) {
    // Skip if URL is a local path starting with /
    if (strpos($image_url, '/') === 0 && strpos($image_url, '//') !== 0) {
        update_post_meta($post_id, 'featured_image_url', $image_url);
        return false;
    }
    
    // Skip if already set
    if (has_post_thumbnail($post_id)) {
        return true;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Download and attach
    $attachment_id = media_sideload_image($image_url, $post_id, null, 'id');
    
    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, $attachment_id);
        return true;
    }
    
    // Store URL as fallback
    update_post_meta($post_id, 'featured_image_url', $image_url);
    return false;
}

/**
 * ============================================================================
 * ADMIN PAGE - Manual Import & Data Management
 * ============================================================================
 */

/**
 * Add admin menu for data management
 */
function eatisfamily_add_data_admin_menu() {
    add_submenu_page(
        'themes.php',
        __('Import JSON Data', 'eatisfamily'),
        __('Import JSON Data', 'eatisfamily'),
        'manage_options',
        'eatisfamily-import',
        'eatisfamily_import_admin_page'
    );
}
add_action('admin_menu', 'eatisfamily_add_data_admin_menu');

/**
 * Admin page for manual import
 */
function eatisfamily_import_admin_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_reimport']) && check_admin_referer('eatisfamily_reimport_nonce')) {
        // Reset import flag
        delete_option('eatisfamily_data_imported');
        
        // Re-import all data
        eatisfamily_import_all_json_data();
        
        // Mark as imported
        update_option('eatisfamily_data_imported', true);
        update_option('eatisfamily_import_date', current_time('mysql'));
        
        echo '<div class="notice notice-success"><p>' . __('Data imported successfully!', 'eatisfamily') . '</p></div>';
    }
    
    if (isset($_POST['eatisfamily_reset']) && check_admin_referer('eatisfamily_reset_nonce')) {
        // Delete all imported data
        eatisfamily_delete_all_imported_data();
        
        // Reset import flag
        delete_option('eatisfamily_data_imported');
        delete_option('eatisfamily_import_date');
        
        echo '<div class="notice notice-warning"><p>' . __('All imported data has been deleted.', 'eatisfamily') . '</p></div>';
    }
    
    $import_date = get_option('eatisfamily_import_date', 'Never');
    $is_imported = get_option('eatisfamily_data_imported', false);
    
    ?>
    <div class="wrap">
        <h1><?php _e('Eat Is Family - JSON Data Import', 'eatisfamily'); ?></h1>
        
        <div class="card" style="max-width: 600px; padding: 20px;">
            <h2><?php _e('Import Status', 'eatisfamily'); ?></h2>
            <p>
                <strong><?php _e('Status:', 'eatisfamily'); ?></strong> 
                <?php echo $is_imported ? '<span style="color: green;">✓ ' . __('Imported', 'eatisfamily') . '</span>' : '<span style="color: orange;">○ ' . __('Not imported', 'eatisfamily') . '</span>'; ?>
            </p>
            <p>
                <strong><?php _e('Last Import:', 'eatisfamily'); ?></strong> 
                <?php echo esc_html($import_date); ?>
            </p>
            
            <h3><?php _e('Data Files Location', 'eatisfamily'); ?></h3>
            <p><code><?php echo esc_html(get_template_directory() . '/data/'); ?></code></p>
            <p class="description"><?php _e('Place your JSON files in the /data/ folder of the theme:', 'eatisfamily'); ?></p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li>activities.json</li>
                <li>events.json</li>
                <li>jobs.json</li>
                <li>venues.json</li>
                <li>blog-posts.json</li>
                <li>site-content.json</li>
                <li>pages-content.json</li>
            </ul>
        </div>
        
        <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
            <h2><?php _e('Actions', 'eatisfamily'); ?></h2>
            
            <form method="post" style="margin-bottom: 15px;">
                <?php wp_nonce_field('eatisfamily_reimport_nonce'); ?>
                <p>
                    <button type="submit" name="eatisfamily_reimport" class="button button-primary">
                        <?php _e('Import / Re-import JSON Data', 'eatisfamily'); ?>
                    </button>
                </p>
                <p class="description"><?php _e('This will import new data from JSON files. Existing items will be skipped.', 'eatisfamily'); ?></p>
            </form>
            
            <hr>
            
            <form method="post" onsubmit="return confirm('<?php _e('Are you sure? This will delete all imported posts!', 'eatisfamily'); ?>');">
                <?php wp_nonce_field('eatisfamily_reset_nonce'); ?>
                <p>
                    <button type="submit" name="eatisfamily_reset" class="button button-secondary" style="color: #dc3232;">
                        <?php _e('Delete All Imported Data', 'eatisfamily'); ?>
                    </button>
                </p>
                <p class="description" style="color: #dc3232;"><?php _e('Warning: This will permanently delete all imported posts, events, jobs, venues, and activities.', 'eatisfamily'); ?></p>
            </form>
        </div>
        
        <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
            <h2><?php _e('Import Statistics', 'eatisfamily'); ?></h2>
            <?php
            $stats = array(
                'Activities' => wp_count_posts('activity')->publish ?? 0,
                'Events' => wp_count_posts('event')->publish ?? 0,
                'Jobs' => wp_count_posts('job')->publish ?? 0,
                'Venues' => wp_count_posts('venue')->publish ?? 0,
                'Blog Posts' => wp_count_posts('post')->publish ?? 0,
            );
            ?>
            <table class="widefat" style="max-width: 300px;">
                <thead>
                    <tr>
                        <th><?php _e('Content Type', 'eatisfamily'); ?></th>
                        <th><?php _e('Count', 'eatisfamily'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $label => $count): ?>
                    <tr>
                        <td><?php echo esc_html($label); ?></td>
                        <td><?php echo esc_html($count); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Delete all imported data
 */
function eatisfamily_delete_all_imported_data() {
    $post_types = array('activity', 'event', 'job', 'venue');
    
    foreach ($post_types as $post_type) {
        $posts = get_posts(array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));
        
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
    
    // Delete blog posts with original_id meta (imported ones)
    $blog_posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_key' => 'original_id',
        'post_status' => 'any',
    ));
    
    foreach ($blog_posts as $post) {
        wp_delete_post($post->ID, true);
    }
    
    // Delete options
    delete_option('eatisfamily_site_content');
    delete_option('eatisfamily_pages_content');
    delete_option('eatisfamily_venues_metadata');
    delete_option('eatisfamily_event_types');
    delete_option('eatisfamily_stats');
    
    error_log('EatIsFamily: All imported data deleted');
}

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
    
    // Global settings endpoint (Customizer + all config)
    register_rest_route($namespace, '/settings', array(
        'methods' => 'GET',
        'callback' => 'eatisfamily_get_global_settings',
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
        'requirements' => eatisfamily_parse_array_field(get_post_meta($post->ID, 'requirements', true)),
        'benefits' => eatisfamily_parse_array_field(get_post_meta($post->ID, 'benefits', true)),
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
        'amenities' => eatisfamily_parse_array_field(get_post_meta($post->ID, 'amenities', true)),
        'services' => eatisfamily_parse_array_field(get_post_meta($post->ID, 'services', true)),
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
 * Now includes partners, services, gallery, and sustainability data
 */
function eatisfamily_get_pages_content($request) {
    $pages_content = get_option('eatisfamily_pages_content', array());
    
    // Merge with partners data
    $partners_data = get_option('eatisfamily_partners', array());
    if (!empty($partners_data)) {
        if (!isset($pages_content['homepage'])) {
            $pages_content['homepage'] = array();
        }
        $pages_content['homepage']['partners_title'] = $partners_data['title'] ?? '';
        $pages_content['homepage']['partners'] = $partners_data['partners'] ?? array();
    }
    
    // Merge with services data
    $services_data = get_option('eatisfamily_services', array());
    if (!empty($services_data)) {
        if (!isset($pages_content['homepage'])) {
            $pages_content['homepage'] = array();
        }
        $pages_content['homepage']['services_section'] = array(
            'tag' => $services_data['tag'] ?? '',
            'title' => $services_data['title'] ?? array(),
            'services' => $services_data['services'] ?? array()
        );
    }
    
    // Merge with gallery data
    $gallery_data = get_option('eatisfamily_gallery', array());
    if (!empty($gallery_data)) {
        if (!isset($pages_content['homepage'])) {
            $pages_content['homepage'] = array();
        }
        $pages_content['homepage']['gallery_section'] = array(
            'images' => $gallery_data['images'] ?? array()
        );
    }
    
    // Merge with sustainability data
    $sustainability_data = get_option('eatisfamily_sustainability', array());
    if (!empty($sustainability_data)) {
        if (!isset($pages_content['homepage'])) {
            $pages_content['homepage'] = array();
        }
        $pages_content['homepage']['sustainable_service_title'] = $sustainability_data['title'] ?? '';
        $pages_content['homepage']['sustainable_service'] = $sustainability_data['items'] ?? array();
    }
    
    return rest_ensure_response($pages_content);
}

/**
 * API Callback Functions - Global Settings
 * Combines Customizer settings with site/pages content for complete config
 */
function eatisfamily_get_global_settings($request) {
    // Get Customizer settings
    $customizer_settings = eatisfamily_get_customizer_settings();
    
    // Get site content for additional data
    $site_content = get_option('eatisfamily_site_content', array());
    
    // Build complete settings object
    $settings = array(
        // Brand & Identity
        'brand' => array_merge(
            $customizer_settings['brand'],
            array(
                'site_name' => $site_content['site']['name'] ?? get_bloginfo('name'),
                'tagline' => $site_content['site']['tagline'] ?? get_bloginfo('description'),
            )
        ),
        
        // Header Configuration
        'header' => $customizer_settings['header'],
        
        // Navigation
        'navigation' => $customizer_settings['navigation'],
        
        // Footer
        'footer' => array_merge(
            $customizer_settings['footer'],
            array(
                'contact_email' => $customizer_settings['contact']['email'],
                'contact_phone' => $customizer_settings['contact']['phone'],
            )
        ),
        
        // Map & Markers
        'map' => $customizer_settings['map'],
        'markers' => $customizer_settings['markers'],
        
        // SEO
        'seo' => $customizer_settings['seo'],
        
        // Social Media
        'social' => $customizer_settings['social'],
        
        // Contact Information
        'contact' => $customizer_settings['contact'],
        
        // Global Config
        'config' => $customizer_settings['config'],
        
        // UI Strings (for i18n)
        'strings' => $customizer_settings['strings'],
        
        // Background Images
        'backgrounds' => $customizer_settings['backgrounds'],
    );
    
    return rest_ensure_response($settings);
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
