<?php
    function pageBanner($args = NULL){
        // php logic will live here
        if (!isset($args['title'])) {
            $args['title'] = get_the_title();
          }
          if (!isset($args['subtitle'])) {
            $args['subtitle'] = get_field('page_banner_subtitle');
          }
          if (!isset($args['photo'])) {
            if (get_field('page_banner_background_image')) {
              $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else {
              $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
          }
        ?>

            <div class="page-banner">
                <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
                <div class="page-banner__content container container--narrow">
                    <h1 class="page-banner__title"><?php echo $args['title']?></h1>
                    <div class="page-banner__intro">
                    <p><?php echo $args['subtitle'] ?></p>
                    </div>
                </div>
            </div>
        <?php
    }
    function university_files()  {
        wp_enqueue_style("font_awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
        wp_enqueue_style("google_fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
        wp_enqueue_style('university_main_styles',get_theme_file_uri('build/style-index.css'));
        wp_enqueue_style('university_extra_styles',get_theme_file_uri('build/index.css'));
        wp_enqueue_script("main_university_js", get_theme_file_uri("build/index.js"), array("jquery"), "1.0", true);
    }
    add_action('wp_enqueue_scripts','university_files');

    function university_feater(){
        register_nav_menu('headerMenuLocation','Header Menu Location');
        register_nav_menu('footerLocationOne','Footer Location One');
        register_nav_menu('footerLocationTwo','Footer Location Two');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_image_size('professorLandscape',400,260,true);
        add_image_size('professorPortrait',280,450,true);
        add_image_size('pageBanner',1500,350,true);
    }
    add_action('after_setup_theme', 'university_feater');

    function university_post_types(){
        // Event Post  Type
        register_post_type('event', array(
            'rewrite' => array('slug' => 'event'),
            'supports' => array('title','editor','excerpt','custom-fields'),
            'has_archive' => true,
            'public' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Events',
                'add_new_item' => 'Add New Event',
                'edit_item' => 'Edit Event',
                'all_items' => 'All Events',
                'singular_name' => 'Event'
            ),
            'menu_icon' => 'dashicons-calendar'
        ));

        // Program Post Type
        register_post_type('program', array(
            'rewrite' => array('slug' => 'programs'),
            'supports' => array('title','editor'),
            'has_archive' => true,
            'public' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Programs',
                'add_new_item' => 'Add New Program',
                'edit_item' => 'Edit Program',
                'all_items' => 'All Programs',
                'singular_name' => 'Program'
            ),
            'menu_icon' => 'dashicons-awards'
        ));

        // Professor Post Type
        register_post_type('professor', array(
            'supports' => array('title','editor','thumbnail'),
            'public' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Professors',
                'add_new_item' => 'Add New Professor',
                'edit_item' => 'Edit Professor',
                'all_items' => 'All Professors',
                'singular_name' => 'Professor'
            ),
            'menu_icon' => 'dashicons-welcome-learn-more'
        ));
    }
    add_action('init','university_post_types');

    function add_event_archive_settings() {
        add_submenu_page(
            'edit.php?post_type=event', // Attach to "Event" menu
            'Archive Description', // Page title
            'Archive Description', // Menu name
            'manage_options', // Required capability
            'event-archive-settings', // Page slug
            'event_archive_settings_page' // Callback function
        );
    }
    add_action('admin_menu', 'add_event_archive_settings');
    
    function event_archive_settings_page() {
        ?>
        <div class="wrap">
            <h1>Archive Event Description</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('event_archive_settings');
                do_settings_sections('event_archive_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    function register_event_archive_settings() {
        register_setting('event_archive_settings', 'event_archive_description');
        add_settings_section('event_archive_section', '', null, 'event_archive_settings');
    
        add_settings_field(
            'event_archive_description',
            'Archive Page Description:',
            function () {
                $value = get_option('event_archive_description', '');
                echo '<textarea name="event_archive_description" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
            },
            'event_archive_settings',
            'event_archive_section'
        );
    }
    add_action('admin_init', 'register_event_archive_settings');
    
    function add_custom_fields_metabox() {
        add_meta_box(
            'custom_fields_box',
            'Custom Fields',
            'custom_fields_callback',
            'event', // Change to your post type
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_custom_fields_metabox');
    
    function custom_fields_callback($post) {
        wp_nonce_field('save_custom_fields', 'custom_fields_nonce');
        $custom_fields = get_post_meta($post->ID);
    
        echo '<table style="width:100%;" id="custom-fields-table">';
        echo '<tr><th style="width:40%;">Name</th><th style="width:40%;">Value</th><th style="width:20%;">Actions</th></tr>';
    
        foreach ($custom_fields as $key => $value) {
            if (!str_starts_with($key, '_')) { // Ignore hidden system fields
                echo '<tr>';
                echo '<td><input type="text" name="custom_field_keys[' . esc_attr($key) . ']" value="' . esc_attr($key) . '" style="width:100%;" /></td>';
                echo '<td><textarea name="custom_field_values[' . esc_attr($key) . ']" style="width:100%;">' . esc_textarea($value[0]) . '</textarea></td>';
                echo '<td style="white-space: nowrap;">
                        <button type="submit" name="update_custom_field" value="' . esc_attr($key) . '" class="button">Update</button>
                        <button type="submit" name="delete_custom_field" value="' . esc_attr($key) . '" class="button">Delete</button>
                      </td>';
                echo '</tr>';
            }
        }
        echo '</table>';
    
        echo '<h4>Add New Custom Field:</h4>';
        echo '<input type="text" name="new_custom_field_key" placeholder="Name" style="width:45%;" />';
        echo '<input type="text" name="new_custom_field_value" placeholder="Value" style="width:45%;" />';
        echo '<button type="submit" name="add_custom_field" class="button">Add</button>';
    }
  
    function save_custom_fields($post_id) {
        if (!isset($_POST['custom_fields_nonce']) || !wp_verify_nonce($_POST['custom_fields_nonce'], 'save_custom_fields')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        // DELETE FIELD
        if (!empty($_POST['delete_custom_field'])) {
            $field_key = sanitize_text_field($_POST['delete_custom_field']);
            delete_post_meta($post_id, $field_key);
        }
    
        // UPDATE FIELD (INCLUDING NAME)
        if (!empty($_POST['update_custom_field']) && !empty($_POST['custom_field_values'])) {
            $old_key = sanitize_text_field($_POST['update_custom_field']);
            $new_key = sanitize_text_field($_POST['custom_field_keys'][$old_key]);
            $new_value = sanitize_text_field($_POST['custom_field_values'][$old_key]);
    
            // If key name has changed, delete old key and add new one
            if ($old_key !== $new_key) {
                delete_post_meta($post_id, $old_key);
                add_post_meta($post_id, $new_key, $new_value, true);
            } else {
                update_post_meta($post_id, $old_key, $new_value);
            }
        }
    
        // ADD NEW FIELD
        if (!empty($_POST['new_custom_field_key']) && !empty($_POST['new_custom_field_value'])) {
            add_post_meta($post_id, sanitize_text_field($_POST['new_custom_field_key']), sanitize_text_field($_POST['new_custom_field_value']), true);
        }
    }
    add_action('save_post', 'save_custom_fields');
    
    function university_adjust_queries($query){
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('event')) {
            $today = strtotime('today'); // Chuyển ngày hôm nay thành timestamp

            $query->set('meta_query', array(
                array(
                    'key'     => 'event-date',
                    'compare' => '<=',  
                    'value'   => $today, // So sánh timestamp
                    'type'    => 'NUMERIC'
                )
            ));
    
            $query->set('meta_key', 'event-date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC'); // Sắp xếp từ gần đến xa
        }
    }
    add_action('pre_get_posts', 'university_adjust_queries');
    
    
?>