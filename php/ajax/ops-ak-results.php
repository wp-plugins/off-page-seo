<?php
require_once('../../../../../wp-load.php');
require_once('../tools/alexarank.php');
require_once('../tools/pagerank.php');
require_once('../tools/simple-html-dom.php');
require_once('../ops.php');
$settings = Off_Page_SEO::ops_get_settings();

$pr = new Page_Rank();
$ar = new Alexa_Rank();
$n = 1;

$url = 'http://www.google.'.$settings['google_domain'].'/search?hl=' . $settings['lang'] . '&start=0&q=' . urlencode($_POST['query']) . '&num=20';
$str = ops_curl($url);
$html = str_get_html($str);
$linkObjs = $html->find('h3.r a');

foreach ($linkObjs as $linkObj) {
    $results[$n]['rank'] = $n;
    $results[$n]['title'] = trim($linkObj->plaintext);

    $results[$n]['link'] = trim($linkObj->href);

    // if it is not a direct link but url reference found inside it, then extract
    if (!preg_match('/^https?/', $results[$n]['link']) && preg_match('/q=(.+)&amp;sa=/U', $results[$n]['link'], $matches) && preg_match('/^https?/', $matches[1])) {
        $results[$n]['link'] = $matches[1];
    } else if (!preg_match('/^https?/', $results[$n]['link'])) { // skip if it is not a valid link
        continue;
    }

    $results[$n]['pr'] = $pr->get_google_pagerank($results[$n]['link']);
    $results[$n]['ar'] = number_format($ar->get_rank($results[$n]['link']));
    $n++;
}
?>
<meta http-equiv="content-Type" content="text/html; charset=utf-8"/>
<div class="ops-ac-results">
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
                <span>of page</span>
            </div>
            <div class="tab">
                AlexaRank
                <span>of site</span>
            </div>
            <div class="tab">
                Open Link Profiler
                <span>unlimited</span>
            </div>
            <div class="tab">
                Ahrefs
                <span>1-2/day</span>
            </div>
            <div class="tab">
                Majestic
                <span>3-6/day</span>
            </div>
            <div class="tab">
                SEOMOZ
                <span>3/day</span>
            </div>
        </div>
    </div>
    <?php foreach ($results as $result) : ?>
        <div class="result">
            <div class="number">
                #<?php echo $result['rank'] ?>

            </div>
            <div class="google-like">
                <a href="<?php echo $result['link']; ?>" target="_blank" class="site"><?php echo $result['title']; ?></a>
                <div class="link">
                    <?php echo mb_substr(strip_tags($result['link']), 0, 60, 'UTF-8'); ?>
                    <?php echo (strlen($result['link']) > 60) ? "..." : ""; ?>
                </div>

            </div>
            <div class="info">
                <div class="tab">
                    <?php echo $result['pr']; ?>
                </div>
                <div class="tab">
                    <?php echo $result['ar']; ?>
                </div>

                <div class="tab">
                    <?php
                    $no_http = str_replace('http://', '', $result['link']);
                    $no_https = str_replace('https://', '', $no_http);
                    $exploded = explode('/', $no_https);
                    $domain = $exploded[0];
                    ?>
                    <a href="http://www.openlinkprofiler.org/r/<?php echo $domain ?>?special=all&st=0&sq=&dt=0&dq=&trust=all&tt=0&tq=&at=0&aq=&ind=all&cat=all&tld=all&follow=all&found=0&num=20&sort=12&filter=Filter+backlinks" target="_blank" class="logo olp"><img src="<?php echo $_POST['plugins_url'] ?>/off-page-seo/img/logo-olp.png" alt="open link profiler" /></a>
                </div>


                <div class="tab">
                    <a href="https://ahrefs.com/site-explorer/overview/subdomains/?target=<?php echo urlencode($result['link']) ?>" target="_blank" class="logo ah"><img src="<?php echo $_POST['plugins_url'] ?>/off-page-seo/img/logo-ah.png" alt="ahrefs" /></a>
                </div>

                <div class="tab">
                    <a href="https://majestic.com/reports/site-explorer?q=<?php echo urlencode($result['link']) ?>&IndexDataSource=F" target="_blank" class="logo ms"><img src="<?php echo $_POST['plugins_url'] ?>/off-page-seo/img/logo-ms.png" alt="majestic seo" /></a>
                </div>

                <div class="tab">
                    <a href="https://moz.com/researchtools/ose/links?site=<?php echo urlencode($result['link']) ?>&filter=&source=external&target=page&group=0&page=1&sort=page_authority&anchor_id=&anchor_type=&anchor_text=&from_site=" target="_blank" class="logo ole"><img src="<?php echo $_POST['plugins_url'] ?>/off-page-seo/img/logo-ose.png" alt="open site explorer" /></a>
                </div>

            </div>

        </div>
    <?php endforeach; ?>

</div>