<?php

class OPS_Dashboard_Widget_Backlinks {

    /**
     * Initialization of Dashboard Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();
        $this->ops_render_backlinks($settings);
    }

    public function ops_render_backlinks($settings) {
        ?>
        <div class="wrapper" id="ops-widget-backlinks">
            <div class="wid-row">
                
                <!--OUR DATABASE-->
                <a href="admin.php?page=ops_backlinks&subcat=backlinks_feed" class="button button-primary">Our database</a>

                <!--GUEST POSTING-->
                <?php if ($settings['site_info']['guest_posting'] == '1'): ?>
                    <a href="admin.php?page=ops_backlinks&subcat=backlinks_gb" class="button button-primary">Guest Posting</a>
                <?php else: ?>
                    <a target="_blank" href="<?php echo Off_Page_SEO::$mother ?>/add?site_name=<?php echo urlencode(get_bloginfo('name')) ?>&lang=<?php echo $settings['lang'] ?>&site_url=<?php echo urlencode(get_home_url()) ?>&email=<?php echo urlencode(get_option('admin_email')) ?>" class="button button-primary">Join the Guest Posting Network</a>
                <?php endif; ?>

                <!--COMMENTING-->
                <a href="admin.php?page=ops_backlinks&subcat=backlinks_comment" class="button button-primary">Comment</a>

                <!--BUY BACKLINKS-->
                <a href="<?php echo OPS_Backlinks::ops_backlinks_buy_link(); ?>" target="_blank" class="button button-primary">Buy backlinks</a>
                
                <a href="admin.php?page=ops_knowledge_base" class="learn-more">Learn more about SEO.</a>
                <a href="admin.php?page=ops_analyze_keyword" class="learn-more">Start analyzing competitors.</a>
            </div>
        </div>
        <?php
    }

}
