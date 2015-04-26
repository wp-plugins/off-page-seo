<?php

require_once('../../../../../wp-load.php');
require_once('../ops.php');
require_once('../ops-rank-reporter.php');
$settings = Off_Page_SEO::ops_get_settings();
$n = 0;
if (isset($_POST['links'])) {
    foreach ($_POST['links'] as $link) {
        $output[$n]['url'] = sanitize_text_field($link['url']);
        $output[$n]['type'] = sanitize_text_field($link['type']);
        $output[$n]['price'] = sanitize_text_field($link['price']);

        if (isset($link['reciprocal'])) {
            $output[$n]['reciprocal'] = 1;
        } else {
            $output[$n]['reciprocal'] = 0;
        }
        
        if (isset($link['reciprocal_referer'])) {
            $output[$n]['reciprocal_referer'] = sanitize_text_field($link['reciprocal_referer']);
        } else {
            $output[$n]['reciprocal_referer'] = 0;
        }
        
        if (isset($link['reciprocal_status'])) {
            $output[$n]['reciprocal_status'] = sanitize_text_field($link['reciprocal_status']);
        } else {
            $output[$n]['reciprocal_status'] = 4;
        }

        if ($link['date'] == '') {
            $date = time();
        } else {
            $date = strtotime(sanitize_text_field($link['date']));
        }
        
        $output[$n]['comment'] = sanitize_text_field($link['comment']);

        $output[$n]['date'] = sanitize_text_field($date);

        $n++;
    }
} else {
    $output = array();
}
$data = serialize($output);
$update = OPS_Rank_Reporter::ops_update_backlinks_db($data, $_POST['rowid']);
OPS_Rank_Reporter::ops_render_backlinks($_POST['rowid']);
echo '<a href="#" class="ops-dash-edit-mode" data-rowid="' . $_POST['rowid'] . '">Edit mode</a>';
