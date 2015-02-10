<?php

class OPS_Commenting {

    /**
     * Initialization of Commenting class
     * */
    public function __construct() {
        ?>
        <div class="wrap" id="ops-commenting">
            <h2 class="ops-h2">Commenting</h2>
            <div class="postbox ops-padding form-wrapper">
                <form action="" method="post">
                    <input type="text" name="query" placeholder="Please insert keyword" class="query" />
                    <input type="hidden" name="plugins_url" value="<?php echo plugins_url() ?>" />
                    <input type="hidden" name="lang" value="<?php echo Off_Page_SEO::ops_get_lang() ?>" />

                    <select name="option">
                        <option value="edu-blogs">.edu Blogs</option>
                        <option value="gov-blogs">.gov Blogs</option>
                        <option value="html-comments">Anchor Text In Comment Blogs</option>
                        <option value="comment-luv-premium">CommentLuv Premium Blogs</option>
                        <option value="do-follow-comments">Do Follow Comment Blogs</option>
                        <option value="expression-engine">Expression Engine Forums</option>
                        <option value="hubpages">Hubpages - Hot Hubs</option>
                        <option value="keywordluv">KeywordLuv Blogs</option>
                        <option value="livefyre">LiveFyre Blogs</option>
                        <option value="intensedebate">Intense Debate Blogs</option>
                        <option value="squidoo-addtolist">Squidoo lenses - Add To List</option>
                    </select>
                    <input type="submit" value="Search" class="button button-primary"/>
                </form>
                <span class="preloader"></span>
            </div>

            <div class="postbox results">
                <div class="branding branding-comments">
                    <div class="text">
                        Search opportunities to post a comment.
                        <div class="subtext">
                            Every buzz is a good thing.
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                $('#ops-commenting form').on('submit', function (e) {
                    e.preventDefault();
                    var query = $('#ops-commenting form input[name=query]').val();
                    var option = $('#ops-commenting form select[name=option]').val();
                    var plugins_url = $('#ops-commenting form input[name=plugins_url]').val();
                    var lang = $('#ops-commenting form input[name=lang]').val();
                    $('.branding-comments').fadeOut(1500);
                    // show before
                    $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-com-results.php') ?>',
                        type: "POST",
                        data: {query: query, plugins_url: plugins_url, option: option, lang: lang},
                        beforeSend: function () {
                            $('#ops-commenting .preloader').html('<img src="<?php echo plugins_url('off-page-seo/img/preloader.GIF') ?>" />');
                        },
                        success: function (data) {
                            $('#ops-commenting .results').html(data);
                            $('#ops-commenting .preloader').html('');
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
