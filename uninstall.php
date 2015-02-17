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


if(is_multisite()){
    $blogs = wp_get_sites();
    foreach($blogs as $blog){
        delete_blog_option($blog['blog_id'], 'ops_settings');
    }
}