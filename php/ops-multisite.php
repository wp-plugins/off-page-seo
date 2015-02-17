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
                'donate' => 'on',
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

}
