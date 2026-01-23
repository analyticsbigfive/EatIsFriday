<?php
/**
 * Data Import Script for Eat Is Family Theme
 * 
 * IMPORTANT: This file should be secured or removed after use.
 * To trigger import, visit: yoursite.com/?import_eatisfamily_data=SECRET_KEY
 * 
 * @package EatIsFamily
 */

// Security check - change this key before use
define('IMPORT_SECRET_KEY', 'your_secret_key_here_change_this');

// Only run if the secret key matches
if (!isset($_GET['import_eatisfamily_data']) || $_GET['import_eatisfamily_data'] !== IMPORT_SECRET_KEY) {
    return;
}

// Load WordPress
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check user permissions
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access');
}

/**
 * Import Activities from JSON
 */
function import_activities() {
    $json_file = ABSPATH . 'public/api/activities.json';
    
    if (!file_exists($json_file)) {
        echo "Activities JSON file not found.<br>";
        return;
    }
    
    $activities = json_decode(file_get_contents($json_file), true);
    
    foreach ($activities as $activity) {
        // Check if post already exists
        $existing = get_page_by_path($activity['slug'], OBJECT, 'activity');
        
        if ($existing) {
            $post_id = $existing->ID;
            echo "Updating Activity: {$activity['title']['rendered']}<br>";
        } else {
            // Create new post
            $post_id = wp_insert_post(array(
                'post_title' => $activity['title']['rendered'],
                'post_content' => $activity['content']['rendered'],
                'post_name' => $activity['slug'],
                'post_type' => 'activity',
                'post_status' => 'publish',
            ));
            
            echo "Created Activity: {$activity['title']['rendered']}<br>";
        }
        
        // Update custom fields
        update_post_meta($post_id, 'description', $activity['description']);
        update_post_meta($post_id, 'activity_date', $activity['date']);
        update_post_meta($post_id, 'location', $activity['location']);
        update_post_meta($post_id, 'capacity', $activity['capacity']);
        update_post_meta($post_id, 'available_spots', $activity['available_spots']);
        update_post_meta($post_id, 'category', $activity['category']);
        update_post_meta($post_id, 'price', $activity['price']);
        update_post_meta($post_id, 'duration', $activity['duration']);
        update_post_meta($post_id, 'status', $activity['status']);
        
        // Set featured image from URL
        if (!empty($activity['featured_media'])) {
            set_featured_image_from_url($post_id, $activity['featured_media']);
        }
    }
}

/**
 * Import Blog Posts from JSON
 */
function import_blog_posts() {
    $json_file = ABSPATH . 'public/api/blog-posts.json';
    
    if (!file_exists($json_file)) {
        echo "Blog posts JSON file not found.<br>";
        return;
    }
    
    $posts = json_decode(file_get_contents($json_file), true);
    
    foreach ($posts as $post_data) {
        $existing = get_page_by_path($post_data['slug'], OBJECT, 'post');
        
        if ($existing) {
            $post_id = $existing->ID;
            echo "Updating Blog Post: {$post_data['title']['rendered']}<br>";
        } else {
            $post_id = wp_insert_post(array(
                'post_title' => $post_data['title']['rendered'],
                'post_content' => $post_data['content']['rendered'],
                'post_excerpt' => $post_data['excerpt']['rendered'],
                'post_name' => $post_data['slug'],
                'post_type' => 'post',
                'post_status' => 'publish',
                'post_date' => $post_data['date'],
            ));
            
            echo "Created Blog Post: {$post_data['title']['rendered']}<br>";
        }
        
        if (!empty($post_data['featured_media'])) {
            set_featured_image_from_url($post_id, $post_data['featured_media']);
        }
    }
}

/**
 * Import Events from JSON
 */
function import_events() {
    $json_file = ABSPATH . 'public/api/events.json';
    
    if (!file_exists($json_file)) {
        echo "Events JSON file not found.<br>";
        return;
    }
    
    $events = json_decode(file_get_contents($json_file), true);
    
    foreach ($events as $index => $event) {
        $slug = sanitize_title($event['title']);
        $existing = get_page_by_path($slug, OBJECT, 'event');
        
        if ($existing) {
            $post_id = $existing->ID;
            echo "Updating Event: {$event['title']}<br>";
        } else {
            $post_id = wp_insert_post(array(
                'post_title' => $event['title'],
                'post_content' => $event['description'],
                'post_name' => $slug,
                'post_type' => 'event',
                'post_status' => 'publish',
                'menu_order' => $event['id'],
            ));
            
            echo "Created Event: {$event['title']}<br>";
        }
        
        update_post_meta($post_id, 'event_type', $event['event_type']);
        update_post_meta($post_id, 'image', $event['image']);
        update_post_meta($post_id, 'event_order', $event['id']);
        
        if (!empty($event['image'])) {
            set_featured_image_from_url($post_id, $event['image']);
        }
    }
}

/**
 * Import Jobs from JSON
 */
