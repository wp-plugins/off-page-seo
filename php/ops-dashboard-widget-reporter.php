<?php

class OPS_Dashboard_Widget_Reporter {

    /**
     * Initialization of Dashboard Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();
        $this->ops_render_positions($settings);
    }

    public function ops_render_positions($settings) {
        ?>
        <div class="ops-dashboard-widget">
            <?php
            $n = 0;
            if (isset($settings['graphs'])):
                foreach ($settings['graphs'] as $graph) :
                    $n++;

                    // set positions
                    $positions = OPS_Rank_Reporter::ops_get_positions($graph['url'], $graph['keyword']);
                    ?>

                    <div class="ops-row">

                        <!--CONTAINER-->
                        <div class="left-col">

                            <div class="ops-graph-kw">
                                <?php echo $graph['keyword'] ?>
                                <a href="http://www.google.<?php echo $settings['google_domain'] ?>/search?hl=<?php echo $settings['lang'] ?>&q=<?php echo urlencode($graph['keyword']) ?>" target="_blank">
                                    <img src="<?php echo plugins_url('off-page-seo/img/icon-link.png') ?>" />
                                </a>
                                <?php if (isset($graph['volume']) && $graph['volume']): ?>
                                    <span class="ops-volume">(<?php echo $graph['volume'] ?> per month)</span>
                                <?php endif; ?>
                            </div>
                            <div class="ops-graph-url">
                                <?php echo $graph['url'] ?>
                            </div>

                        </div>

                        <div class="right-col">
                            <!--NOW-->
                            <?php if (isset($positions[0]['position'])): ?>
                                <div class="position">
                                    <strong><?php echo $positions[0]['position'] ?></strong>
                                </div>
                            <?php endif; ?>

                            <!--WEEK AGO-->
                            <?php if (isset($positions[1]['position'])): ?>
                                <div class="position">
                                    <?php echo $positions[1]['position'] ?>
                                </div>
                            <?php endif; ?>

                            <!--MONTH AGO-->
                            <?php if (isset($positions[2]['position'])): ?>
                                <div class="position">
                                    <?php echo $positions[2]['position'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <?php if ($n == 2 && Off_Page_SEO::ops_is_premium() == 0): ?>
                        You are using free version. Please <a href="<?php echo Off_Page_SEO::$mother ?>" target="_blank">upgrade to premium</a> to analyze more keywords.
                        <?php break; ?>
                    <?php endif; ?>

                <?php endforeach; ?> 
                <div class="ops-schedule">
                    Last check: <strong><?php echo date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check']); ?></strong>
                    <br/>
                    Next scheduled check: <strong><?php echo date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check'] + 259200); ?></strong>
                </div>
            <?php else: ?>
                <a href="admin.php?page=ops_settings">Please specify the keywords and language in Settings.</a>
            <?php endif; ?>
        </div>
        <?php
    }

}
