<?php
/**
 * EIF Backend - Admin Pages
 *
 * This file handles admin pages for editing Site Content and Pages Content
 * that were previously stored in JSON files.
 *
 * @package EIFBackend
 * @version 4.0.0
 * 
 * NOTE: Uses AJAX with base64 encoding to bypass mod_security restrictions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * AJAX HANDLER FOR PAGES CONTENT (Bypasses mod_security 403 errors)
 * ============================================================================
 */
add_action('wp_ajax_eatisfamily_save_pages_content', 'eatisfamily_ajax_save_pages_content');

function eatisfamily_ajax_save_pages_content() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eatisfamily_ajax_pages_content')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied'));
        return;
    }
    
    // Get and decode the base64 data
    if (!isset($_POST['encoded_data'])) {
        wp_send_json_error(array('message' => 'No data received'));
        return;
    }
    
    $decoded_data = base64_decode($_POST['encoded_data']);
    if ($decoded_data === false) {
        wp_send_json_error(array('message' => 'Failed to decode data'));
        return;
    }
    
    $form_data = json_decode($decoded_data, true);
    if ($form_data === null) {
        wp_send_json_error(array('message' => 'Invalid JSON data'));
        return;
    }
    
    // Build pages_content structure (same as before but from decoded data)
    $pages_content = eatisfamily_build_pages_content_from_data($form_data);
    
    // Save to database
    $result = update_option('eatisfamily_pages_content', $pages_content);
    
    if ($result !== false) {
        wp_send_json_success(array('message' => 'Pages content saved successfully!'));
    } else {
        // Check if data is identical (update_option returns false if unchanged)
        $current = get_option('eatisfamily_pages_content', array());
        if ($current == $pages_content) {
            wp_send_json_success(array('message' => 'Pages content saved (no changes detected)'));
        } else {
            wp_send_json_error(array('message' => 'Failed to save to database'));
        }
    }
}

/**
 * Build pages_content array from form data
 */
