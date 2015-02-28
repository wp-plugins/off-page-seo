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

        add_action('admin_menu', array($this, 'init'));

        add_action('init', array($this, 'ops_cache_site_info'));
    }

    /**
     * Add administration menu and styles
     * */
    public function init() {
        // analyze competitors
        add_menu_page('Off Page SEO', 'Off Page SEO', 'read', 'ops', array($this, 'ops_dashboard'), 'dashicons-groups', '2.0981816');

        // settings
        add_submenu_page('ops', 'Analyze Keyword', 'Analyze Keyword', 'read', 'ops_analyze_keyword', array($this, 'ops_analyze_keyword'));


        // settings
        add_submenu_page('ops', 'Backlinks', 'Backlinks', 'read', 'ops_backlinks', array($this, 'ops_backlinks'));

        // settings
        add_submenu_page('ops', 'Knowledge Base', 'Knowledge Base', 'read', 'ops_knowledge_base', array($this, 'ops_knowledge_base'));

        // settings
        add_submenu_page('ops', 'Settings', 'Settings', 'read', 'ops_settings', array($this, 'ops_settings'));

        wp_enqueue_style('off_page_seo_css', plugins_url('off-page-seo/css/style.css'));

        wp_enqueue_script('off_page_seo_js', plugins_url('off-page-seo/js/ops-main.js'));

        wp_enqueue_style('ops_select_2_css', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/css/select2.min.css');

        wp_enqueue_script('ops_select_2_js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/js/select2.min.js');
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

    /**
     * Runs every 3 days and updates positions in Google
     */
    public static function ops_position_cron() {

        $settings = Off_Page_SEO::ops_get_settings();

        $now = time();
        $diff = $now - $settings['last_check'];
        if ($diff > 259200 && !is_admin()) { // 259200
            // update last check first
            $settings['last_check'] = $now;
            self::ops_update_option('ops_settings', serialize($settings));

            // insert iframe
            add_action('wp_footer', array('Off_Page_SEO', 'ops_update_positions_cron'));
        }
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
     * Every day it saves site info to the database (page rank, alexa rank, social shares)
     */
    public function ops_cache_site_info() {

        $settings = Off_Page_SEO::ops_get_settings();
        $now = time();
        $diff = $now - $settings['last_check_site_info'];
        $home = get_home_url();
        if ($diff > 86400) { //86400
            $pr = new Page_Rank();
            $ar = new Alexa_Rank();
            $socials = file_get_contents('http://count.donreach.com/?url=' . $home);
            $socials = json_decode($socials);

            $alexa_rank = number_format($ar->get_rank($home));
            $page_rank = $pr->get_google_pagerank($home);

            $settings['last_check_site_info'] = $now;
            $settings['site_info']['page_rank'] = $page_rank;
            $settings['site_info']['alexa_rank'] = $alexa_rank;
            if (isset($socials->shares->facebook)) {
                $settings['site_info']['facebook'] = $socials->shares->facebook;
            } else {
                $settings['site_info']['facebook'] = 0;
            }
            if (isset($socials->shares->google)) {
                $settings['site_info']['google'] = $socials->shares->google;
            } else {
                $settings['site_info']['google'] = 0;
            }
            if (isset($socials->shares->twitter)) {
                $settings['site_info']['twitter'] = $socials->shares->twitter;
            } else {
                $settings['site_info']['twitter'] = 0;
            }
            if (isset($socials->shares->pinterest)) {
                $settings['site_info']['pinterest'] = $socials->shares->pinterest;
            } else {
                $settings['site_info']['pinterest'] = 0;
            }
            if (isset($socials->shares->stumbleupon)) {
                $settings['site_info']['stumbleupon'] = $socials->shares->stumbleupon;
            } else {
                $settings['site_info']['stumbleupon'] = 0;
            }
            if (isset($socials->shares->delicious)) {
                $settings['site_info']['delicious'] = $socials->shares->delicious;
            } else {
                $settings['site_info']['delicious'] = 0;
            }
            if (isset($socials->shares->reddit)) {
                $settings['site_info']['reddit'] = $socials->shares->reddit;
            } else {
                $settings['site_info']['reddit'] = 0;
            }
            if (isset($socials->shares->linkedin)) {
                $settings['site_info']['linkedin'] = $socials->shares->linkedin;
            } else {
                $settings['site_info']['linkedin'] = 0;
            }

            // check for GB
            $url = Off_Page_SEO::$mother . '/check?site_url=' . urlencode(get_home_url());
            $str = ops_curl($url);
            $html = str_get_html($str);

            if (strpos($html, 'IS')) {
                $settings['site_info']['guest_posting'] = true;
            } else {
                $settings['site_info']['guest_posting'] = false;
            }

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
            update_blog_option(get_current_blog_id(), 'ops_settings', $value);
        } else {
            update_site_option($option, $value);
        }
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
