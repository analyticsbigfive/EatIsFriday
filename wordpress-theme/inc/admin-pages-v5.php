<?php
/**
 * EIF Backend - Admin Pages V5
 * Complete unified content management with ALL administrable fields
 *
 * @package EIFBackend
 * @version 5.0.0
 * 
 * CHANGELOG v5.0.0:
 * - Unified admin interface combining admin-pages.php and admin-pages-extended.php
 * - Added "Forms" tab with Job Search Form, Contact Form, Job Application Form
 * - Added "Components" tab for Navbar and Footer management
 * - Added complete Blog, Job Detail, Apply Activities, Apply Jobs, Events pages
 * - All 263+ administrable fields now accessible from the admin interface
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * ADMIN MENUS REGISTRATION - V5
 * ============================================================================
 */
function eatisfamily_register_admin_menus_v5() {
    // Remove old menu registrations to avoid duplicates
    remove_action('admin_menu', 'eatisfamily_register_admin_menus', 5);
    
    // Main menu - Site Content
    add_menu_page(
        __('EatIsFamily', 'eatisfamily'),
        __('EatIsFamily', 'eatisfamily'),
        'manage_options',
        'eatisfamily-dashboard',
        'eatisfamily_dashboard_page_v5',
        'dashicons-food',
        30
    );
    
    // Submenu - Site Content (Global Settings)
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Site Content', 'eatisfamily'),
        __('Site Content', 'eatisfamily'),
        'manage_options',
        'eatisfamily-site-content',
        'eatisfamily_site_content_page'
    );
    
    // Submenu - Pages Content (Unified)
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Pages Content', 'eatisfamily'),
        __('Pages Content', 'eatisfamily'),
        'manage_options',
        'eatisfamily-pages-content-v5',
        'eatisfamily_pages_content_page_v5'
    );
    
    // Submenu - Forms & Labels
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Forms & Labels', 'eatisfamily'),
        __('Forms & Labels', 'eatisfamily'),
        'manage_options',
        'eatisfamily-forms',
        'eatisfamily_forms_page_v5'
    );
    
    // Submenu - Components (Navbar, Footer)
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Components', 'eatisfamily'),
        __('Components', 'eatisfamily'),
        'manage_options',
        'eatisfamily-components',
        'eatisfamily_components_page_v5'
    );
    
    // Submenu - Partners
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Partners', 'eatisfamily'),
        __('Partners', 'eatisfamily'),
        'manage_options',
        'eatisfamily-partners',
        'eatisfamily_partners_page'
    );
    
    // Submenu - Services
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Services', 'eatisfamily'),
        __('Services', 'eatisfamily'),
        'manage_options',
        'eatisfamily-services',
        'eatisfamily_services_page'
    );
    
    // Submenu - Sustainability
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Sustainability', 'eatisfamily'),
        __('Sustainability', 'eatisfamily'),
        'manage_options',
        'eatisfamily-sustainability',
        'eatisfamily_sustainability_page'
    );
    
    // Submenu - Gallery
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Gallery', 'eatisfamily'),
        __('Gallery', 'eatisfamily'),
        'manage_options',
        'eatisfamily-gallery',
        'eatisfamily_gallery_page'
    );
    
    // Submenu - Data Management
    add_submenu_page(
        'eatisfamily-dashboard',
        __('Data Management', 'eatisfamily'),
        __('Data Management', 'eatisfamily'),
        'manage_options',
        'eatisfamily-data-management',
        'eatisfamily_data_management_page'
    );
}
add_action('admin_menu', 'eatisfamily_register_admin_menus_v5', 10);

/**
 * ============================================================================
 * DASHBOARD PAGE - V5
 * ============================================================================
 */
