<?php

/**
 * Main plugin class
 * */
class Off_Page_SEO {

    public static $mother = "http://www.offpageseoplugin.com";

    /**
     * Initialization of main class
     * */
    public function __construct() {

        add_action('wp_dashboard_setup', array($this, 'ops_add_dashboard_widgets'));

        add_action('add_meta_boxes', array($this, 'ops_add_post_meta_box_shares'));

        add_action('admin_menu', array($this, 'init'));

        add_action('init', array($this, 'ops_cache_site_info'));
        
        add_filter('plugin_action_links_off-page-seo/off-page-seo.php', array($this, 'ops_add_settings_link'));
        
        // on admin pages, check reciprocal backlinks
        if (is_admin()) {
            add_action('admin_footer', array($this, 'ops_reciprocal_cron'));
        }
        
    }

    public function ops_add_settings_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('admin.php?page=ops_settings') . '">Settings</a>',
        );
        return array_merge($links, $mylinks);
    }

    public static function ops_start_session() {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Add administration menu and styles
     * */
    public function init() {
        // analyze competitors
        add_menu_page('Off Page SEO', 'Off Page SEO', 'read', 'ops', array($this, 'ops_dashboard'), 'dashicons-groups', '2.0981816');

        // analyze kw
        add_submenu_page('ops', 'Analyze Keyword', 'Analyze Keyword', 'read', 'ops_analyze_keyword', array($this, 'ops_analyze_keyword'));

        // backlinks
        add_submenu_page('ops', 'Backlinks', 'Backlinks', 'read', 'ops_backlinks', array($this, 'ops_backlinks'));

        // share counter
        add_submenu_page('ops', 'Social Networks', 'Social Networks', 'read', 'ops_social_networks', array($this, 'ops_social_networks'));

        // knowledge base
        add_submenu_page('ops', 'Knowledge Base', 'Knowledge Base', 'read', 'ops_knowledge_base', array($this, 'ops_knowledge_base'));

        // settings
        add_submenu_page('ops', 'Settings', 'Settings', 'read', 'ops_settings', array($this, 'ops_settings'));

        wp_enqueue_style('off_page_seo_css', plugins_url('off-page-seo/css/style.css'));

        wp_enqueue_script('off_page_seo_js', plugins_url('off-page-seo/js/ops-main.js'));

        wp_enqueue_style('ops_select_2_css', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/css/select2.min.css');

        wp_enqueue_script('ops_select_2_js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/js/select2.min.js');
    }

    /**
     * Adds meta boxes with shares
     */
    public function ops_add_post_meta_box_shares() {

        $meta_box = new OPS_Meta_Box_Shares;
        $settings = self::ops_get_settings();
        $post_types = $settings['post_types'];
        foreach ($post_types as $post_type) {
            add_meta_box(
                    'ops-share-counter-box', esc_html__('Social Networks', 'ops'), array($meta_box, 'init'), $post_type, // Admin page (or post type)
                    'side', // Context
                    'default'         // Priority
            );
        }
    }

    /**
     * call dashboard
     */
    public function ops_add_dashboard_widgets() {
        wp_add_dashboard_widget('off_page_seo_wp_dashboard_reporter', 'Off Page SEO Rank Reporter', array($this, 'ops_render_dashboard_widget_reporter'));
        wp_add_dashboard_widget('off_page_seo_wp_dashboard_backlinks', 'Off Page SEO Rank Backlinks', array($this, 'ops_render_dashboard_widget_backlinks'));
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_render_dashboard_widget_reporter() {
        new OPS_Dashboard_Widget_Reporter;
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_render_dashboard_widget_backlinks() {
        new OPS_Dashboard_Widget_Backlinks;
    }

    /**
     * Custom Dashboard Page Call
     */
    public function ops_dashboard() {
        new OPS_Dashboard;
    }

    /**
     * Analyze Competitors Page Call
     */
    public function ops_analyze_keyword() {
        new OPS_Analyze_Keyword;
    }

    /**
     * PR Submission Page Call
     */
    public function ops_backlinks() {
        new OPS_Backlinks;
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_social_networks() {
        new OPS_Social_Networks();
    }

    /**
     * Knowledge Base Page Call
     */
    public function ops_knowledge_base() {
        new OPS_Knowledge_Base;
    }

    /**
     * Settings Page Call
     */
    public function ops_settings() {
        new OPS_Settings;
    }

    public static function ops_get_date_format() {
        $settings = Off_Page_SEO::ops_get_settings();
        if (isset($settings['date_format'])) {
            return $settings['date_format'];
        } else {
            return 'F d, Y';
        }
    }

    /**
     * Runs every 3 days and updates positions in Google
     */
    public static function ops_position_cron() {
        add_action('wp_footer', array('Off_Page_SEO', 'ops_update_positions_cron'));
    }

    /**
     * First it inserts Iframe - in case there is some error, it will not crash the page
     */
    public static function ops_update_positions_cron() {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var doCron = '1';
                $.ajax({
                    url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-do-cron.php') ?>',
                    type: "POST",
                    data: {doCron: doCron},
                    success: function (data) {
                    },
                    error: function () {
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * First it inserts Iframe - in case there is some error, it will not crash the page
     */
    public static function ops_reciprocal_cron() {
        $settings = self::ops_get_settings();
        if (!isset($settings['reciprocal_timer'])) {
            $settings['reciprocal_timer'] = 0;
        }
        if ($settings['reciprocal_timer'] < time() && isset($settings['reciprocal_control']) && $settings['reciprocal_control'] == 'on' && self::ops_is_premium() == 1) {
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    var doCron = '1';
                    $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-reciprocal.php') ?>',
                        type: "POST",
                        data: {doCron: doCron},
                        beforeSend: function () {
                            $('body').append('<div class="ops-recip-check"><span>Please wait while we check backlinks. <br/>This can occur couple times now in admin area.<span></div>');
                            $('body').find('.ops-recip-check').fadeIn(500);
                        },
                        success: function (data) {
                            //                            $('.postbox').html(data);
                            $('body').find('.ops-recip-check').html('<span>Thank you.<span>');
                            setTimeout(function () {
                                $('body').find('.ops-recip-check').fadeOut(500, function () {
                                    $(this).remove();
                                });
                            }, 500);
                        },
                        error: function () {
                            $('body').find('.ops-recip-check').html('<span>Error, contact author of the plugin.<span>');
                            setTimeout(function () {
                                $('body').find('.ops-recip-check').fadeOut(500, function () {
                                    $(this).remove();
                                });
                            }, 500);
                        }
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Recognize if the site is multisite. It returns either blog settings or page settings.
     * returns: array
     */
    public static function ops_get_settings() {
        if (is_multisite()) {
            $settings = unserialize(get_blog_option(get_current_blog_id(), 'ops_settings'));
        } else {
            $settings = unserialize(get_site_option('ops_settings'));
        }

        return $settings;
    }

    /**
     * Recognize if the site is multisite. It returns either blog settings or page settings.
     * returns: array
     */
    public static function ops_is_premium() {
        if (is_multisite()) {
            $premium = unserialize(get_blog_option(get_current_blog_id(), 'ops_premium'));
        } else {
            $premium = unserialize(get_site_option('ops_premium'));
        }
        if (isset($premium['premium']) && $premium['premium'] == 1) {
            return $premium['premium'];
        } else {
            return false;
        }
    }

    /**
     * Recognize if the site is multisite. It returns either blog settings or page settings.
     * returns: array
     */
    public static function ops_update_settings($what, $value) {
        if (is_multisite()) {
            $settings = unserialize(get_blog_option(get_current_blog_id(), 'ops_settings'));
        } else {
            $settings = unserialize(get_site_option('ops_settings'));
        }

        $new_settings = $settings;
        $new_settings[$what] = $value;

        $save_this = serialize($new_settings);

        if (is_multisite()) {
            update_blog_option(get_current_blog_id(), 'ops_settings', $save_this);
        } else {
            update_site_option('ops_settings', $save_this);
        }
    }

    /**
     * Every day it saves site info to the database (page rank, alexa rank, social shares)
     */
    public function ops_cache_site_info() {

        $settings = Off_Page_SEO::ops_get_settings();
        $now = time();
        $diff = $now - $settings['last_check_site_info'];
        $home = get_home_url();
        if ($diff > 172800) { //86400
            // first save timer to prevent other testings
            $settings['last_check_site_info'] = $now;
            Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));

            // check alexa and page rank
            $pr = new Page_Rank();
            $ar = new Alexa_Rank();

            $alexa_rank = number_format($ar->get_rank($home));
            $page_rank = $pr->get_google_pagerank($home);

            $settings['last_check_site_info'] = $now;
            $settings['site_info']['page_rank'] = $page_rank;
            $settings['site_info']['alexa_rank'] = $alexa_rank;

            $settings['premium_code'] = isset($settings['premium_code']) ? $settings['premium_code'] : "";
            // check for GB
            $url = Off_Page_SEO::$mother . '/api/check-site/?site_url=' . urlencode(get_home_url()) . '&licence_key=' . $settings['premium_code'];

            // GET DATA FROM MOTHER
            $str = ops_curl($url);
            $html = new simple_html_dom();
            $html->load($str, true, false);
            foreach ($html->find('div') as $d) {
                $response = json_decode($d->plaintext);
                break;
            }


            // some error on mother website
            if (!isset($response)) {
                return;
            }


            // SET GUESTPOST
            if ($response->guestpost == 1) {
                $settings['site_info']['guest_posting'] = true;
            } else {
                $settings['site_info']['guest_posting'] = false;
            }

            // SET PREMIUM
            $premium = array();
            $premium['premium_expiration'] = (isset($response->premium_expiration) && strlen($response->premium_expiration) > 0) ? $response->premium_expiration : 0;
            if ($response->premium == 1) {
                $premium['premium'] = '1';
                // if the code has expired
                if ($response->premium_expiration < time()) {
                    $premium['premium'] = '2';
                }
            } elseif ($response->premium == 3) {
                $premium['premium'] = '3';
            } else {
                $premium['premium'] = '0';
            }


            // check if user had plugin before turning into premium
            if ($response->before_premium == 1) {
                $premium['before_premium'] = 1;
            } else {
                $premium['before_premium'] = 0;
            }

            Off_Page_SEO::ops_update_option('ops_premium', serialize($premium));

            // SET SOCIAL SHARES
            $fb = 0;
            $tw = 0;
            $go = 0;
            $po = 0;
            $pi = 0;
            $total = 0;

            $post_types = self::ops_get_post_types();
            $args = array(
                'post_type' => $post_types,
                'orderby' => 'date',
                'order' => 'DESC',
                'posts_per_page' => 10000,
                'meta_query' => array(
                    array(
                        'key' => 'ops_shares',
                        'compare' => 'EXISTS',
                    ),
                ),
            );
            $wp_query = new WP_Query($args);

            if ($wp_query->have_posts()) :
                while ($wp_query->have_posts()): $wp_query->the_post();

                    $meta = get_post_meta(get_the_ID());
                    $shares = unserialize($meta['ops_shares'][0]);

                    foreach ($shares['count'] as $share) {
                        $total = $total + $share;
                    }

                    $fb = $fb + $shares['count']['facebook'];
                    $tw = $tw + $shares['count']['twitter'];
                    $go = $go + $shares['count']['googleplus'];
                    $po = $po + $shares['count']['pocket'];
                    $pi = $pi + $shares['count']['pinterest'];

                endwhile;
            endif;

            $settings['site_info']['shares_total'] = $total;
            $settings['site_info']['shares_facebook'] = $fb;
            $settings['site_info']['shares_twitter'] = $tw;
            $settings['site_info']['shares_googleplus'] = $go;
            $settings['site_info']['shares_pocket'] = $po;
            $settings['site_info']['shares_pinterest'] = $pi;


            Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
        }
    }

    /**
     * Recognize if its multisite and saves either blog option or site option
     * @param type $option
     * @param type $value
     */
    public static function ops_update_option($option, $value) {
        if (is_multisite()) {
            update_blog_option(get_current_blog_id(), $option, $value);
        } else {
            update_site_option($option, $value);
        }
    }

    /**
     * GET Option based on multistite / or not
     * @param type $option
     * @param type $value
     */
    public static function ops_get_option($option) {
        if (is_multisite()) {
            $return = unserialize(get_blog_option(get_current_blog_id(), $option));
        } else {
            $return = unserialize(get_site_option($option));
        }
        return $return;
    }

    /**
     * Returns nice language 
     */
    public static function ops_get_language($id) {
        $languages = Off_Page_SEO::ops_lang_array();
        return $languages[$id];
    }

    /**
     * Returns current language ID
     * @return type current language ID
     */
    public static function ops_get_lang() {
        $settings = Off_Page_SEO::ops_get_settings();
        return $settings['lang'];
    }

    /**
     * Get post types
     * @return boolean
     */
    public static function ops_get_post_types() {
        $settings = self::ops_get_settings();
        if (isset($settings['post_types'])) {
            return $settings['post_types'];
        } else {
            return false;
        }
    }

    /**
     * If the post types is checked
     */
    public static function ops_post_type_is_checked($type) {
        $settings = self::ops_get_settings();
        if (isset($settings['post_types'])) {
            foreach ($settings['post_types'] as $pt) {
                if ($pt == $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Allowed post types
     * @return type
     */
    public static function ops_get_allowed_post_types() {
        $types = get_post_types();
        $banned = array('wpcf7_contact_form', 'nav_menu_item', 'revision', 'attachment', 'acf');
        foreach ($types as $type) {
            if (!in_array($type, $banned))
                $data[] = $type;
        }

        return $data;
    }

    /**
     * Array of all languages
     * @return array
     */
    public static function ops_lang_array() {
        $languages = array(
            'aa' => 'Afar',
            'ab' => 'Abkhaz',
            'ae' => 'Avestan',
            'af' => 'Afrikaans',
            'ak' => 'Akan',
            'am' => 'Amharic',
            'an' => 'Aragonese',
            'ar' => 'Arabic',
            'as' => 'Assamese',
            'av' => 'Avaric',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'ba' => 'Bashkir',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bm' => 'Bambara',
            'bn' => 'Bengali',
            'bo' => 'Tibetan Standard, Tibetan, Central',
            'br' => 'Breton',
            'bs' => 'Bosnian',
            'ca' => 'Catalan; Valencian',
            'ce' => 'Chechen',
            'ch' => 'Chamorro',
            'co' => 'Corsican',
            'cr' => 'Cree',
            'cs' => 'Czech',
            'cv' => 'Chuvash',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'dv' => 'Divehi; Dhivehi; Maldivian;',
            'dz' => 'Dzongkha',
            'ee' => 'Ewe',
            'el' => 'Greek, Modern',
            'en' => 'English',
            'es' => 'Spanish; Castilian',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Persian',
            'ff' => 'Fula; Fulah; Pulaar; Pular',
            'fi' => 'Finnish',
            'fj' => 'Fijian',
            'fo' => 'Faroese',
            'fr' => 'French',
            'fy' => 'Western Frisian',
            'ga' => 'Irish',
            'gd' => 'Scottish Gaelic; Gaelic',
            'gl' => 'Galician',
            'gu' => 'Gujarati',
            'gv' => 'Manx',
            'ha' => 'Hausa',
            'he' => 'Hebrew (modern)',
            'hi' => 'Hindi',
            'ho' => 'Hiri Motu',
            'hr' => 'Croatian',
            'ht' => 'Haitian; Haitian Creole',
            'hu' => 'Hungarian',
            'hy' => 'Armenian',
            'hz' => 'Herero',
            'ia' => 'Interlingua',
            'id' => 'Indonesian',
            'ie' => 'Interlingue',
            'ig' => 'Igbo',
            'ii' => 'Nuosu',
            'ik' => 'Inupiaq',
            'io' => 'Ido',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'iu' => 'Inuktitut',
            'ja' => 'Japanese (ja)',
            'jv' => 'Javanese (jv)',
            'ka' => 'Georgian',
            'kg' => 'Kongo',
            'ki' => 'Kikuyu, Gikuyu',
            'kj' => 'Kwanyama, Kuanyama',
            'kk' => 'Kazakh',
            'kl' => 'Kalaallisut, Greenlandic',
            'km' => 'Khmer',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'kr' => 'Kanuri',
            'ks' => 'Kashmiri',
            'ku' => 'Kurdish',
            'kv' => 'Komi',
            'kw' => 'Cornish',
            'ky' => 'Kirghiz, Kyrgyz',
            'la' => 'Latin',
            'lb' => 'Luxembourgish, Letzeburgesch',
            'lg' => 'Luganda',
            'li' => 'Limburgish, Limburgan, Limburger',
            'ln' => 'Lingala',
            'lo' => 'Lao',
            'lt' => 'Lithuanian',
            'lu' => 'Luba-Katanga',
            'lv' => 'Latvian',
            'mg' => 'Malagasy',
            'mh' => 'Marshallese',
            'mi' => 'Maori',
            'mk' => 'Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'mr' => 'Marathi (Mara?hi)',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'my' => 'Burmese',
            'na' => 'Nauru',
            'nb' => 'Norwegian BokmÃ¥l',
            'nd' => 'North Ndebele',
            'ne' => 'Nepali',
            'ng' => 'Ndonga',
            'nl' => 'Dutch',
            'nn' => 'Norwegian Nynorsk',
            'no' => 'Norwegian',
            'nr' => 'South Ndebele',
            'nv' => 'Navajo, Navaho',
            'ny' => 'Chichewa; Chewa; Nyanja',
            'oc' => 'Occitan',
            'oj' => 'Ojibwe, Ojibwa',
            'om' => 'Oromo',
            'or' => 'Oriya',
            'os' => 'Ossetian, Ossetic',
            'pa' => 'Panjabi, Punjabi',
            'pi' => 'Pali',
            'pl' => 'Polish',
            'ps' => 'Pashto, Pushto',
            'pt' => 'Portuguese',
            'qu' => 'Quechua',
            'rm' => 'Romansh',
            'rn' => 'Kirundi',
            'ro' => 'Romanian, Moldavian, Moldovan',
            'ru' => 'Russian',
            'rw' => 'Kinyarwanda',
            'sa' => 'Sanskrit',
            'sc' => 'Sardinian',
            'sd' => 'Sindhi',
            'se' => 'Northern Sami',
            'sg' => 'Sango',
            'si' => 'Sinhala, Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovene',
            'sm' => 'Samoan',
            'sn' => 'Shona',
            'so' => 'Somali',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'ss' => 'Swati',
            'st' => 'Southern Sotho',
            'su' => 'Sundanese',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'tg' => 'Tajik',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'tk' => 'Turkmen',
            'tl' => 'Tagalog',
            'tn' => 'Tswana',
            'to' => 'Tonga (Tonga Islands)',
            'tr' => 'Turkish',
            'ts' => 'Tsonga',
            'tt' => 'Tatar',
            'tw' => 'Twi',
            'ty' => 'Tahitian',
            'ug' => 'Uighur, Uyghur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            've' => 'Venda',
            'vi' => 'Vietnamese',
            'wa' => 'Walloon',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'za' => 'Zhuang, Chuang',
            'zh' => 'Chinese',
            'zu' => 'Zulu',
        );
        return $languages;
    }

    public static function ops_google_domains_array() {
        $google_domains = array(
            "com" => "Default - google.com",
            "as" => "American Samoa - google.as",
            "off.ai" => "Anguilla - google.off.ai",
            "com.ag" => "Antigua and Barbuda - google.com.ag",
            "com.ar" => "Argentina - google.com.ar",
            "com.au" => "Australia - google.com.au",
            "at" => "Austria - google.at",
            "az" => "Azerbaijan - google.az",
            "be" => "Belgium - google.be",
            "com.br" => "Brazil - google.com.br",
            "vg" => "British Virgin Islands - google.vg",
            "bi" => "Burundi - google.bi",
            "ca" => "Canada - google.ca",
            "td" => "Chad - google.td",
            "cl" => "Chile - google.cl",
            "com.co" => "Colombia - google.com.co",
            "co.cr" => "Costa Rica - google.co.cr",
            "ci" => "Côte d\'Ivoire - google.ci",
            "com.cu" => "Cuba - google.com.cu",
            "cz" => "Czech Republic - google.cz",
            "cd" => "Dem. Rep. of the Congo - google.cd",
            "dk" => "Denmark - google.dk",
            "dj" => "Djibouti - google.dj",
            "com.do" => "Dominican Republic - google.com.do",
            "com.ec" => "Ecuador - google.com.ec",
            "com.sv" => "El Salvador - google.com.sv",
            "fm" => "Federated States of Micronesia - google.fm",
            "com.fj" => "Fiji - google.com.fj",
            "fi" => "Finland - google.fi",
            "fr" => "France - google.fr",
            "gm" => "The Gambia - google.gm",
            "ge" => "Georgia - google.ge",
            "de" => "Germany - google.de",
            "com.gi" => "Gibraltar - google.com.gi",
            "com.gr" => "Greece - google.com.gr",
            "gl" => "Greenland - google.gl",
            "gg" => "Guernsey - google.gg",
            "hn" => "Honduras - google.hn",
            "com.hk" => "Hong Kong - google.com.hk",
            "co.hu" => "Hungary - google.co.hu",
            "co.in" => "India - google.co.in",
            "ie" => "Ireland - google.ie",
            "co.im" => "Isle of Man - google.co.im",
            "co.il" => "Israel - google.co.il",
            "it" => "Italy - google.it",
            "com.jm" => "Jamaica - google.com.jm",
            "co.jp" => "Japan - google.co.jp",
            "co.je" => "Jersey - google.co.je",
            "kz" => "Kazakhstan - google.kz",
            "co.kr" => "Korea - google.co.kr",
            "lv" => "Latvia - google.lv",
            "co.ls" => "Lesotho - google.co.ls",
            "li" => "Liechtenstein - google.li",
            "lt" => "Lithuania - google.lt",
            "lu" => "Luxembourg - google.lu",
            "mw" => "Malawi - google.mw",
            "com.my" => "Malaysia - google.com.my",
            "com.mt" => "Malta - google.com.mt",
            "mu" => "Mauritius - google.mu",
            "com.mx" => "México - google.com.mx",
            "ms" => "Montserrat - google.ms",
            "com.na" => "Namibia - google.com.na",
            "com.np" => "Nepal - google.com.np",
            "nl" => "Netherlands - google.nl",
            "co.nz" => "New Zealand - google.co.nz",
            "com.ni" => "Nicaragua - google.com.ni",
            "com.nf" => "Norfolk Island - google.com.nf",
            "com.pk" => "Pakistan - google.com.pk",
            "com.pa" => "Panamá - google.com.pa",
            "com.py" => "Paraguay - google.com.py",
            "com.pe" => "Perú - google.com.pe",
            "com.ph" => "Philippines - google.com.ph",
            "pn" => "Pitcairn Islands - google.pn",
            "pl" => "Poland - google.pl",
            "pt" => "Portugal - google.pt",
            "com.pr" => "Puerto Rico - google.com.pr",
            "cg" => "Rep. of the Congo - google.cg",
            "ro" => "Romania - google.ro",
            "ru" => "Russia - google.ru",
            "rw" => "Rwanda - google.rw",
            "sh" => "Saint Helena - google.sh",
            "sm" => "San Marino - google.sm",
            "com.sg" => "Singapore - google.com.sg",
            "sk" => "Slovakia - google.sk",
            "co.za" => "South Africa - google.co.za",
            "es" => "Spain - google.es",
            "se" => "Sweden - google.se",
            "ch" => "Switzerland - google.ch",
            "com.tw" => "Taiwan - google.com.tw",
            "co.th" => "Thailand - google.co.th",
            "tt" => "Trinidad and Tobago - google.tt",
            "com.tr" => "Turkey - google.com.tr",
            "com.ua" => "Ukraine - google.com.ua",
            "ae" => "United Arab Emirates - google.ae",
            "co.uk" => "United Kingdom - google.co.uk",
            "com.uy" => "Uruguay - google.com.uy",
            "uz" => "Uzbekistan - google.uz",
            "vu" => "Vanuatu - google.vu",
            "co.ve" => "Venezuela - google.co.ve"
        );
        return $google_domains;
    }

}
