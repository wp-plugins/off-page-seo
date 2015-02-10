<?php
require_once('../tools/pagerank.php');
require_once('../tools/simple-html-dom.php');
$pr = new Page_Rank();

// set some counters
$r = 1;
$c = 0;

// get query
$query = $_POST['query'];

// set tail
if ($_POST['option'] == 'edu-blogs') {
    $tail = 'site:.edu inurl:blog "post a comment" -"you must be logged in"';
} elseif ($_POST['option'] == 'gov-blogs') {
    $tail = 'site:.gov inurl:blog "post a comment" -"you must be logged in"';
} elseif ($_POST['option'] == 'html-comments') {
    $tail = '"Allowed HTML tags:"';
} elseif ($_POST['option'] == 'comment-luv-premium') {
    $tail = '"This blog uses premium CommentLuv" -"The version of CommentLuv on this site is no longer supported."';
} elseif ($_POST['option'] == 'do-follow-comments') {
    $tail = '"Notify me of follow-up comments?" "Submit the word you see below:"';
} elseif ($_POST['option'] == 'expression-engine') {
    $tail = '"powered by expressionengine"';
} elseif ($_POST['option'] == 'hubpages') {
    $tail = 'site:hubpages.com "hot hubs"';
} elseif ($_POST['option'] == 'keywordluv') {
    $tail = '"Enter YourName@YourKeywords"';
} elseif ($_POST['option'] == 'livefyre') {
    $tail = '"get livefyre" "comment help" -"Comments have been disabled for this post"';
} elseif ($_POST['option'] == 'intensedebate') {
    $tail = '"if you have a website, link to it here" "post a new comment"';
} elseif ($_POST['option'] == 'squidoo-addtolist') {
    $tail = 'site:squidoo.com "add to this list"';
} else {
    $tail = '';
}

// merge query and tail
$q = $query . ' ' . $tail;
$q = urlencode($q);


// get results
$start = $n * 10;
$url = 'http://www.google.com/search?hl=' . $_POST["lang"] . '&q=' . $q;

$str = ops_curl($url);

// parse to html
$html = str_get_html($str);

//return;
if ($html) {
    // if not bot, get results
    $linkObjs = $html->find('h3.r a');
    $descrObjs = $html->find('.s .st');

    $descr = array();
    foreach ($descrObjs as $descr) {
        $descrs[] = $descr->outertext;
    }

    foreach ($linkObjs as $linkObj) {
        $title = trim($linkObj->plaintext);
        $link = trim($linkObj->href);

        if (!preg_match('/^https?/', $link) && preg_match('/q=(.+)&amp;sa=/U', $link, $matches) && preg_match('/^https?/', $matches[1])) {
            $link = $matches[1];
        } else if (!preg_match('/^https?/', $link)) {
            continue;
        }

        $results[$c]['title'] = $title;
        $results[$c]['link'] = $link;
        $results[$c]['descr'] = $descrs[$c];
        $results[$c]['pr'] = $pr->get_google_pagerank($link);
        $c++;
    }
} else {
    // if bot, results are blank
    $results = '';
}
?>

<?php if ($results): ?>
    <!--RENDER RESULTS-->
    <meta http-equiv="content-Type" content="text/html; charset=utf-8"/>
    <div class="ops-com-results">

        <div class="result first-line">
            <div class="number">

            </div>
            <div class="google-like">
                <div class="title">
                    Search results
                </div>    
            </div>
            <div class="info">
                <div class="tab">
                    PageRank
                    <span>&nbsp;</span>
                </div>
            </div>
        </div>


    <?php foreach ($results as $result): ?>
            <div class="result">
                <div class="number">
                    #<?php echo $r ?>
                </div>
                <div class="google-like">
                    <a href="<?php echo $result['link']; ?>" target="_blank" class="site"><?php echo $result['title']; ?></a>
                    <div class="link">
                        <?php echo $result['link']; ?>
                    </div>
                    <div class="description">
        <?php echo $result['descr']; ?>
                    </div>
                </div>
                <div class="info">
                    <div class="tab">
        <?php echo $result['pr']; ?>
                    </div>
                </div>
            </div>
        <?php $r++ ?>
    <?php endforeach ?>
        <div class="more-results">
            <a href="<?php echo $url ?>" class="button button-primary" target="_blank">More results on Google</a>
        </div>
    </div>
<?php else : ?>
    <div class="ops-com-results">
        <div class="error-message">
            Ops. You've been temporarily marked as bot. You can still see results on Google.
        </div>
        <div class="more-results">
            <a href="<?php echo $url ?>" class="button button-primary" target="_blank">See results on Google</a>
        </div>
    </div>

<?php endif; ?>