<?php

class OPS_Share_Counter {

    public function __construct() {

        // If we have no post types set
        $post_types = Off_Page_SEO::ops_get_post_types();
        if (!is_array($post_types)) {
            return;
        }
        // check if we have timers set up
        $settings = Off_Page_SEO::ops_get_settings();

        if (!isset($settings['ops_all_shares_checked'])) {
            Off_Page_SEO::ops_update_settings('ops_all_shares_checked', 0);
        }
        if (!isset($settings['ops_share_timer'])) {
            Off_Page_SEO::ops_update_settings('ops_share_timer', 0);
        }

        // procceed with check
        $timer = $settings['ops_share_timer'];
        $all_checked = $settings['ops_all_shares_checked'];

        /*
         * INSETS AJAX
         */
        if ($timer < time() && $all_checked == 0) { // if we are in state of initial control
            $timer = time() + 40;
            Off_Page_SEO::ops_update_settings('ops_share_timer', $timer);

            // ajax script
            add_action('wp_footer', array($this, 'ops_script_all_check')); // insert ajax 
        } else {
            // ajax script
            add_action('wp_footer', array($this, 'ops_script_continuous_check')); // insert script with ajax    
        }
    }

    /**
     * Insert script to check all posts
     */
    public static function ops_script_all_check() {
        ?>
        <script type="text/javascript" >
            jQuery(document).ready(function ($) {
                //xxx
                $.ajax({
                    type: 'POST',
                    url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-shares-all.php') ?>',
                    data: {},
                    success: function (data) {
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * Script calling ajax every time 1/2 days passed
     */
    public function ops_script_continuous_check() {
        wp_reset_query();

        // do we check this post type?
        $pt = get_post_type(get_the_ID());
        $check_this_post_type = Off_Page_SEO::ops_post_type_is_checked($pt);
        if (!$check_this_post_type) {
            return;
        }

        // when was the last check
        $last_check = get_post_meta(get_the_ID(), 'ops_share_timer');

        // if we have new post
        if (!isset($last_check[0])) {
            $last_check = 0;
        }

        $diff = time() - $last_check[0];

        if ($diff > 43200) : // 43200 if we have not controlled shares for 1/2 day
            ?>
            <script type="text/javascript" >
                jQuery(document).ready(function ($) {
                    //ccc
                    var pid = <?php echo get_the_ID() ?>;
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-shares-continuous.php') ?>',
                        data: {pid: pid},
                        success: function () {
                        }
                    });
                });
            </script>
            <?php
        endif;
    }

    /**
     * On install, this controls all posts
     * @global type $wpdb
     */
    public function ops_check_all() {
        global $wpdb;
        /*
         * PRE - QUERY
         */
        $post_types = Off_Page_SEO::ops_get_post_types();
        /*
         * MAIN ARGS - ALL CHECK
         */
        $args = array(
            'post_type' => $post_types,
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 20,
            'meta_query' => array(
                array(
                    'key' => 'ops_shares',
                    'compare' => 'NOT EXISTS',
                )
            ),
        );
        $wp_query = new WP_Query($args);
        if ($wp_query->have_posts()) { // if we found posts to check
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                
                $shares = self::ops_api_multi_request(get_the_permalink());
                update_post_meta(get_the_ID(), 'ops_shares', $shares);

                $total = 0;
                foreach ($shares['count'] as $share) {
                    $total = $total + $share;
                }

                update_post_meta(get_the_ID(), 'ops_shares_total', $total);

                update_post_meta(get_the_ID(), 'ops_share_timer', time());
            }
        } else {
            /*
             * IF THERE ARE NO POSTS, we finished initial check
             */
            Off_Page_SEO::ops_update_settings('ops_share_timer', time());
            Off_Page_SEO::ops_update_settings('ops_all_shares_checked', 1);
        }

        wp_die();
    }

    /**
     * Controls shares every 1/2 day when post is accessed
     */
    public function ops_ajax_continuous_check() {
        $pid = sanitize_text_field($_POST['pid']);

        $shares = self::ops_api_multi_request(get_permalink($pid));
        update_post_meta($pid, 'ops_shares', $shares);

        $total = 0;
        foreach ($shares['count'] as $share) {
            $total = $total + $share;
        }

        update_post_meta($_POST['pid'], 'ops_shares_total', $total);

        update_post_meta($pid, 'ops_share_timer', time());

        wp_die();
    }

    /**
     * @param type $url
     * @return array
     */
    public static function ops_api_request_urls($url) {

        $urls = array(
            'facebook' => 'https://api.facebook.com/method/links.getStats?urls=' . rawurlencode($url) . '&format=json',
            'twitter' => 'http://urls.api.twitter.com/1/urls/count.json?url=' . rawurlencode($url) . '&callback=?',
            'googleplus' => 'https://apis.google.com/u/0/_/+1/fastbutton?usegapi=1&hl=en-GB&url=' . rawurlencode($url),
            'pocket' => 'https://widgets.getpocket.com/v1/button?label=pocket&count=horizontal&v=1&url=' . rawurlencode($url),
            'pinterest' => 'http://api.pinterest.com/v1/urls/count.json?url=' . rawurlencode($url) . '&callback=receiveCount',
        );

        return $urls;
    }

    /**
     * Mulit Request
     */
    public static function ops_api_multi_request($url, $timeout = 5) {
        $result = false;
        $mh = curl_multi_init();
        $ch = array();
        $counts = array();
        $apilist = self::ops_api_request_urls($url);

        if (!$apilist) {
            return false;
        }

        foreach ($apilist as $key => $url) :
            $ch[$url] = curl_init();
            curl_setopt($ch[$url], CURLOPT_URL, $url);
            curl_setopt($ch[$url], CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch[$url], CURLOPT_ENCODING, "");
            curl_setopt($ch[$url], CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch[$url], CURLOPT_USERAGENT, "WordPress/WPGame");
            curl_setopt($ch[$url], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch[$url], CURLOPT_SSL_VERIFYHOST, false);
            if (preg_match('/\Ahttps?:\/\/apis\.google\.com\/+/i', $url)) {
                curl_setopt($ch[$url], CURLOPT_HEADER, TRUE);
            }
            if ($timeout > 0) {
                curl_setopt($ch[$url], CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch[$url], CURLOPT_TIMEOUT, $timeout);
            }
            curl_multi_add_handle($mh, $ch[$url]);
        endforeach;


        $running = null;
        do {
            $mrc = curl_multi_exec($mh, $running);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($running && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $running);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }


        foreach ($apilist as $key => $url) :
            $getcontent = curl_multi_getcontent($ch[$url]);

            if (preg_match('/\Ahttps?:\/\/api\.facebook\.com\/+/i', $url)) {
                $content = json_decode($getcontent);
                $counts['count']['facebook'] = (int) $content[0]->total_count;
            } elseif (preg_match('/\Ahttps?:\/\/urls\.api\.twitter\.com\/+/i', $url)) {
                $content = json_decode($getcontent);
                if (isset($content->count)) {
                    $counts['count']['twitter'] = (int) $content->count;
                } else {
                    $counts['count']['twitter'] = (int) 0;
                }
            } elseif (preg_match('/\Ahttps?:\/\/apis\.google\.com\/+/i', $url)) {
                $content = preg_match('/<div\sid=\"aggregateCount\"\sclass=\"Oy\">([0-9]+)<\/div>/i', $getcontent, $matches);
                if (!isset($matches[1]))
                    $matches[1] = 0;
                $counts['count']['googleplus'] = (int) $matches[1];
            } elseif (preg_match('/\Ahttps?:\/\/widgets\.getpocket\.com\/+/i', $url)) {
                $content = preg_match('/<em\sid=\"cnt\">([0-9]+)<\/em>/i', $getcontent, $matches);
                if (!isset($matches[1]))
                    $matches[1] = 0;
                $counts['count']['pocket'] = (int) $matches[1];
            } elseif (preg_match('/\Ahttps?:\/\/api\.pinterest\.com\/+/i', $url)) {
                $content = json_decode(preg_replace('/\AreceiveCount\((.*)\)$/', "\\1", $getcontent));
                if (isset($content->count)) {
                    $counts['count']['pinterest'] = (int) $content->count;
                } else {
                    $counts['count']['pinterest'] = (int) 0;
                }
            }

            curl_multi_remove_handle($mh, $ch[$url]);
            curl_close($ch[$url]);
        endforeach;

        curl_multi_close($mh);

        if (isset($counts)) {
            $result = $counts;
        }

        return $result;
    }

}
