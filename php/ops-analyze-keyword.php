<?php

class OPS_Analyze_Keyword {

    /**
     * Initialization of Competitors class
     * */
    public function __construct() {
        ?>
        <div class="wrap" id="ops-analyze-competitors">
            <h2 class="ops-h2">Analyze Keyword</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li>Analyze Keyword</li>
                </ul>
            </div>
            <div class="postbox ops-padding form-wrapper">
                <form action="" method="post">
                    <input type="text" name="query" placeholder="Please insert keyword" class="query" value="<?php echo (isset($_GET['ops_kw']) && $_GET['ops_kw'] != '') ? urldecode(sanitize_text_field($_GET['ops_kw'])) : ""; ?>" />
                    <input type="hidden" name="plugins_url" value="<?php echo plugins_url() ?>" />
                    <input type="hidden" name="lang" value="<?php echo Off_Page_SEO::ops_get_lang() ?>" />
                    <input type="submit" value="Analyze" class="button button-primary"/>
                </form>
                <span class="preloader"></span>
            </div>

            <div class="postbox results">
                <div class="branding branding-ac">
                    <div class="text">
                        Analyze your competitors using the most popular SEO tools
                        <div class="subtext">
                            and get similar high authority backlinks.
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <?php if (isset($_GET['ops_kw']) && $_GET['ops_kw'] != ''): ?>
            <script>
                jQuery(document).ready(function ($) {
                    var query = '<?php echo urldecode(sanitize_text_field($_GET['ops_kw'])) ?>';
                    var plugins_url = $('#ops-analyze-competitors form input[name=plugins_url]').val();
                    var lang = $('#ops-analyze-competitors form input[name=lang]').val();
                    $('.branding-ac').delay(1000).fadeOut(1500);
                    // show before
                    $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-ak-results.php') ?>',
                        type: "POST",
                        data: {query: query, plugins_url: plugins_url, lang: lang},
                        beforeSend: function () {
                            $('#ops-analyze-competitors .preloader').html('<img src="<?php echo plugins_url('off-page-seo/img/preloader.GIF') ?>" />');
                        },
                        success: function (data) {

                            $('#ops-analyze-competitors .results').html(data);
                            $('#ops-analyze-competitors .preloader').html('');
                        },
                        error: function () {
                        }
                    });
                });
            </script>
        <?php endif ?>

        <script>
            jQuery(document).ready(function ($) {
                $('#ops-analyze-competitors form').on('submit', function (e) {
                    e.preventDefault();
                    var query = $('#ops-analyze-competitors form input[name=query]').val();
                    var plugins_url = $('#ops-analyze-competitors form input[name=plugins_url]').val();
                    var lang = $('#ops-analyze-competitors form input[name=lang]').val();
                    $('.branding-ac').delay(1000).fadeOut(1500);
                    // show before
                    $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-ak-results.php') ?>',
                        type: "POST",
                        data: {query: query, plugins_url: plugins_url},
                        beforeSend: function () {
                            $('#ops-analyze-competitors .preloader').html('<img src="<?php echo plugins_url('off-page-seo/img/preloader.GIF') ?>" />');
                        },
                        success: function (data) {

                            $('#ops-analyze-competitors .results').html(data);
                            $('#ops-analyze-competitors .preloader').html('');
                        },
                        error: function () {
                        }
                    });
                });
            });
        </script>

        <?php
    }

}
