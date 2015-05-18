<?php

class OPS_Multisite {

    /**
     * If the site is multisite, check if there is settings every time it inits
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();
        if (!$settings) {
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

            Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
            Off_Page_SEO::ops_update_option('ops_premium', serialize($premium));
        }
    }

}