function eatisfamily_build_pages_content_from_data($data) {
    return array(
        'homepage' => array(
            'seo' => array(
                'title' => sanitize_text_field($data['homepage_seo_title'] ?? ''),
                'description' => sanitize_textarea_field($data['homepage_seo_description'] ?? ''),
                'keywords' => sanitize_text_field($data['homepage_seo_keywords'] ?? ''),
                'og_title' => sanitize_text_field($data['homepage_seo_og_title'] ?? ''),
                'og_description' => sanitize_textarea_field($data['homepage_seo_og_description'] ?? ''),
                'og_image' => esc_url_raw($data['homepage_seo_og_image'] ?? ''),
            ),
            'hero_section' => array(
                'bg' => esc_url_raw($data['homepage_hero_bg'] ?? ''),
                'video_type' => sanitize_text_field($data['homepage_hero_video_type'] ?? 'image'),
                'video_url' => esc_url_raw($data['homepage_hero_video_url'] ?? ''),
                'youtube_id' => sanitize_text_field($data['homepage_hero_youtube_id'] ?? ''),
                'title' => array(
                    'line_1' => wp_kses_post($data['homepage_hero_title_line1'] ?? ''),
                    'line_2' => wp_kses_post($data['homepage_hero_title_line2'] ?? ''),
                    'line_3' => wp_kses_post($data['homepage_hero_title_line3'] ?? ''),
                ),
            ),
            'intro_section' => array(
                'texte' => wp_kses_post($data['homepage_intro_texte'] ?? ''),
            ),
            'services_section' => array(
                'tag' => sanitize_text_field($data['homepage_services_tag'] ?? ''),
                'title' => array(
                    'line_1' => sanitize_text_field($data['homepage_services_title_line1'] ?? ''),
                    'highlight' => sanitize_text_field($data['homepage_services_title_highlight'] ?? ''),
                    'line_2' => sanitize_text_field($data['homepage_services_title_line2'] ?? ''),
                ),
            ),
            'cta_section' => array(
                'title' => sanitize_text_field($data['homepage_cta_title'] ?? ''),
                'description' => wp_kses_post($data['homepage_cta_description'] ?? ''),
            ),
            'sustainable_service_title' => sanitize_text_field($data['homepage_sustainable_title'] ?? ''),
            'beautiful' => array(
                'title' => sanitize_text_field($data['homepage_beautiful_title'] ?? ''),
                'text' => wp_kses_post($data['homepage_beautiful_text'] ?? ''),
                'image' => esc_url_raw($data['homepage_beautiful_image'] ?? ''),
            ),
            'partners_title' => sanitize_text_field($data['homepage_partners_title'] ?? ''),
            'homepageCTA' => array(
                'title' => sanitize_text_field($data['homepage_cta_block_title'] ?? ''),
                'image' => esc_url_raw($data['homepage_cta_block_image'] ?? ''),
                'description' => wp_kses_post($data['homepage_cta_block_description'] ?? ''),
                'additionalText' => wp_kses_post($data['homepage_cta_block_additional'] ?? ''),
            ),
        ),
        'about' => array(
            'hero' => array(
                'title' => sanitize_text_field($data['about_hero_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['about_hero_subtitle'] ?? ''),
                'description' => wp_kses_post($data['about_hero_description'] ?? ''),
                'image' => array(
                    'src' => esc_url_raw($data['about_hero_image'] ?? ''),
                    'alt' => sanitize_text_field($data['about_hero_image_alt'] ?? ''),
                ),
            ),
            'section_titles' => array(
                'values' => sanitize_text_field($data['about_values_title'] ?? ''),
                'team' => sanitize_text_field($data['about_team_title'] ?? ''),
            ),
            'seo' => array(
                'title' => sanitize_text_field($data['about_seo_title'] ?? ''),
                'description' => sanitize_textarea_field($data['about_seo_description'] ?? ''),
            ),
        ),
        'contact' => array(
            'hero' => array(
                'title' => sanitize_text_field($data['contact_hero_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['contact_hero_subtitle'] ?? ''),
            ),
            'form_title' => sanitize_text_field($data['contact_form_title'] ?? ''),
            'form_subtitle' => sanitize_text_field($data['contact_form_subtitle'] ?? ''),
        ),
        'careers' => array(
            'seo' => array(
                'title' => sanitize_text_field($data['careers_seo_title'] ?? ''),
                'description' => sanitize_textarea_field($data['careers_seo_description'] ?? ''),
            ),
            'hero_default' => array(
                'title_line_1' => sanitize_text_field($data['careers_hero_title_line1'] ?? ''),
                'title_line_2' => sanitize_text_field($data['careers_hero_title_line2'] ?? ''),
                'image' => esc_url_raw($data['careers_hero_image'] ?? ''),
                'background_image' => esc_url_raw($data['careers_hero_bg'] ?? ''),
            ),
            'join_box' => array(
                'title' => sanitize_text_field($data['careers_join_title'] ?? ''),
                'description' => wp_kses_post($data['careers_join_description'] ?? ''),
            ),
            'benefits_title' => sanitize_text_field($data['careers_benefits_title'] ?? ''),
            'benefits' => isset($data['careers_benefits']) ? array_filter(array_map('sanitize_text_field', (array)$data['careers_benefits'])) : array(),
        ),
        'events' => array(
            'hero' => array(
                'title' => sanitize_text_field($data['events_hero_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['events_hero_subtitle'] ?? ''),
            ),
        ),
        'forms' => array(
            'job_search' => array(
                'title' => sanitize_text_field($data['form_job_search_title'] ?? 'Find Your Perfect Role'),
                'subtitle' => sanitize_text_field($data['form_job_search_subtitle'] ?? 'Explore open positions across France'),
                'job_title_placeholder' => sanitize_text_field($data['form_job_search_job_placeholder'] ?? 'Select job title'),
                'site_placeholder' => sanitize_text_field($data['form_job_search_site_placeholder'] ?? 'Select sites'),
                'all_jobs_label' => sanitize_text_field($data['form_job_search_all_jobs'] ?? 'All job titles'),
                'all_sites_label' => sanitize_text_field($data['form_job_search_all_sites'] ?? 'All sites'),
                'search_button' => sanitize_text_field($data['form_job_search_button'] ?? 'Search'),
                'loading_text' => sanitize_text_field($data['form_job_search_loading'] ?? 'Loading...'),
            ),
            'contact_form' => array(
                'name_label' => sanitize_text_field($data['form_contact_name_label'] ?? 'Your Name'),
                'name_placeholder' => sanitize_text_field($data['form_contact_name_placeholder'] ?? 'Enter your name'),
                'email_label' => sanitize_text_field($data['form_contact_email_label'] ?? 'Email Address'),
                'email_placeholder' => sanitize_text_field($data['form_contact_email_placeholder'] ?? 'Enter your email'),
                'subject_label' => sanitize_text_field($data['form_contact_subject_label'] ?? 'Subject'),
                'subject_placeholder' => sanitize_text_field($data['form_contact_subject_placeholder'] ?? 'What is this about?'),
                'message_label' => sanitize_text_field($data['form_contact_message_label'] ?? 'Message'),
                'message_placeholder' => sanitize_text_field($data['form_contact_message_placeholder'] ?? 'Write your message...'),
                'submit_button' => sanitize_text_field($data['form_contact_submit_button'] ?? 'Send Message'),
                'submitting_button' => sanitize_text_field($data['form_contact_submitting_button'] ?? 'Sending...'),
                'success_message' => sanitize_text_field($data['form_contact_success_message'] ?? 'Thank you! Your message has been sent.'),
                'error_message' => sanitize_text_field($data['form_contact_error_message'] ?? 'Sorry, there was an error. Please try again.'),
            ),
            'job_application' => array(
                'title' => sanitize_text_field($data['form_job_app_title'] ?? 'Apply for this Position'),
                'firstname_label' => sanitize_text_field($data['form_job_app_firstname_label'] ?? 'First Name'),
                'firstname_placeholder' => sanitize_text_field($data['form_job_app_firstname_placeholder'] ?? 'Enter your first name'),
                'lastname_label' => sanitize_text_field($data['form_job_app_lastname_label'] ?? 'Last Name'),
                'lastname_placeholder' => sanitize_text_field($data['form_job_app_lastname_placeholder'] ?? 'Enter your last name'),
                'email_label' => sanitize_text_field($data['form_job_app_email_label'] ?? 'Email'),
                'email_placeholder' => sanitize_text_field($data['form_job_app_email_placeholder'] ?? 'Enter your email'),
                'phone_label' => sanitize_text_field($data['form_job_app_phone_label'] ?? 'Phone Number'),
                'phone_placeholder' => sanitize_text_field($data['form_job_app_phone_placeholder'] ?? 'Enter your phone number'),
                'resume_label' => sanitize_text_field($data['form_job_app_resume_label'] ?? 'Upload Resume/CV'),
                'resume_placeholder' => sanitize_text_field($data['form_job_app_resume_placeholder'] ?? 'Choose file (PDF, DOC, DOCX)'),
                'coverletter_label' => sanitize_text_field($data['form_job_app_coverletter_label'] ?? 'Cover Letter (Optional)'),
                'coverletter_placeholder' => sanitize_text_field($data['form_job_app_coverletter_placeholder'] ?? 'Tell us why you would be great for this role...'),
                'submit_button' => sanitize_text_field($data['form_job_app_submit_button'] ?? 'Submit Application'),
                'submitting_button' => sanitize_text_field($data['form_job_app_submitting_button'] ?? 'Submitting...'),
                'success_message' => sanitize_text_field($data['form_job_app_success_message'] ?? 'Application submitted successfully!'),
                'error_message' => sanitize_text_field($data['form_job_app_error_message'] ?? 'Error submitting application. Please try again.'),
            ),
        ),
    );
}

/**
 * ============================================================================
 * ADMIN MENU PAGES
 * ============================================================================
 */

// Note: Admin menus (Site Content, Pages Content, Partners, Services, Sustainability, Gallery)
// are registered in inc/admin-pages-extended.php via eatisfamily_register_admin_menus() at priority 5.

/**
 * ============================================================================
 * SITE CONTENT EDITOR
 * ============================================================================
 */

/**
 * Site Content admin page
 */
function eatisfamily_site_content_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_site_content_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_site_content_nonce'], 'save_site_content')) {
        
        $site_content = array(
            'site' => array(
                'name' => sanitize_text_field($_POST['site_name'] ?? ''),
                'tagline' => sanitize_text_field($_POST['site_tagline'] ?? ''),
                'description' => sanitize_textarea_field($_POST['site_description'] ?? ''),
                'seo' => array(
                    'default_title' => sanitize_text_field($_POST['seo_default_title'] ?? ''),
                    'default_description' => sanitize_textarea_field($_POST['seo_default_description'] ?? ''),
                    'keywords' => sanitize_text_field($_POST['seo_keywords'] ?? ''),
                    'og_image' => esc_url_raw($_POST['seo_og_image'] ?? ''),
                ),
                'contact' => array(
                    'email' => sanitize_email($_POST['contact_email'] ?? ''),
                    'phone' => sanitize_text_field($_POST['contact_phone'] ?? ''),
                ),
                'social' => array(
                    'facebook' => esc_url_raw($_POST['social_facebook'] ?? ''),
                    'instagram' => esc_url_raw($_POST['social_instagram'] ?? ''),
                    'twitter' => esc_url_raw($_POST['social_twitter'] ?? ''),
                    'linkedin' => esc_url_raw($_POST['social_linkedin'] ?? ''),
                    'youtube' => esc_url_raw($_POST['social_youtube'] ?? ''),
                ),
            ),
        );
        
        update_option('eatisfamily_site_content', $site_content);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Site content saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $site_content = get_option('eatisfamily_site_content', array());
    $site = $site_content['site'] ?? array();
    $seo = $site['seo'] ?? array();
    $contact = $site['contact'] ?? array();
    $social = $site['social'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Site Content Settings', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Edit global site information, SEO settings, contact details, and social media links.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_site_content', 'eatisfamily_site_content_nonce'); ?>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <!-- Site Information -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Site Information', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_name"><?php _e('Site Name', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="site_name" id="site_name" value="<?php echo esc_attr($site['name'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_tagline"><?php _e('Tagline', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="site_tagline" id="site_tagline" value="<?php echo esc_attr($site['tagline'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="site_description"><?php _e('Description', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <textarea name="site_description" id="site_description" rows="3" class="large-text"><?php echo esc_textarea($site['description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    
                    <!-- SEO Settings -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('SEO Settings', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_default_title"><?php _e('Default Title', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_default_title" id="seo_default_title" value="<?php echo esc_attr($seo['default_title'] ?? ''); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_default_description"><?php _e('Default Description', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <textarea name="seo_default_description" id="seo_default_description" rows="3" class="large-text"><?php echo esc_textarea($seo['default_description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_keywords"><?php _e('Keywords', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_keywords" id="seo_keywords" value="<?php echo esc_attr($seo['keywords'] ?? ''); ?>" class="large-text">
                            <p class="description"><?php _e('Comma-separated keywords', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seo_og_image"><?php _e('Default OG Image', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seo_og_image" id="seo_og_image" value="<?php echo esc_attr($seo['og_image'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="seo_og_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                    
                    <!-- Contact Information -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Contact Information', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="contact_email"><?php _e('Email', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="email" name="contact_email" id="contact_email" value="<?php echo esc_attr($contact['email'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="contact_phone"><?php _e('Phone', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr($contact['phone'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <!-- Social Media -->
                    <tr>
                        <th colspan="2">
                            <h2><?php _e('Social Media Links', 'eatisfamily'); ?></h2>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_facebook"><?php _e('Facebook', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_facebook" id="social_facebook" value="<?php echo esc_attr($social['facebook'] ?? ''); ?>" class="regular-text" placeholder="https://facebook.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_instagram"><?php _e('Instagram', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_instagram" id="social_instagram" value="<?php echo esc_attr($social['instagram'] ?? ''); ?>" class="regular-text" placeholder="https://instagram.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_twitter"><?php _e('Twitter', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_twitter" id="social_twitter" value="<?php echo esc_attr($social['twitter'] ?? ''); ?>" class="regular-text" placeholder="https://twitter.com/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_linkedin"><?php _e('LinkedIn', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_linkedin" id="social_linkedin" value="<?php echo esc_attr($social['linkedin'] ?? ''); ?>" class="regular-text" placeholder="https://linkedin.com/company/...">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="social_youtube"><?php _e('YouTube', 'eatisfamily'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="social_youtube" id="social_youtube" value="<?php echo esc_attr($social['youtube'] ?? ''); ?>" class="regular-text" placeholder="https://youtube.com/...">
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button(__('Save Site Content', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.eatisfamily-upload-media').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            
            var frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + targetId).val(attachment.url);
            });
            
            frame.open();
        });
    });
    </script>
    <?php
}

/**
 * ============================================================================
 * PAGES CONTENT EDITOR
 * ============================================================================
 * 
 * This admin page matches the EXACT structure of pages-content.json
 * Structure: homepage.hero_section.title.line_1, line_2, line_3
 * Supports video backgrounds (image, YouTube, MP4)
 */

/**
 * Pages Content admin page - CORRECTED TO MATCH JSON STRUCTURE
 */
function eatisfamily_pages_content_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_pages_content_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_pages_content_nonce'], 'save_pages_content')) {
        
        // Build pages_content with CORRECT structure matching pages-content.json
        $pages_content = array(
            'homepage' => array(
                'seo' => array(
                    'title' => sanitize_text_field($_POST['homepage_seo_title'] ?? ''),
                    'description' => sanitize_textarea_field($_POST['homepage_seo_description'] ?? ''),
                    'keywords' => sanitize_text_field($_POST['homepage_seo_keywords'] ?? ''),
                    'og_title' => sanitize_text_field($_POST['homepage_seo_og_title'] ?? ''),
                    'og_description' => sanitize_textarea_field($_POST['homepage_seo_og_description'] ?? ''),
                    'og_image' => esc_url_raw($_POST['homepage_seo_og_image'] ?? ''),
                ),
                // CORRECT STRUCTURE: hero_section (not hero)
                'hero_section' => array(
                    'bg' => esc_url_raw($_POST['homepage_hero_bg'] ?? ''),
                    'video_type' => sanitize_text_field($_POST['homepage_hero_video_type'] ?? 'image'),
                    'video_url' => esc_url_raw($_POST['homepage_hero_video_url'] ?? ''),
                    'youtube_id' => sanitize_text_field($_POST['homepage_hero_youtube_id'] ?? ''),
                    // CORRECT STRUCTURE: title with line_1, line_2, line_3
                    'title' => array(
                        'line_1' => wp_kses_post($_POST['homepage_hero_title_line1'] ?? ''),
                        'line_2' => wp_kses_post($_POST['homepage_hero_title_line2'] ?? ''),
                        'line_3' => wp_kses_post($_POST['homepage_hero_title_line3'] ?? ''),
                    ),
                ),
                'intro_section' => array(
                    'texte' => wp_kses_post($_POST['homepage_intro_texte'] ?? ''),
                ),
                'services_section' => array(
                    'tag' => sanitize_text_field($_POST['homepage_services_tag'] ?? ''),
                    'title' => array(
                        'line_1' => sanitize_text_field($_POST['homepage_services_title_line1'] ?? ''),
                        'highlight' => sanitize_text_field($_POST['homepage_services_title_highlight'] ?? ''),
                        'line_2' => sanitize_text_field($_POST['homepage_services_title_line2'] ?? ''),
                    ),
                ),
                'cta_section' => array(
                    'title' => sanitize_text_field($_POST['homepage_cta_title'] ?? ''),
                    'description' => wp_kses_post($_POST['homepage_cta_description'] ?? ''),
                ),
                'sustainable_service_title' => sanitize_text_field($_POST['homepage_sustainable_title'] ?? ''),
                'beautiful' => array(
                    'title' => sanitize_text_field($_POST['homepage_beautiful_title'] ?? ''),
                    'text' => wp_kses_post($_POST['homepage_beautiful_text'] ?? ''),
                    'image' => esc_url_raw($_POST['homepage_beautiful_image'] ?? ''),
                ),
                'partners_title' => sanitize_text_field($_POST['homepage_partners_title'] ?? ''),
                'homepageCTA' => array(
                    'title' => sanitize_text_field($_POST['homepage_cta_block_title'] ?? ''),
                    'image' => esc_url_raw($_POST['homepage_cta_block_image'] ?? ''),
                    'description' => wp_kses_post($_POST['homepage_cta_block_description'] ?? ''),
                    'additionalText' => wp_kses_post($_POST['homepage_cta_block_additional'] ?? ''),
                ),
            ),
            'about' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['about_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['about_hero_subtitle'] ?? ''),
                    'description' => wp_kses_post($_POST['about_hero_description'] ?? ''),
                    'image' => array(
                        'src' => esc_url_raw($_POST['about_hero_image'] ?? ''),
                        'alt' => sanitize_text_field($_POST['about_hero_image_alt'] ?? ''),
                    ),
                ),
                'section_titles' => array(
                    'values' => sanitize_text_field($_POST['about_values_title'] ?? ''),
                    'team' => sanitize_text_field($_POST['about_team_title'] ?? ''),
                ),
                'seo' => array(
                    'title' => sanitize_text_field($_POST['about_seo_title'] ?? ''),
                    'description' => sanitize_textarea_field($_POST['about_seo_description'] ?? ''),
                ),
            ),
            'contact' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['contact_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['contact_hero_subtitle'] ?? ''),
                ),
                'form_title' => sanitize_text_field($_POST['contact_form_title'] ?? ''),
                'form_subtitle' => sanitize_text_field($_POST['contact_form_subtitle'] ?? ''),
            ),
            'careers' => array(
                'seo' => array(
                    'title' => sanitize_text_field($_POST['careers_seo_title'] ?? ''),
                    'description' => sanitize_textarea_field($_POST['careers_seo_description'] ?? ''),
                ),
                'hero_default' => array(
                    'title_line_1' => sanitize_text_field($_POST['careers_hero_title_line1'] ?? ''),
                    'title_line_2' => sanitize_text_field($_POST['careers_hero_title_line2'] ?? ''),
                    'image' => esc_url_raw($_POST['careers_hero_image'] ?? ''),
                    'background_image' => esc_url_raw($_POST['careers_hero_bg'] ?? ''),
                ),
                'join_box' => array(
                    'title' => sanitize_text_field($_POST['careers_join_title'] ?? ''),
                    'description' => wp_kses_post($_POST['careers_join_description'] ?? ''),
                ),
                'benefits_title' => sanitize_text_field($_POST['careers_benefits_title'] ?? ''),
                'benefits' => array_filter(array_map('sanitize_text_field', $_POST['careers_benefits'] ?? array())),
            ),
            'events' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['events_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['events_hero_subtitle'] ?? ''),
                ),
            ),
            // Form Labels for all forms
            'forms' => array(
                'job_search' => array(
                    'title' => sanitize_text_field($_POST['form_job_search_title'] ?? 'Find Your Perfect Role'),
                    'subtitle' => sanitize_text_field($_POST['form_job_search_subtitle'] ?? 'Explore open positions across France'),
                    'job_title_placeholder' => sanitize_text_field($_POST['form_job_search_job_placeholder'] ?? 'Select job title'),
                    'site_placeholder' => sanitize_text_field($_POST['form_job_search_site_placeholder'] ?? 'Select sites'),
                    'all_jobs_label' => sanitize_text_field($_POST['form_job_search_all_jobs'] ?? 'All job titles'),
                    'all_sites_label' => sanitize_text_field($_POST['form_job_search_all_sites'] ?? 'All sites'),
                    'search_button' => sanitize_text_field($_POST['form_job_search_button'] ?? 'Search'),
                    'loading_text' => sanitize_text_field($_POST['form_job_search_loading'] ?? 'Loading...'),
                ),
                'contact_form' => array(
                    'name_label' => sanitize_text_field($_POST['form_contact_name_label'] ?? 'Your Name'),
                    'name_placeholder' => sanitize_text_field($_POST['form_contact_name_placeholder'] ?? 'Enter your name'),
                    'email_label' => sanitize_text_field($_POST['form_contact_email_label'] ?? 'Email Address'),
                    'email_placeholder' => sanitize_text_field($_POST['form_contact_email_placeholder'] ?? 'Enter your email'),
                    'subject_label' => sanitize_text_field($_POST['form_contact_subject_label'] ?? 'Subject'),
                    'subject_placeholder' => sanitize_text_field($_POST['form_contact_subject_placeholder'] ?? 'What is this about?'),
                    'message_label' => sanitize_text_field($_POST['form_contact_message_label'] ?? 'Message'),
                    'message_placeholder' => sanitize_text_field($_POST['form_contact_message_placeholder'] ?? 'Write your message...'),
                    'submit_button' => sanitize_text_field($_POST['form_contact_submit_button'] ?? 'Send Message'),
                    'submitting_button' => sanitize_text_field($_POST['form_contact_submitting_button'] ?? 'Sending...'),
                    'success_message' => sanitize_text_field($_POST['form_contact_success_message'] ?? 'Thank you! Your message has been sent.'),
                    'error_message' => sanitize_text_field($_POST['form_contact_error_message'] ?? 'Sorry, there was an error. Please try again.'),
                ),
                'job_application' => array(
                    'title' => sanitize_text_field($_POST['form_job_app_title'] ?? 'Apply for this Position'),
                    'firstname_label' => sanitize_text_field($_POST['form_job_app_firstname_label'] ?? 'First Name'),
                    'firstname_placeholder' => sanitize_text_field($_POST['form_job_app_firstname_placeholder'] ?? 'Enter your first name'),
                    'lastname_label' => sanitize_text_field($_POST['form_job_app_lastname_label'] ?? 'Last Name'),
                    'lastname_placeholder' => sanitize_text_field($_POST['form_job_app_lastname_placeholder'] ?? 'Enter your last name'),
                    'email_label' => sanitize_text_field($_POST['form_job_app_email_label'] ?? 'Email'),
                    'email_placeholder' => sanitize_text_field($_POST['form_job_app_email_placeholder'] ?? 'Enter your email'),
                    'phone_label' => sanitize_text_field($_POST['form_job_app_phone_label'] ?? 'Phone Number'),
                    'phone_placeholder' => sanitize_text_field($_POST['form_job_app_phone_placeholder'] ?? 'Enter your phone number'),
                    'resume_label' => sanitize_text_field($_POST['form_job_app_resume_label'] ?? 'Upload Resume/CV'),
                    'resume_placeholder' => sanitize_text_field($_POST['form_job_app_resume_placeholder'] ?? 'Choose file (PDF, DOC, DOCX)'),
                    'coverletter_label' => sanitize_text_field($_POST['form_job_app_coverletter_label'] ?? 'Cover Letter (Optional)'),
                    'coverletter_placeholder' => sanitize_text_field($_POST['form_job_app_coverletter_placeholder'] ?? 'Tell us why you would be great for this role...'),
                    'submit_button' => sanitize_text_field($_POST['form_job_app_submit_button'] ?? 'Submit Application'),
                    'submitting_button' => sanitize_text_field($_POST['form_job_app_submitting_button'] ?? 'Submitting...'),
                    'success_message' => sanitize_text_field($_POST['form_job_app_success_message'] ?? 'Application submitted successfully!'),
                    'error_message' => sanitize_text_field($_POST['form_job_app_error_message'] ?? 'Error submitting application. Please try again.'),
                ),
            ),
        );
        
        update_option('eatisfamily_pages_content', $pages_content);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Pages content saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $pages_content = get_option('eatisfamily_pages_content', array());
    $homepage = $pages_content['homepage'] ?? array();
    $about = $pages_content['about'] ?? array();
    $contact = $pages_content['contact'] ?? array();
    $careers = $pages_content['careers'] ?? array();
    $events = $pages_content['events'] ?? array();
    $forms = $pages_content['forms'] ?? array();
    
    // Generate AJAX nonce for secure submission
    $ajax_nonce = wp_create_nonce('eatisfamily_ajax_pages_content');
    
    ?>
    <div class="wrap eatisfamily-pages-content">
        <h1><?php _e('Pages Content Settings', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Edit content for each page of your website. Changes are reflected immediately on the Vue.js frontend.', 'eatisfamily'); ?></p>
        
        <!-- AJAX Status Message -->
        <div id="eatisfamily-ajax-message" style="display:none;" class="notice is-dismissible"><p></p></div>
        
        <form method="post" action="" id="eatisfamily-pages-form">
            <input type="hidden" id="eatisfamily_ajax_nonce" value="<?php echo esc_attr($ajax_nonce); ?>">
            
            <h2 class="nav-tab-wrapper">
                <a href="#homepage" class="nav-tab nav-tab-active"><?php _e('Homepage', 'eatisfamily'); ?></a>
                <a href="#about" class="nav-tab"><?php _e('About', 'eatisfamily'); ?></a>
                <a href="#contact" class="nav-tab"><?php _e('Contact', 'eatisfamily'); ?></a>
                <a href="#careers" class="nav-tab"><?php _e('Careers', 'eatisfamily'); ?></a>
                <a href="#events" class="nav-tab"><?php _e('Events', 'eatisfamily'); ?></a>
                <a href="#forms" class="nav-tab"><?php _e('📝 Forms Labels', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- ============================================================ -->
            <!-- HOMEPAGE TAB - CORRECTED STRUCTURE -->
            <!-- ============================================================ -->
            <div id="homepage" class="tab-content" style="display: block;">
                
                <!-- Hero Section with Video Support -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎬 Hero Section', 'eatisfamily'); ?></h3>
                    <p class="description"><?php _e('The main hero area. You can use an image, YouTube video, or MP4 video as background.', 'eatisfamily'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_hero_video_type"><?php _e('Background Type', 'eatisfamily'); ?></label></th>
                            <td>
                                <select name="homepage_hero_video_type" id="homepage_hero_video_type" class="regular-text">
                                    <option value="image" <?php selected($homepage['hero_section']['video_type'] ?? 'image', 'image'); ?>><?php _e('Image', 'eatisfamily'); ?></option>
                                    <option value="youtube" <?php selected($homepage['hero_section']['video_type'] ?? '', 'youtube'); ?>><?php _e('YouTube Video', 'eatisfamily'); ?></option>
                                    <option value="mp4" <?php selected($homepage['hero_section']['video_type'] ?? '', 'mp4'); ?>><?php _e('MP4 Video', 'eatisfamily'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="video-field video-field-image">
                            <th scope="row"><label for="homepage_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_bg" id="homepage_hero_bg" value="<?php echo esc_attr($homepage['hero_section']['bg'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_hero_bg"><?php _e('Select Image', 'eatisfamily'); ?></button>
                                <?php if (!empty($homepage['hero_section']['bg'])): ?>
                                    <div class="image-preview" style="margin-top: 10px;">
                                        <img src="<?php echo esc_url($homepage['hero_section']['bg']); ?>" style="max-width: 300px; max-height: 150px;">
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="video-field video-field-youtube" style="display: none;">
                            <th scope="row"><label for="homepage_hero_youtube_id"><?php _e('YouTube Video ID', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_youtube_id" id="homepage_hero_youtube_id" value="<?php echo esc_attr($homepage['hero_section']['youtube_id'] ?? ''); ?>" class="regular-text" placeholder="dQw4w9WgXcQ">
                                <p class="description"><?php _e('Enter only the video ID (the part after v= in the URL). Example: for https://youtube.com/watch?v=dQw4w9WgXcQ, enter dQw4w9WgXcQ', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                        <tr class="video-field video-field-mp4" style="display: none;">
                            <th scope="row"><label for="homepage_hero_video_url"><?php _e('MP4 Video URL', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_video_url" id="homepage_hero_video_url" value="<?php echo esc_attr($homepage['hero_section']['video_url'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_hero_video_url" data-type="video"><?php _e('Select Video', 'eatisfamily'); ?></button>
                                <p class="description"><?php _e('Upload or select an MP4 video file.', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th colspan="2"><hr><h4><?php _e('Hero Title Lines', 'eatisfamily'); ?></h4>
                            <p class="description"><?php _e('The hero title is split into 3 parts for styling purposes. Use \\n for line breaks within each part.', 'eatisfamily'); ?></p></th>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_hero_title_line1" id="homepage_hero_title_line1" rows="3" class="large-text"><?php echo esc_textarea($homepage['hero_section']['title']['line_1'] ?? ''); ?></textarea>
                                <p class="description"><?php _e('First bold headline (e.g., "Feeding\nExperiences.")', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_hero_title_line2" id="homepage_hero_title_line2" rows="3" class="large-text"><?php echo esc_textarea($homepage['hero_section']['title']['line_2'] ?? ''); ?></textarea>
                                <p class="description"><?php _e('Second bold headline (e.g., "Serving\nMoments")', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line3"><?php _e('Title Line 3 (Description)', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_hero_title_line3" id="homepage_hero_title_line3" rows="4" class="large-text"><?php echo esc_textarea($homepage['hero_section']['title']['line_3'] ?? ''); ?></textarea>
                                <p class="description"><?php _e('Descriptive paragraph below the headlines.', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Intro Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📝 Intro Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label><?php _e('Intro Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $homepage['intro_section']['texte'] ?? '', 
                                    'homepage_intro_texte', 
                                    array(
                                        'textarea_name' => 'homepage_intro_texte',
                                        'textarea_rows' => 6,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                        'quicktags' => array('buttons' => 'strong,em,link'),
                                    )
                                );
                                ?>
                                <p class="description"><?php _e('The "We Are Eat Is Family!" intro text. Use <strong> for bold and <span> for highlights.', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Services Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🛠️ Services Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_services_tag"><?php _e('Section Tag', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_services_tag" id="homepage_services_tag" value="<?php echo esc_attr($homepage['services_section']['tag'] ?? 'OUR SERVICES'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_services_title_line1" id="homepage_services_title_line1" value="<?php echo esc_attr($homepage['services_section']['title']['line_1'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_highlight"><?php _e('Highlighted Word', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_services_title_highlight" id="homepage_services_title_highlight" value="<?php echo esc_attr($homepage['services_section']['title']['highlight'] ?? ''); ?>" class="regular-text">
                                <p class="description"><?php _e('This word will have a special underline style.', 'eatisfamily'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_services_title_line2" id="homepage_services_title_line2" value="<?php echo esc_attr($homepage['services_section']['title']['line_2'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                    <p class="description"><?php _e('Individual services are managed in the Services admin page.', 'eatisfamily'); ?></p>
                </div>
                
                <!-- CTA Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📢 CTA Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_cta_title" id="homepage_cta_title" value="<?php echo esc_attr($homepage['cta_section']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $homepage['cta_section']['description'] ?? '', 
                                    'homepage_cta_description', 
                                    array(
                                        'textarea_name' => 'homepage_cta_description',
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Sustainable Service Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🌱 Sustainable Service Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_sustainable_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_sustainable_title" id="homepage_sustainable_title" rows="2" class="large-text"><?php echo esc_textarea($homepage['sustainable_service_title'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                    </table>
                    <p class="description"><?php _e('Sustainability items are managed in the Sustainability admin page.', 'eatisfamily'); ?></p>
                </div>
                
                <!-- Beautiful Moments Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('✨ Beautiful Moments Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_beautiful_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_beautiful_title" id="homepage_beautiful_title" rows="2" class="large-text"><?php echo esc_textarea($homepage['beautiful']['title'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $homepage['beautiful']['text'] ?? '', 
                                    'homepage_beautiful_text', 
                                    array(
                                        'textarea_name' => 'homepage_beautiful_text',
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_beautiful_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_beautiful_image" id="homepage_beautiful_image" value="<?php echo esc_attr($homepage['beautiful']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_beautiful_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Partners Section -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🤝 Partners Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_partners_title"><?php _e('Partners Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_partners_title" id="homepage_partners_title" rows="2" class="large-text"><?php echo esc_textarea($homepage['partners_title'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                    </table>
                    <p class="description"><?php _e('Partners logos are managed in the Partners admin page.', 'eatisfamily'); ?></p>
                </div>
                
                <!-- Homepage CTA Block -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎯 Bottom CTA Block', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_cta_block_title" id="homepage_cta_block_title" value="<?php echo esc_attr($homepage['homepageCTA']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_cta_block_image" id="homepage_cta_block_image" value="<?php echo esc_attr($homepage['homepageCTA']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_cta_block_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $homepage['homepageCTA']['description'] ?? '', 
                                    'homepage_cta_block_description', 
                                    array(
                                        'textarea_name' => 'homepage_cta_block_description',
                                        'textarea_rows' => 4,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Additional Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $homepage['homepageCTA']['additionalText'] ?? '', 
                                    'homepage_cta_block_additional', 
                                    array(
                                        'textarea_name' => 'homepage_cta_block_additional',
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- SEO Section -->
                <div class="eatisfamily-section eatisfamily-seo-section">
                    <h3 class="section-title"><?php _e('🔍 SEO Settings', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_seo_title"><?php _e('Meta Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_seo_title" id="homepage_seo_title" value="<?php echo esc_attr($homepage['seo']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_description"><?php _e('Meta Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_seo_description" id="homepage_seo_description" rows="3" class="large-text"><?php echo esc_textarea($homepage['seo']['description'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_keywords"><?php _e('Keywords', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_seo_keywords" id="homepage_seo_keywords" value="<?php echo esc_attr($homepage['seo']['keywords'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_og_title"><?php _e('OG Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_seo_og_title" id="homepage_seo_og_title" value="<?php echo esc_attr($homepage['seo']['og_title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_og_description"><?php _e('OG Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="homepage_seo_og_description" id="homepage_seo_og_description" rows="2" class="large-text"><?php echo esc_textarea($homepage['seo']['og_description'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_og_image"><?php _e('OG Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_seo_og_image" id="homepage_seo_og_image" value="<?php echo esc_attr($homepage['seo']['og_image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_seo_og_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- ABOUT TAB -->
            <!-- ============================================================ -->
            <div id="about" class="tab-content" style="display: none;">
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎬 Hero Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="about_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_title" id="about_hero_title" value="<?php echo esc_attr($about['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_subtitle" id="about_hero_subtitle" value="<?php echo esc_attr($about['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $about['hero']['description'] ?? '', 
                                    'about_hero_description', 
                                    array(
                                        'textarea_name' => 'about_hero_description',
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_image"><?php _e('Hero Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_image" id="about_hero_image" value="<?php echo esc_attr($about['hero']['image']['src'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="about_hero_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_image_alt"><?php _e('Image Alt Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_image_alt" id="about_hero_image_alt" value="<?php echo esc_attr($about['hero']['image']['alt'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📋 Section Titles', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="about_values_title"><?php _e('Values Section Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_values_title" id="about_values_title" value="<?php echo esc_attr($about['section_titles']['values'] ?? 'Our Values'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_team_title"><?php _e('Team Section Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_team_title" id="about_team_title" value="<?php echo esc_attr($about['section_titles']['team'] ?? 'Meet Our Team'); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section eatisfamily-seo-section">
                    <h3 class="section-title"><?php _e('🔍 SEO Settings', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="about_seo_title"><?php _e('Meta Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_seo_title" id="about_seo_title" value="<?php echo esc_attr($about['seo']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_seo_description"><?php _e('Meta Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="about_seo_description" id="about_seo_description" rows="3" class="large-text"><?php echo esc_textarea($about['seo']['description'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- CONTACT TAB -->
            <!-- ============================================================ -->
            <div id="contact" class="tab-content" style="display: none;">
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎬 Hero Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="contact_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_hero_title" id="contact_hero_title" value="<?php echo esc_attr($contact['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_hero_subtitle" id="contact_hero_subtitle" value="<?php echo esc_attr($contact['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📝 Contact Form', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="contact_form_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_form_title" id="contact_form_title" value="<?php echo esc_attr($contact['form_title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_form_subtitle"><?php _e('Form Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="contact_form_subtitle" id="contact_form_subtitle" value="<?php echo esc_attr($contact['form_subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- CAREERS TAB -->
            <!-- ============================================================ -->
            <div id="careers" class="tab-content" style="display: none;">
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎬 Hero Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_hero_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_title_line1" id="careers_hero_title_line1" value="<?php echo esc_attr($careers['hero_default']['title_line_1'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_title_line2" id="careers_hero_title_line2" value="<?php echo esc_attr($careers['hero_default']['title_line_2'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_image"><?php _e('Hero Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_image" id="careers_hero_image" value="<?php echo esc_attr($careers['hero_default']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="careers_hero_image"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_bg" id="careers_hero_bg" value="<?php echo esc_attr($careers['hero_default']['background_image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="careers_hero_bg"><?php _e('Select Image', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📦 Join Box', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_join_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_join_title" id="careers_join_title" value="<?php echo esc_attr($careers['join_box']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <?php 
                                wp_editor(
                                    $careers['join_box']['description'] ?? '', 
                                    'careers_join_description', 
                                    array(
                                        'textarea_name' => 'careers_join_description',
                                        'textarea_rows' => 4,
                                        'media_buttons' => false,
                                        'teeny' => true,
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('✅ Benefits Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_benefits_title"><?php _e('Benefits Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_benefits_title" id="careers_benefits_title" value="<?php echo esc_attr($careers['benefits_title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e('Benefits List', 'eatisfamily'); ?></label></th>
                            <td>
                                <div class="eatisfamily-repeater">
                                    <div class="repeater-items">
                                        <?php 
                                        $benefits = $careers['benefits'] ?? array('');
                                        foreach ($benefits as $benefit): 
                                        ?>
                                        <div class="repeater-item">
                                            <input type="text" name="careers_benefits[]" value="<?php echo esc_attr($benefit); ?>" class="regular-text" placeholder="<?php esc_attr_e('Enter a benefit...', 'eatisfamily'); ?>">
                                            <button type="button" class="button repeater-remove">✕</button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="button repeater-add">+ <?php _e('Add Benefit', 'eatisfamily'); ?></button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="eatisfamily-section eatisfamily-seo-section">
                    <h3 class="section-title"><?php _e('🔍 SEO Settings', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_seo_title"><?php _e('Meta Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_seo_title" id="careers_seo_title" value="<?php echo esc_attr($careers['seo']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_seo_description"><?php _e('Meta Description', 'eatisfamily'); ?></label></th>
                            <td>
                                <textarea name="careers_seo_description" id="careers_seo_description" rows="3" class="large-text"><?php echo esc_textarea($careers['seo']['description'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- EVENTS TAB -->
            <!-- ============================================================ -->
            <div id="events" class="tab-content" style="display: none;">
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🎬 Hero Section', 'eatisfamily'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="events_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="events_hero_title" id="events_hero_title" value="<?php echo esc_attr($events['hero']['title'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="events_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="events_hero_subtitle" id="events_hero_subtitle" value="<?php echo esc_attr($events['hero']['subtitle'] ?? ''); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- FORMS LABELS TAB -->
            <!-- ============================================================ -->
            <div id="forms" class="tab-content" style="display: none;">
                
                <!-- Job Search Form -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('🔍 Job Search Form (Homepage Hero)', 'eatisfamily'); ?></h3>
                    <p class="description"><?php _e('Labels and placeholders for the job search form on the homepage.', 'eatisfamily'); ?></p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="form_job_search_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_title" id="form_job_search_title" value="<?php echo esc_attr($forms['job_search']['title'] ?? 'Find Your Perfect Role'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_subtitle"><?php _e('Form Subtitle', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_subtitle" id="form_job_search_subtitle" value="<?php echo esc_attr($forms['job_search']['subtitle'] ?? 'Explore open positions across France'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_job_placeholder"><?php _e('Job Title Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_job_placeholder" id="form_job_search_job_placeholder" value="<?php echo esc_attr($forms['job_search']['job_title_placeholder'] ?? 'Select job title'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_site_placeholder"><?php _e('Site Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_site_placeholder" id="form_job_search_site_placeholder" value="<?php echo esc_attr($forms['job_search']['site_placeholder'] ?? 'Select sites'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_all_jobs"><?php _e('"All Jobs" Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_all_jobs" id="form_job_search_all_jobs" value="<?php echo esc_attr($forms['job_search']['all_jobs_label'] ?? 'All job titles'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_all_sites"><?php _e('"All Sites" Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_all_sites" id="form_job_search_all_sites" value="<?php echo esc_attr($forms['job_search']['all_sites_label'] ?? 'All sites'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_search_loading"><?php _e('Loading Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_search_loading" id="form_job_search_loading" value="<?php echo esc_attr($forms['job_search']['loading_text'] ?? 'Loading...'); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Contact Form -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('📧 Contact Form', 'eatisfamily'); ?></h3>
                    <p class="description"><?php _e('Labels, placeholders and messages for the contact form.', 'eatisfamily'); ?></p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="form_contact_name_label"><?php _e('Name Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_name_label" id="form_contact_name_label" value="<?php echo esc_attr($forms['contact_form']['name_label'] ?? 'Your Name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_name_placeholder"><?php _e('Name Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_name_placeholder" id="form_contact_name_placeholder" value="<?php echo esc_attr($forms['contact_form']['name_placeholder'] ?? 'Enter your name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_email_label"><?php _e('Email Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_email_label" id="form_contact_email_label" value="<?php echo esc_attr($forms['contact_form']['email_label'] ?? 'Email Address'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_email_placeholder"><?php _e('Email Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_email_placeholder" id="form_contact_email_placeholder" value="<?php echo esc_attr($forms['contact_form']['email_placeholder'] ?? 'Enter your email'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_subject_label"><?php _e('Subject Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_subject_label" id="form_contact_subject_label" value="<?php echo esc_attr($forms['contact_form']['subject_label'] ?? 'Subject'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_subject_placeholder"><?php _e('Subject Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_subject_placeholder" id="form_contact_subject_placeholder" value="<?php echo esc_attr($forms['contact_form']['subject_placeholder'] ?? 'What is this about?'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_message_label"><?php _e('Message Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_message_label" id="form_contact_message_label" value="<?php echo esc_attr($forms['contact_form']['message_label'] ?? 'Message'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_message_placeholder"><?php _e('Message Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_message_placeholder" id="form_contact_message_placeholder" value="<?php echo esc_attr($forms['contact_form']['message_placeholder'] ?? 'Write your message...'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_submit_button"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_submit_button" id="form_contact_submit_button" value="<?php echo esc_attr($forms['contact_form']['submit_button'] ?? 'Send Message'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_submitting_button"><?php _e('Submitting Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_submitting_button" id="form_contact_submitting_button" value="<?php echo esc_attr($forms['contact_form']['submitting_button'] ?? 'Sending...'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_success_message"><?php _e('Success Message', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_success_message" id="form_contact_success_message" value="<?php echo esc_attr($forms['contact_form']['success_message'] ?? 'Thank you! Your message has been sent.'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_contact_error_message"><?php _e('Error Message', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_contact_error_message" id="form_contact_error_message" value="<?php echo esc_attr($forms['contact_form']['error_message'] ?? 'Sorry, there was an error. Please try again.'); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Job Application Form -->
                <div class="eatisfamily-section">
                    <h3 class="section-title"><?php _e('💼 Job Application Form', 'eatisfamily'); ?></h3>
                    <p class="description"><?php _e('Labels, placeholders and messages for the job application form.', 'eatisfamily'); ?></p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="form_job_app_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_title" id="form_job_app_title" value="<?php echo esc_attr($forms['job_application']['title'] ?? 'Apply for this Position'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_firstname_label"><?php _e('First Name Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_firstname_label" id="form_job_app_firstname_label" value="<?php echo esc_attr($forms['job_application']['firstname_label'] ?? 'First Name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_firstname_placeholder"><?php _e('First Name Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_firstname_placeholder" id="form_job_app_firstname_placeholder" value="<?php echo esc_attr($forms['job_application']['firstname_placeholder'] ?? 'Enter your first name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_lastname_label"><?php _e('Last Name Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_lastname_label" id="form_job_app_lastname_label" value="<?php echo esc_attr($forms['job_application']['lastname_label'] ?? 'Last Name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_lastname_placeholder"><?php _e('Last Name Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_lastname_placeholder" id="form_job_app_lastname_placeholder" value="<?php echo esc_attr($forms['job_application']['lastname_placeholder'] ?? 'Enter your last name'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_email_label"><?php _e('Email Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_email_label" id="form_job_app_email_label" value="<?php echo esc_attr($forms['job_application']['email_label'] ?? 'Email'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_email_placeholder"><?php _e('Email Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_email_placeholder" id="form_job_app_email_placeholder" value="<?php echo esc_attr($forms['job_application']['email_placeholder'] ?? 'Enter your email'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_phone_label"><?php _e('Phone Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_phone_label" id="form_job_app_phone_label" value="<?php echo esc_attr($forms['job_application']['phone_label'] ?? 'Phone Number'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_phone_placeholder"><?php _e('Phone Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_phone_placeholder" id="form_job_app_phone_placeholder" value="<?php echo esc_attr($forms['job_application']['phone_placeholder'] ?? 'Enter your phone number'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_resume_label"><?php _e('Resume Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_resume_label" id="form_job_app_resume_label" value="<?php echo esc_attr($forms['job_application']['resume_label'] ?? 'Upload Resume/CV'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_resume_placeholder"><?php _e('Resume Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_resume_placeholder" id="form_job_app_resume_placeholder" value="<?php echo esc_attr($forms['job_application']['resume_placeholder'] ?? 'Choose file (PDF, DOC, DOCX)'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_coverletter_label"><?php _e('Cover Letter Label', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_coverletter_label" id="form_job_app_coverletter_label" value="<?php echo esc_attr($forms['job_application']['coverletter_label'] ?? 'Cover Letter (Optional)'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_coverletter_placeholder"><?php _e('Cover Letter Placeholder', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_coverletter_placeholder" id="form_job_app_coverletter_placeholder" value="<?php echo esc_attr($forms['job_application']['coverletter_placeholder'] ?? 'Tell us why you would be great for this role...'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_submit_button"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_submit_button" id="form_job_app_submit_button" value="<?php echo esc_attr($forms['job_application']['submit_button'] ?? 'Submit Application'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_submitting_button"><?php _e('Submitting Text', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_submitting_button" id="form_job_app_submitting_button" value="<?php echo esc_attr($forms['job_application']['submitting_button'] ?? 'Submitting...'); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_success_message"><?php _e('Success Message', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_success_message" id="form_job_app_success_message" value="<?php echo esc_attr($forms['job_application']['success_message'] ?? 'Application submitted successfully!'); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="form_job_app_error_message"><?php _e('Error Message', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="form_job_app_error_message" id="form_job_app_error_message" value="<?php echo esc_attr($forms['job_application']['error_message'] ?? 'Error submitting application. Please try again.'); ?>" class="large-text">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <p class="submit">
                <button type="button" id="eatisfamily-ajax-submit" class="button button-primary button-hero">
                    <?php _e('💾 Save Pages Content', 'eatisfamily'); ?>
                </button>
                <span id="eatisfamily-saving-indicator" style="display:none; margin-left: 15px;">
                    <span class="spinner is-active" style="float: none; margin-top: 0;"></span>
                    <?php _e('Saving...', 'eatisfamily'); ?>
                </span>
            </p>
        </form>
    </div>
    
    <style>
        .eatisfamily-pages-content .nav-tab-wrapper { margin-bottom: 0; }
        .eatisfamily-pages-content .tab-content { 
            padding: 20px; 
            background: #fff; 
            border: 1px solid #ccd0d4; 
            border-top: none;
            max-width: 1200px;
        }
        .eatisfamily-section {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .eatisfamily-section .section-title {
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #2271b1;
            font-size: 16px;
        }
        .eatisfamily-seo-section {
            background: #f0f6fc;
            border-color: #c3c4c7;
        }
        .repeater-item { 
            display: flex; 
            gap: 5px; 
            margin-bottom: 8px; 
            align-items: center;
        }
        .repeater-item input { flex: 1; }
        .repeater-remove { color: #d63638 !important; }
        .image-preview img {
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .video-field { transition: all 0.3s ease; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').hide();
            $(target).show();
        });
        
        // Video type toggle
        function toggleVideoFields() {
            var videoType = $('#homepage_hero_video_type').val();
            $('.video-field').hide();
            $('.video-field-' + videoType).show();
        }
        
        $('#homepage_hero_video_type').on('change', toggleVideoFields);
        toggleVideoFields(); // Initial state
        
        // Media uploader
        $(document).on('click', '.eatisfamily-upload-media', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            var mediaType = button.data('type') || 'image';
            
            var frame = wp.media({
                title: mediaType === 'video' ? 'Select Video' : 'Select Image',
                button: { text: 'Use this ' + mediaType },
                multiple: false,
                library: {
                    type: mediaType === 'video' ? 'video' : 'image'
                }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + targetId).val(attachment.url);
            });
            
            frame.open();
        });
        
        // Repeater functionality
        $(document).on('click', '.repeater-add', function() {
            var $container = $(this).closest('.eatisfamily-repeater');
            var $items = $container.find('.repeater-items');
            var $firstItem = $items.find('.repeater-item').first();
            var name = $firstItem.find('input').attr('name');
            var placeholder = $firstItem.find('input').attr('placeholder');
            
            var $newItem = $('<div class="repeater-item">' +
                '<input type="text" name="' + name + '" value="" class="regular-text" placeholder="' + placeholder + '">' +
                '<button type="button" class="button repeater-remove">✕</button>' +
                '</div>');
            
            $items.append($newItem);
        });
        
        $(document).on('click', '.repeater-remove', function() {
            var $items = $(this).closest('.repeater-items').find('.repeater-item');
            if ($items.length > 1) {
                $(this).closest('.repeater-item').remove();
            } else {
                $(this).siblings('input').val('');
            }
        });
        
        // ================================================================
        // AJAX Form Submission (bypasses mod_security 403 errors)
        // ================================================================
        $('#eatisfamily-ajax-submit').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $indicator = $('#eatisfamily-saving-indicator');
            var $message = $('#eatisfamily-ajax-message');
            var $form = $('#eatisfamily-pages-form');
            
            // Disable button and show loading
            $button.prop('disabled', true);
            $indicator.show();
            $message.hide();
            
            // Collect all form data
            var formData = {};
            
            // Regular inputs, selects, textareas
            $form.find('input[type="text"], input[type="url"], input[type="email"], input[type="hidden"], select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name !== 'eatisfamily_pages_content_nonce' && name !== '_wp_http_referer') {
                    // Handle arrays (like careers_benefits[])
                    if (name.endsWith('[]')) {
                        var baseName = name.slice(0, -2);
                        if (!formData[baseName]) {
                            formData[baseName] = [];
                        }
                        if ($(this).val()) {
                            formData[baseName].push($(this).val());
                        }
                    } else {
                        formData[name] = $(this).val();
                    }
                }
            });
            
            // Encode data to base64 to bypass mod_security
            var encodedData = btoa(unescape(encodeURIComponent(JSON.stringify(formData))));
            
            // Send via AJAX
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'eatisfamily_save_pages_content',
                    nonce: $('#eatisfamily_ajax_nonce').val(),
                    encoded_data: encodedData
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $indicator.hide();
                    
                    if (response.success) {
                        $message.removeClass('notice-error').addClass('notice-success');
                        $message.find('p').text(response.data.message);
                    } else {
                        $message.removeClass('notice-success').addClass('notice-error');
                        $message.find('p').text(response.data.message || 'An error occurred');
                    }
                    $message.show();
                    
                    // Scroll to top to show message
                    $('html, body').animate({ scrollTop: 0 }, 300);
                    
                    // Auto-hide success message after 5 seconds
                    if (response.success) {
                        setTimeout(function() {
                            $message.fadeOut();
                        }, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    $button.prop('disabled', false);
                    $indicator.hide();
                    
                    $message.removeClass('notice-success').addClass('notice-error');
                    $message.find('p').text('Server error: ' + error + '. Please try again.');
                    $message.show();
                    
                    $('html, body').animate({ scrollTop: 0 }, 300);
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * Enqueue media uploader on admin pages
 */
function eatisfamily_admin_pages_scripts($hook) {
    if ($hook === 'toplevel_page_eatisfamily-site-content' || 
        $hook === 'site-content_page_eatisfamily-pages-content' ||
        $hook === 'site-content_page_eatisfamily-data-management') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'eatisfamily_admin_pages_scripts');

/**
 * ============================================================================
 * DATA MANAGEMENT PAGE
 * ============================================================================
 */

/**
 * Data Management admin page - Manual JSON import
 */
function eatisfamily_data_management_page() {
    // Handle manual import
    if (isset($_POST['eatisfamily_import_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_import_nonce'], 'manual_import')) {
        
        $json_dir = get_template_directory() . '/data/';
        $results = array();
        
        if (isset($_POST['import_activities'])) {
            require_once get_template_directory() . '/functions.php';
            $count = eatisfamily_import_activities($json_dir . 'activities.json');
            $results[] = sprintf(__('Activities: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_events'])) {
            $count = eatisfamily_import_events($json_dir . 'events.json');
            $results[] = sprintf(__('Events: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_jobs'])) {
            $count = eatisfamily_import_jobs($json_dir . 'jobs.json');
            $results[] = sprintf(__('Jobs: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_venues'])) {
            $count = eatisfamily_import_venues($json_dir . 'venues.json');
            $results[] = sprintf(__('Venues: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_blog'])) {
            $count = eatisfamily_import_blog_posts($json_dir . 'blog-posts.json');
            $results[] = sprintf(__('Blog Posts: %d imported', 'eatisfamily'), $count ?: 0);
        }
        
        if (isset($_POST['import_site_content'])) {
            eatisfamily_import_site_content($json_dir . 'site-content.json');
            $results[] = __('Site Content: Updated', 'eatisfamily');
        }
        
        if (isset($_POST['import_pages_content'])) {
            eatisfamily_import_pages_content($json_dir . 'pages-content.json');
            $results[] = __('Pages Content: Updated', 'eatisfamily');
        }
        
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . __('Import completed!', 'eatisfamily') . '</strong><br>' . implode('<br>', $results) . '</p></div>';
    }
    
    // Get stats
    $activities_count = wp_count_posts('activity')->publish;
    $events_count = wp_count_posts('event')->publish;
    $jobs_count = wp_count_posts('job')->publish;
    $venues_count = wp_count_posts('venue')->publish;
    $blog_count = wp_count_posts('post')->publish;
    
    $json_dir = get_template_directory() . '/data/';
    ?>
    <div class="wrap">
        <h1><?php _e('Data Management', 'eatisfamily'); ?></h1>
        
        <div class="card" style="max-width: 800px;">
            <h2><?php _e('Current Content', 'eatisfamily'); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Content Type', 'eatisfamily'); ?></th>
                        <th><?php _e('Count', 'eatisfamily'); ?></th>
                        <th><?php _e('Manage', 'eatisfamily'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php _e('Activities', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $activities_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=activity'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Events', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $events_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=event'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Jobs', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $jobs_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=job'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Venues', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $venues_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=venue'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Blog Posts', 'eatisfamily'); ?></strong></td>
                        <td><?php echo $blog_count; ?></td>
                        <td><a href="<?php echo admin_url('edit.php'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Timeline Events', 'eatisfamily'); ?></strong></td>
                        <td><?php echo wp_count_posts('timeline_event')->publish; ?></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=timeline_event'); ?>" class="button"><?php _e('View All', 'eatisfamily'); ?></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2><?php _e('Manual Import from JSON Files', 'eatisfamily'); ?></h2>
            <p class="description">
                <?php _e('Import data from JSON files located in the /data/ folder. This will NOT overwrite existing content - only new items will be added.', 'eatisfamily'); ?>
            </p>
            
            <form method="post" action="">
                <?php wp_nonce_field('manual_import', 'eatisfamily_import_nonce'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php _e('Select Content to Import', 'eatisfamily'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="import_activities" value="1">
                                        <?php _e('Activities', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'activities.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_events" value="1">
                                        <?php _e('Events', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'events.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_jobs" value="1">
                                        <?php _e('Jobs', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'jobs.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_venues" value="1">
                                        <?php _e('Venues', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'venues.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_blog" value="1">
                                        <?php _e('Blog Posts', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'blog-posts.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_site_content" value="1">
                                        <?php _e('Site Content', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'site-content.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="import_pages_content" value="1">
                                        <?php _e('Pages Content', 'eatisfamily'); ?>
                                        <?php if (file_exists($json_dir . 'pages-content.json')): ?>
                                            <span style="color: green;">✓</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ <?php _e('File not found', 'eatisfamily'); ?></span>
                                        <?php endif; ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(__('Import Selected Data', 'eatisfamily'), 'primary', 'submit', true, array('onclick' => 'return confirm("' . esc_js(__('This will import data from JSON files. Existing content will not be overwritten. Continue?', 'eatisfamily')) . '");')); ?>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <h2><?php _e('⚠️ Important Notes', 'eatisfamily'); ?></h2>
            <ul>
                <li><?php _e('All content should be managed through WordPress admin panels (Activities, Jobs, Venues, Events, etc.).', 'eatisfamily'); ?></li>
                <li><?php _e('JSON import is only for initial data migration or restoring from backup.', 'eatisfamily'); ?></li>
                <li><?php _e('Changes made in WordPress will NOT update JSON files automatically.', 'eatisfamily'); ?></li>
                <li><?php _e('The Nuxt.js frontend will fetch data from WordPress REST API, not from JSON files.', 'eatisfamily'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

