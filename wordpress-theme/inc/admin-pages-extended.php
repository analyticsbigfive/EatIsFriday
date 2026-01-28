<?php
/**
 * EIF Backend - Extended Admin Pages
 * Complete content management for all pages including partners
 *
 * @package EIFBackend
 * @version 4.0.0
 * 
 * NOTE: Menu registration has been moved to admin-pages-v5.php for unified management.
 * This file now only contains the page rendering functions (Partners, Services, etc.)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register admin menus - DISABLED
 * Menu registration is now handled by admin-pages-v5.php to avoid duplicate menus.
 * The functions below (eatisfamily_partners_page, etc.) are still used by v5.
 */
// function eatisfamily_register_admin_menus() { ... }
// add_action('admin_menu', 'eatisfamily_register_admin_menus', 5); // DISABLED

/**
 * ============================================
 * PARTNERS MANAGEMENT PAGE
 * ============================================
 */
function eatisfamily_partners_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_partners_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_partners_nonce'], 'save_partners')) {
        
        $partners_data = array(
            'title' => wp_kses_post($_POST['partners_title'] ?? ''),
            'partners' => array()
        );
        
        // Process partners
        if (isset($_POST['partner_logo']) && is_array($_POST['partner_logo'])) {
            foreach ($_POST['partner_logo'] as $index => $logo) {
                if (!empty($logo)) {
                    $partners_data['partners'][] = array(
                        'logo' => esc_url_raw($logo),
                        'alt' => sanitize_text_field($_POST['partner_alt'][$index] ?? ''),
                        'name' => sanitize_text_field($_POST['partner_name'][$index] ?? ''),
                        'url' => esc_url_raw($_POST['partner_url'][$index] ?? '')
                    );
                }
            }
        }
        
        update_option('eatisfamily_partners', $partners_data);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Partners saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $partners_data = get_option('eatisfamily_partners', array());
    $partners = $partners_data['partners'] ?? array();
    $partners_title = $partners_data['title'] ?? '';
    
    ?>
    <div class="wrap">
        <h1><?php _e('Partners Management', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Manage the partners/clients logos displayed on the homepage.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_partners', 'eatisfamily_partners_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="partners_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                    <td>
                        <textarea name="partners_title" id="partners_title" class="large-text" rows="3"><?php echo esc_textarea($partners_title); ?></textarea>
                        <p class="description"><?php _e('The title displayed above the partners carousel. Use \n for line breaks.', 'eatisfamily'); ?></p>
                    </td>
                </tr>
            </table>
            
            <h2><?php _e('Partners List', 'eatisfamily'); ?></h2>
            <p class="description"><?php _e('Add, edit or remove partners. Drag to reorder.', 'eatisfamily'); ?></p>
            
            <div id="partners-list" class="eatisfamily-repeater">
                <?php 
                if (!empty($partners)) {
                    foreach ($partners as $index => $partner) {
                        eatisfamily_render_partner_row($index, $partner);
                    }
                } else {
                    // Show one empty row
                    eatisfamily_render_partner_row(0, array());
                }
                ?>
            </div>
            
            <p>
                <button type="button" class="button" id="add-partner"><?php _e('+ Add Partner', 'eatisfamily'); ?></button>
            </p>
            
            <?php submit_button(__('Save Partners', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script type="text/html" id="partner-row-template">
        <?php eatisfamily_render_partner_row('{{INDEX}}', array()); ?>
    </script>
    
    <script>
    jQuery(document).ready(function($) {
        var partnerIndex = <?php echo count($partners); ?>;
        
        $('#add-partner').on('click', function() {
            var template = $('#partner-row-template').html();
            template = template.replace(/\{\{INDEX\}\}/g, partnerIndex);
            $('#partners-list').append(template);
            partnerIndex++;
        });
        
        $(document).on('click', '.remove-partner', function() {
            $(this).closest('.partner-row').remove();
        });
        
        // Media uploader
        $(document).on('click', '.eatisfamily-upload-media', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            
            var mediaUploader = wp.media({
                title: '<?php _e('Select Image', 'eatisfamily'); ?>',
                button: { text: '<?php _e('Use this image', 'eatisfamily'); ?>' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetField).val(attachment.url);
                button.siblings('.preview-image').remove();
                button.after('<img src="' + attachment.url + '" class="preview-image" style="max-width:150px;display:block;margin-top:10px;">');
            });
            
            mediaUploader.open();
        });
    });
    </script>
    
    <style>
    .partner-row {
        background: #fff;
        border: 1px solid #ccd0d4;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    .partner-row .form-field {
        margin-bottom: 10px;
    }
    .partner-row label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .partner-row input[type="text"] {
        width: 100%;
    }
    .remove-partner {
        color: #a00;
        cursor: pointer;
    }
    .remove-partner:hover {
        color: #dc3232;
    }
    .preview-image {
        max-width: 150px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    </style>
    <?php
}

function eatisfamily_render_partner_row($index, $partner) {
    $logo = $partner['logo'] ?? '';
    $alt = $partner['alt'] ?? '';
    $name = $partner['name'] ?? '';
    $url = $partner['url'] ?? '';
    ?>
    <div class="partner-row">
        <div class="form-field">
            <label><?php _e('Partner Name', 'eatisfamily'); ?></label>
            <input type="text" name="partner_name[<?php echo $index; ?>]" value="<?php echo esc_attr($name); ?>" placeholder="<?php _e('e.g. Adidas Arena', 'eatisfamily'); ?>">
        </div>
        <div class="form-field">
            <label><?php _e('Logo URL', 'eatisfamily'); ?></label>
            <input type="text" name="partner_logo[<?php echo $index; ?>]" id="partner_logo_<?php echo $index; ?>" value="<?php echo esc_attr($logo); ?>">
            <button type="button" class="button eatisfamily-upload-media" data-target="partner_logo_<?php echo $index; ?>"><?php _e('Select Image', 'eatisfamily'); ?></button>
            <?php if ($logo): ?>
                <img src="<?php echo esc_url($logo); ?>" class="preview-image">
            <?php endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Alt Text', 'eatisfamily'); ?></label>
            <input type="text" name="partner_alt[<?php echo $index; ?>]" value="<?php echo esc_attr($alt); ?>" placeholder="<?php _e('e.g. Adidas Arena Logo', 'eatisfamily'); ?>">
        </div>
        <div class="form-field">
            <label><?php _e('Website URL (optional)', 'eatisfamily'); ?></label>
            <input type="text" name="partner_url[<?php echo $index; ?>]" value="<?php echo esc_attr($url); ?>" placeholder="https://">
        </div>
        <p><span class="remove-partner"><?php _e('Remove Partner', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * ============================================
 * SERVICES MANAGEMENT PAGE
 * ============================================
 */
function eatisfamily_services_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_services_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_services_nonce'], 'save_services')) {
        
        $services_data = array(
            'tag' => sanitize_text_field($_POST['services_tag'] ?? ''),
            'title' => array(
                'line_1' => sanitize_text_field($_POST['services_title_line1'] ?? ''),
                'highlight' => sanitize_text_field($_POST['services_title_highlight'] ?? ''),
                'line_2' => sanitize_text_field($_POST['services_title_line2'] ?? '')
            ),
            'services' => array()
        );
        
        // Process services
        if (isset($_POST['service_title']) && is_array($_POST['service_title'])) {
            foreach ($_POST['service_title'] as $index => $title) {
                if (!empty($title)) {
                    $services_data['services'][] = array(
                        'title' => wp_kses_post($_POST['service_title'][$index] ?? ''),
                        'description' => wp_kses_post($_POST['service_description'][$index] ?? ''),
                        'thumbnail' => esc_url_raw($_POST['service_thumbnail'][$index] ?? ''),
                        'btnImage' => esc_url_raw($_POST['service_btn_image'][$index] ?? ''),
                        'linkTo' => sanitize_text_field($_POST['service_link'][$index] ?? '/')
                    );
                }
            }
        }
        
        update_option('eatisfamily_services', $services_data);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Services saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $services_data = get_option('eatisfamily_services', array());
    $services = $services_data['services'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('Services Management', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Manage the services section on the homepage.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_services', 'eatisfamily_services_nonce'); ?>
            
            <h2><?php _e('Section Header', 'eatisfamily'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="services_tag"><?php _e('Tag', 'eatisfamily'); ?></label></th>
                    <td>
                        <input type="text" name="services_tag" id="services_tag" value="<?php echo esc_attr($services_data['tag'] ?? 'OUR SERVICES'); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="services_title_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                    <td>
                        <input type="text" name="services_title_line1" id="services_title_line1" value="<?php echo esc_attr($services_data['title']['line_1'] ?? ''); ?>" class="large-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="services_title_highlight"><?php _e('Title Highlight', 'eatisfamily'); ?></label></th>
                    <td>
                        <input type="text" name="services_title_highlight" id="services_title_highlight" value="<?php echo esc_attr($services_data['title']['highlight'] ?? ''); ?>" class="regular-text">
                        <p class="description"><?php _e('This word will be highlighted with underline.', 'eatisfamily'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="services_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                    <td>
                        <input type="text" name="services_title_line2" id="services_title_line2" value="<?php echo esc_attr($services_data['title']['line_2'] ?? ''); ?>" class="large-text">
                    </td>
                </tr>
            </table>
            
            <h2><?php _e('Services List', 'eatisfamily'); ?></h2>
            <div id="services-list" class="eatisfamily-repeater">
                <?php 
                if (!empty($services)) {
                    foreach ($services as $index => $service) {
                        eatisfamily_render_service_row($index, $service);
                    }
                } else {
                    eatisfamily_render_service_row(0, array());
                }
                ?>
            </div>
            
            <p>
                <button type="button" class="button" id="add-service"><?php _e('+ Add Service', 'eatisfamily'); ?></button>
            </p>
            
            <?php submit_button(__('Save Services', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script type="text/html" id="service-row-template">
        <?php eatisfamily_render_service_row('{{INDEX}}', array()); ?>
    </script>
    
    <script>
    jQuery(document).ready(function($) {
        var serviceIndex = <?php echo count($services); ?>;

        function initWysiwygOnTextareas(container) {
            $(container).find('textarea.service-wysiwyg').each(function() {
                var textarea = this;
                var id = $(textarea).attr('id');
                if (!id) {
                    id = 'service_wysiwyg_' + Math.random().toString(36).substr(2, 9);
                    $(textarea).attr('id', id);
                }
                if (typeof wp !== 'undefined' && wp.editor) {
                    wp.editor.initialize(id, {
                        tinymce: {
                            wpautop: true,
                            plugins: 'charmap colorpicker hr lists paste tabfocus textcolor wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
                            toolbar1: 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo'
                        },
                        quicktags: true,
                        mediaButtons: false
                    });
                }
            });
        }

        $('#add-service').on('click', function() {
            var template = $('#service-row-template').html();
            template = template.replace(/\{\{INDEX\}\}/g, serviceIndex);
            var $newRow = $(template);
            $('#services-list').append($newRow);
            initWysiwygOnTextareas($newRow);
            serviceIndex++;
        });

        $(document).on('click', '.remove-service', function() {
            var $row = $(this).closest('.service-row');
            $row.find('textarea.service-wysiwyg').each(function() {
                var id = $(this).attr('id');
                if (id && typeof wp !== 'undefined' && wp.editor) {
                    wp.editor.remove(id);
                }
            });
            $row.remove();
        });

        // Media uploader for service images
        $(document).on('click', '.eatisfamily-upload-media', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');

            var mediaUploader = wp.media({
                title: '<?php _e("Select Image", "eatisfamily"); ?>',
                button: { text: '<?php _e("Use this image", "eatisfamily"); ?>' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetField).val(attachment.url);
                button.siblings('.preview-image').remove();
                button.after('<img src="' + attachment.url + '" class="preview-image" style="max-width:150px;display:block;margin-top:10px;">');
            });

            mediaUploader.open();
        });
    });
    </script>
    <?php
}

function eatisfamily_render_service_row($index, $service) {
    $is_template = !is_numeric($index);
    ?>
    <div class="service-row partner-row">
        <div class="form-field">
            <label><?php _e('Service Title', 'eatisfamily'); ?></label>
            <?php if ($is_template): ?>
                <textarea name="service_title[<?php echo $index; ?>]" class="service-wysiwyg" rows="2" style="width:100%"><?php echo esc_textarea($service['title'] ?? ''); ?></textarea>
            <?php else:
                wp_editor(
                    $service['title'] ?? '',
                    'service_title_' . $index,
                    array(
                        'textarea_name' => 'service_title[' . $index . ']',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                        'quicktags' => true,
                    )
                );
            endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Description', 'eatisfamily'); ?></label>
            <?php if ($is_template): ?>
                <textarea name="service_description[<?php echo $index; ?>]" class="service-wysiwyg" rows="4" style="width:100%"><?php echo esc_textarea($service['description'] ?? ''); ?></textarea>
            <?php else:
                wp_editor(
                    $service['description'] ?? '',
                    'service_description_' . $index,
                    array(
                        'textarea_name' => 'service_description[' . $index . ']',
                        'textarea_rows' => 6,
                        'media_buttons' => false,
                        'teeny' => true,
                        'quicktags' => true,
                    )
                );
            endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Thumbnail Image', 'eatisfamily'); ?></label>
            <input type="text" name="service_thumbnail[<?php echo $index; ?>]" id="service_thumbnail_<?php echo $index; ?>" value="<?php echo esc_attr($service['thumbnail'] ?? ''); ?>" class="regular-text">
            <button type="button" class="button eatisfamily-upload-media" data-target="service_thumbnail_<?php echo $index; ?>"><?php _e('Select Image', 'eatisfamily'); ?></button>
            <?php if (!empty($service['thumbnail'])): ?>
                <img src="<?php echo esc_url($service['thumbnail']); ?>" class="preview-image" style="max-width:150px;display:block;margin-top:10px;">
            <?php endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Button Image', 'eatisfamily'); ?></label>
            <input type="text" name="service_btn_image[<?php echo $index; ?>]" id="service_btn_image_<?php echo $index; ?>" value="<?php echo esc_attr($service['btnImage'] ?? ''); ?>" class="regular-text">
            <button type="button" class="button eatisfamily-upload-media" data-target="service_btn_image_<?php echo $index; ?>"><?php _e('Select Image', 'eatisfamily'); ?></button>
            <?php if (!empty($service['btnImage'])): ?>
                <img src="<?php echo esc_url($service['btnImage']); ?>" class="preview-image" style="max-width:150px;display:block;margin-top:10px;">
            <?php endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Link To', 'eatisfamily'); ?></label>
            <input type="text" name="service_link[<?php echo $index; ?>]" value="<?php echo esc_attr($service['linkTo'] ?? '/'); ?>">
        </div>
        <p><span class="remove-service remove-partner"><?php _e('Remove Service', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * ============================================
 * SUSTAINABILITY MANAGEMENT PAGE
 * ============================================
 */
function eatisfamily_sustainability_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_sustainability_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_sustainability_nonce'], 'save_sustainability')) {
        
        $sustainability_data = array(
            'title' => wp_kses_post($_POST['sustainability_title'] ?? ''),
            'items' => array()
        );
        
        // Process items
        if (isset($_POST['sustainability_item_title']) && is_array($_POST['sustainability_item_title'])) {
            foreach ($_POST['sustainability_item_title'] as $index => $title) {
                if (!empty($title)) {
                    $sustainability_data['items'][] = array(
                        'title' => wp_kses_post($title),
                        'description' => wp_kses_post($_POST['sustainability_item_description'][$index] ?? ''),
                        'icone' => esc_url_raw($_POST['sustainability_item_icon'][$index] ?? ''),
                        'background' => sanitize_text_field($_POST['sustainability_item_bg'][$index] ?? '')
                    );
                }
            }
        }
        
        update_option('eatisfamily_sustainability', $sustainability_data);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Sustainability content saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $sustainability_data = get_option('eatisfamily_sustainability', array());
    $items = $sustainability_data['items'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('🌱 Sustainability Section', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Manage the "Our Commitment To Sustainable Service" section.', 'eatisfamily'); ?></p>
        
        <style>
            .sustainability-row {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                margin-bottom: 15px;
                border-radius: 4px;
                position: relative;
            }
            .sustainability-row .form-field {
                margin-bottom: 15px;
            }
            .sustainability-row label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .sustainability-row .icon-preview {
                max-width: 80px;
                max-height: 80px;
                margin-top: 10px;
                display: block;
            }
            .sustainability-row .remove-sustainability {
                color: #d63638;
                cursor: pointer;
                text-decoration: underline;
                display: inline-block;
                padding: 5px 10px;
                background: #fff5f5;
                border: 1px solid #d63638;
                border-radius: 3px;
            }
            .sustainability-row .remove-sustainability:hover {
                color: #fff;
                background: #d63638;
            }
            .sustainability-row .item-number {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #0073aa;
                color: #fff;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
        </style>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_sustainability', 'eatisfamily_sustainability_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="sustainability_title"><?php _e('Section Title', 'eatisfamily'); ?></label></th>
                    <td>
                        <?php
                        wp_editor(
                            $sustainability_data['title'] ?? '',
                            'sustainability_title',
                            array(
                                'textarea_name' => 'sustainability_title',
                                'textarea_rows' => 3,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true
                            )
                        );
                        ?>
                    </td>
                </tr>
            </table>
            
            <h2><?php _e('Sustainability Items', 'eatisfamily'); ?></h2>
            <div id="sustainability-list" class="eatisfamily-repeater">
                <?php 
                if (!empty($items)) {
                    foreach ($items as $index => $item) {
                        eatisfamily_render_sustainability_row($index, $item);
                    }
                } else {
                    eatisfamily_render_sustainability_row(0, array());
                }
                ?>
            </div>
            
            <p>
                <button type="button" class="button button-primary" id="add-sustainability"><?php _e('+ Add Item', 'eatisfamily'); ?></button>
            </p>
            
            <?php submit_button(__('Save Sustainability', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script type="text/html" id="sustainability-row-template">
        <?php eatisfamily_render_sustainability_row_template(); ?>
    </script>
    
    <script type="text/javascript">
    (function($) {
        'use strict';
        
        $(document).ready(function() {
            var sustainabilityIndex = <?php echo count($items); ?>;
            
            // Function to initialize TinyMCE on a textarea
            function initTinyMCE(editorId) {
                if (typeof tinymce !== 'undefined') {
                    // Remove existing editor if any
                    tinymce.execCommand('mceRemoveEditor', true, editorId);
                    
                    // Initialize new editor
                    tinymce.init({
                        selector: '#' + editorId,
                        menubar: false,
                        toolbar: 'bold italic underline | bullist numlist | link unlink',
                        plugins: 'link lists',
                        height: 200,
                        branding: false,
                        relative_urls: false,
                        remove_script_host: false
                    });
                }
            }
            
            // Add new item
            $('#add-sustainability').on('click', function() {
                var template = $('#sustainability-row-template').html();
                template = template.replace(/\{\{INDEX\}\}/g, sustainabilityIndex);
                $('#sustainability-list').append(template);
                updateItemNumbers();
                
                // Initialize TinyMCE for the new textarea
                setTimeout(function() {
                    initTinyMCE('sustainability_desc_' + sustainabilityIndex);
                    sustainabilityIndex++;
                }, 100);
            });
            
            // Remove item
            $(document).on('click', '.remove-sustainability', function(e) {
                e.preventDefault();
                if (confirm('<?php _e('Are you sure you want to remove this item?', 'eatisfamily'); ?>')) {
                    var $row = $(this).closest('.sustainability-row');
                    var editorId = $row.find('.sustainability-wysiwyg').attr('id');
                    
                    // Remove TinyMCE instance
                    if (typeof tinymce !== 'undefined' && editorId) {
                        tinymce.execCommand('mceRemoveEditor', true, editorId);
                    }
                    
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        updateItemNumbers();
                    });
                }
            });
            
            // Update item numbers
            function updateItemNumbers() {
                $('#sustainability-list .sustainability-row').each(function(index) {
                    $(this).find('.item-number').text(index + 1);
                });
            }
            
            // Media uploader for sustainability icons
            $(document).on('click', '.sustainability-upload-btn', function(e) {
                e.preventDefault();
                var $button = $(this);
                var targetId = $button.data('target');
                var $targetInput = $('#' + targetId);
                var $preview = $button.siblings('.icon-preview');
                
                console.log('Upload clicked for:', targetId);
                
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                    alert('Media uploader not available. Please refresh the page.');
                    return;
                }
                
                var mediaUploader = wp.media({
                    title: '<?php _e('Select Icon Image', 'eatisfamily'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'eatisfamily'); ?>'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    console.log('Image selected:', attachment.url);
                    $targetInput.val(attachment.url);
                    
                    if ($preview.length) {
                        $preview.attr('src', attachment.url).show();
                    } else {
                        $button.after('<img src="' + attachment.url + '" class="icon-preview" style="max-width:80px;max-height:80px;margin-top:10px;display:block;">');
                    }
                });
                
                mediaUploader.open();
            });
            
            // Initialize
            updateItemNumbers();
        });
    })(jQuery);
    </script>
    <?php
}

function eatisfamily_render_sustainability_row($index, $item) {
    $icon_url = $item['icone'] ?? '';
    $editor_id = 'sustainability_desc_' . $index;
    ?>
    <div class="sustainability-row">
        <span class="item-number"><?php echo is_numeric($index) ? $index + 1 : '?'; ?></span>
        <div class="form-field">
            <label><?php _e('Title', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_title[<?php echo $index; ?>]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="large-text">
        </div>
        <div class="form-field">
            <label><?php _e('Description', 'eatisfamily'); ?></label>
            <?php
            wp_editor(
                $item['description'] ?? '',
                $editor_id,
                array(
                    'textarea_name' => 'sustainability_item_description[' . $index . ']',
                    'textarea_rows' => 5,
                    'media_buttons' => false,
                    'teeny' => false,
                    'quicktags' => true,
                    'tinymce' => array(
                        'toolbar1' => 'bold,italic,underline,bullist,numlist,link,unlink',
                        'toolbar2' => '',
                    )
                )
            );
            ?>
        </div>
        <div class="form-field">
            <label><?php _e('Icon Image', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_icon[<?php echo $index; ?>]" id="sustainability_item_icon_<?php echo $index; ?>" value="<?php echo esc_attr($icon_url); ?>" class="regular-text">
            <button type="button" class="button sustainability-upload-btn" data-target="sustainability_item_icon_<?php echo $index; ?>"><?php _e('Select Image', 'eatisfamily'); ?></button>
            <?php if ($icon_url): ?>
                <img src="<?php echo esc_url($icon_url); ?>" class="icon-preview">
            <?php endif; ?>
        </div>
        <div class="form-field">
            <label><?php _e('Background SVG Path', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_bg[<?php echo $index; ?>]" value="<?php echo esc_attr($item['background'] ?? ''); ?>" class="regular-text" placeholder="/images/sus1.svg">
            <p class="description"><?php _e('Path to the background SVG image (e.g., /images/sus1.svg)', 'eatisfamily'); ?></p>
        </div>
        <p><span class="remove-sustainability"><?php _e('Remove Item', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * Render sustainability row template for new items (uses plain textarea for JS init)
 */
function eatisfamily_render_sustainability_row_template() {
    ?>
    <div class="sustainability-row">
        <span class="item-number">?</span>
        <div class="form-field">
            <label><?php _e('Title', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_title[{{INDEX}}]" value="" class="large-text">
        </div>
        <div class="form-field">
            <label><?php _e('Description', 'eatisfamily'); ?></label>
            <textarea name="sustainability_item_description[{{INDEX}}]" id="sustainability_desc_{{INDEX}}" rows="5" class="large-text sustainability-wysiwyg"></textarea>
        </div>
        <div class="form-field">
            <label><?php _e('Icon Image', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_icon[{{INDEX}}]" id="sustainability_item_icon_{{INDEX}}" value="" class="regular-text">
            <button type="button" class="button sustainability-upload-btn" data-target="sustainability_item_icon_{{INDEX}}"><?php _e('Select Image', 'eatisfamily'); ?></button>
        </div>
        <div class="form-field">
            <label><?php _e('Background SVG Path', 'eatisfamily'); ?></label>
            <input type="text" name="sustainability_item_bg[{{INDEX}}]" value="" class="regular-text" placeholder="/images/sus1.svg">
            <p class="description"><?php _e('Path to the background SVG image (e.g., /images/sus1.svg)', 'eatisfamily'); ?></p>
        </div>
        <p><span class="remove-sustainability"><?php _e('Remove Item', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * ============================================
 * GALLERY MANAGEMENT PAGE
 * ============================================
 */
function eatisfamily_gallery_page() {
    // Handle form submission
    if (isset($_POST['eatisfamily_gallery_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_gallery_nonce'], 'save_gallery')) {
        
        $gallery_data = array(
            'homepage' => array('images' => array()),
            'about_1' => array('images' => array()),
            'about_2' => array('images' => array()),
            'events' => array('images' => array())
        );
        
        // Process Homepage Gallery
        if (isset($_POST['homepage_gallery_src']) && is_array($_POST['homepage_gallery_src'])) {
            foreach ($_POST['homepage_gallery_src'] as $index => $src) {
                if (!empty($src)) {
                    $gallery_data['homepage']['images'][] = array(
                        'src' => esc_url_raw($src),
                        'alt' => sanitize_text_field($_POST['homepage_gallery_alt'][$index] ?? '')
                    );
                }
            }
        }
        
        // Process About Gallery 1
        if (isset($_POST['about1_gallery_src']) && is_array($_POST['about1_gallery_src'])) {
            foreach ($_POST['about1_gallery_src'] as $index => $src) {
                if (!empty($src)) {
                    $gallery_data['about_1']['images'][] = array(
                        'src' => esc_url_raw($src),
                        'alt' => sanitize_text_field($_POST['about1_gallery_alt'][$index] ?? '')
                    );
                }
            }
        }
        
        // Process About Gallery 2
        if (isset($_POST['about2_gallery_src']) && is_array($_POST['about2_gallery_src'])) {
            foreach ($_POST['about2_gallery_src'] as $index => $src) {
                if (!empty($src)) {
                    $gallery_data['about_2']['images'][] = array(
                        'src' => esc_url_raw($src),
                        'alt' => sanitize_text_field($_POST['about2_gallery_alt'][$index] ?? '')
                    );
                }
            }
        }
        
        // Process Events Gallery
        if (isset($_POST['events_gallery_src']) && is_array($_POST['events_gallery_src'])) {
            foreach ($_POST['events_gallery_src'] as $index => $src) {
                if (!empty($src)) {
                    $gallery_data['events']['images'][] = array(
                        'src' => esc_url_raw($src),
                        'alt' => sanitize_text_field($_POST['events_gallery_alt'][$index] ?? '')
                    );
                }
            }
        }
        
        update_option('eatisfamily_gallery', $gallery_data);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Galleries saved successfully!', 'eatisfamily') . '</p></div>';
    }
    
    // Get current values
    $gallery_data = get_option('eatisfamily_gallery', array());
    $homepage_images = $gallery_data['homepage']['images'] ?? $gallery_data['images'] ?? array();
    $about1_images = $gallery_data['about_1']['images'] ?? array();
    $about2_images = $gallery_data['about_2']['images'] ?? array();
    $events_images = $gallery_data['events']['images'] ?? array();
    
    ?>
    <div class="wrap">
        <h1><?php _e('🖼️ Galleries Management', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Manage gallery images for each page of the website. Drag and drop to reorder images.', 'eatisfamily'); ?></p>
        
        <style>
            .gallery-list {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 15px;
                min-height: 100px;
                padding: 10px;
                background: #f9f9f9;
                border: 2px dashed #ccd0d4;
                border-radius: 8px;
            }
            .gallery-row {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 15px;
                padding-top: 35px;
                border-radius: 4px;
                width: 200px;
                cursor: move;
                position: relative;
                transition: box-shadow 0.2s, transform 0.2s;
            }
            .gallery-row:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .gallery-row.ui-sortable-helper {
                box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                transform: rotate(2deg);
            }
            .gallery-row.ui-sortable-placeholder {
                visibility: visible !important;
                background: #e0f0ff;
                border: 2px dashed #0073aa;
            }
            .gallery-row .order-number {
                position: absolute;
                top: 5px;
                right: 5px;
                background: #0073aa;
                color: #fff;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }
            .gallery-row .move-buttons {
                position: absolute;
                top: 5px;
                left: 5px;
                display: flex;
                flex-direction: column;
                gap: 2px;
                z-index: 10;
            }
            .gallery-row .move-buttons .button {
                padding: 2px 6px;
                min-height: 24px;
                line-height: 1;
                font-size: 10px;
            }
            .gallery-row .move-buttons .button:hover {
                background: #0073aa;
                color: #fff;
                border-color: #0073aa;
            }
            .gallery-row img {
                max-width: 100%;
                height: 120px;
                object-fit: cover;
                display: block;
                margin-bottom: 10px;
                border-radius: 4px;
            }
            .gallery-buttons {
                display: flex;
                gap: 10px;
                margin-top: 20px;
                padding-top: 20px;
                clear: both;
            }
            .gallery-buttons .button-primary {
                background: #2271b1;
            }
            .remove-gallery {
                color: #d63638;
                cursor: pointer;
                text-decoration: underline;
                display: inline-block;
                padding: 5px 10px;
                background: #fff5f5;
                border: 1px solid #d63638;
                border-radius: 3px;
            }
            .remove-gallery:hover {
                color: #fff;
                background: #d63638;
            }
        </style>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_gallery', 'eatisfamily_gallery_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#homepage-gallery" class="nav-tab nav-tab-active"><?php _e('🏠 Homepage', 'eatisfamily'); ?></a>
                <a href="#about1-gallery" class="nav-tab"><?php _e('📖 About (Gallery 1)', 'eatisfamily'); ?></a>
                <a href="#about2-gallery" class="nav-tab"><?php _e('📖 About (Gallery 2)', 'eatisfamily'); ?></a>
                <a href="#events-gallery" class="nav-tab"><?php _e('🎉 Events', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- Homepage Gallery -->
            <div id="homepage-gallery" class="tab-content" style="display:block;margin-top:20px;">
                <h3><?php _e('Homepage Gallery', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Gallery displayed on the homepage. First image is larger on the left. Drag to reorder.', 'eatisfamily'); ?></p>
                
                <div id="homepage-gallery-list" class="gallery-list sortable-gallery" data-gallery="homepage">
                    <?php 
                    if (!empty($homepage_images)) {
                        foreach ($homepage_images as $index => $image) {
                            eatisfamily_render_gallery_row_new('homepage', $index, $image);
                        }
                    }
                    ?>
                </div>
                <div class="gallery-buttons">
                    <button type="button" class="button add-gallery-btn" data-gallery="homepage"><?php _e('+ Add Image', 'eatisfamily'); ?></button>
                    <button type="button" class="button button-primary add-multiple-btn" data-gallery="homepage"><?php _e('+ Add Multiple Images', 'eatisfamily'); ?></button>
                </div>
            </div>
            
            <!-- About Gallery 1 -->
            <div id="about1-gallery" class="tab-content" style="display:none;margin-top:20px;">
                <h3><?php _e('About Page - Gallery 1', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('First gallery on the About page (after Vision section). Drag to reorder.', 'eatisfamily'); ?></p>
                
                <div id="about1-gallery-list" class="gallery-list sortable-gallery" data-gallery="about1">
                    <?php 
                    if (!empty($about1_images)) {
                        foreach ($about1_images as $index => $image) {
                            eatisfamily_render_gallery_row_new('about1', $index, $image);
                        }
                    }
                    ?>
                </div>
                <div class="gallery-buttons">
                    <button type="button" class="button add-gallery-btn" data-gallery="about1"><?php _e('+ Add Image', 'eatisfamily'); ?></button>
                    <button type="button" class="button button-primary add-multiple-btn" data-gallery="about1"><?php _e('+ Add Multiple Images', 'eatisfamily'); ?></button>
                </div>
            </div>
            
            <!-- About Gallery 2 -->
            <div id="about2-gallery" class="tab-content" style="display:none;margin-top:20px;">
                <h3><?php _e('About Page - Gallery 2', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Second gallery on the About page (at the bottom). Drag to reorder.', 'eatisfamily'); ?></p>
                
                <div id="about2-gallery-list" class="gallery-list sortable-gallery" data-gallery="about2">
                    <?php 
                    if (!empty($about2_images)) {
                        foreach ($about2_images as $index => $image) {
                            eatisfamily_render_gallery_row_new('about2', $index, $image);
                        }
                    }
                    ?>
                </div>
                <div class="gallery-buttons">
                    <button type="button" class="button add-gallery-btn" data-gallery="about2"><?php _e('+ Add Image', 'eatisfamily'); ?></button>
                    <button type="button" class="button button-primary add-multiple-btn" data-gallery="about2"><?php _e('+ Add Multiple Images', 'eatisfamily'); ?></button>
                </div>
            </div>
            
            <!-- Events Gallery -->
            <div id="events-gallery" class="tab-content" style="display:none;margin-top:20px;">
                <h3><?php _e('Events Page Gallery', 'eatisfamily'); ?></h3>
                <p class="description"><?php _e('Gallery displayed on the Events page. Drag to reorder.', 'eatisfamily'); ?></p>
                
                <div id="events-gallery-list" class="gallery-list sortable-gallery" data-gallery="events">
                    <?php 
                    if (!empty($events_images)) {
                        foreach ($events_images as $index => $image) {
                            eatisfamily_render_gallery_row_new('events', $index, $image);
                        }
                    }
                    ?>
                </div>
                <div class="gallery-buttons">
                    <button type="button" class="button add-gallery-btn" data-gallery="events"><?php _e('+ Add Image', 'eatisfamily'); ?></button>
                    <button type="button" class="button button-primary add-multiple-btn" data-gallery="events"><?php _e('+ Add Multiple Images', 'eatisfamily'); ?></button>
                </div>
            </div>
            
            <?php submit_button(__('Save All Galleries', 'eatisfamily')); ?>
        </form>
    </div>
    
    <!-- Templates for each gallery -->
    <script type="text/html" id="homepage-gallery-template">
        <?php eatisfamily_render_gallery_row_new('homepage', '{{INDEX}}', array()); ?>
    </script>
    <script type="text/html" id="about1-gallery-template">
        <?php eatisfamily_render_gallery_row_new('about1', '{{INDEX}}', array()); ?>
    </script>
    <script type="text/html" id="about2-gallery-template">
        <?php eatisfamily_render_gallery_row_new('about2', '{{INDEX}}', array()); ?>
    </script>
    <script type="text/html" id="events-gallery-template">
        <?php eatisfamily_render_gallery_row_new('events', '{{INDEX}}', array()); ?>
    </script>
    
    <script type="text/javascript">
    (function($) {
        'use strict';
        
        // Wait for DOM to be fully ready
        $(window).on('load', function() {
            initGalleryAdmin();
        });
        
        // Also try on document ready as backup
        $(document).ready(function() {
            setTimeout(initGalleryAdmin, 1000);
        });
        
        var initialized = false;
        
        function initGalleryAdmin() {
            if (initialized) return;
            initialized = true;
            
            console.log('=== Gallery Admin Initializing ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('jQuery UI Sortable:', typeof $.fn.sortable);
            
            // Gallery indexes
            var galleryIndexes = {
                homepage: <?php echo count($homepage_images); ?>,
                about1: <?php echo count($about1_images); ?>,
                about2: <?php echo count($about2_images); ?>,
                events: <?php echo count($events_images); ?>
            };
            console.log('Gallery indexes:', galleryIndexes);
            
            // Tab navigation
            $(document).on('click', '.nav-tab', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                console.log('Tab clicked:', href);
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').hide();
                $(href).show();
            });
            
            // Update visual order numbers
            function updateOrderNumbers($gallery) {
                console.log('Updating order numbers for:', $gallery.attr('id'));
                $gallery.find('.gallery-row').each(function(index) {
                    $(this).find('.order-number').text(index + 1);
                });
            }
            
            // Reindex input names after sort
            function reindexGallery($gallery) {
                var galleryName = $gallery.data('gallery');
                var prefix = galleryName + '_gallery';
                console.log('Reindexing gallery:', galleryName);
                
                $gallery.find('.gallery-row').each(function(index) {
                    var $row = $(this);
                    $row.attr('data-index', index);
                    
                    var $srcInput = $row.find('.gallery-src-input');
                    $srcInput.attr('name', prefix + '_src[' + index + ']');
                    $srcInput.attr('id', prefix + '_src_' + index);
                    
                    $row.find('.eatisfamily-upload-media').attr('data-target', prefix + '_src_' + index);
                    
                    var $altInput = $row.find('.gallery-alt-input');
                    $altInput.attr('name', prefix + '_alt[' + index + ']');
                });
                
                galleryIndexes[galleryName] = $gallery.find('.gallery-row').length;
            }
            
            // REMOVE IMAGE - Using event delegation on document
            $(document).off('click.removeGallery').on('click.removeGallery', '.remove-gallery', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Remove clicked!');
                
                var $row = $(this).closest('.gallery-row');
                var $gallery = $row.closest('.sortable-gallery');
                
                if (confirm('<?php _e('Are you sure you want to remove this image?', 'eatisfamily'); ?>')) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        if ($gallery.length) {
                            updateOrderNumbers($gallery);
                            reindexGallery($gallery);
                        }
                    });
                }
            });
            
            // MOVE UP BUTTON
            $(document).off('click.moveUp').on('click.moveUp', '.move-up-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Move up clicked!');
                
                var $row = $(this).closest('.gallery-row');
                var $prev = $row.prev('.gallery-row');
                var $gallery = $row.closest('.sortable-gallery');
                
                if ($prev.length) {
                    $row.insertBefore($prev);
                    updateOrderNumbers($gallery);
                    reindexGallery($gallery);
                }
            });
            
            // MOVE DOWN BUTTON
            $(document).off('click.moveDown').on('click.moveDown', '.move-down-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Move down clicked!');
                
                var $row = $(this).closest('.gallery-row');
                var $next = $row.next('.gallery-row');
                var $gallery = $row.closest('.sortable-gallery');
                
                if ($next.length) {
                    $row.insertAfter($next);
                    updateOrderNumbers($gallery);
                    reindexGallery($gallery);
                }
            });
            
            // ADD SINGLE IMAGE BUTTON
            $(document).off('click.addGallery').on('click.addGallery', '.add-gallery-btn', function(e) {
                e.preventDefault();
                console.log('Add image clicked!');
                
                var gallery = $(this).data('gallery');
                var template = $('#' + gallery + '-gallery-template').html();
                template = template.replace(/\{\{INDEX\}\}/g, galleryIndexes[gallery]);
                
                var $newRow = $(template);
                $('#' + gallery + '-gallery-list').append($newRow);
                galleryIndexes[gallery]++;
                updateOrderNumbers($('#' + gallery + '-gallery-list'));
            });
            
            // ADD MULTIPLE IMAGES BUTTON
            $(document).off('click.addMultiple').on('click.addMultiple', '.add-multiple-btn', function(e) {
                e.preventDefault();
                console.log('Add multiple clicked!');
                
                var gallery = $(this).data('gallery');
                var $galleryList = $('#' + gallery + '-gallery-list');
                
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                    alert('Media uploader not available');
                    return;
                }
                
                var mediaUploader = wp.media({
                    title: '<?php _e('Select Images', 'eatisfamily'); ?>',
                    button: { text: '<?php _e('Add to Gallery', 'eatisfamily'); ?>' },
                    multiple: true,
                    library: { type: 'image' }
                });
                
                mediaUploader.on('select', function() {
                    var attachments = mediaUploader.state().get('selection').toJSON();
                    console.log('Selected', attachments.length, 'images');
                    
                    attachments.forEach(function(attachment) {
                        var template = $('#' + gallery + '-gallery-template').html();
                        template = template.replace(/\{\{INDEX\}\}/g, galleryIndexes[gallery]);
                        var $newRow = $(template);
                        
                        $newRow.find('.gallery-src-input').val(attachment.url);
                        $newRow.find('img').attr('src', attachment.url).show();
                        if (attachment.alt) {
                            $newRow.find('.gallery-alt-input').val(attachment.alt);
                        }
                        
                        $galleryList.append($newRow);
                        galleryIndexes[gallery]++;
                    });
                    
                    updateOrderNumbers($galleryList);
                });
                
                mediaUploader.open();
            });
            
            // MEDIA UPLOADER FOR SINGLE IMAGE
            $(document).off('click.uploadMedia').on('click.uploadMedia', '.eatisfamily-upload-media', function(e) {
                e.preventDefault();
                console.log('Upload media clicked!');
                
                var $button = $(this);
                var targetId = $button.data('target');
                var $targetInput = $('#' + targetId);
                
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                    alert('Media uploader not available');
                    return;
                }
                
                var mediaUploader = wp.media({
                    title: '<?php _e('Select Image', 'eatisfamily'); ?>',
                    button: { text: '<?php _e('Use this image', 'eatisfamily'); ?>' },
                    multiple: false,
                    library: { type: 'image' }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $targetInput.val(attachment.url);
                    
                    var $previewImg = $button.closest('.gallery-row').find('img');
                    if ($previewImg.length) {
                        $previewImg.attr('src', attachment.url).show();
                    }
                });
                
                mediaUploader.open();
            });
            
            // Initialize jQuery UI Sortable if available
            if (typeof $.fn.sortable !== 'undefined') {
                console.log('Initializing sortable...');
                $('.sortable-gallery').sortable({
                    items: '> .gallery-row',
                    cursor: 'move',
                    opacity: 0.7,
                    placeholder: 'gallery-row ui-sortable-placeholder',
                    update: function(event, ui) {
                        var $gallery = $(this);
                        updateOrderNumbers($gallery);
                        reindexGallery($gallery);
                    }
                });
                console.log('Sortable initialized');
            }
            
            console.log('=== Gallery Admin Ready ===');
        }
        
    })(jQuery);
    </script>
    <?php
}

function eatisfamily_render_gallery_row($index, $image) {
    $src = $image['src'] ?? '';
    ?>
    <div class="gallery-row" style="background:#fff;border:1px solid #ccd0d4;padding:15px;border-radius:4px;width:200px;">
        <div class="form-field">
            <input type="hidden" name="gallery_image_src[<?php echo $index; ?>]" id="gallery_image_src_<?php echo $index; ?>" value="<?php echo esc_attr($src); ?>">
            <?php if ($src): ?>
                <img src="<?php echo esc_url($src); ?>" style="max-width:100%;height:auto;display:block;margin-bottom:10px;">
            <?php endif; ?>
            <button type="button" class="button eatisfamily-upload-media" data-target="gallery_image_src_<?php echo $index; ?>" style="width:100%"><?php _e('Select Image', 'eatisfamily'); ?></button>
        </div>
        <div class="form-field" style="margin-top:10px;">
            <input type="text" name="gallery_image_alt[<?php echo $index; ?>]" value="<?php echo esc_attr($image['alt'] ?? ''); ?>" placeholder="<?php _e('Alt text', 'eatisfamily'); ?>" style="width:100%">
        </div>
        <p style="text-align:center;margin:10px 0 0;"><span class="remove-gallery remove-partner"><?php _e('Remove', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * Render a gallery row for new multi-gallery system
 */
function eatisfamily_render_gallery_row_new($gallery_name, $index, $image) {
    $src = $image['src'] ?? '';
    $field_prefix = $gallery_name . '_gallery';
    ?>
    <div class="gallery-row" data-index="<?php echo $index; ?>">
        <span class="order-number"><?php echo $index + 1; ?></span>
        <div class="move-buttons">
            <button type="button" class="button button-small move-up-btn" title="<?php _e('Move Up', 'eatisfamily'); ?>">▲</button>
            <button type="button" class="button button-small move-down-btn" title="<?php _e('Move Down', 'eatisfamily'); ?>">▼</button>
        </div>
        <div class="form-field">
            <input type="hidden" class="gallery-src-input" name="<?php echo $field_prefix; ?>_src[<?php echo $index; ?>]" id="<?php echo $field_prefix; ?>_src_<?php echo $index; ?>" value="<?php echo esc_attr($src); ?>">
            <?php if ($src): ?>
                <img src="<?php echo esc_url($src); ?>">
            <?php else: ?>
                <img src="" style="display:none;">
            <?php endif; ?>
            <button type="button" class="button eatisfamily-upload-media" data-target="<?php echo $field_prefix; ?>_src_<?php echo $index; ?>" style="width:100%"><?php _e('Select Image', 'eatisfamily'); ?></button>
        </div>
        <div class="form-field" style="margin-top:10px;">
            <input type="text" class="gallery-alt-input" name="<?php echo $field_prefix; ?>_alt[<?php echo $index; ?>]" value="<?php echo esc_attr($image['alt'] ?? ''); ?>" placeholder="<?php _e('Alt text', 'eatisfamily'); ?>" style="width:100%">
        </div>
        <p style="text-align:center;margin:10px 0 0;"><span class="remove-gallery"><?php _e('Remove', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * ============================================
 * EXTENDED PAGES CONTENT PAGE
 * ============================================
 */
function eatisfamily_pages_content_page_extended() {
    // Handle form submission
    if (isset($_POST['eatisfamily_pages_content_nonce']) && 
        wp_verify_nonce($_POST['eatisfamily_pages_content_nonce'], 'save_pages_content')) {
        
        $pages_content = array(
            'homepage' => array(
                'hero_section' => array(
                    'bg' => esc_url_raw($_POST['homepage_hero_bg'] ?? ''),
                    'title' => array(
                        'line_1' => wp_kses_post($_POST['homepage_hero_title_line1'] ?? ''),
                        'line_2' => wp_kses_post($_POST['homepage_hero_title_line2'] ?? ''),
                        'line_3' => wp_kses_post($_POST['homepage_hero_title_line3'] ?? '')
                    )
                ),
                'intro_section' => array(
                    'texte' => wp_kses_post($_POST['homepage_intro_text'] ?? '')
                ),
                'cta_section' => array(
                    'title' => sanitize_text_field($_POST['homepage_cta_title'] ?? ''),
                    'description' => wp_kses_post($_POST['homepage_cta_description'] ?? '')
                ),
                'beautiful' => array(
                    'title' => wp_kses_post($_POST['homepage_beautiful_title'] ?? ''),
                    'text' => wp_kses_post($_POST['homepage_beautiful_text'] ?? ''),
                    'image' => esc_url_raw($_POST['homepage_beautiful_image'] ?? '')
                ),
                'examples' => array(),
                'homepageCTA' => array(
                    'title' => sanitize_text_field($_POST['homepage_final_cta_title'] ?? ''),
                    'description' => wp_kses_post($_POST['homepage_final_cta_description'] ?? ''),
                    'image' => esc_url_raw($_POST['homepage_final_cta_image'] ?? ''),
                    'link' => sanitize_text_field($_POST['homepage_final_cta_link'] ?? ''),
                    'button' => esc_url_raw($_POST['homepage_final_cta_button'] ?? ''),
                    'additionalText' => wp_kses_post($_POST['homepage_final_cta_additional'] ?? ''),
                    'button2' => esc_url_raw($_POST['homepage_final_cta_button2'] ?? ''),
                    'link2' => sanitize_text_field($_POST['homepage_final_cta_link2'] ?? '')
                )
            ),
            'about' => array(
                'hero' => array(
                    'title' => sanitize_text_field($_POST['about_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['about_hero_subtitle'] ?? ''),
                    'description' => wp_kses_post($_POST['about_hero_description'] ?? ''),
                    'background_image' => esc_url_raw($_POST['about_hero_bg'] ?? '')
                ),
                'intro_section' => array(
                    'title' => sanitize_text_field($_POST['about_intro_title'] ?? ''),
                    'content' => wp_kses_post($_POST['about_intro_content'] ?? '')
                ),
                'timeline_title' => sanitize_text_field($_POST['about_timeline_title'] ?? 'Our History')
            ),
            'contact' => array(
                'hero_section' => array(
                    'title' => array(
                        'line_1' => sanitize_text_field($_POST['contact_hero_line1'] ?? ''),
                        'line_1_highlight' => sanitize_text_field($_POST['contact_hero_line1_highlight'] ?? ''),
                        'line_2' => sanitize_text_field($_POST['contact_hero_line2'] ?? ''),
                        'line_3' => sanitize_text_field($_POST['contact_hero_line3'] ?? ''),
                        'line_3_highlight' => sanitize_text_field($_POST['contact_hero_line3_highlight'] ?? '')
                    ),
                    'description' => wp_kses_post($_POST['contact_hero_description'] ?? '')
                ),
                'form' => array(
                    'name_placeholder' => sanitize_text_field($_POST['contact_form_name'] ?? ''),
                    'email_placeholder' => sanitize_text_field($_POST['contact_form_email'] ?? ''),
                    'event_type_placeholder' => sanitize_text_field($_POST['contact_form_event'] ?? ''),
                    'location_placeholder' => sanitize_text_field($_POST['contact_form_location'] ?? ''),
                    'date_placeholder' => sanitize_text_field($_POST['contact_form_date'] ?? ''),
                    'guests_placeholder' => sanitize_text_field($_POST['contact_form_guests'] ?? ''),
                    'message_placeholder' => sanitize_text_field($_POST['contact_form_message'] ?? ''),
                    'submit_button' => sanitize_text_field($_POST['contact_form_submit'] ?? '')
                )
            ),
            'careers' => array(
                'hero_default' => array(
                    'title_line_1' => sanitize_text_field($_POST['careers_hero_line1'] ?? ''),
                    'title_line_2' => sanitize_text_field($_POST['careers_hero_line2'] ?? ''),
                    'image' => esc_url_raw($_POST['careers_hero_image'] ?? ''),
                    'background_image' => esc_url_raw($_POST['careers_hero_bg'] ?? '')
                ),
                'join_box' => array(
                    'title' => sanitize_text_field($_POST['careers_join_title'] ?? ''),
                    'description' => wp_kses_post($_POST['careers_join_description'] ?? '')
                ),
                'search_section' => array(
                    'search_placeholder' => sanitize_text_field($_POST['careers_search_placeholder'] ?? ''),
                    'all_sites_label' => sanitize_text_field($_POST['careers_all_sites'] ?? ''),
                    'job_types' => array_filter(array_map('sanitize_text_field', explode("\n", $_POST['careers_job_types'] ?? '')))
                ),
                'cta_section' => array(
                    'title' => sanitize_text_field($_POST['careers_cta_title'] ?? ''),
                    'description' => wp_kses_post($_POST['careers_cta_description'] ?? '')
                )
            ),
            'events' => array(
                'hero_section' => array(
                    'title' => sanitize_text_field($_POST['events_hero_title'] ?? ''),
                    'subtitle' => sanitize_text_field($_POST['events_hero_subtitle'] ?? '')
                )
            )
        );
        
        // Process examples
        if (isset($_POST['example_title']) && is_array($_POST['example_title'])) {
            foreach ($_POST['example_title'] as $index => $title) {
                if (!empty($title)) {
                    $pages_content['homepage']['examples'][] = array(
                        'title' => wp_kses_post($title),
                        'texte' => wp_kses_post($_POST['example_text'][$index] ?? ''),
                        'btn' => esc_url_raw($_POST['example_btn'][$index] ?? ''),
                        'link' => sanitize_text_field($_POST['example_link'][$index] ?? '')
                    );
                }
            }
        }
        
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
    
    ?>
    <div class="wrap">
        <h1><?php _e('Pages Content Settings', 'eatisfamily'); ?></h1>
        <p class="description"><?php _e('Edit content for each page of your website.', 'eatisfamily'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_pages_content', 'eatisfamily_pages_content_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#homepage" class="nav-tab nav-tab-active"><?php _e('Homepage', 'eatisfamily'); ?></a>
                <a href="#about" class="nav-tab"><?php _e('About', 'eatisfamily'); ?></a>
                <a href="#contact" class="nav-tab"><?php _e('Contact', 'eatisfamily'); ?></a>
                <a href="#careers" class="nav-tab"><?php _e('Careers', 'eatisfamily'); ?></a>
                <a href="#events" class="nav-tab"><?php _e('Events', 'eatisfamily'); ?></a>
            </h2>
            
            <!-- Homepage Tab -->
            <div id="homepage" class="tab-content" style="display: block;">
                <h3><?php _e('Hero Section', 'eatisfamily'); ?></h3>
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
                        <td>
                            <textarea name="homepage_hero_title_line1" id="homepage_hero_title_line1" class="large-text" rows="2"><?php echo esc_textarea($homepage['hero_section']['title']['line_1'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('Use \n for line breaks', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_hero_title_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_hero_title_line2" id="homepage_hero_title_line2" class="large-text" rows="2"><?php echo esc_textarea($homepage['hero_section']['title']['line_2'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_hero_title_line3"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_hero_title_line3" id="homepage_hero_title_line3" class="large-text" rows="3"><?php echo esc_textarea($homepage['hero_section']['title']['line_3'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Intro Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="homepage_intro_text"><?php _e('Intro Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <?php 
                            wp_editor($homepage['intro_section']['texte'] ?? '', 'homepage_intro_text', array(
                                'textarea_name' => 'homepage_intro_text',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                            ));
                            ?>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('CTA Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="homepage_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_cta_title" id="homepage_cta_title" value="<?php echo esc_attr($homepage['cta_section']['title'] ?? ''); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_cta_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                        <td>
                            <?php 
                            wp_editor($homepage['cta_section']['description'] ?? '', 'homepage_cta_description', array(
                                'textarea_name' => 'homepage_cta_description',
                                'textarea_rows' => 4,
                                'media_buttons' => false,
                            ));
                            ?>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Beautiful Moments Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="homepage_beautiful_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_beautiful_title" id="homepage_beautiful_title" class="large-text" rows="2"><?php echo esc_textarea($homepage['beautiful']['title'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_beautiful_text"><?php _e('Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_beautiful_text" id="homepage_beautiful_text" class="large-text" rows="4"><?php echo esc_textarea($homepage['beautiful']['text'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_beautiful_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_beautiful_image" id="homepage_beautiful_image" value="<?php echo esc_attr($homepage['beautiful']['image'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="homepage_beautiful_image"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Examples (Concession & Events)', 'eatisfamily'); ?></h3>
                <div id="examples-list" class="eatisfamily-repeater">
                    <?php 
                    $examples = $homepage['examples'] ?? array();
                    if (!empty($examples)) {
                        foreach ($examples as $index => $example) {
                            eatisfamily_render_example_row($index, $example);
                        }
                    } else {
                        eatisfamily_render_example_row(0, array());
                    }
                    ?>
                </div>
                <p>
                    <button type="button" class="button" id="add-example"><?php _e('+ Add Example', 'eatisfamily'); ?></button>
                </p>
                
                <h3><?php _e('Final CTA Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_title" id="homepage_final_cta_title" value="<?php echo esc_attr($homepage['homepageCTA']['title'] ?? ''); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_image"><?php _e('Image', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_image" id="homepage_final_cta_image" value="<?php echo esc_attr($homepage['homepageCTA']['image'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="homepage_final_cta_image"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_final_cta_description" id="homepage_final_cta_description" class="large-text" rows="3"><?php echo esc_textarea($homepage['homepageCTA']['description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_link"><?php _e('Button 1 Link', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_link" id="homepage_final_cta_link" value="<?php echo esc_attr($homepage['homepageCTA']['link'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_button"><?php _e('Button 1 Image', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_button" id="homepage_final_cta_button" value="<?php echo esc_attr($homepage['homepageCTA']['button'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="homepage_final_cta_button"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_additional"><?php _e('Additional Text', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="homepage_final_cta_additional" id="homepage_final_cta_additional" class="large-text" rows="4"><?php echo esc_textarea($homepage['homepageCTA']['additionalText'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_link2"><?php _e('Button 2 Link', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_link2" id="homepage_final_cta_link2" value="<?php echo esc_attr($homepage['homepageCTA']['link2'] ?? ''); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="homepage_final_cta_button2"><?php _e('Button 2 Image', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="homepage_final_cta_button2" id="homepage_final_cta_button2" value="<?php echo esc_attr($homepage['homepageCTA']['button2'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="homepage_final_cta_button2"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- About Tab -->
            <div id="about" class="tab-content" style="display: none;">
                <h3><?php _e('Hero Section', 'eatisfamily'); ?></h3>
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
                        <td><textarea name="about_hero_description" id="about_hero_description" class="large-text" rows="3"><?php echo esc_textarea($about['hero']['description'] ?? ''); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="about_hero_bg"><?php _e('Background Image', 'eatisfamily'); ?></label></th>
                        <td>
                            <input type="text" name="about_hero_bg" id="about_hero_bg" value="<?php echo esc_attr($about['hero']['background_image'] ?? ''); ?>" class="regular-text">
                            <button type="button" class="button eatisfamily-upload-media" data-target="about_hero_bg"><?php _e('Select', 'eatisfamily'); ?></button>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Intro Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="about_intro_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="about_intro_title" id="about_intro_title" value="<?php echo esc_attr($about['intro_section']['title'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="about_intro_content"><?php _e('Content', 'eatisfamily'); ?></label></th>
                        <td>
                            <?php 
                            wp_editor($about['intro_section']['content'] ?? '', 'about_intro_content', array(
                                'textarea_name' => 'about_intro_content',
                                'textarea_rows' => 8,
                                'media_buttons' => true,
                            ));
                            ?>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Timeline', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="about_timeline_title"><?php _e('Timeline Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="about_timeline_title" id="about_timeline_title" value="<?php echo esc_attr($about['timeline_title'] ?? 'Our History'); ?>" class="large-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- Contact Tab -->
            <div id="contact" class="tab-content" style="display: none;">
                <h3><?php _e('Hero Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="contact_hero_line1"><?php _e('Line 1', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_hero_line1" id="contact_hero_line1" value="<?php echo esc_attr($contact['hero_section']['title']['line_1'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_hero_line1_highlight"><?php _e('Line 1 Highlight', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_hero_line1_highlight" id="contact_hero_line1_highlight" value="<?php echo esc_attr($contact['hero_section']['title']['line_1_highlight'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_hero_line2"><?php _e('Line 2', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_hero_line2" id="contact_hero_line2" value="<?php echo esc_attr($contact['hero_section']['title']['line_2'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_hero_line3"><?php _e('Line 3', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_hero_line3" id="contact_hero_line3" value="<?php echo esc_attr($contact['hero_section']['title']['line_3'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_hero_line3_highlight"><?php _e('Line 3 Highlight', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_hero_line3_highlight" id="contact_hero_line3_highlight" value="<?php echo esc_attr($contact['hero_section']['title']['line_3_highlight'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_hero_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                        <td><textarea name="contact_hero_description" id="contact_hero_description" class="large-text" rows="3"><?php echo esc_textarea($contact['hero_section']['description'] ?? ''); ?></textarea></td>
                    </tr>
                </table>
                
                <h3><?php _e('Form Placeholders', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="contact_form_name"><?php _e('Name', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_name" id="contact_form_name" value="<?php echo esc_attr($contact['form']['name_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_email"><?php _e('Email', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_email" id="contact_form_email" value="<?php echo esc_attr($contact['form']['email_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_event"><?php _e('Event Type', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_event" id="contact_form_event" value="<?php echo esc_attr($contact['form']['event_type_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_location"><?php _e('Location', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_location" id="contact_form_location" value="<?php echo esc_attr($contact['form']['location_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_date"><?php _e('Date', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_date" id="contact_form_date" value="<?php echo esc_attr($contact['form']['date_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_guests"><?php _e('Guests', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_guests" id="contact_form_guests" value="<?php echo esc_attr($contact['form']['guests_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_message"><?php _e('Message', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_message" id="contact_form_message" value="<?php echo esc_attr($contact['form']['message_placeholder'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="contact_form_submit"><?php _e('Submit Button', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="contact_form_submit" id="contact_form_submit" value="<?php echo esc_attr($contact['form']['submit_button'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- Careers Tab -->
            <div id="careers" class="tab-content" style="display: none;">
                <h3><?php _e('Hero Section (Default)', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="careers_hero_line1"><?php _e('Title Line 1', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="careers_hero_line1" id="careers_hero_line1" value="<?php echo esc_attr($careers['hero_default']['title_line_1'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="careers_hero_line2"><?php _e('Title Line 2', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="careers_hero_line2" id="careers_hero_line2" value="<?php echo esc_attr($careers['hero_default']['title_line_2'] ?? ''); ?>" class="large-text"></td>
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
                
                <h3><?php _e('Join Box', 'eatisfamily'); ?></h3>
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
                
                <h3><?php _e('Search Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="careers_search_placeholder"><?php _e('Search Placeholder', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="careers_search_placeholder" id="careers_search_placeholder" value="<?php echo esc_attr($careers['search_section']['search_placeholder'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="careers_all_sites"><?php _e('All Sites Label', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="careers_all_sites" id="careers_all_sites" value="<?php echo esc_attr($careers['search_section']['all_sites_label'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="careers_job_types"><?php _e('Job Types', 'eatisfamily'); ?></label></th>
                        <td>
                            <textarea name="careers_job_types" id="careers_job_types" class="large-text" rows="5"><?php echo esc_textarea(implode("\n", $careers['search_section']['job_types'] ?? array())); ?></textarea>
                            <p class="description"><?php _e('One job type per line', 'eatisfamily'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('CTA Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="careers_cta_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="careers_cta_title" id="careers_cta_title" value="<?php echo esc_attr($careers['cta_section']['title'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="careers_cta_description"><?php _e('Description', 'eatisfamily'); ?></label></th>
                        <td><textarea name="careers_cta_description" id="careers_cta_description" class="large-text" rows="3"><?php echo esc_textarea($careers['cta_section']['description'] ?? ''); ?></textarea></td>
                    </tr>
                </table>
            </div>
            
            <!-- Events Tab -->
            <div id="events" class="tab-content" style="display: none;">
                <h3><?php _e('Hero Section', 'eatisfamily'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="events_hero_title"><?php _e('Title', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="events_hero_title" id="events_hero_title" value="<?php echo esc_attr($events['hero_section']['title'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="events_hero_subtitle"><?php _e('Subtitle', 'eatisfamily'); ?></label></th>
                        <td><input type="text" name="events_hero_subtitle" id="events_hero_subtitle" value="<?php echo esc_attr($events['hero_section']['subtitle'] ?? ''); ?>" class="large-text"></td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(__('Save All Pages Content', 'eatisfamily')); ?>
        </form>
    </div>
    
    <script type="text/html" id="example-row-template">
        <?php eatisfamily_render_example_row('{{INDEX}}', array()); ?>
    </script>
    
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
        
        // Examples repeater
        var exampleIndex = <?php echo count($homepage['examples'] ?? array()); ?>;
        
        $('#add-example').on('click', function() {
            var template = $('#example-row-template').html();
            template = template.replace(/\{\{INDEX\}\}/g, exampleIndex);
            $('#examples-list').append(template);
            exampleIndex++;
        });
        
        $(document).on('click', '.remove-example', function() {
            $(this).closest('.example-row').remove();
        });
        
        // Media uploader
        $(document).on('click', '.eatisfamily-upload-media', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            
            var mediaUploader = wp.media({
                title: '<?php _e('Select Image', 'eatisfamily'); ?>',
                button: { text: '<?php _e('Use this image', 'eatisfamily'); ?>' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetField).val(attachment.url);
            });
            
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

function eatisfamily_render_example_row($index, $example) {
    ?>
    <div class="example-row partner-row">
        <div class="form-field">
            <label><?php _e('Title', 'eatisfamily'); ?></label>
            <textarea name="example_title[<?php echo $index; ?>]" rows="2" style="width:100%"><?php echo esc_textarea($example['title'] ?? ''); ?></textarea>
        </div>
        <div class="form-field">
            <label><?php _e('Text', 'eatisfamily'); ?></label>
            <textarea name="example_text[<?php echo $index; ?>]" rows="4" style="width:100%"><?php echo esc_textarea($example['texte'] ?? ''); ?></textarea>
        </div>
        <div class="form-field">
            <label><?php _e('Button Image', 'eatisfamily'); ?></label>
            <input type="text" name="example_btn[<?php echo $index; ?>]" id="example_btn_<?php echo $index; ?>" value="<?php echo esc_attr($example['btn'] ?? ''); ?>">
            <button type="button" class="button eatisfamily-upload-media" data-target="example_btn_<?php echo $index; ?>"><?php _e('Select', 'eatisfamily'); ?></button>
        </div>
        <div class="form-field">
            <label><?php _e('Link', 'eatisfamily'); ?></label>
            <input type="text" name="example_link[<?php echo $index; ?>]" value="<?php echo esc_attr($example['link'] ?? ''); ?>">
        </div>
        <p><span class="remove-example remove-partner"><?php _e('Remove', 'eatisfamily'); ?></span></p>
    </div>
    <?php
}

/**
 * Enqueue admin scripts for media uploader and sortable
 */
function eatisfamily_admin_scripts_extended($hook) {
    if (strpos($hook, 'eatisfamily') !== false) {
        wp_enqueue_media();
        wp_enqueue_editor();
        
        // Enqueue jQuery UI Sortable for drag & drop galleries
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-sortable');
        
        // Enqueue dashicons for drag handle
        wp_enqueue_style('dashicons');
    }
}
add_action('admin_enqueue_scripts', 'eatisfamily_admin_scripts_extended');
