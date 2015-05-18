<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

/*
 * DROP Table with positions
 */
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}ops_rank_report");

/*
 * Delete option OPS SETTINGS
 */
delete_option('ops_settings');
delete_site_option('ops_settings');

delete_option('ops_premium');
delete_site_option('ops_premium');


$types = get_post_types();
$args = array(
    'post_type' => $types,
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'ops_shares',
            'compare' => 'EXISTS',
        ),
    )
);
$wp_query = new WP_Query($args);

if ($wp_query->have_posts()) :
    while ($wp_query->have_posts()): $wp_query->the_post();
        delete_post_meta(get_the_ID(), 'ops_shares');
        delete_post_meta(get_the_ID(), 'ops_shares_total');
        delete_post_meta(get_the_ID(), 'ops_share_timer');
    endwhile;

endif;


if (is_multisite()) {
    $blogs = wp_get_sites();
    foreach ($blogs as $blog) {
        delete_blog_option($blog['blog_id'], 'ops_settings');
        delete_blog_option($blog['blog_id'], 'ops_premium');
    }
}