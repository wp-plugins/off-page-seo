<?php

class OPS_Backlinks_GB {

    /**
     * Initialization of GB Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();

        // search
        $search = '';
        if (isset($_GET['ops_search']) && $_GET['ops_search'] != '') {
            $search = sanitize_text_field($_GET['ops_search']);
        }



        // feed from another site
        $url = Off_Page_SEO::$mother . '/api/guest-posting/?lang=' . $settings['lang'] . '&ops_search=' . $search;
        $data = ops_curl($url, 1);
        ?>

        <!--RENDER-->
        <div class="wrap" id="ops-pr-submissions">
            <h2 class="ops-h2">Guest Posting</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li><a href="admin.php?page=ops_backlinks">Backlinks</a> &#8658;</li>
                    <li>Guest Posting</li>
                </ul>
            </div>
            <!--FILTER-->
            <div class="postbox ops-padding form-wrapper">
                <form method="get" action="admin.php">
                    <input type="text" name="ops_search" placeholder="Search" value="<?php echo $search ?>" />
                    <input type="hidden" name="page" value="ops_backlinks">
                    <input type="hidden" name="subcat" value="backlinks_gb">
                    <input type="submit" value="Apply" class="button button-primary"/>
                    <span>Language: <?php echo Off_Page_SEO::ops_get_language($settings['lang']); ?></span>
                </form>
            </div>

            <!--FEED-->
            <div class="remote-feed">
                <?php echo $data; ?>
            </div>

        </div>
        <script>
            jQuery(document).ready(function ($) {
                $('body, html').on('click', '.owner-email a', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    var aObj = $(this);
                    var email = $(this).data('mail');
                    $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-send-mail.php') ?>',
                        type: "POST",
                        data: {email: email},
                        success: function (data) {
                            $(aObj).closest('.owner-email').html(data);
                        },
                        error: function (data) {
                            $(aObj).closest('.owner-email').html(data);
                        }
                    });
                });
            });
        </script>

        <?php
    }

}
