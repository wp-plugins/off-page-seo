<?php

class OPS_Meta_Box_Shares {

    /**
     * Initialization of Dashboard Class
     * */
    public static function init() {
        $settings = Off_Page_SEO::ops_get_settings();
        self::ops_render_meta_box_shares($settings);
    }

    public static function ops_render_meta_box_shares($settings) {
        ?>
        <div class="wrapper" id="ops-meta-box-shares">
            <?php
            $pid = sanitize_text_field($_GET['post']);
            $meta = get_post_meta($pid);
            $shares = unserialize($meta['ops_shares'][0]);
            ?>
            <?php foreach ($shares['count'] as $key => $value): ?>
                <div class="ops-row <?php echo $key ?>">
                    <?php echo $value; ?>
                </div>
            <?php endforeach; ?>
            <p>Social Shares for this article.</p>
        </div>
        <?php
    }

}
