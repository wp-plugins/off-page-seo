<?php

class OPS_Backlinks {

    /**
     * Initialization of PR Submission Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();

        /*
         * Statuses 0 = show all
         *          1 = show free only
         *          2 = show paid only
         */
        $show = 0;
        if (isset($_GET['show']) && $_GET['show'] != 0) {
            $show = sanitize_text_field($_GET['show']);
        }

        // search
        $search = '';
        if (isset($_GET['ops_search']) && $_GET['ops_search'] != '') {
            $search = sanitize_text_field($_GET['ops_search']);
        }
        
        $type = 'all-types';
        if (isset($_GET['type']) && $_GET['type'] != '') {
            $type = sanitize_text_field($_GET['type']);
        }

        // feed from another site
        $url = Off_Page_SEO::$mother . '/pr-submissions/?lang=' . $settings['lang'] . '&show=' . $show . '&ops_search=' . $search . '&type=' . $type;
        $data = ops_curl($url, 1);
        ?>

        <!--RENDER-->
        <div class="wrap" id="ops-pr-submissions">
            <h2 class="ops-h2">Backlinks</h2>
            <!--FILTER-->
            <div class="postbox ops-padding form-wrapper">
                <form method="get" action="">
                    Price: 
                    <select name="show">
                        <option value="0" <?php echo ($show == 0) ? "selected" : ""; ?>>Show all</option>
                        <option value="1" <?php echo ($show == 1) ? "selected" : ""; ?>>Show free only</option>
                        <option value="2" <?php echo ($show == 2) ? "selected" : ""; ?>>Show paid only</option>
                    </select>
                    Type: 
                    <select name="type">
                        <option value="all-types" <?php echo ($type == 'bookmarks') ? "selected" : ""; ?>>All types</option>
                        <option value="bookmarks" <?php echo ($type == 'bookmarks') ? "selected" : ""; ?>>Bookmarks</option>
                        <option value="directory" <?php echo ($type == 'directory') ? "selected" : ""; ?>>Directory</option>
                        <option value="pr-website" <?php echo ($type == 'pr-website') ? "selected" : ""; ?>>PR Website</option>
                        <option value="visitor-counter" <?php echo ($type == 'visitor-counter') ? "selected" : ""; ?>>Visitor counter</option>
                    </select>
                    <input type="text" name="ops_search" placeholder="Search" value="<?php echo $search ?>" />
                    <input type="hidden" name="page" value="ops_pr_submissions" />
                    <input type="submit" value="Apply" class="button button-primary"/>
                    <span>Language: <?php echo Off_Page_SEO::ops_get_language($settings['lang']);?></span>
                </form>
            </div>

            <!--FEED-->
            <div class="remote-feed">
                <?php echo $data; ?>
            </div>

        </div>

        <?php
    }
   
}
