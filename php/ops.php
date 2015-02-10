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

        // add_action('wp_dashboard_setup', array($this, 'ops_add_dashboard_graph'));

        add_action('admin_menu', array($this, 'init'));

        add_action('init', array($this, 'ops_cache_site_info'));
    }

    /**
     * Add administration menu and styles
     * */
    public function init() {


        // analyze competitors
        add_menu_page('Off Page SEO', 'Off Page SEO', 'read', 'ops', array($this, 'ops_dashboard'), 'dashicons-groups', 91);

        // settings
        add_submenu_page('ops', 'Analyze Competitors', 'Analyze Competitors', 'read', 'ops_analyze_competitors', array($this, 'ops_analyze_competitors'));

        // settings
        add_submenu_page('ops', 'Commenting', 'Commenting', 'read', 'ops_commenting', array($this, 'ops_commenting'));

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
    public function ops_add_dashboard_widget() {
        wp_add_dashboard_widget('cool_beans', 'Tutorials & Videos', array($this, 'ops_render_dashboard_widget'));
    }

    /**
     * Render Dashboard Widget
     */
    public function ops_render_dashboard_widget() {
        echo "this will be added in next versions";
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
    public function ops_analyze_competitors() {
        new OPS_Analyze_Competitors;
    }

    /**
     * Commenting Page Call
     */
    public function ops_commenting() {
        new OPS_Commenting;
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
     * Runs every 3,5 days and updates positions in Google
     */
    public static function ops_position_cron() {
        $settings = Off_Page_SEO::ops_get_settings();
        $diff = time() - $settings['last_check'];
        if ($diff > 302400 && !is_admin()) { // 302400
            // update last check first
            $now = time();
            $settings['last_check'] = $now;
            self::ops_update_option('ops_settings', serialize($settings));

            // insert iframe
            add_action('wp_head', array('Off_Page_SEO', 'ops_insert_iframe_for_possitions'));
        }
    }

    /**
     * First it inserts Iframe - in case there is some error, it will not crash the page
     */
    public static function ops_insert_iframe_for_possitions() {
        ?>
        <div style="display:none; overflow: hidden;">
            <iframe src="<?php echo get_the_permalink() ?>?update_positions=do_it" width="1" height="1"></iframe>
        </div>
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
        if ($diff > 86400) {
            $pr = new Page_Rank();
            $ar = new Alexa_Rank();
            $socials = file_get_contents('http://count.donreach.com/?url=' . $home);
            $socials = json_decode($socials);
            $alexa_rank = number_format($ar->get_rank($home));
            $page_rank = $pr->get_google_pagerank($home);

            $settings['last_check_site_info'] = $now;
            $settings['site_info']['page_rank'] = $page_rank;
            $settings['site_info']['alexa_rank'] = $alexa_rank;
            $settings['site_info']['facebook'] = $socials->shares->facebook;
            $settings['site_info']['twitter'] = $socials->shares->twitter;
            $settings['site_info']['google'] = $socials->shares->google;
            $settings['site_info']['pinterest'] = $socials->shares->pinterest;
            $settings['site_info']['stumbleupon'] = $socials->shares->stumbleupon;
            $settings['site_info']['delicious'] = $socials->shares->delicious;
            $settings['site_info']['reddit'] = $socials->shares->reddit;
            $settings['site_info']['linkedin'] = $socials->shares->linkedin;

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

}
