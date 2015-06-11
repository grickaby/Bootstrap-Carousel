<?php
/**
 * Plugin Name: A Simple Slideshow
 * Plugin URI: http://www.geoffreyrickaby.com
 * Description: A simple plugin that creates a slideshow.
 * Version: 1.0.0
 * Author: Geoffrey Rickaby
 * Author URI: http://www.geoffreyrickaby.com
 * License: GNU General Public License (GPL) Version 2
 */
add_shortcode('simple-slide', 'do_slideshow_shortcode');
add_theme_support('post-thumbnails');
add_action('init', 'ss_postType');
add_action('admin_init', 'adjust_post_ui');
add_action('save_post', 'save_ss_slideshow_meta_box');

// Create Post Type
function ss_postType() {
    register_post_type('slides', array(
        'labels' => array(
            'name' => __('Slides'),
            'singular_name' => __('Slide'),
            'menu_name' => 'Simple Slideshow',
            'all_items' => 'View Slides',
            'add_new' => 'Add Slide'
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'excerpt', 'thumbnail'),
        'menu_icon' => 'dashicons-format-gallery',
        'exclude_from_search' => true,
            )
    );
}

// Adjust the UI for custom meta boxes
function adjust_post_ui() {
    remove_meta_box('postexcerpt', 'slides', 'normal');
    add_meta_box('ss-slidecontent', __('Slide Content'), 'ss_slideshow_meta_box', 'slides', 'normal', 'high');
}

// Create custom meta box for the Slideshow
function ss_slideshow_meta_box($post) {

    wp_nonce_field('ss_slideshow_meta_box', 'ss_slideshow_meta_box_nonce');

    $value = get_post_meta($post->ID, '_ss_slideshow_content_', true);

    echo '<label for="myplugin_new_field">';
    esc_html_e('Put slide content here. HTML is allowed.', 'ss-slidecontent');
    echo '</label> ';
    echo '<textarea id="slide_content" style="width:100%;" rows="10" name="_ss_slideshow_content_">' . esc_attr($value) . '</textarea>';
}

// Save the meta box content
function save_ss_slideshow_meta_box($post_id) {
    if (!isset($_POST['ss_slideshow_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['ss_slideshow_meta_box_nonce'], 'ss_slideshow_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (!isset($_POST['_ss_slideshow_content_'])) {
        return;
    }

    update_post_meta($post_id, '_ss_slideshow_content_', $_POST['_ss_slideshow_content_']);
}

// Run the Slideshow
function do_slideshow() {

    if (!is_front_page())
        return;

    // Query Post Type
    $args = array(
        'post_type' => 'slides',
        'post_status' => 'publish',
        'order' => 'asc',
        'orderby' => 'id'
    );

    $i = 0;
    $the_query = new WP_Query($args);

    // Build It
    if ($the_query->have_posts()) :
        ?>
        <div id="slideshow-container">

            <?php
            while ($the_query->have_posts()) : $the_query->the_post();

                $slide_content = get_post_meta(get_the_ID(), '_ss_slideshow_content_', true);
                ?>

                <div class="slide <?php if (!$i++) echo 'active'; ?>">
                    <?php the_post_thumbnail('full') ?>
                </div>	
            <?php endwhile; ?>

            <?php wp_reset_postdata(); ?>
        </div>
        <?php
    endif;
}

function do_slideshow_shortcode() {
    // Query Post Type
    $args = array(
        'post_type' => 'slides',
        'post_status' => 'publish',
        'order' => 'asc',
        'orderby' => 'id'
    );

    $i = 0;
    $the_query = new WP_Query($args);

    // Build It
    if ($the_query->have_posts()) :
        ?>


        if ($the_query->have_posts()) :
        ?>
        <div id="slideshow-container">

            <?php
            while ($the_query->have_posts()) : $the_query->the_post();

                $slide_content = get_post_meta(get_the_ID(), '_ss_slideshow_content_', true);
                ?>

                <div class="slide <?php if (!$i++) echo 'active'; ?>">
                    <?php the_post_thumbnail('full') ?>
                </div>	
            <?php endwhile; ?>

            <?php wp_reset_postdata(); ?>
        </div>
        <?php
    endif;
}
