<?php
/*
Plugin Name: Custom Elementor Widget
Description: A plugin to create a frontend form using Elementor and save the data in a custom post type 'submission'.
Version: 1.0
Author: Usman Shaikh
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}



function register_custom_form_widget($widgets_manager) {
     require_once( __DIR__ . '/widgets/custom-form-widget.php' );
    $widgets_manager->register( new \Custom_Form_Widget() );
}
add_action('elementor/widgets/register', 'register_custom_form_widget');

function register_sunrise_sunset_widget( $widgets_manager ) {
    require_once( __DIR__ . '/widgets/sunrise-sunset-widget.php' );
    $widgets_manager->register( new \Sunrise_Sunset_Widget() );
}
add_action( 'elementor/widgets/register', 'register_sunrise_sunset_widget' );


// Enqueue scripts and styles
function custom_contact_form_enqueue_scripts() {
    // Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
	wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-validation', 'https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js', array('jquery'), null, true);
    
    wp_enqueue_script('custom-contact-form-js', plugin_dir_url(__FILE__) . 'custom-contact-form.js', array('jquery'), null, true);

    // Localize the script with new data
    wp_localize_script('custom-contact-form-js', 'customForm', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'custom_contact_form_enqueue_scripts');

// Register the custom post type
function create_submission_post_type() {
    $labels = array(
        'name'               => _x('Submissions', 'post type general name', 'textdomain'),
        'singular_name'      => _x('Submission', 'post type singular name', 'textdomain'),
        'menu_name'          => _x('Submissions', 'admin menu', 'textdomain'),
        'name_admin_bar'     => _x('Submission', 'add new on admin bar', 'textdomain'),
        'add_new'            => _x('Add New', 'submission', 'textdomain'),
        'add_new_item'       => __('Add New Submission', 'textdomain'),
        'new_item'           => __('New Submission', 'textdomain'),
        'edit_item'          => __('Edit Submission', 'textdomain'),
        'view_item'          => __('View Submission', 'textdomain'),
        'all_items'          => __('All Submissions', 'textdomain'),
        'search_items'       => __('Search Submissions', 'textdomain'),
        'parent_item_colon'  => __('Parent Submissions:', 'textdomain'),
        'not_found'          => __('No submissions found.', 'textdomain'),
        'not_found_in_trash' => __('No submissions found in Trash.', 'textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'submission'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('submission', $args);
}
add_action('init', 'create_submission_post_type');

// Register Custom Taxonomy
function create_submission_taxonomy() {

    $labels = array(
        'name' => _x('Submission Categories', 'Taxonomy General Name', 'textdomain'),
        'singular_name' => _x('Submission Category', 'Taxonomy Singular Name', 'textdomain'),
        'menu_name' => __('Submission Categories', 'textdomain'),
        'all_items' => __('All Categories', 'textdomain'),
        'parent_item' => __('Parent Category', 'textdomain'),
        'parent_item_colon' => __('Parent Category:', 'textdomain'),
        'new_item_name' => __('New Category Name', 'textdomain'),
        'add_new_item' => __('Add New Category', 'textdomain'),
        'edit_item' => __('Edit Category', 'textdomain'),
        'update_item' => __('Update Category', 'textdomain'),
        'view_item' => __('View Category', 'textdomain'),
        'separate_items_with_commas' => __('Separate categories with commas', 'textdomain'),
        'add_or_remove_items' => __('Add or remove categories', 'textdomain'),
        'choose_from_most_used' => __('Choose from the most used', 'textdomain'),
        'popular_items' => __('Popular Categories', 'textdomain'),
        'search_items' => __('Search Categories', 'textdomain'),
        'not_found' => __('Not Found', 'textdomain'),
        'no_terms' => __('No categories', 'textdomain'),
        'items_list' => __('Categories list', 'textdomain'),
        'items_list_navigation' => __('Categories list navigation', 'textdomain'),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('submission_category', array('submission'), $args);

}
add_action('init', 'create_submission_taxonomy', 0);

// Create shortcode for contact form
function custom_contact_form_shortcode() {
    ob_start(); ?>
    <form id="custom-submission-form" class="p-3">
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text" class="form-control" id="title" name="title" required>
		</div>
		 <div class="form-group">
            <label for="content">Content</label>
            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
        </div>
		<div class="form-group">
            <label for="featured_image">Featured Image</label>
            <input type="file" class="form-control" id="featured_image" name="featured_image">
        </div>
		<div class="form-group">
            <label for="taxonomy">Category</label>
            <?php
            $terms = get_terms(array(
                'taxonomy' => 'submission_category',
                'hide_empty' => false,
            ));
            if (!empty($terms) && !is_wp_error($terms)) {
                echo '<select class="form-control" id="taxonomy" name="taxonomy" required>';
                foreach ($terms as $term) {
                    echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                }
                echo '</select>';
            }
            ?>
        </div>
		<div class="form-group">
			<label for="author_name">Author Name</label>
			<input type="text" class="form-control" id="author_name" name="author_name" required>
		</div>
		<div class="form-group">
			<label for="author_email">Author Email</label>
			<input type="email" class="form-control" id="author_email" name="author_email" required>
		</div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div id="form-message" class="mt-3"></div>
    <?php
    return ob_get_clean();
}
//add_shortcode('custom_contact_form', 'custom_contact_form_shortcode');

// Handle form submission
function handle_custom_submission_form() {
    if (!isset($_POST['title'], $_POST['content'], $_POST['author_name'], $_POST['author_email'])) {
        wp_send_json_error(array('message' => 'All fields are required.'));
    }

    $title = sanitize_text_field($_POST['title']);
    $content = sanitize_textarea_field($_POST['content']);
    $author_name = sanitize_text_field($_POST['author_name']);
    $author_email = sanitize_email($_POST['author_email']);
    $taxonomy = isset($_POST['taxonomy']) ? (int) $_POST['taxonomy'] : 0;

    // Create new post
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_type' => 'submission',
        'meta_input' => array(
            'author_name' => $author_name,
            'author_email' => $author_email,
        ),
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Failed to create submission.'));
    }

    // Set taxonomy term
    if ($taxonomy) {
        wp_set_post_terms($post_id, array($taxonomy), 'submission_category');
    }

    // Handle featured image
    if (!empty($_FILES['featured_image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $file = $_FILES['featured_image'];
        $allowed_types = array('image/jpeg', 'image/png');

        // Check file type
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => 'Only JPG and PNG files are allowed.'));
        }

        $upload = wp_handle_upload($file, array('test_form' => false));

        if (isset($upload['file'])) {
            $attachment_id = wp_insert_attachment(array(
                'post_mime_type' => $upload['type'],
                'post_title' => sanitize_file_name($upload['file']),
                'post_content' => '',
                'post_status' => 'inherit'
            ), $upload['file'], $post_id);

            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }

    wp_send_json_success(array('message' => 'Submission successfully received.'));
}
add_action('wp_ajax_submit_custom_submission_form', 'handle_custom_submission_form');
add_action('wp_ajax_nopriv_submit_custom_submission_form', 'handle_custom_submission_form');


// Add a meta box to the submission post type
function add_submission_meta_boxes() {
    add_meta_box(
        'submission_meta_box',
        'Submission Details',
        'render_submission_meta_box',
        'submission',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_submission_meta_boxes');

function render_submission_meta_box($post) {
    // Retrieve current values for author_name and author_email
    $author_name = get_post_meta($post->ID, 'author_name', true);
    $author_email = get_post_meta($post->ID, 'author_email', true);
    
    // Display the form fields
    ?>
    <label for="author_name">Author Name:</label>
    <input type="text" id="author_name" name="author_name" value="<?php echo esc_attr($author_name); ?>" size="40" />
    <br><br>
    <label for="author_email">Author Email:</label>
    <input type="email" id="author_email" name="author_email" value="<?php echo esc_attr($author_email); ?>" size="40" />
    <?php
}

function save_submission_meta($post_id) {
    // Check if the fields are set and save their values
    if (isset($_POST['author_name'])) {
        update_post_meta($post_id, 'author_name', sanitize_text_field($_POST['author_name']));
    }
    if (isset($_POST['author_email'])) {
        update_post_meta($post_id, 'author_email', sanitize_email($_POST['author_email']));
    }
}
add_action('save_post', 'save_submission_meta');

function add_help_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=submission', // Parent slug
        __('Help', 'textdomain'),        // Page title
        __('Help', 'textdomain'),        // Menu title
        'manage_options',                // Capability
        'submission-help',               // Menu slug
        'render_help_page'               // Callback function
    );
}
add_action('admin_menu', 'add_help_submenu_page');

function render_help_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Submission Help', 'textdomain'); ?></h1>
        <p><?php _e('Here you can find documentation and help for managing Submissions.', 'textdomain'); ?></p>
        <h2><?php _e('Getting Started', 'textdomain'); ?></h2>
        <p><?php _e('This is custom contact form with elementor, created custom post type with taxonony and submit all entry in Submission Post Type.', 'textdomain'); ?></p>
        <h2><?php _e('Steps', 'textdomain'); ?></h2>
        <p><?php _e('1) Download Plugin from GitHub', 'textdomain'); ?></p>
		<p><?php _e('2) Upload Plugin', 'textdomain'); ?></p>
		<p><?php _e('3) Go to WP Dashboard, Check left bar Submission, Add Submission Categories (career form, contact form, etc).', 'textdomain'); ?></p>
		<p><?php _e('4) Create New Page in Elementor and Edit mode as Elementor, search Custom Widget Form and drop in Page Content.', 'textdomain'); ?></p>
		<p><?php _e('5) Page Publish', 'textdomain'); ?></p>
		<p><?php _e('6) Check Public Page, form willbe display.', 'textdomain'); ?></p>
		<p><?php _e('7) Submit test entry', 'textdomain'); ?></p>
		<p><?php _e('8) check entry in Dashboard > Submission', 'textdomain'); ?></p>
        <!-- Add more sections as needed -->
    </div>
    <?php
}