function import_jobs() {
    $json_file = ABSPATH . 'public/api/jobs.json';
    
    if (!file_exists($json_file)) {
        echo "Jobs JSON file not found.<br>";
        return;
    }
    
    $jobs = json_decode(file_get_contents($json_file), true);
    
    foreach ($jobs as $job) {
        $existing = get_page_by_path($job['slug'], OBJECT, 'job');
        
        if ($existing) {
            $post_id = $existing->ID;
            echo "Updating Job: {$job['title']['rendered']}<br>";
        } else {
            $post_id = wp_insert_post(array(
                'post_title' => $job['title']['rendered'],
                'post_content' => $job['content']['rendered'],
                'post_excerpt' => $job['excerpt']['rendered'],
                'post_name' => $job['slug'],
                'post_type' => 'job',
                'post_status' => 'publish',
            ));
            
            echo "Created Job: {$job['title']['rendered']}<br>";
        }
        
        update_post_meta($post_id, 'venue_id', $job['venue_id']);
        update_post_meta($post_id, 'department', $job['department']);
        update_post_meta($post_id, 'job_type', $job['job_type']);
        update_post_meta($post_id, 'salary', $job['salary']);
        update_post_meta($post_id, 'requirements', json_encode($job['requirements']));
        update_post_meta($post_id, 'benefits', json_encode($job['benefits']));
        
        if (!empty($job['featured_media'])) {
            set_featured_image_from_url($post_id, $job['featured_media']);
        }
    }
}

/**
 * Import Venues from JSON
 */
function import_venues() {
    $json_file = ABSPATH . 'public/api/venues.json';
    
    if (!file_exists($json_file)) {
        echo "Venues JSON file not found.<br>";
        return;
    }
    
    $data = json_decode(file_get_contents($json_file), true);
    
    // Import metadata
    if (isset($data['metadata'])) {
        update_option('eatisfamily_venues_metadata', $data['metadata']);
        echo "Updated venues metadata<br>";
    }
    
    // Import event types
    if (isset($data['event_types'])) {
        update_option('eatisfamily_event_types', $data['event_types']);
        echo "Updated event types<br>";
    }
    
    // Import stats
    if (isset($data['stats'])) {
        update_option('eatisfamily_stats', $data['stats']);
        echo "Updated stats<br>";
    }
    
    // Import venues
    if (isset($data['venues'])) {
        foreach ($data['venues'] as $venue) {
            $existing = get_page_by_path($venue['id'], OBJECT, 'venue');
            
            if ($existing) {
                $post_id = $existing->ID;
                echo "Updating Venue: {$venue['name']}<br>";
            } else {
                $post_id = wp_insert_post(array(
                    'post_title' => $venue['name'],
                    'post_content' => $venue['description'] ?? '',
                    'post_name' => $venue['id'],
                    'post_type' => 'venue',
                    'post_status' => 'publish',
                ));
                
                echo "Created Venue: {$venue['name']}<br>";
            }
            
            update_post_meta($post_id, 'location', $venue['location']);
            update_post_meta($post_id, 'city', $venue['city']);
            update_post_meta($post_id, 'country', $venue['country']);
            update_post_meta($post_id, 'venue_type', $venue['type']);
            update_post_meta($post_id, 'latitude', $venue['lat']);
            update_post_meta($post_id, 'longitude', $venue['lng']);
            
            if (isset($venue['capacity'])) {
                update_post_meta($post_id, 'capacity', $venue['capacity']);
            }
            
            if (isset($venue['amenities'])) {
                update_post_meta($post_id, 'amenities', json_encode($venue['amenities']));
            }
            
            if (!empty($venue['image'])) {
                set_featured_image_from_url($post_id, $venue['image']);
            }
        }
    }
}

/**
 * Import Site Content from JSON
 */
function import_site_content() {
    $json_file = ABSPATH . 'public/api/site-content.json';
    
    if (!file_exists($json_file)) {
        echo "Site content JSON file not found.<br>";
        return;
    }
    
    $content = json_decode(file_get_contents($json_file), true);
    update_option('eatisfamily_site_content', $content);
    echo "Updated site content<br>";
}

/**
 * Import Pages Content from JSON
 */
function import_pages_content() {
    $json_file = ABSPATH . 'public/api/pages-content.json';
    
    if (!file_exists($json_file)) {
        echo "Pages content JSON file not found.<br>";
        return;
    }
    
    $content = json_decode(file_get_contents($json_file), true);
    update_option('eatisfamily_pages_content', $content);
    echo "Updated pages content<br>";
}

/**
 * Helper function to set featured image from URL
 */
function set_featured_image_from_url($post_id, $image_url) {
    // Check if image already exists
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id) {
        return $thumbnail_id;
    }
    
    // Download image
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return false;
    }
    
    $file_array = array(
        'name' => basename($image_url),
        'tmp_name' => $tmp
    );
    
    $id = media_handle_sideload($file_array, $post_id);
    
    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }
    
    set_post_thumbnail($post_id, $id);
    return $id;
}

// Run the import
echo "<h1>Eat Is Family - Data Import</h1>";
echo "<p>Starting import process...</p><hr>";

echo "<h2>Importing Activities...</h2>";
import_activities();
echo "<hr>";

echo "<h2>Importing Blog Posts...</h2>";
import_blog_posts();
echo "<hr>";

echo "<h2>Importing Events...</h2>";
import_events();
echo "<hr>";

echo "<h2>Importing Jobs...</h2>";
import_jobs();
echo "<hr>";

echo "<h2>Importing Venues...</h2>";
import_venues();
echo "<hr>";

echo "<h2>Importing Site Content...</h2>";
import_site_content();
echo "<hr>";

echo "<h2>Importing Pages Content...</h2>";
import_pages_content();
echo "<hr>";

echo "<h1 style='color: green;'>Import completed successfully!</h1>";
echo "<p><strong>IMPORTANT:</strong> For security reasons, please delete or rename this file (import-data.php) now.</p>";
