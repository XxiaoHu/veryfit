<?php
/**
 * Video Functions
 *
 * Helper functions for Video post type
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get video posts
 */
function feryfit_get_videos( $args = array() ) {
    $defaults = array(
        'post_type'      => 'video',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'order'          => 'ASC',
        'orderby'        => 'menu_order ID',
    );

    $args = wp_parse_args( $args, $defaults );

    $query = new WP_Query( $args );

    return $query->posts;
}

/**
 * Get video by ID
 */
function feryfit_get_video( $video_id ) {
    return get_post( $video_id );
}

/**
 * Get video URL
 */
function feryfit_get_video_url( $video_id ) {
    return get_post_meta( $video_id, '_video_url', true );
}

/**
 * Get video permalink
 */
function feryfit_get_video_permalink( $video_id ) {
    return home_url( '/index.php?post_type=video&p=' . $video_id );
}
