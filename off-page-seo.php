<?php

/*
  Plugin Name: Off Page SEO
  Plugin URI: http://www.offpageseoplugin.com
  Description: Provides lot of stools to help you with the off-page SEO.
  Version: 2.0.1.
  Author: Jakub Glos
  Author URI: http://www.offpageseoplugin.com
  License: CC-NC-ND
  Text Domain: off-page-seo
 */

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);
// no need on cron job
if (defined('DOING_CRON') || isset($_GET['doing_wp_cron'])) {
    return;
}
// don't call the file directly
if (!defined('ABSPATH'))
    return;

/*
 * CLASSES
 */
require_once('php/ops.php');
require_once('php/ops-dashboard.php');
require_once('php/ops-dashboard-widget-reporter.php');
require_once('php/ops-dashboard-widget-backlinks.php');
require_once('php/ops-meta-box-shares.php');
require_once('php/ops-rank-reporter.php');
require_once('php/ops-analyze-keyword.php');
require_once('php/ops-backlinks.php');
require_once('php/ops-backlinks-database.php');
require_once('php/ops-backlinks-gb.php');
require_once('php/ops-backlinks-comment.php');
require_once('php/ops-knowledge-base.php');
require_once('php/ops-share-counter.php');
require_once('php/ops-social-networks.php');
require_once('php/ops-email.php');
require_once('php/ops-settings.php');

/*
 * TOOLS
 */
require_once('php/tools/simple-html-dom.php');
require_once('php/tools/alexarank.php');
require_once('php/tools/pagerank.php');





/*
 *  INITIATE MAIN CLASS IN ADMIN
 */
if (is_admin()) {
    $ops = new Off_Page_SEO();

    if (is_multisite()) {
        require_once('php/ops-multisite.php');
        $ops = new OPS_Multisite();
    }
}





/*
 * CHECK REFERER
 */
// make sure sessions are on
Off_Page_SEO::ops_start_session();

// check referrer
if (!isset($_SESSION['ops_referer']) && isset($_SERVER['HTTP_REFERER'])) {
    OPS_Rank_Reporter::ops_check_referer();
}







/*
 *  DO CRONS!
 */
$settings = Off_Page_SEO::ops_get_settings();
$diff = time() - $settings['last_check'];
if ($diff > 259200 && !is_admin()) { // 259200
    // insert iframe
    Off_Page_SEO::ops_position_cron();
} else {
    /* Create Class Share Counter */
    if (!is_admin() && isset($settings['control_shares']) && $settings['control_shares'] == 'on') {
        new OPS_Share_Counter();
    }
}













/*
 * UPDATING DATABASE
 */
global $wpdb;
$create_table_query = "
            CREATE TABLE `{$wpdb->base_prefix}ops_rank_report` (
              id int(11) NOT NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
              url text NOT NULL,
              keyword text NOT NULL,
              positions text NOT NULL,
              active int(11) NOT NULL,
              links text NOT NULL,
              feature1 text NOT NULL,
              feature2 text NOT NULL,
              feature3 text NOT NULL
            ) DEFAULT CHARSET=utf8;
    ";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$db = dbDelta($create_table_query);


/*
 * ACTIVATE
 */
register_activation_hook(__FILE__, 'ops_on_activate');

function ops_on_activate() {

    /**
     * Add Option if not multisite
     */
    $settings = Off_Page_SEO::ops_get_settings();

    if (!is_multisite() && !isset($settings['lang'])) {
        $settings = array(
            'null' => 'yes',
            'last_check' => 0,
            'last_check_site_info' => 0,
            'ops_share_timer' => 0,
            'ops_all_shares_checked' => 0,
            'reciprocal_control' => '',
            'control_shares' => '',
            'premium_code' => '',
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
            'notification_email' => '',
            'currency' => '$',
            'date_format' => 'F d, Y',
            'lang' => 'en',
            'google_domain' => 'com',
            'graphs' => array(),
            'post_types' => array(),
            'show' => array(
                'page_rank' => 'on',
                'alexa_rank' => 'on'
            ),
            'guest_posting' => array(
                'email_subject' => '',
                'email_reply' => '',
                'email_content' => ''
            )
        );

        $premium = array(
            'premium' => 0,
            'premium_expiration' => 0
        );

        Off_Page_SEO::ops_update_option('ops_premium', serialize($premium));
        Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
    }
}
