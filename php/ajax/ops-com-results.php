<?php
require_once('../../../../../wp-load.php');
require_once('../tools/pagerank.php');
require_once('../tools/simple-html-dom.php');
require_once('../ops-backlinks-comment.php');
require_once('../ops.php');
$pr = new Page_Rank();
$settings = Off_Page_SEO::ops_get_settings();

// set some counters
$r = 1;
$c = 0;

// get query
$query = $_POST['query'];

$all_tails = OPS_Backlinks_Comment::ops_get_comment_queries($_POST['lang']);
$tail_string = $all_tails[$_POST['option']]['tail'];

// check if keyword is in it
if (strpos($tail_string, '%keyword%') !== false) {
    $q = str_replace('%keyword%', $query, $tail_string);
} else {
    $q = $query . ' ' . $tail_string;
}
$q = urlencode($q);




// get results

$url = 'http://www.google.'.$settings['google_domain'].'/search?hl=' . $_POST['lang'] . '&q=' . $q . '&num=30';

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

<?php if (isset($results)): ?>
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
            <a href="<?php echo $url ?>&start=30" class="button button-primary" target="_blank">More results on Google</a>
        </div>

    </div>
<?php else : ?>
    <div class="ops-com-results">
        <div class="error-message">
            Oops! You've been temporarily marked as bot for this query. Try another one or see the results on Google directly using link below.
        </div>
        <div class="more-results">
            <a href="<?php echo $url ?>" class="button button-primary" target="_blank">See results on Google</a>
        </div>
    </div>

<?php endif; ?>