<?php

class OPS_Knowledge_Base {

    /**
     * Initialization of Knowledge Base Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();
        $url = Off_Page_SEO::$mother . '/api/knowledge-base/?lang=' . $settings['lang'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        ?>
        <div class="wrap" id="ops-knowledge-base">
            <h2 class="ops-h2">Knowledge Base</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li>Knowledge Base</li>
                </ul>
            </div>
            <div class="ops-knowledge-base-feed">
                <?php echo $data; ?>
            </div>

        </div>
        <?php
    }

}
