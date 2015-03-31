<?php

class OPS_Backlinks {

    /**
     * Initialization of PR Submission Class
     * */
    public function __construct() {
        if (!isset($_GET['subcat'])) {
            $this->ops_backlinks_main();
        } elseif ($_GET['subcat'] == 'backlinks_feed') {
            $this->ops_backlinks_feed();
        } elseif ($_GET['subcat'] == 'backlinks_comment') {
            $this->ops_backlinks_comment();
        } elseif ($_GET['subcat'] == 'backlinks_gb') {
            $this->ops_backlinks_gb();
        }
    }

    public function ops_backlinks_main() {
        $settings = Off_Page_SEO::ops_get_settings();
        ?>
        <div class="wrap">

            <h2 class="ops-h2">Backlink Opportunities</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li>Backlinks</li>
                </ul>
            </div>

            <div class="backlink-tab postbox ops-padding">
                <div class="ops-letter">D.</div>
                <div class="right-wrapper">
                    <div class="explore">
                        <a href="admin.php?page=ops_backlinks&subcat=backlinks_feed" class="button button-primary">Explore !</a>
                    </div>
                    <div class="customs">
                        <h3>Our database</h3>
                        <a href="admin.php?show=0&type=directory&ops_search=&page=ops_backlinks&subcat=backlinks_feed">Directories</a><br/>
                        <a href="admin.php?show=0&type=pr-website&ops_search=&page=ops_backlinks&subcat=backlinks_feed">PR Websites</a><br/>
                        <a href="admin.php?show=0&type=bookmarks&ops_search=&page=ops_backlinks&subcat=backlinks_feed">Bookmarks</a><br/>
                        <a href="admin.php?show=0&type=visitor-counter&ops_search=&page=ops_backlinks&subcat=backlinks_feed">Visitor Counters</a>
                    </div>
                </div>
            </div>

            <div class="backlink-tab postbox ops-padding">
                <div class="ops-letter">G.</div>
                <div class="right-wrapper">
                    <?php if ($settings['site_info']['guest_posting'] == '1'): ?>
                        <div class="explore">
                            <a href="admin.php?page=ops_backlinks&subcat=backlinks_gb" class="button button-primary">Explore !</a>
                        </div>
                        <div class="customs">
                            <h3>Guest Posting</h3>
                            <p>Start guest posting!</p>
                            <a target="_blank" href="<?php echo Off_Page_SEO::$mother ?>/api/remove-site?site_name=<?php echo urlencode(get_bloginfo('name')) ?>&lang=<?php echo $settings['lang'] ?>&site_url=<?php echo urlencode(get_home_url()) ?>&email=<?php echo urlencode(get_option('admin_email')) ?>">Leave network</a>
                        </div>
                    <?php else: ?>
                        <div class="explore">
                            <a target="_blank" href="<?php echo Off_Page_SEO::$mother ?>/api/add-site?site_name=<?php echo urlencode(get_bloginfo('name')) ?>&lang=<?php echo $settings['lang'] ?>&site_url=<?php echo urlencode(get_home_url()) ?>&email=<?php echo urlencode(get_option('admin_email')) ?>" class="button button-primary ops-join">Join !</a>
                        </div>
                        <div class="customs">
                            <h3>Guest Posting</h3>
                            <p>Join the Guest Posting Network today!</p>
                            <p><a href="admin.php?page=ops_settings&signed=true&saved=true">Already signed up?</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>



            <div class="backlink-tab postbox ops-padding">
                <div class="ops-letter">C.</div>
                <div class="right-wrapper">
                    <div class="explore">
                        <a href="admin.php?page=ops_backlinks&subcat=backlinks_comment" class="button button-primary">Comment !</a>
                    </div>
                    <div class="customs">
                        <h3>Comment</h3>
                        <p>Search for new comment opportunities.</p>
                    </div>
                </div>
            </div>


            <div class="backlink-tab postbox ops-padding">
                <div class="ops-letter">B.</div>
                <div class="right-wrapper">
                    <div class="explore">
                        <a href="<?php echo $this->ops_backlinks_buy_link(); ?>" class="button button-primary" target="_blank">Buy backlinks !</a>
                    </div>
                    <div class="customs">
                        <h3>Buy backlinks</h3>
                        <p>Let other people do SEO for you.</p>
                        <p><i>Be careful, bad links can harm you.</i></p>
                    </div>
                </div>
            </div>


        </div>
        <?php
    }

    public function ops_backlinks_feed() {
        new OPS_Backlinks_Feed();
    }

    public function ops_backlinks_comment() {
        new OPS_Backlinks_Comment();
    }

    public function ops_backlinks_gb() {
        new OPS_Backlinks_GB();
    }

    public static function ops_backlinks_buy_link() {
        $lang = Off_Page_SEO::ops_get_lang();
        if ($lang == 'cs') {
            $url = 'http://www.stovkomat.cz/kategorie/zpetne-odkazy?af=czechstudio';
        } elseif ($lang == 'en') {
            $url = 'http://tracking.fiverr.com/aff_c?offer_id=1712&aff_id=6020&url_id=190';
        }
        return $url;
    }

}
