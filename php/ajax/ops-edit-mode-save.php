<?php

require_once('../../../../../wp-load.php');
require_once('../ops.php');
require_once('../ops-rank-reporter.php');
$settings = Off_Page_SEO::ops_get_settings();

$n = 0;
$dates = array(
    'today' => time(),
    'yesterday' => time() - 86400,
    'three-days-ago' => time() - 259200,
    'week-ago' => time() - 604800
);
if (isset($_POST['links'])) {
    foreach ($_POST['links'] as $link) {
        $output[$n]['url'] = sanitize_text_field($link['url']);
        $output[$n]['type'] = sanitize_text_field($link['type']);
        $output[$n]['price'] = sanitize_text_field($link['price']);
        
        if (is_numeric($link['date'])) {
            $output[$n]['date'] = $link['date'];
        } else {
            $date = $link['date'];
            $output[$n]['date'] = $dates[$date];
        }
        $n++;
    }
} else {
    $output = array();
}
$data = serialize($output);
$update = OPS_Rank_Reporter::ops_update_backlinks_db($data, $_POST['rowid']);
OPS_Rank_Reporter::ops_render_backlinks($_POST['rowid']);
echo '<a href="#" class="ops-dash-edit-mode" data-rowid="' . $_POST['rowid'] . '">Edit mode</a>';
