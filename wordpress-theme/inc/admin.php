<?php
/**
 * Admin Customization for Eat Is Family Theme
 * Additional admin interface improvements
 * 
 * @package EatIsFamily
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Activities admin list
 */
function eatisfamily_activity_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['activity_date'] = __('Date', 'eatisfamily');
    $new_columns['category'] = __('Category', 'eatisfamily');
    $new_columns['price'] = __('Price', 'eatisfamily');
    $new_columns['spots'] = __('Available Spots', 'eatisfamily');
    $new_columns['status'] = __('Status', 'eatisfamily');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_activity_posts_columns', 'eatisfamily_activity_columns');

/**
 * Fill custom columns with data
 */
function eatisfamily_activity_custom_column($column, $post_id) {
    switch ($column) {
        case 'activity_date':
            $date = get_post_meta($post_id, 'activity_date', true);
            echo $date ? esc_html(date('M j, Y', strtotime($date))) : '—';
            break;
            
        case 'category':
            echo esc_html(get_post_meta($post_id, 'category', true) ?: '—');
            break;
            
        case 'price':
            echo esc_html(get_post_meta($post_id, 'price', true) ?: '—');
            break;
            
        case 'spots':
            $available = get_post_meta($post_id, 'available_spots', true);
            $capacity = get_post_meta($post_id, 'capacity', true);
            if ($available && $capacity) {
                echo esc_html($available . ' / ' . $capacity);
            } else {
                echo '—';
            }
            break;
            
        case 'status':
            $status = get_post_meta($post_id, 'status', true) ?: 'open';
            $colors = array(
                'open' => 'green',
                'closed' => 'red',
                'full' => 'orange',
            );
            $color = $colors[$status] ?? 'gray';
            echo '<span style="color: ' . esc_attr($color) . '; font-weight: bold;">' . esc_html(ucfirst($status)) . '</span>';
            break;
    }
}
add_action('manage_activity_posts_custom_column', 'eatisfamily_activity_custom_column', 10, 2);

/**
 * Add custom columns to Jobs admin list
 */
function eatisfamily_job_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['department'] = __('Department', 'eatisfamily');
    $new_columns['job_type'] = __('Type', 'eatisfamily');
    $new_columns['venue'] = __('Venue', 'eatisfamily');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_job_posts_columns', 'eatisfamily_job_columns');

/**
 * Fill Job custom columns
 */
function eatisfamily_job_custom_column($column, $post_id) {
    switch ($column) {
        case 'department':
            echo esc_html(get_post_meta($post_id, 'department', true) ?: '—');
            break;
            
        case 'job_type':
            echo esc_html(get_post_meta($post_id, 'job_type', true) ?: '—');
            break;
            
        case 'venue':
            echo esc_html(get_post_meta($post_id, 'venue_id', true) ?: '—');
            break;
    }
}
add_action('manage_job_posts_custom_column', 'eatisfamily_job_custom_column', 10, 2);

/**
 * Add meta boxes for custom fields
 */