function eatisfamily_dashboard_page_v5() {
    ?>
    <div class="wrap">
        <h1><?php _e('EatIsFamily - Administration v5.0', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Bienvenue dans l\'interface d\'administration unifiée du thème EatIsFamily.', 'eatisfamily'); ?></p>
        
        <div class="eatisfamily-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
            
            <div class="eatisfamily-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3>📄 Pages Content</h3>
                <p><?php _e('Gérez le contenu de toutes les pages : Homepage, About, Contact, Careers, Events, Blog, etc.', 'eatisfamily'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=eatisfamily-pages-content-v5'); ?>" class="button button-primary"><?php _e('Gérer', 'eatisfamily'); ?></a>
            </div>
            
            <div class="eatisfamily-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3>📝 Forms & Labels</h3>
                <p><?php _e('Configurez les formulaires : Job Search, Contact, Job Application avec tous leurs labels et placeholders.', 'eatisfamily'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=eatisfamily-forms'); ?>" class="button button-primary"><?php _e('Gérer', 'eatisfamily'); ?></a>
            </div>
            
            <div class="eatisfamily-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3>🧩 Components</h3>
                <p><?php _e('Gérez la Navbar et le Footer : liens de navigation, réseaux sociaux, copyright.', 'eatisfamily'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=eatisfamily-components'); ?>" class="button button-primary"><?php _e('Gérer', 'eatisfamily'); ?></a>
            </div>
            
            <div class="eatisfamily-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3>🤝 Partners</h3>
                <p><?php _e('Gérez les logos des partenaires affichés sur la page d\'accueil.', 'eatisfamily'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=eatisfamily-partners'); ?>" class="button button-primary"><?php _e('Gérer', 'eatisfamily'); ?></a>
            </div>
            
        </div>
        
        <div style="margin-top: 40px; padding: 20px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
            <h3 style="margin-top: 0;">📋 Version 5.0 - Nouveautés</h3>
            <ul>
                <li>✅ Interface d'administration unifiée</li>
                <li>✅ Section "Forms & Labels" avec Job Search Form, Contact Form, Job Application</li>
                <li>✅ Section "Components" pour Navbar et Footer</li>
                <li>✅ Tous les ~263 champs administrables accessibles</li>
                <li>✅ Support complet du fichier pages-content.json</li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * ============================================================================
 * FORMS & LABELS PAGE - V5 (NEW!)
 * ============================================================================
 */
function eatisfamily_forms_page_v5() {
    // Handle form submission
    if (isset($_POST['eatisfamily_forms_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_forms_nonce'], 'save_forms')) {
        
        $forms_content = array(
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
            'activity_registration' => array(
                'title' => sanitize_text_field($_POST['form_activity_title'] ?? 'Register for Activity'),
                'name_label' => sanitize_text_field($_POST['form_activity_name_label'] ?? 'Full Name'),
                'name_placeholder' => sanitize_text_field($_POST['form_activity_name_placeholder'] ?? 'Enter your full name'),
                'email_label' => sanitize_text_field($_POST['form_activity_email_label'] ?? 'Email'),
                'email_placeholder' => sanitize_text_field($_POST['form_activity_email_placeholder'] ?? 'Enter your email'),
                'phone_label' => sanitize_text_field($_POST['form_activity_phone_label'] ?? 'Phone'),
                'phone_placeholder' => sanitize_text_field($_POST['form_activity_phone_placeholder'] ?? 'Enter your phone number'),
                'participants_label' => sanitize_text_field($_POST['form_activity_participants_label'] ?? 'Number of Participants'),
                'dietary_label' => sanitize_text_field($_POST['form_activity_dietary_label'] ?? 'Dietary Restrictions'),
                'dietary_placeholder' => sanitize_text_field($_POST['form_activity_dietary_placeholder'] ?? 'Any allergies or dietary requirements?'),
                'additional_label' => sanitize_text_field($_POST['form_activity_additional_label'] ?? 'Additional Information'),
                'additional_placeholder' => sanitize_text_field($_POST['form_activity_additional_placeholder'] ?? 'Anything else we should know?'),
                'submit_button' => sanitize_text_field($_POST['form_activity_submit_button'] ?? 'Register'),
                'success_message' => sanitize_text_field($_POST['form_activity_success_message'] ?? 'Registration successful!'),
            ),
        );
        
        update_option('eatisfamily_forms', $forms_content);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Forms & Labels saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $forms = get_option('eatisfamily_forms', array());
    $job_search = $forms['job_search'] ?? array();
    $contact_form = $forms['contact_form'] ?? array();
    $job_application = $forms['job_application'] ?? array();
    $activity_registration = $forms['activity_registration'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Forms & Labels', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Configurez tous les textes, labels et placeholders des formulaires du site.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_forms', 'eatisfamily_forms_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#job-search" class="nav-tab nav-tab-active"><?php _e('🔍 Job Search', 'eatisfamily'); ?></a>
                <a href="#contact-form" class="nav-tab"><?php _e('📧 Contact', 'eatisfamily'); ?></a>
                <a href="#job-application" class="nav-tab"><?php _e('💼 Job Application', 'eatisfamily'); ?></a>
                <a href="#activity-registration" class="nav-tab"><?php _e('🎯 Activity Registration', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- JOB SEARCH FORM -->
            <div id="job-search" class="tab-content" style="display: block;">
                <h3><?php _e('🔍 Job Search Form (Homepage Hero)', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Labels et placeholders pour le formulaire de recherche d\'emploi sur la page d\'accueil.', 'eatisfamily'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="form_job_search_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_title" id="form_job_search_title" value="<?php echo esc_attr($job_search['title'] ?? 'Find Your Perfect Role'); ?>" class="large-text">
                            <p class="description"><?php _e('Ex: "Find Your Perfect Role" ou "Trouvez Votre Poste Idéal"', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_subtitle"><?php _e('Form Subtitle', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_subtitle" id="form_job_search_subtitle" value="<?php echo esc_attr($job_search['subtitle'] ?? 'Explore open positions across France'); ?>" class="large-text">
                            <p class="description"><?php _e('Ex: "Explore open positions across France" ou "Explorez les postes ouverts en France"', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_job_placeholder"><?php _e('Job Title Placeholder', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_job_placeholder" id="form_job_search_job_placeholder" value="<?php echo esc_attr($job_search['job_title_placeholder'] ?? 'Select job title'); ?>" class="regular-text">
                            <p class="description"><?php _e('Ex: "Select job title" ou "Sélectionner un poste"', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_site_placeholder"><?php _e('Site Placeholder', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_site_placeholder" id="form_job_search_site_placeholder" value="<?php echo esc_attr($job_search['site_placeholder'] ?? 'Select sites'); ?>" class="regular-text">
                            <p class="description"><?php _e('Ex: "Select sites" ou "Sélectionner un site"', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_all_jobs"><?php _e('"All Jobs" Label', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_all_jobs" id="form_job_search_all_jobs" value="<?php echo esc_attr($job_search['all_jobs_label'] ?? 'All job titles'); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_all_sites"><?php _e('"All Sites" Label', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_all_sites" id="form_job_search_all_sites" value="<?php echo esc_attr($job_search['all_sites_label'] ?? 'All sites'); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_button"><?php _e('Search Button', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_button" id="form_job_search_button" value="<?php echo esc_attr($job_search['search_button'] ?? 'Search'); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_search_loading"><?php _e('Loading Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="form_job_search_loading" id="form_job_search_loading" value="<?php echo esc_attr($job_search['loading_text'] ?? 'Loading...'); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- CONTACT FORM -->
            <div id="contact-form" class="tab-content" style="display: none;">
                <h3><?php _e('📧 Contact Form', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Labels, placeholders et messages pour le formulaire de contact.', 'eatisfamily'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="form_contact_name_label"><?php _e('Name Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_name_label" id="form_contact_name_label" value="<?php echo esc_attr($contact_form['name_label'] ?? 'Your Name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_name_placeholder"><?php _e('Name Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_name_placeholder" id="form_contact_name_placeholder" value="<?php echo esc_attr($contact_form['name_placeholder'] ?? 'Enter your name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_email_label"><?php _e('Email Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_email_label" id="form_contact_email_label" value="<?php echo esc_attr($contact_form['email_label'] ?? 'Email Address'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_email_placeholder"><?php _e('Email Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_email_placeholder" id="form_contact_email_placeholder" value="<?php echo esc_attr($contact_form['email_placeholder'] ?? 'Enter your email'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_subject_label"><?php _e('Subject Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_subject_label" id="form_contact_subject_label" value="<?php echo esc_attr($contact_form['subject_label'] ?? 'Subject'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_subject_placeholder"><?php _e('Subject Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_subject_placeholder" id="form_contact_subject_placeholder" value="<?php echo esc_attr($contact_form['subject_placeholder'] ?? 'What is this about?'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_message_label"><?php _e('Message Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_message_label" id="form_contact_message_label" value="<?php echo esc_attr($contact_form['message_label'] ?? 'Message'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_message_placeholder"><?php _e('Message Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_message_placeholder" id="form_contact_message_placeholder" value="<?php echo esc_attr($contact_form['message_placeholder'] ?? 'Write your message...'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_submit_button"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_submit_button" id="form_contact_submit_button" value="<?php echo esc_attr($contact_form['submit_button'] ?? 'Send Message'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_submitting_button"><?php _e('Submitting Text', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_submitting_button" id="form_contact_submitting_button" value="<?php echo esc_attr($contact_form['submitting_button'] ?? 'Sending...'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_success_message"><?php _e('Success Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_success_message" id="form_contact_success_message" value="<?php echo esc_attr($contact_form['success_message'] ?? 'Thank you! Your message has been sent.'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_contact_error_message"><?php _e('Error Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_contact_error_message" id="form_contact_error_message" value="<?php echo esc_attr($contact_form['error_message'] ?? 'Sorry, there was an error. Please try again.'); ?>" class="large-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- JOB APPLICATION FORM -->
            <div id="job-application" class="tab-content" style="display: none;">
                <h3><?php _e('💼 Job Application Form', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Labels, placeholders et messages pour le formulaire de candidature.', 'eatisfamily'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="form_job_app_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_title" id="form_job_app_title" value="<?php echo esc_attr($job_application['title'] ?? 'Apply for this Position'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_firstname_label"><?php _e('First Name Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_firstname_label" id="form_job_app_firstname_label" value="<?php echo esc_attr($job_application['firstname_label'] ?? 'First Name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_firstname_placeholder"><?php _e('First Name Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_firstname_placeholder" id="form_job_app_firstname_placeholder" value="<?php echo esc_attr($job_application['firstname_placeholder'] ?? 'Enter your first name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_lastname_label"><?php _e('Last Name Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_lastname_label" id="form_job_app_lastname_label" value="<?php echo esc_attr($job_application['lastname_label'] ?? 'Last Name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_lastname_placeholder"><?php _e('Last Name Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_lastname_placeholder" id="form_job_app_lastname_placeholder" value="<?php echo esc_attr($job_application['lastname_placeholder'] ?? 'Enter your last name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_email_label"><?php _e('Email Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_email_label" id="form_job_app_email_label" value="<?php echo esc_attr($job_application['email_label'] ?? 'Email'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_email_placeholder"><?php _e('Email Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_email_placeholder" id="form_job_app_email_placeholder" value="<?php echo esc_attr($job_application['email_placeholder'] ?? 'Enter your email'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_phone_label"><?php _e('Phone Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_phone_label" id="form_job_app_phone_label" value="<?php echo esc_attr($job_application['phone_label'] ?? 'Phone Number'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_phone_placeholder"><?php _e('Phone Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_phone_placeholder" id="form_job_app_phone_placeholder" value="<?php echo esc_attr($job_application['phone_placeholder'] ?? 'Enter your phone number'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_resume_label"><?php _e('Resume Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_resume_label" id="form_job_app_resume_label" value="<?php echo esc_attr($job_application['resume_label'] ?? 'Upload Resume/CV'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_resume_placeholder"><?php _e('Resume Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_resume_placeholder" id="form_job_app_resume_placeholder" value="<?php echo esc_attr($job_application['resume_placeholder'] ?? 'Choose file (PDF, DOC, DOCX)'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_coverletter_label"><?php _e('Cover Letter Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_coverletter_label" id="form_job_app_coverletter_label" value="<?php echo esc_attr($job_application['coverletter_label'] ?? 'Cover Letter (Optional)'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_coverletter_placeholder"><?php _e('Cover Letter Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_coverletter_placeholder" id="form_job_app_coverletter_placeholder" value="<?php echo esc_attr($job_application['coverletter_placeholder'] ?? 'Tell us why you would be great for this role...'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_submit_button"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_submit_button" id="form_job_app_submit_button" value="<?php echo esc_attr($job_application['submit_button'] ?? 'Submit Application'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_submitting_button"><?php _e('Submitting Text', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_submitting_button" id="form_job_app_submitting_button" value="<?php echo esc_attr($job_application['submitting_button'] ?? 'Submitting...'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_success_message"><?php _e('Success Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_success_message" id="form_job_app_success_message" value="<?php echo esc_attr($job_application['success_message'] ?? 'Application submitted successfully!'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_job_app_error_message"><?php _e('Error Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_job_app_error_message" id="form_job_app_error_message" value="<?php echo esc_attr($job_application['error_message'] ?? 'Error submitting application. Please try again.'); ?>" class="large-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- ACTIVITY REGISTRATION FORM -->
            <div id="activity-registration" class="tab-content" style="display: none;">
                <h3><?php _e('🎯 Activity Registration Form', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Labels et messages pour le formulaire d\'inscription aux activités.', 'eatisfamily'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="form_activity_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_title" id="form_activity_title" value="<?php echo esc_attr($activity_registration['title'] ?? 'Register for Activity'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_name_label"><?php _e('Name Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_name_label" id="form_activity_name_label" value="<?php echo esc_attr($activity_registration['name_label'] ?? 'Full Name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_name_placeholder"><?php _e('Name Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_name_placeholder" id="form_activity_name_placeholder" value="<?php echo esc_attr($activity_registration['name_placeholder'] ?? 'Enter your full name'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_email_label"><?php _e('Email Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_email_label" id="form_activity_email_label" value="<?php echo esc_attr($activity_registration['email_label'] ?? 'Email'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_email_placeholder"><?php _e('Email Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_email_placeholder" id="form_activity_email_placeholder" value="<?php echo esc_attr($activity_registration['email_placeholder'] ?? 'Enter your email'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_phone_label"><?php _e('Phone Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_phone_label" id="form_activity_phone_label" value="<?php echo esc_attr($activity_registration['phone_label'] ?? 'Phone'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_phone_placeholder"><?php _e('Phone Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_phone_placeholder" id="form_activity_phone_placeholder" value="<?php echo esc_attr($activity_registration['phone_placeholder'] ?? 'Enter your phone number'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_participants_label"><?php _e('Participants Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_participants_label" id="form_activity_participants_label" value="<?php echo esc_attr($activity_registration['participants_label'] ?? 'Number of Participants'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_dietary_label"><?php _e('Dietary Restrictions Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_dietary_label" id="form_activity_dietary_label" value="<?php echo esc_attr($activity_registration['dietary_label'] ?? 'Dietary Restrictions'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_dietary_placeholder"><?php _e('Dietary Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_dietary_placeholder" id="form_activity_dietary_placeholder" value="<?php echo esc_attr($activity_registration['dietary_placeholder'] ?? 'Any allergies or dietary requirements?'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_additional_label"><?php _e('Additional Info Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_additional_label" id="form_activity_additional_label" value="<?php echo esc_attr($activity_registration['additional_label'] ?? 'Additional Information'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_additional_placeholder"><?php _e('Additional Info Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_additional_placeholder" id="form_activity_additional_placeholder" value="<?php echo esc_attr($activity_registration['additional_placeholder'] ?? 'Anything else we should know?'); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_submit_button"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_submit_button" id="form_activity_submit_button" value="<?php echo esc_attr($activity_registration['submit_button'] ?? 'Register'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="form_activity_success_message"><?php _e('Success Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="form_activity_success_message" id="form_activity_success_message" value="<?php echo esc_attr($activity_registration['success_message'] ?? 'Registration successful!'); ?>" class="large-text"></td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(__('Save Forms & Labels', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            // Update tabs
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Update content
            $('.tab-content').hide();
            $(target).show();
        });
    });
    </script>
    <?php
}

/**
 * ============================================================================
 * COMPONENTS PAGE - V5 (NEW!)
 * ============================================================================
 */
function eatisfamily_components_page_v5() {
    // Handle form submission
    if (isset($_POST['eatisfamily_components_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_components_nonce'], 'save_components')) {
        
        $components = array(
            'navbar' => array(
                'brand_name' => sanitize_text_field($_POST['navbar_brand_name'] ?? 'Eat Is'),
                'brand_highlight' => sanitize_text_field($_POST['navbar_brand_highlight'] ?? 'Friday'),
                'cta_desktop' => sanitize_text_field($_POST['navbar_cta_desktop'] ?? 'Contact Us'),
                'cta_mobile' => sanitize_text_field($_POST['navbar_cta_mobile'] ?? 'Contact'),
            ),
            'footer' => array(
                'logoFooter' => esc_url_raw($_POST['footer_logo'] ?? ''),
                'brand_name' => sanitize_text_field($_POST['footer_brand_name'] ?? 'Eat Is Friday'),
                'brand_description' => wp_kses_post($_POST['footer_brand_description'] ?? ''),
                'contact_email' => sanitize_email($_POST['footer_contact_email'] ?? ''),
                'contact_phone' => sanitize_text_field($_POST['footer_contact_phone'] ?? ''),
                'company_title' => sanitize_text_field($_POST['footer_company_title'] ?? 'Company'),
                'policy_title' => sanitize_text_field($_POST['footer_policy_title'] ?? 'Policy'),
                'copyright_template' => sanitize_text_field($_POST['footer_copyright'] ?? '© {year} Eat Is Friday. All rights reserved.'),
                'back_to_top' => sanitize_text_field($_POST['footer_back_to_top'] ?? 'Back to top'),
            ),
            'header' => array(
                'logo' => sanitize_text_field($_POST['header_logo'] ?? 'Eat Is Friday'),
                'nav_links' => array(
                    'about' => sanitize_text_field($_POST['header_nav_about'] ?? 'About'),
                    'activities' => sanitize_text_field($_POST['header_nav_activities'] ?? 'Activities'),
                    'events' => sanitize_text_field($_POST['header_nav_events'] ?? 'Events'),
                    'careers' => sanitize_text_field($_POST['header_nav_careers'] ?? 'Careers'),
                    'blogs' => sanitize_text_field($_POST['header_nav_blogs'] ?? 'Blog'),
                    'contact' => sanitize_text_field($_POST['header_nav_contact'] ?? 'Contact'),
                ),
            ),
        );
        
        update_option('eatisfamily_components', $components);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Components saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $components = get_option('eatisfamily_components', array());
    $navbar = $components['navbar'] ?? array();
    $footer = $components['footer'] ?? array();
    $header = $components['header'] ?? array();
    $nav_links = $header['nav_links'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Components', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Gérez les composants globaux : Header, Navbar et Footer.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_components', 'eatisfamily_components_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#header-section" class="nav-tab nav-tab-active"><?php _e('🔝 Header', 'eatisfamily'); ?></a>
                <a href="#footer-section" class="nav-tab"><?php _e('🔻 Footer', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- HEADER -->
            <div id="header-section" class="tab-content" style="display: block;">
                <h3><?php _e('🔝 Header / Navigation', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="header_logo"><?php _e('Logo Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="header_logo" id="header_logo" value="<?php echo esc_attr($header['logo'] ?? 'Eat Is Friday'); ?>" class="regular-text">
                            <p class="description"><?php _e('Le texte affiché comme logo', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h4><?php _e('Navigation Links', 'eatisfamily'); ?></h4></th>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_about"><?php _e('About Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_about" id="header_nav_about" value="<?php echo esc_attr($nav_links['about'] ?? 'About'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_activities"><?php _e('Activities Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_activities" id="header_nav_activities" value="<?php echo esc_attr($nav_links['activities'] ?? 'Activities'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_events"><?php _e('Events Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_events" id="header_nav_events" value="<?php echo esc_attr($nav_links['events'] ?? 'Events'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_careers"><?php _e('Careers Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_careers" id="header_nav_careers" value="<?php echo esc_attr($nav_links['careers'] ?? 'Careers'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_blogs"><?php _e('Blog Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_blogs" id="header_nav_blogs" value="<?php echo esc_attr($nav_links['blogs'] ?? 'Blog'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="header_nav_contact"><?php _e('Contact Link', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="header_nav_contact" id="header_nav_contact" value="<?php echo esc_attr($nav_links['contact'] ?? 'Contact'); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- FOOTER -->
            <div id="footer-section" class="tab-content" style="display: none;">
                <h3><?php _e('🔻 Footer', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="footer_logo"><?php _e('Footer Logo URL', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="footer_logo" id="footer_logo" value="<?php echo esc_attr($footer['logoFooter'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="footer_logo"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_brand_name"><?php _e('Brand Name', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="footer_brand_name" id="footer_brand_name" value="<?php echo esc_attr($footer['brand_name'] ?? 'Eat Is Friday'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_brand_description"><?php _e('Brand Description', 'eatisfamily'); ?></label></th>
                        <td><textarea name="footer_brand_description" id="footer_brand_description" rows="3" class="large-text"><?php echo esc_textarea($footer['brand_description'] ?? ''); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_contact_email"><?php _e('Contact Email', 'eatisfamily'); ?></label></th>
                        <td><input type="email" name="footer_contact_email" id="footer_contact_email" value="<?php echo esc_attr($footer['contact_email'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_contact_phone"><?php _e('Contact Phone', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="footer_contact_phone" id="footer_contact_phone" value="<?php echo esc_attr($footer['contact_phone'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_company_title"><?php _e('Company Section Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="footer_company_title" id="footer_company_title" value="<?php echo esc_attr($footer['company_title'] ?? 'Company'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_policy_title"><?php _e('Policy Section Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="footer_policy_title" id="footer_policy_title" value="<?php echo esc_attr($footer['policy_title'] ?? 'Policy'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_copyright"><?php _e('Copyright Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="footer_copyright" id="footer_copyright" value="<?php echo esc_attr($footer['copyright_template'] ?? '© {year} Eat Is Friday. All rights reserved.'); ?>" class="large-text">
                            <p class="description"><?php _e('Utilisez {year} pour l\'année actuelle', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_back_to_top"><?php _e('Back to Top Text', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="footer_back_to_top" id="footer_back_to_top" value="<?php echo esc_attr($footer['back_to_top'] ?? 'Back to top'); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(__('Save Components', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').hide();
            $(target).show();
        });
        
        // Media uploader
        $('.eatisfamily-upload-media').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            
            var frame = wp.media({
                title: '<?php _e('Select Image', 'eatisfamily'); ?>',
                button: { text: '<?php _e('Use this image', 'eatisfamily'); ?>' },
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
 * PAGES CONTENT PAGE - V5 (Extended with all pages)
 * ============================================================================
 */
function eatisfamily_pages_content_page_v5() {
    // Handle form submission
    if (isset($_POST['eatisfamily_pages_content_v5_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_pages_content_v5_nonce'], 'save_pages_content_v5')) {
        
        $pages_content = eatisfamily_build_pages_content_v5($_POST);
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
    $blog = $pages_content['blog'] ?? array();
    $job_detail = $pages_content['job_detail'] ?? array();
    $apply_activities = $pages_content['apply_activities'] ?? array();
    $apply_jobs = $pages_content['apply_jobs'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Pages Content', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Gérez le contenu de toutes les pages du site.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_pages_content_v5', 'eatisfamily_pages_content_v5_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#homepage" class="nav-tab nav-tab-active"><?php _e('🏠 Homepage', 'eatisfamily'); ?></a>
                <a href="#about" class="nav-tab"><?php _e('ℹ️ About', 'eatisfamily'); ?></a>
                <a href="#contact" class="nav-tab"><?php _e('📧 Contact', 'eatisfamily'); ?></a>
                <a href="#careers" class="nav-tab"><?php _e('💼 Careers', 'eatisfamily'); ?></a>
                <a href="#events" class="nav-tab"><?php _e('🎉 Events', 'eatisfamily'); ?></a>
                <a href="#blog" class="nav-tab"><?php _e('📝 Blog', 'eatisfamily'); ?></a>
                <a href="#job-detail" class="nav-tab"><?php _e('📋 Job Detail', 'eatisfamily'); ?></a>
                <a href="#apply-activities" class="nav-tab"><?php _e('🎯 Apply Activities', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- HOMEPAGE -->
            <div id="homepage" class="tab-content" style="display: block;">
                <h3><?php _e('🏠 Homepage', 'eatisfamily'); ?></h3>
                
                <!-- Hero Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Hero Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_hero_bg" id="homepage_hero_bg" value="<?php echo esc_attr($homepage['hero_section']['bg'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_hero_bg"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_hero_title_line1" id="homepage_hero_title_line1" class="large-text" rows="2"><?php echo esc_textarea($homepage['hero_section']['title']['line_1'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_hero_title_line2" id="homepage_hero_title_line2" class="large-text" rows="2"><?php echo esc_textarea($homepage['hero_section']['title']['line_2'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_hero_title_line3"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_hero_title_line3" id="homepage_hero_title_line3" class="large-text" rows="2"><?php echo esc_textarea($homepage['hero_section']['title']['line_3'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Intro Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Intro Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_intro_texte"><?php _e('Intro Text', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_intro_texte" id="homepage_intro_texte" class="large-text" rows="4"><?php echo esc_textarea($homepage['intro_section']['texte'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Services Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Services Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_services_tag"><?php _e('Tag', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_services_tag" id="homepage_services_tag" value="<?php echo esc_attr($homepage['services_section']['tag'] ?? ''); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_services_title_line1" id="homepage_services_title_line1" value="<?php echo esc_attr($homepage['services_section']['title']['line_1'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_highlight"><?php _e('Title Highlight', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_services_title_highlight" id="homepage_services_title_highlight" value="<?php echo esc_attr($homepage['services_section']['title']['highlight'] ?? ''); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_services_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_services_title_line2" id="homepage_services_title_line2" value="<?php echo esc_attr($homepage['services_section']['title']['line_2'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
                
                <!-- CTA Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('CTA Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_cta_title" id="homepage_cta_title" value="<?php echo esc_attr($homepage['cta_section']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_cta_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_cta_description" id="homepage_cta_description" class="large-text" rows="3"><?php echo esc_textarea($homepage['cta_section']['description'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Beautiful Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Beautiful Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_beautiful_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_beautiful_title" id="homepage_beautiful_title" class="large-text" rows="2"><?php echo esc_textarea($homepage['beautiful']['title'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_beautiful_text"><?php _e('Text', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_beautiful_text" id="homepage_beautiful_text" class="large-text" rows="3"><?php echo esc_textarea($homepage['beautiful']['text'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_beautiful_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_beautiful_image" id="homepage_beautiful_image" value="<?php echo esc_attr($homepage['beautiful']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_beautiful_image"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Partners Title -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Partners Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_partners_title"><?php _e('Partners Title', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_partners_title" id="homepage_partners_title" class="large-text" rows="2"><?php echo esc_textarea($homepage['partners_title'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Sustainable Service Title -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Sustainable Service', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_sustainable_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_sustainable_title" id="homepage_sustainable_title" class="large-text" rows="2"><?php echo esc_textarea($homepage['sustainable_service_title'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Homepage CTA Block -->
                <div class="eatisfamily-section">
                    <h4><?php _e('Final CTA Block', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_cta_block_title" id="homepage_cta_block_title" value="<?php echo esc_attr($homepage['homepageCTA']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_cta_block_description" id="homepage_cta_block_description" class="large-text" rows="3"><?php echo esc_textarea($homepage['homepageCTA']['description'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="homepage_cta_block_image" id="homepage_cta_block_image" value="<?php echo esc_attr($homepage['homepageCTA']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="homepage_cta_block_image"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_cta_block_additional"><?php _e('Additional Text', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_cta_block_additional" id="homepage_cta_block_additional" class="large-text" rows="2"><?php echo esc_textarea($homepage['homepageCTA']['additionalText'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <!-- SEO Section -->
                <div class="eatisfamily-section">
                    <h4><?php _e('SEO Settings', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="homepage_seo_title"><?php _e('Meta Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_seo_title" id="homepage_seo_title" value="<?php echo esc_attr($homepage['seo']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_description"><?php _e('Meta Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="homepage_seo_description" id="homepage_seo_description" class="large-text" rows="2"><?php echo esc_textarea($homepage['seo']['description'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="homepage_seo_keywords"><?php _e('Keywords', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="homepage_seo_keywords" id="homepage_seo_keywords" value="<?php echo esc_attr($homepage['seo']['keywords'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ABOUT PAGE -->
            <div id="about" class="tab-content" style="display: none;">
                <h3><?php _e('ℹ️ About Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Hero Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="about_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="about_hero_title" id="about_hero_title" value="<?php echo esc_attr($about['hero']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="about_hero_subtitle" id="about_hero_subtitle" value="<?php echo esc_attr($about['hero']['subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="about_hero_description" id="about_hero_description" class="large-text" rows="4"><?php echo esc_textarea($about['hero']['description'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_hero_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="about_hero_image" id="about_hero_image" value="<?php echo esc_attr($about['hero']['image']['src'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="about_hero_image"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Section Titles', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="about_values_title"><?php _e('Values Section Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="about_values_title" id="about_values_title" value="<?php echo esc_attr($about['section_titles']['values'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="about_team_title"><?php _e('Team Section Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="about_team_title" id="about_team_title" value="<?php echo esc_attr($about['section_titles']['team'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- CONTACT PAGE -->
            <div id="contact" class="tab-content" style="display: none;">
                <h3><?php _e('📧 Contact Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Hero Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="contact_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="contact_hero_title" id="contact_hero_title" value="<?php echo esc_attr($contact['hero']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="contact_hero_subtitle" id="contact_hero_subtitle" value="<?php echo esc_attr($contact['hero']['subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Form Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="contact_form_title"><?php _e('Form Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="contact_form_title" id="contact_form_title" value="<?php echo esc_attr($contact['form_title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="contact_form_subtitle"><?php _e('Form Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="contact_form_subtitle" id="contact_form_subtitle" value="<?php echo esc_attr($contact['form_subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- CAREERS PAGE -->
            <div id="careers" class="tab-content" style="display: none;">
                <h3><?php _e('💼 Careers Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Hero Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_hero_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="careers_hero_title_line1" id="careers_hero_title_line1" value="<?php echo esc_attr($careers['hero_default']['title_line_1'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="careers_hero_title_line2" id="careers_hero_title_line2" value="<?php echo esc_attr($careers['hero_default']['title_line_2'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_image"><?php _e('Hero Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_image" id="careers_hero_image" value="<?php echo esc_attr($careers['hero_default']['image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="careers_hero_image"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="careers_hero_bg" id="careers_hero_bg" value="<?php echo esc_attr($careers['hero_default']['background_image'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="careers_hero_bg"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Join Box', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="careers_join_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="careers_join_title" id="careers_join_title" value="<?php echo esc_attr($careers['join_box']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="careers_join_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="careers_join_description" id="careers_join_description" class="large-text" rows="3"><?php echo esc_textarea($careers['join_box']['description'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- EVENTS PAGE -->
            <div id="events" class="tab-content" style="display: none;">
                <h3><?php _e('🎉 Events Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Hero Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="events_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="events_hero_title" id="events_hero_title" value="<?php echo esc_attr($events['hero']['title'] ?? $events['page_hero']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="events_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><textarea name="events_hero_subtitle" id="events_hero_subtitle" class="large-text" rows="2"><?php echo esc_textarea($events['hero']['subtitle'] ?? $events['page_hero']['subtitle'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- BLOG PAGE -->
            <div id="blog" class="tab-content" style="display: none;">
                <h3><?php _e('📝 Blog Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Index Page', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="blog_section_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_section_title" id="blog_section_title" value="<?php echo esc_attr($blog['index']['section_title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blog_section_subtitle"><?php _e('Section Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_section_subtitle" id="blog_section_subtitle" value="<?php echo esc_attr($blog['index']['section_subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blog_read_article"><?php _e('Read Article Link', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_read_article" id="blog_read_article" value="<?php echo esc_attr($blog['index']['read_article_link'] ?? 'Read Article'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blog_load_more"><?php _e('Load More Button', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_load_more" id="blog_load_more" value="<?php echo esc_attr($blog['index']['load_more_button'] ?? 'Load More'); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Detail Page', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="blog_back_link"><?php _e('Back Link', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_back_link" id="blog_back_link" value="<?php echo esc_attr($blog['detail']['back_link'] ?? 'Back to Blog'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blog_share_button"><?php _e('Share Button', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_share_button" id="blog_share_button" value="<?php echo esc_attr($blog['detail']['share_button'] ?? 'Share'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blog_next_article"><?php _e('Next Article Button', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="blog_next_article" id="blog_next_article" value="<?php echo esc_attr($blog['detail']['next_article_button'] ?? 'Next Article'); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- JOB DETAIL PAGE -->
            <div id="job-detail" class="tab-content" style="display: none;">
                <h3><?php _e('📋 Job Detail Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('CTA Banner', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="job_detail_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_cta_title" id="job_detail_cta_title" value="<?php echo esc_attr($job_detail['cta_banner']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_cta_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_cta_subtitle" id="job_detail_cta_subtitle" value="<?php echo esc_attr($job_detail['cta_banner']['subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_apply_button"><?php _e('Apply Button', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_apply_button" id="job_detail_apply_button" value="<?php echo esc_attr($job_detail['cta_banner']['apply_button'] ?? 'Apply Now'); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Job Description Labels', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="job_detail_section_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_section_title" id="job_detail_section_title" value="<?php echo esc_attr($job_detail['job_description']['section_title'] ?? 'Job Description'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_what_you_do"><?php _e('What You\'ll Do Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_what_you_do" id="job_detail_what_you_do" value="<?php echo esc_attr($job_detail['job_description']['what_you_do_title'] ?? 'What You\'ll Do'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_requirements"><?php _e('Requirements Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_requirements" id="job_detail_requirements" value="<?php echo esc_attr($job_detail['job_description']['requirements_title'] ?? 'Requirements'); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Quick Facts Labels', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="job_detail_quick_facts_title"><?php _e('Quick Facts Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_quick_facts_title" id="job_detail_quick_facts_title" value="<?php echo esc_attr($job_detail['quick_facts']['title'] ?? 'Quick Facts'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_location_label"><?php _e('Location Label', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_location_label" id="job_detail_location_label" value="<?php echo esc_attr($job_detail['quick_facts']['location_label'] ?? 'Location'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_department_label"><?php _e('Department Label', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_department_label" id="job_detail_department_label" value="<?php echo esc_attr($job_detail['quick_facts']['department_label'] ?? 'Department'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="job_detail_employment_label"><?php _e('Employment Type Label', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="job_detail_employment_label" id="job_detail_employment_label" value="<?php echo esc_attr($job_detail['quick_facts']['employment_type_label'] ?? 'Employment Type'); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- APPLY ACTIVITIES PAGE -->
            <div id="apply-activities" class="tab-content" style="display: none;">
                <h3><?php _e('🎯 Apply Activities Page', 'eatisfamily'); ?></h3>
                <div class="eatisfamily-section">
                    <h4><?php _e('Page Hero', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="apply_activities_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><textarea name="apply_activities_hero_title" id="apply_activities_hero_title" class="large-text" rows="2"><?php echo esc_textarea($apply_activities['page_hero']['title'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="apply_activities_hero_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                            <td>
                                <input type="text" name="apply_activities_hero_image" id="apply_activities_hero_image" value="<?php echo esc_attr($apply_activities['page_hero']['image']['src'] ?? ''); ?>" class="regular-text">
                                <button type="button" class="button eatisfamily-upload-media" data-target="apply_activities_hero_image"><?php _e('Select', 'eatisfamily'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="eatisfamily-section">
                    <h4><?php _e('Page Text Section', 'eatisfamily'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="apply_activities_text_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="apply_activities_text_title" id="apply_activities_text_title" value="<?php echo esc_attr($apply_activities['page_text']['title'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="apply_activities_text_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                            <td><input type="text" name="apply_activities_text_subtitle" id="apply_activities_text_subtitle" value="<?php echo esc_attr($apply_activities['page_text']['subtitle'] ?? ''); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="apply_activities_text_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                            <td><textarea name="apply_activities_text_description" id="apply_activities_text_description" class="large-text" rows="4"><?php echo esc_textarea($apply_activities['page_text']['description'] ?? ''); ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php submit_button(__('Save Pages Content', 'eatisfamily')); ?>
        </form>
    </div>
    
    <style>
    .eatisfamily-section {
        background: #fff;
        padding: 15px 20px;
        margin: 15px 0;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
    }
    .eatisfamily-section h4 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').hide();
            $(target).show();
        });
        
        // Media uploader
        $('.eatisfamily-upload-media').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetId = button.data('target');
            
            var frame = wp.media({
                title: '<?php _e('Select Image', 'eatisfamily'); ?>',
                button: { text: '<?php _e('Use this image', 'eatisfamily'); ?>' },
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
 * Build pages_content array from POST data - V5
 */
function eatisfamily_build_pages_content_v5($data) {
    return array(
        'homepage' => array(
            'seo' => array(
                'title' => sanitize_text_field($data['homepage_seo_title'] ?? ''),
                'description' => sanitize_textarea_field($data['homepage_seo_description'] ?? ''),
                'keywords' => sanitize_text_field($data['homepage_seo_keywords'] ?? ''),
            ),
            'hero_section' => array(
                'bg' => esc_url_raw($data['homepage_hero_bg'] ?? ''),
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
                'title' => wp_kses_post($data['homepage_beautiful_title'] ?? ''),
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
                ),
            ),
            'section_titles' => array(
                'values' => sanitize_text_field($data['about_values_title'] ?? ''),
                'team' => sanitize_text_field($data['about_team_title'] ?? ''),
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
        ),
        'events' => array(
            'hero' => array(
                'title' => sanitize_text_field($data['events_hero_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['events_hero_subtitle'] ?? ''),
            ),
        ),
        'blog' => array(
            'index' => array(
                'section_title' => sanitize_text_field($data['blog_section_title'] ?? ''),
                'section_subtitle' => sanitize_text_field($data['blog_section_subtitle'] ?? ''),
                'read_article_link' => sanitize_text_field($data['blog_read_article'] ?? 'Read Article'),
                'load_more_button' => sanitize_text_field($data['blog_load_more'] ?? 'Load More'),
            ),
            'detail' => array(
                'back_link' => sanitize_text_field($data['blog_back_link'] ?? 'Back to Blog'),
                'share_button' => sanitize_text_field($data['blog_share_button'] ?? 'Share'),
                'next_article_button' => sanitize_text_field($data['blog_next_article'] ?? 'Next Article'),
            ),
        ),
        'job_detail' => array(
            'cta_banner' => array(
                'title' => sanitize_text_field($data['job_detail_cta_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['job_detail_cta_subtitle'] ?? ''),
                'apply_button' => sanitize_text_field($data['job_detail_apply_button'] ?? 'Apply Now'),
            ),
            'job_description' => array(
                'section_title' => sanitize_text_field($data['job_detail_section_title'] ?? 'Job Description'),
                'what_you_do_title' => sanitize_text_field($data['job_detail_what_you_do'] ?? 'What You\'ll Do'),
                'requirements_title' => sanitize_text_field($data['job_detail_requirements'] ?? 'Requirements'),
            ),
            'quick_facts' => array(
                'title' => sanitize_text_field($data['job_detail_quick_facts_title'] ?? 'Quick Facts'),
                'location_label' => sanitize_text_field($data['job_detail_location_label'] ?? 'Location'),
                'department_label' => sanitize_text_field($data['job_detail_department_label'] ?? 'Department'),
                'employment_type_label' => sanitize_text_field($data['job_detail_employment_label'] ?? 'Employment Type'),
            ),
        ),
        'apply_activities' => array(
            'page_hero' => array(
                'title' => wp_kses_post($data['apply_activities_hero_title'] ?? ''),
                'image' => array(
                    'src' => esc_url_raw($data['apply_activities_hero_image'] ?? ''),
                ),
            ),
            'page_text' => array(
                'title' => sanitize_text_field($data['apply_activities_text_title'] ?? ''),
                'subtitle' => sanitize_text_field($data['apply_activities_text_subtitle'] ?? ''),
                'description' => wp_kses_post($data['apply_activities_text_description'] ?? ''),
            ),
        ),
    );
}

/**
 * Enqueue admin scripts for media uploader
 */
function eatisfamily_admin_scripts_v5($hook) {
    if (strpos($hook, 'eatisfamily') !== false) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'eatisfamily_admin_scripts_v5');
