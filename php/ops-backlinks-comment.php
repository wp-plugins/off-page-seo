<?php

class OPS_Backlinks_Comment {

    /**
     * Initialization of Commenting class
     * */
    public function __construct() {
        ?>
        <div class="wrap" id="ops-commenting">
            <h2 class="ops-h2">Comment</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li><a href="admin.php?page=ops_backlinks">Backlinks</a> &#8658;</li>
                    <li>Comment</li>
                </ul>
            </div>
            <div class="postbox ops-padding form-wrapper">
                <form action="" method="post">
                    <input type="text" name="query" placeholder="Please insert keyword" class="query" />
                    <input type="hidden" name="plugins_url" value="<?php echo plugins_url() ?>" />
                    <input type="hidden" name="lang" value="<?php echo Off_Page_SEO::ops_get_lang() ?>" />
                    <?php $queries = $this->ops_get_comment_queries(Off_Page_SEO::ops_get_lang()); ?>
                    <select name="option">
                        <?php foreach ($queries as $key => $query): ?>
                            <option value="<?php echo $key ?>"><?php echo $query['name'] ?></option>
                        <?php endforeach; ?>
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

    public static function ops_get_comment_queries($lang) {
        switch ($lang) {
            case 'en':
                $output = array(
                    'edu-blogs' => array(
                        'name' => '.edu Blogs',
                        'tail' => 'site:.edu inurl:blog "post a comment" -"you must be logged in"'
                    ),
                    'gov-blogs' => array(
                        'name' => '.gov Blogs',
                        'tail' => 'site:.gov inurl:blog "post a comment" -"you must be logged in"'
                    ),
                    'html-comments' => array(
                        'name' => 'Anchor Text In Comment Blogs',
                        'tail' => '"Allowed HTML tags:"'
                    ),
                    'comment-luv-premium' => array(
                        'name' => 'CommentLuv Premium Blogs',
                        'tail' => '"This blog uses premium CommentLuv" -"The version of CommentLuv on this site is no longer supported."'
                    ),
                    'do-follow-comments' => array(
                        'name' => 'Do Follow Comment Blogs',
                        'tail' => '"Notify me of follow-up comments?" "Submit the word you see below:"'
                    ),
                    'expression-engine' => array(
                        'name' => 'Expression Engine Forums',
                        'tail' => '"powered by expressionengine"'
                    ),
                    'hubpages' => array(
                        'name' => 'Hubpages - Hot Hubs',
                        'tail' => 'site:hubpages.com "hot hubs"'
                    ),
                    'keywordluv' => array(
                        'name' => 'KeywordLuv Blogs',
                        'tail' => '"Enter YourName@YourKeywords"'
                    ),
                    'livefyre' => array(
                        'name' => 'LiveFyre Blogs',
                        'tail' => '"get livefyre" "comment help" -"Comments have been disabled for this post"'
                    ),
                    'intensedebate' => array(
                        'name' => 'Intense Debate Blogs',
                        'tail' => '"if you have a website, link to it here" "post a new comment"'
                    ),
                    'squidoo-addtolist' => array(
                        'name' => 'Squidoo lenses - Add To List',
                        'tail' => 'site:squidoo.com "add to this list"'
                    )
                );
               
                break;
            
            case 'cs':
                $output = array(
                    'domain-kw' => array(
                        'name' => 'Klíčové slovo v URL',
                        'tail' => '"přidat komentář" inurl:%keyword%'
                    ),
                    'title-kw' => array(
                        'name' => 'Klíčové slovo v titulku',
                        'tail' => '"přidat komentář" intitle:%keyword%'
                    ),
                    'blog-cz' => array(
                        'name' => 'Blog.cz komentáře',
                        'tail' => 'site:blog.cz'
                    ),
                    'forum-kw' => array(
                        'name' => 'Forum s klíčovým slovem',
                        'tail' => '"forum" site:.cz'
                    ),
                    'forum-phpbb' => array(
                        'name' => 'Forum phpBB s klíčovým slovem',
                        'tail' => '"phpBB"'
                    ),
                    'forum-bbpress' => array(
                        'name' => 'Forum BBpress s klíčovým slovem',
                        'tail' => '"powered by BBpress"'
                    ),
                    'discuss-kw' => array(
                        'name' => 'Diskuze s klíčovým slovem',
                        'tail' => '"diskuze" site:.cz'
                    ),
                    'commentluv' => array(
                        'name' => 'CommentLuv',
                        'tail' => 'CommentLuv'
                    ),
                    
                );
                break;
        }
        return $output;
    }

}