function eatisfamily_add_meta_boxes() {
    // Activity meta box
    add_meta_box(
        'activity_details',
        __('Activity Details', 'eatisfamily'),
        'eatisfamily_activity_meta_box',
        'activity',
        'normal',
        'high'
    );
    
    // Job meta box
    add_meta_box(
        'job_details',
        __('Job Details', 'eatisfamily'),
        'eatisfamily_job_meta_box',
        'job',
        'normal',
        'high'
    );
    
    // Venue meta box
    add_meta_box(
        'venue_details',
        __('Venue Details', 'eatisfamily'),
        'eatisfamily_venue_meta_box',
        'venue',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'eatisfamily_add_meta_boxes');

/**
 * Activity meta box content
 */
function eatisfamily_activity_meta_box($post) {
    wp_nonce_field('eatisfamily_activity_meta', 'eatisfamily_activity_nonce');
    
    $description = get_post_meta($post->ID, 'description', true);
    $activity_date = get_post_meta($post->ID, 'activity_date', true);
    $location = get_post_meta($post->ID, 'location', true);
    $capacity = get_post_meta($post->ID, 'capacity', true);
    $available_spots = get_post_meta($post->ID, 'available_spots', true);
    $category = get_post_meta($post->ID, 'category', true);
    $price = get_post_meta($post->ID, 'price', true);
    $duration = get_post_meta($post->ID, 'duration', true);
    $status = get_post_meta($post->ID, 'status', true);
    ?>
    
    <p>
        <label for="description"><strong><?php _e('Short Description', 'eatisfamily'); ?></strong></label><br>
        <textarea id="description" name="description" rows="3" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea>
    </p>
    
    <p>
        <label for="activity_date"><strong><?php _e('Date & Time', 'eatisfamily'); ?></strong></label><br>
        <input type="datetime-local" id="activity_date" name="activity_date" value="<?php echo esc_attr($activity_date ? date('Y-m-d\TH:i', strtotime($activity_date)) : ''); ?>" style="width: 100%;">
        <small>Format: YYYY-MM-DDTHH:MM</small>
    </p>
    
    <p>
        <label for="location"><strong><?php _e('Location', 'eatisfamily'); ?></strong></label><br>
        <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" style="width: 100%;">
    </p>
    
    <p>
        <label for="category"><strong><?php _e('Category', 'eatisfamily'); ?></strong></label><br>
        <input type="text" id="category" name="category" value="<?php echo esc_attr($category); ?>" style="width: 100%;">
    </p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <p>
            <label for="capacity"><strong><?php _e('Capacity', 'eatisfamily'); ?></strong></label><br>
            <input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($capacity); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="available_spots"><strong><?php _e('Available Spots', 'eatisfamily'); ?></strong></label><br>
            <input type="number" id="available_spots" name="available_spots" value="<?php echo esc_attr($available_spots); ?>" style="width: 100%;">
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
        <p>
            <label for="price"><strong><?php _e('Price', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="price" name="price" value="<?php echo esc_attr($price); ?>" placeholder="€85" style="width: 100%;">
        </p>
        
        <p>
            <label for="duration"><strong><?php _e('Duration', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="duration" name="duration" value="<?php echo esc_attr($duration); ?>" placeholder="3 hours" style="width: 100%;">
        </p>
        
        <p>
            <label for="status"><strong><?php _e('Status', 'eatisfamily'); ?></strong></label><br>
            <select id="status" name="status" style="width: 100%;">
                <option value="open" <?php selected($status, 'open'); ?>>Open</option>
                <option value="closed" <?php selected($status, 'closed'); ?>>Closed</option>
                <option value="full" <?php selected($status, 'full'); ?>>Full</option>
            </select>
        </p>
    </div>
    <?php
}

/**
 * Job meta box content
 */
function eatisfamily_job_meta_box($post) {
    wp_nonce_field('eatisfamily_job_meta', 'eatisfamily_job_nonce');
    
    $venue_id = get_post_meta($post->ID, 'venue_id', true);
    $department = get_post_meta($post->ID, 'department', true);
    $job_type = get_post_meta($post->ID, 'job_type', true);
    $salary = get_post_meta($post->ID, 'salary', true);
    $requirements = get_post_meta($post->ID, 'requirements', true);
    $benefits = get_post_meta($post->ID, 'benefits', true);
    ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <p>
            <label for="department"><strong><?php _e('Department', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="department" name="department" value="<?php echo esc_attr($department); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="job_type"><strong><?php _e('Job Type', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="job_type" name="job_type" value="<?php echo esc_attr($job_type); ?>" placeholder="Full-time" style="width: 100%;">
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <p>
            <label for="venue_id"><strong><?php _e('Venue ID', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="venue_id" name="venue_id" value="<?php echo esc_attr($venue_id); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="salary"><strong><?php _e('Salary', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="salary" name="salary" value="<?php echo esc_attr($salary); ?>" placeholder="$150 - $200 / hour" style="width: 100%;">
        </p>
    </div>
    
    <p>
        <label for="requirements"><strong><?php _e('Requirements (JSON Array)', 'eatisfamily'); ?></strong></label><br>
        <textarea id="requirements" name="requirements" rows="4" style="width: 100%; font-family: monospace;"><?php echo esc_textarea($requirements); ?></textarea>
        <small>Format: ["Requirement 1", "Requirement 2", "Requirement 3"]</small>
    </p>
    
    <p>
        <label for="benefits"><strong><?php _e('Benefits (JSON Array)', 'eatisfamily'); ?></strong></label><br>
        <textarea id="benefits" name="benefits" rows="4" style="width: 100%; font-family: monospace;"><?php echo esc_textarea($benefits); ?></textarea>
        <small>Format: ["Benefit 1", "Benefit 2", "Benefit 3"]</small>
    </p>
    <?php
}

/**
 * Venue meta box content
 */
function eatisfamily_venue_meta_box($post) {
    wp_nonce_field('eatisfamily_venue_meta', 'eatisfamily_venue_nonce');
    
    $location = get_post_meta($post->ID, 'location', true);
    $city = get_post_meta($post->ID, 'city', true);
    $country = get_post_meta($post->ID, 'country', true);
    $venue_type = get_post_meta($post->ID, 'venue_type', true);
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
    $capacity = get_post_meta($post->ID, 'capacity', true);
    ?>
    
    <p>
        <label for="location"><strong><?php _e('Full Address', 'eatisfamily'); ?></strong></label><br>
        <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" style="width: 100%;">
    </p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
        <p>
            <label for="city"><strong><?php _e('City', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="city" name="city" value="<?php echo esc_attr($city); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="country"><strong><?php _e('Country', 'eatisfamily'); ?></strong></label><br>
            <input type="text" id="country" name="country" value="<?php echo esc_attr($country); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="venue_type"><strong><?php _e('Type', 'eatisfamily'); ?></strong></label><br>
            <select id="venue_type" name="venue_type" style="width: 100%;">
                <option value="">Select...</option>
                <option value="stadium" <?php selected($venue_type, 'stadium'); ?>>Stadium</option>
                <option value="festival" <?php selected($venue_type, 'festival'); ?>>Festival</option>
                <option value="arena" <?php selected($venue_type, 'arena'); ?>>Arena</option>
            </select>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
        <p>
            <label for="latitude"><strong><?php _e('Latitude', 'eatisfamily'); ?></strong></label><br>
            <input type="number" step="any" id="latitude" name="latitude" value="<?php echo esc_attr($latitude); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="longitude"><strong><?php _e('Longitude', 'eatisfamily'); ?></strong></label><br>
            <input type="number" step="any" id="longitude" name="longitude" value="<?php echo esc_attr($longitude); ?>" style="width: 100%;">
        </p>
        
        <p>
            <label for="capacity"><strong><?php _e('Capacity', 'eatisfamily'); ?></strong></label><br>
            <input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($capacity); ?>" style="width: 100%;">
        </p>
    </div>
    <?php
}

/**
 * Save meta box data
 */
function eatisfamily_save_meta_boxes($post_id) {
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Activity meta
    if (isset($_POST['eatisfamily_activity_nonce']) && wp_verify_nonce($_POST['eatisfamily_activity_nonce'], 'eatisfamily_activity_meta')) {
        $fields = array('description', 'activity_date', 'location', 'capacity', 'available_spots', 'category', 'price', 'duration', 'status');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // Job meta
    if (isset($_POST['eatisfamily_job_nonce']) && wp_verify_nonce($_POST['eatisfamily_job_nonce'], 'eatisfamily_job_meta')) {
        $fields = array('venue_id', 'department', 'job_type', 'salary', 'requirements', 'benefits');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
            }
        }
    }
    
    // Venue meta
    if (isset($_POST['eatisfamily_venue_nonce']) && wp_verify_nonce($_POST['eatisfamily_venue_nonce'], 'eatisfamily_venue_meta')) {
        $fields = array('location', 'city', 'country', 'venue_type', 'latitude', 'longitude', 'capacity');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}
add_action('save_post', 'eatisfamily_save_meta_boxes');

/**
 * Add admin notice after theme activation
 */
function eatisfamily_activation_notice() {
    global $pagenow;
    
    if ($pagenow == 'themes.php' && isset($_GET['activated'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Eat Is Family theme activated!</strong></p>
            <p>Don't forget to:</p>
            <ol>
                <li>Go to <a href="<?php echo admin_url('options-permalink.php'); ?>">Settings > Permalinks</a> and save to refresh permalinks</li>
                <li>Check your <a href="<?php echo rest_url('eatisfamily/v1/'); ?>" target="_blank">API endpoints</a></li>
                <li>Import your data if needed</li>
            </ol>
        </div>
        <?php
    }
}
add_action('admin_notices', 'eatisfamily_activation_notice');
