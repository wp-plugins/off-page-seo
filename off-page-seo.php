<?php

/*
  Plugin Name: Off Page SEO
  Plugin URI: http://www.offpageseoplugin.com
  Description: Gives you tools to boost your SEO.
  Version: 1.1.2.
  Author: Jakub Glos
  Author URI: http://www.offpageseoplugin.com
  License:
  Text Domain: off-page-seo
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// no need on cron job
if (defined('DOING_CRON') || isset($_GET['doing_wp_cron'])) {
    return;
}
// don't call the file directly
if (!defined('ABSPATH'))
    return;

/*
 * Include Classes
 */
require_once('php/ops.php');
require_once('php/ops-dashboard.php');
require_once('php/ops-dashboard-widget-reporter.php');
require_once('php/ops-dashboard-widget-backlinks.php');
require_once('php/ops-rank-reporter.php');
require_once('php/ops-analyze-keyword.php');
require_once('php/ops-backlinks.php');
require_once('php/ops-backlinks-feed.php');
require_once('php/ops-backlinks-gb.php');
require_once('php/ops-backlinks-comment.php');
require_once('php/ops-knowledge-base.php');
require_once('php/ops-settings.php');

/*
 * Tools
 */
require_once('php/tools/simple-html-dom.php');
require_once('php/tools/alexarank.php');
require_once('php/tools/pagerank.php');

/*
 *  Initiate main Class  
 */
if (is_admin()) {
    $ops = new Off_Page_SEO();

    if (is_multisite()) {
        require_once('php/ops-multisite.php');
        $ops = new OPS_Multisite();
    }
}
      
/*
 *  This fires iframe which will update rank positions
 */
Off_Page_SEO::ops_position_cron();


/*
 * If we are in iframe, update positions
 */
if (isset($_GET['update_positions']) && $_GET['update_positions'] == 'do_it') {
    OPS_Rank_Reporter::ops_update_positions();
}

/*
 * Install
 */
register_activation_hook(__FILE__, 'ops_on_activate');

function ops_on_activate() {
    /*
     * Insert database table
     */
    global $wpdb;
    $create_table_query = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}ops_rank_report` (
              `id` INT NOT NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
              `url` text NOT NULL,
              `keyword` text NOT NULL,
              `positions` text NOT NULL,
              `post_id` int NOT NULL,
              `active` int NOT NULL
            ) DEFAULT CHARSET=utf8;
    ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($create_table_query);

    /*
     * Add Option if not multisite
     */
    if (!is_multisite()) {
        $settings = array(
            'null' => 'yes',
            'last_check' => 0,
            'last_check_site_info' => 0,
            'site_info' => array(
                'page_rank' => 0,
                'alexa_rank' => 0,
                'facebook' => 0,
                'twitter' => 0,
                'google' => 0,
                'pinterest' => 0,
                'stumbleupon' => 0,
                'delicious' => 0,
                'reddit' => 0,
                'linkedin' => 0
            ),
            'lang' => 'en',
            'google_domain' => 'com',
            'donate' => 'on',
            'google_domain' => 'com',
            'show' => array(
                'page_rank' => 'on',
                'alexa_rank' => 'on',
                'facebook' => 'on',
                'twitter' => 'on',
                'google' => 'on'
            )
        );

        Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
    }
}
  