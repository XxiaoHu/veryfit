<?php
/**
 * Video Content Functions
 *
 * Helper functions for Video Content post type
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get video content posts
 */
function feryfit_get_video_contents( $args = array() ) {
    $defaults = array(
        'post_type'      => 'video_content',
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
 * Get video content by ID
 */
function feryfit_get_video_content( $video_content_id ) {
    return get_post( $video_content_id );
}

/**
 * Get video content URL
 */
function feryfit_get_video_content_url( $video_content_id ) {
    return get_post_meta( $video_content_id, '_video_url', true );
}

/**
 * Get video content permalink (使用查询参数格式)
 */
function feryfit_get_video_content_permalink( $video_content_id ) {
    return home_url( '/index.php?post_type=video_content&p=' . $video_content_id );
}

/**
 * Get the original video ID that this video content was synced from
 */
function feryfit_get_video_content_source_id( $video_content_id ) {
    return get_post_meta( $video_content_id, '_synced_from_video_id', true );
}

/**
 * Check if video content is synced from video
 */
function feryfit_is_video_content_synced( $video_content_id ) {
    $source_id = feryfit_get_video_content_source_id( $video_content_id );
    return ! empty( $source_id );
}
