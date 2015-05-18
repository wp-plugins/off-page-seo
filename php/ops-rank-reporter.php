<?php

class OPS_Rank_Reporter {

    /**
     * Initialization of Rank Reporter Class
     * */
    public function __construct() {
        wp_enqueue_script('jquery-ui-datepicker');

        $settings = Off_Page_SEO::ops_get_settings();
        if (isset($settings['graphs']) && count($settings['graphs']) > 0) {
            // if we have graphs, fire render functions
            $this->ops_render_master_graph($settings);
            $this->ops_render_positions($settings);
        } else {
            // if we don't have any graphs yet
            ?>
            <a href="admin.php?page=ops_settings">Please specify the keywords and language in Settings.</a>
            <?php
        }
    }

    /**
     * Renders main graph
     * @param type $settings
     */
    public function ops_render_master_graph($settings) {
        $n = 0;
        foreach ($settings['graphs'] as $graph) {
            if (isset($graph['master']) && $graph['master'] == 'on') {
                $positions[$n]['keyword'] = $graph['keyword'];
                $positions[$n]['positions'] = $this->ops_get_positions($graph['url'], $graph['keyword']);
                $n++;
            }
        }
        ?>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="http://code.highcharts.com/modules/exporting.js"></script>

        <?php if (isset($positions[0]['positions'][0])): ?>
            <div class="postbox">
                <div id="master-graph" style="width: 95%; height: 400px;"></div>
            </div>


            <script>
            <?php $highest_value = array() ?>
                //zzz
                jQuery(document).ready(function ($) {
                $('#master-graph').highcharts({
                chart: {
                type: 'spline'
                },
                        title: {
                        text: ''
                        },
                        xAxis: {
                        type: 'datetime',
                                dateTimeLabelFormats: {// don't display the dummy year
                                month: '%e. %b',
                                        year: '%b'
                                },
                                title: {
                                text: 'Date'
                                }
                        },
                        tooltip: {
                        headerFormat: '{series.name} - {point.x:%e. %b}<br/>',
                                pointFormat: ' <b>{point.y}</b>'
                        },
                        plotOptions: {
                        spline: {
                        marker: {
                        enabled: true
                        }
                        }
                        },
                        series: [
            <?php $r = 0 ?>
            <?php foreach ($positions as $position) : $r++; ?>
                <?php $n = 0 ?>
                            {
                            name: '<?php echo $position['keyword'] ?>',
                                    data: [
                <?php foreach ($position['positions'] as $single_position): $n++; ?>
                                        [Date.UTC(<?php echo date('Y, m, d, H, i', $single_position['time']) ?>), <?php echo $single_position['position'] ?>],
                    <?php $highest_value[] = $single_position['position']; ?>
                    <?php
                    if ($n > 20) {
                        break;
                    }
                    ?>
                <?php endforeach; ?>
                                    ]
                            },
                <?php if ($r == 2 && Off_Page_SEO::ops_is_premium() == 0): ?>
                    <?php break; ?>
                <?php endif; ?>
            <?php endforeach; ?>
                        ],
                        yAxis: {
                        title: {
                        text: 'Position'
                        },
                                reversed: true,
                                min: 0,
                                max: <?php echo max($highest_value) ?>
                        }
                });
                });</script>

        <?php endif; ?>
        <?php
    }

    /**
     * Display all graphs we have set up
     * @param type $settings
     */
    public function ops_render_positions($settings) {
        ?>
        <div class="postbox">

            <h3 class="ops-h3">Rank Checker</h3>
            <div class="ops-black-overlay"></div>
            <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
            <script>
                        jQuery(document).ready(function ($) {
                $('body').on('click', '.ops-dash-edit-mode', function (e) {
                e.preventDefault();
                        var thisObj = $(this);
                        var rowId = $(this).data('rowid');
                        $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-edit-mode-load.php') ?>',
                                type: "POST",
                                data: {rowId: rowId},
                                success: function (data) {
                                $(thisObj).parent().html(data);
                                },
                                error: function () {
                                }
                        });
                });
                        $('body').on('submit', 'form.ops-backlink-edit', function (e) {
                e.preventDefault();
                        var thisObj = $(this);
                        var postData = $(this).serializeArray();
                        $.ajax({
                        url: '<?php echo plugins_url('off-page-seo/php/ajax/ops-edit-mode-save.php') ?>',
                                type: "POST",
                                data: postData,
                                success: function (data) {
                                $(thisObj).parent().html(data);
                                },
                                error: function () {
                                }
                        });
                });
                });</script>
            <?php
            $gid = 0;
            $total_graphs = count($settings['graphs']);
            foreach ($settings['graphs'] as $graph) {
                $gid++;

                // set positions
                $positions = $this->ops_get_positions($graph['url'], $graph['keyword']);
                $row_data = $this->ops_get_row_by_url_and_keyword($graph['url'], $graph['keyword']);
                $row_id = $row_data['id'];
                $links = unserialize($row_data['links']);
                ?>
                <div class="ops-kw-graph-wrapper">

                    <div class="ops-kw-wrapper ops-padding">
                        <!--CONTAINER-->
                        <div class="left-col">

                            <div class="ops-graph-kw">
                                <?php echo $graph['keyword'] ?>
                                <a href="http://www.google.<?php echo $settings['google_domain'] ?>/search?hl=<?php echo $settings['lang'] ?>&q=<?php echo urlencode($graph['keyword']) ?>" target="_blank" class="ops-external-link">
                                    <img src="<?php echo plugins_url('off-page-seo/img/icon-link.png') ?>" />
                                </a>

                                <?php if (isset($graph['volume']) && $graph['volume']): ?>
                                    <span class="ops-volume">(<?php echo $graph['volume'] ?> per month)</span>
                                <?php endif; ?>

                                <a href="admin.php?page=ops_analyze_keyword&ops_kw=<?php echo urlencode($graph['keyword']) ?>" class="ops-analyze-kw">Analyze KW</a>
                            </div>

                            <div class="ops-graph-url">
                                <?php echo $graph['url'] ?>
                                <a href="<?php echo $graph['url'] ?>" target="_blank" class="ops-external-link">
                                    <img src="<?php echo plugins_url('off-page-seo/img/icon-link.png') ?>" />
                                </a>
                            </div>

                        </div>

                        <div class="right-col">
                            <?php if (isset($positions[0]['position'])): ?>
                                <div class="ops-show-graph">
                                    <a href="" class="button button-primary">Analyze</a><br/>
                                    <a href="" class="ops-show-backlinks">Backlinks (<?php echo $this->ops_count_backlinks($row_id) ?>)</a>
                                </div>
                            <?php else : ?>
                                <div class="ops-show-graph">
                                    No data, next scheduled check: <strong><?php echo date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check'] + 259200); ?></strong> or you can force test in Settings.
                                </div>
                            <?php endif; ?>

                            <!--NOW-->
                            <?php if (isset($positions[0]['position'])): ?>
                                <div class="position">
                                    <span class="when-time"></span> 
                                    <u><?php echo $positions[0]['position'] ?></u>
                                    <?php
                                    if (!isset($positions[1]['position'])) {
                                        $positions[1]['position'] = $positions[0]['position'];
                                    }
                                    ?>
                                    <?php $diff = $positions[1]['position'] - $positions[0]['position']; ?>
                                    <?php
                                    if ($diff < 0) {
                                        $class = 'bad';
                                        $arrow = '&darr;';
                                    } elseif ($diff == 0) {
                                        $class = 'neutral';
                                        $arrow = '';
                                    } else {
                                        $class = 'good';
                                        $arrow = '&uarr;';
                                    }
                                    ?>
                                    <span class="change <?php echo $class ?>"><?php echo $diff . ' ' . $arrow; ?></span>
                                </div>
                            <?php endif; ?>

                            <!--WEEK AGO-->
                            <?php if (isset($positions[1]['position'])): ?>
                                <div class="position">
                                    <span class="when-time"></span> <?php echo $positions[1]['position'] ?>
                                </div>
                            <?php endif; ?>

                            <!--MONTH AGO-->
                            <?php if (isset($positions[2]['position'])): ?>
                                <div class="position">
                                    <span class="when-time"></span> <?php echo $positions[2]['position'] ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="ops-backlinks-list" style="display:none;">
                        <?php $price_total = $this->ops_render_backlinks($row_id); ?>
                        <div class="ops-reciprocal-info">
                            <?php if (isset($price_total) && $price_total > 0): ?>
                                Total backlinks costs : <b><?php echo number_format($price_total) ?> <?php echo ($settings['currency']) ? $settings['currency'] : "$"; ?></b> | 
                            <?php endif; ?>

                            <?php if (Off_Page_SEO::ops_is_premium() == 1): ?>
                                <?php if (isset($settings['reciprocal_control']) && $settings['reciprocal_control'] == 'on'): ?>
                                    Next backlink check will be on <b><?php echo date(Off_Page_SEO::ops_get_date_format(), $settings['reciprocal_timer']) ?></b>.
                                <?php else : ?>
                                    Reciprocal checking is turned off. You can turn it on in <a href="admin.php?page=ops_settings">Settings</a>. But first select which backlinks to check (in the edit mode).
                                <?php endif; ?>
                            <?php else : ?>
                                    Reciprocal checking is unavailable in free version.
                            <?php endif; ?>
                        </div>
                        <a href="#" class="ops-dash-edit-mode" data-rowid="<?php echo $row_id ?>">Edit mode</a>
                    </div>

                    <div class="ops-graph-wrapper">
                        <div class="ops-graph" id="ops-graph-<?php echo $gid ?>"></div>
                    </div>


                    <?php if ($positions): ?>
                        <script>
                                    //xxx
                                    jQuery(document).ready(function ($) {
                            $('#ops-graph-<?php echo $gid ?>').highcharts({
                            chart: {
                            },
                                    title: {
                                    text: '<?php echo $graph['keyword'] ?>'
                                    },
                                    xAxis: [{
                                    categories: [<?php $this->ops_render_graph_categories($positions) ?>],
                                            crosshair: false,
                                            reversed: true
                                    }],
                                    yAxis: [{// Primary yAxis
                                    labels: {
                                    format: '{value}',
                                            style: {
                                            color: Highcharts.getOptions().colors[1]
                                            }
                                    },
                                            title: {
                                            text: 'Position',
                                                    style: {
                                                    color: Highcharts.getOptions().colors[1]
                                                    }
                                            },
                                            reversed: true,
                                            min: 0,
                                            max: 100
                                    }, {// Secondary yAxis
                                    title: {
                                    text: 'Backlinks',
                                            style: {
                                            color: Highcharts.getOptions().colors[0]
                                            }
                                    },
                                            labels: {
                                            format: '{value}',
                                                    style: {
                                                    color: Highcharts.getOptions().colors[0]
                                                    }
                                            },
                                            opposite: true
                                    }],
                                    tooltip: {
                                    shared: true
                                    },
                                    legend: {
                                    layout: 'vertical',
                                            align: 'left',
                                            x: 120,
                                            verticalAlign: 'top',
                                            y: 40,
                                            floating: true,
                                            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                                    },
                                    series: [{
                                    name: 'Backlinks',
                                            type: 'column',
                                            yAxis: 1,
                                            data: [<?php $this->ops_render_graph_backlinks($positions, $row_id) ?>],
                                            tooltip: {
                                            valueSuffix: ''
                                            }

                                    }, {
                                    name: 'Position',
                                            type: 'spline',
                                            data: [<?php $this->ops_render_graph_positions($positions); ?>],
                                            tooltip: {
                                            valueSuffix: ''
                                            }
                                    }]
                            });
                            });
                        </script>
                    <?php endif; ?>
                </div>
                <?php if ($gid == 2 && Off_Page_SEO::ops_is_premium() == 0): ?>
                    <div class="ops-dashboard-premium">
                        <div class="ops-left-col">
                            <a href="<?php echo Off_Page_SEO::$mother; ?>" class="ops-logo" target="_blank"></a>
                            <a href="<?php echo Off_Page_SEO::$mother; ?>" class="ops-buy" target="_blank">Buy premium</a>
                            <span>3 licences only for $19 / year</span>
                        </div>
                        <div class="ops-right-col">
                            <ul>
                                <li>Unlimited keywords checking</li>
                                <li>Reciprocal backlink control</li>
                                <li>Commerical use of the plugin</li>
                            </ul>
                        </div>
                    </div>
                    <?php break; ?>
                <?php endif; ?>
            <?php } ?>
        </div>

        <a href="admin.php?page=ops_settings" class="button button-primary ops-add-new-kw">Add new</a>
        <?php
    }

    /**
     * Returns ranking of URL in Google Search Results
     * @param type $search_this
     * @param type $keyword
     * @return int
     */
    public static function ops_get_position($search_this, $keyword) {

        $settings = Off_Page_SEO::ops_get_settings();
        $n = 1;
        $position = 100;

        $url = 'http://www.google.' . $settings['google_domain'] . '/search?hl=' . $settings['lang'] . '&start=0&q=' . urlencode($keyword) . '&num=100&pws=0&adtest=off';
        $str = ops_curl($url);
        $html = str_get_html($str);
        $linkObjs = $html->find('h3.r a');
        foreach ($linkObjs as $linkObj) {

            $results[$n]['link'] = trim($linkObj->href);

            // if it is not a direct link but url reference found inside it, then extract
            if (!preg_match('/^https?/', $results[$n]['link']) && preg_match('/q=(.+)&amp;sa=/U', $results[$n]['link'], $matches) && preg_match('/^https?/', $matches[1])) {
                $results[$n]['link'] = $matches[1];
            } else if (!preg_match('/^https?/', $results[$n]['link'])) {
                continue;
            }

            if (str_replace('/', '', $search_this) == str_replace('/', '', $results[$n]['link'])) {
                $position = $n;
                return $position;
            }

            $n++;
        }


        return $position;
    }

    /**
     * Updates Positions of keywords and URL set up in settings, saves them to special table in database
     */
    public static function ops_update_positions() {
        // update last check
        $now = time();
        $settings = Off_Page_SEO::ops_get_settings();

        // update last check first
        $settings['last_check'] = $now;
        Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));

        // if we dont have any graphs set 
        if (count($settings['graphs']) == 0) {
            return;
        }

        global $wpdb;
        // get positions
        foreach ($settings['graphs'] as $graph) {
            $position = self::ops_get_position($graph['url'], $graph['keyword']);
            $new_position = array('position' => $position, 'time' => $now);

            $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE url = '" . $graph['url'] . "' AND keyword = '" . $graph['keyword'] . "'", ARRAY_A);

            $positions = unserialize($row['positions']);
            
            // prepend element to array
            array_unshift($positions, $new_position);

            // serialize 
            $positions_save = serialize($positions);

            // save
            $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('positions' => $positions_save), array('url' => $graph['url'], 'keyword' => $graph['keyword']));
        }

        // send email
        OPS_Email::ops_send_rank_report();
    }

    /**
     * Returns all positions URL based on keyword saved in database
     * @param type $url
     * @param type $keyword
     * @return array
     */
    public static function ops_get_positions($url, $keyword) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE url = '" . $url . "' AND keyword = '" . $keyword . "'", ARRAY_A);
        $positions = unserialize($row['positions']);
        return $positions;
    }

    public static function ops_get_row_id($url, $keyword) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE url = '" . $url . "' AND keyword = '" . $keyword . "'", ARRAY_A);
        return $row['id'];
    }

    public static function ops_get_row_data($id) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE id = '" . $id . "'", ARRAY_A);
        return $row;
    }

    public static function ops_get_row_by_url_and_keyword($url, $keyword) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE url = '" . $url . "' AND keyword = '" . $keyword . "'", ARRAY_A);
        return $row;
    }

    public function ops_count_backlinks($id) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "ops_rank_report WHERE id = '" . $id . "'", ARRAY_A);
        $links = unserialize($row['links']);
        if (is_array($links)) {
            return count($links);
        } else {
            return 0;
        }
    }

    public function ops_get_backlinks_to_date($to_date, $backlinks) {
        $bl = 0;
        if (is_array($backlinks)) {
            foreach ($backlinks as $backlink) {
                if ($backlink['date'] < $to_date) {
                    $bl++;
                }
            }
        }
        return $bl;
    }

    public static function ops_get_days_ago($time) {
        $diff = time() - $time;
        $n = $diff / 86400;
        return round($n);
    }

    public static function ops_update_backlinks_db($data, $id) {
        global $wpdb;
        $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('links' => $data), array('id' => $id));
    }

    public static function ops_render_backlinks($id) {
        $data = self::ops_get_row_data($id);
        $uns_data = unserialize($data['links']);
        $settings = Off_Page_SEO::ops_get_settings();

        $price_total = 0;

        if (is_array($uns_data) && count($uns_data) != 0) {

            // sort according to date
            usort($uns_data, function($a, $b) {
                return $b['date'] - $a['date'];
            });
            foreach ($uns_data as $link):
                ?>
                <div class = "single-backlink">
                    <div class = "link">
                        <?php
                        if (isset($link['reciprocal']) && $link['reciprocal'] == 1) { // if we test it
                            if (!isset($link['reciprocal_status'])) {
                                $link['reciprocal_status'] = 4;
                            }

                            if (!isset($link['reciprocal_checked'])) {
                                $link['reciprocal_checked'] = 0;
                            }
                            self::ops_render_reciprocal_status($link['reciprocal_status'], $settings);
                        } else { // if we dont test
                            self::ops_render_reciprocal_status(0, $settings);
                        }
                        ?>
                        <a href = "<?php echo $link['url'] ?>" target = "blank">
                            <?php echo mb_substr(strip_tags($link['url']), 0, 45, 'UTF-8');
                            ?>
                            <?php echo (strlen($link['url']) > 45) ? "..." : ""; ?>
                        </a>
                        <?php if (isset($link['comment']) && strlen($link['comment']) > 0): ?>
                            <div class="ops-comment-icon">
                                <div class="explanation">
                                    <?php echo $link['comment'] ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="type">
                        <?php echo $link['type'] ?>
                    </div>
                    <div class="price">
                        <?php echo $link['price'] ?>&nbsp;<?php echo $settings['currency']; ?>
                        <?php $price_total = $price_total + $link['price']; ?>
                    </div>
                    <div class="referer">
                        <?php if (!isset($link['reciprocal_referer'])): ?>
                            <span>0</span>
                        <?php else : ?>
                            <span><?php echo $link['reciprocal_referer'] ?></span>
                        <?php endif; ?>
                        <div class="explanation">
                            Number of people that came to your site through this link.
                        </div>
                    </div>
                    <div class="date">
                        <span><b><?php echo self::ops_get_days_ago($link['date']) ?></b> days ago</span>
                        <div class="explanation">
                            <?php echo date(Off_Page_SEO::ops_get_date_format(), $link['date']) ?>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
        } else {
            echo "You don't have any backlinks yet, switch into Edit mode and add some.<br/>";
        }
        return $price_total;
    }

    public static function ops_control_reciprocal() {
        $settings = Off_Page_SEO::ops_get_settings();
        $n = 0;
        foreach ($settings['graphs'] as $graph) {
            $row_data = self::ops_get_row_by_url_and_keyword($graph['url'], $graph['keyword']);
            $data[$n]['active'] = $row_data['active'];
            $data[$n]['url'] = $row_data['url'];
            $data[$n]['keyword'] = $row_data['keyword'];
            $data[$n]['row_id'] = $row_data['id'];
            $data[$n]['links'] = unserialize($row_data['links']);
            $n++;
        }

        $now = time();

        foreach ($data as $group) {
            $master_diff = $now - $group['active'];
            // lets test the group of links
            if ($master_diff > 5000) { // 3000000
                $k = 0;
                $output_links = array();

                if (is_array($group['links']) && count($group['links']) != 0) {

                    foreach ($group['links'] as $reciprocal) {

                        // if we test it
                        if (isset($reciprocal['reciprocal']) && $reciprocal['reciprocal'] == 1) {

                            // check backlink
                            $result = self::ops_check_backlink($group['url'], $reciprocal['url']);
                            if ($result == 1) {
                                $reciprocal['reciprocal_status'] = 1;
                            } elseif ($result == 2) {
                                $reciprocal['reciprocal_status'] = 2;
                            } else {
                                $reciprocal['reciprocal_status'] = 3;
                            }
                        }

                        $output_links[$k] = $reciprocal;
                        $k++;
                    }
                }
                self::ops_update_reciprocal_status($output_links, $group['row_id']);
                return;
            }
        }

        // if we get so far (here), we haven't tested any backlinks and all are tested already, set up next schedule
        Off_Page_SEO::ops_update_settings('reciprocal_timer', $now + 259000);
    }

    public static function ops_check_backlink($my_url, $target_url) {

        $html = ops_curl($target_url);

        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");

        $result = self::ops_is_my_link_there($hrefs, $my_url);

        return $result;
    }

    public static function ops_update_reciprocal_status($links, $row_id) {
        global $wpdb;
        $save_this = serialize($links);
        $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('links' => $save_this, 'active' => time()), array('ID' => $row_id));
    }

    public static function ops_is_my_link_there($hrefs, $my_url) {
        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            if (str_replace('/', '', $my_url) == str_replace('/', '', $url)) {
                $rel = $href->getAttribute('rel');
                if ($rel == 'nofollow') {
                    return 2;
                }
                return 3;
            }
        }
        return 1;
    }

    public function ops_render_graph_positions_date($positions) {
        foreach ($positions as $single_position):
            ?>
            ['<?php echo $this->ops_get_days_ago($single_position['time']) ?> days ago', <?php echo $single_position['position'] ?>],
            <?php
        endforeach;
    }

    public function ops_render_graph_positions($positions) {
        $r = 0;
        foreach ($positions as $single_position):
            ?>
            ['<?php echo $this->ops_get_days_ago($single_position['time']) ?> days ago', <?php echo $single_position['position'] ?>],
            <?php
            $r++;
            if ($r > 18) {
                break;
            }
        endforeach;
    }

    public function ops_render_graph_backlinks($positions, $row_id) {
        $r = 0;
        $row_data = OPS_Rank_Reporter::ops_get_row_data($row_id);
        ?>
        <?php foreach ($positions as $single_position): ?>
            ['<?php echo $this->ops_get_days_ago($single_position['time']) ?> days ago', <?php echo $this->ops_get_backlinks_to_date($single_position['time'], unserialize($row_data['links'])) ?>],
            <?php
            $r++;
            if ($r > 18) {
                break;
            }
        endforeach;
    }

    public function ops_render_graph_categories($positions) {
        $r = 0;
        foreach ($positions as $single_position):
            ?>
            '<b><?php echo $this->ops_get_days_ago($single_position['time']) ?></b> d ago',
            <?php
            $r++;
            if ($r > 18) {
                break;
            }
        endforeach;
    }

    public static function ops_render_reciprocal_status($status, $settings) {

        if (Off_Page_SEO::ops_is_premium() == 0) {
            $color = '#2ea2cc';
            $message = 'Reciprocal control is unavailable in the free version.';
        } elseif ($status == 0) {
            $color = '#ececec';
            $message = 'Checking is turned off.';
        } elseif ($status == 1) {
            $color = 'red';
            $message = 'We could not find the link. Last tested: ' . date(Off_Page_SEO::ops_get_date_format(), $settings['reciprocal_timer'] - 259000);
        } elseif ($status == 2) {
            $color = 'orange';
            $message = 'We found the link, but its no-follow. Last tested: ' . date(Off_Page_SEO::ops_get_date_format(), $settings['reciprocal_timer'] - 259000);
        } elseif ($status == 3) {
            $color = 'green';
            $message = 'Your link is present. Last tested: ' . date(Off_Page_SEO::ops_get_date_format(), $settings['reciprocal_timer'] - 259000);
        } elseif ($status == 4) {
            $color = '#2ea2cc';
            $message = 'Waiting for the first check.';
        } else {
            $color = '#2ea2cc';
            $message = 'Unknown error. Please contact the author of this plugin.';
        }
        ?>
        <div class="ops-reciprocal-button" style="background-color: <?php echo $color; ?>">
            <div class="explanation">
                <?php echo $message ?>
            </div>
        </div>
        <?php
    }

    public static function ops_check_referer() {
        $referer = $_SERVER['HTTP_REFERER'];
        $settings = Off_Page_SEO::ops_get_settings();

        // if we have no graphs set uo
        if (!isset($settings['graphs']) || count($settings['graphs']) == 0) {
            return;
        }

        $n = 0;
        foreach ($settings['graphs'] as $graph) {
            $row_data = self::ops_get_row_by_url_and_keyword($graph['url'], $graph['keyword']);
            $data[$n]['url'] = $row_data['url'];
            $data[$n]['keyword'] = $row_data['keyword'];
            $data[$n]['row_id'] = $row_data['id'];
            $data[$n]['links'] = unserialize($row_data['links']);
            $n++;
        }



        // go through every graph/kw
        foreach ($data as $kw) {

            // if for some reason not array
            if (!is_array($kw['links'])) {
                return;
            }

            // for every link check if its referer
            foreach ($kw['links'] as $link) {

                // if its the one
                if (str_replace('/', '', $link['url']) == str_replace('/', '', $referer)) {

                    // going to update some data
                    $old_data = self::ops_get_row_data($kw['row_id']);
                    $links_only = unserialize($old_data['links']);

                    // go through the links and find the right one
                    $k = 0;
                    foreach ($links_only as $link_single) {
                        $links_only_new[$k] = $link_single;

                        // if its the link, update referer
                        if ($link['url'] == $link_single['url']) {
                            if (!isset($link_single['reciprocal_referer'])) {
                                $link_single['reciprocal_referer'] = 0;
                            }
                            $old_referer = $link_single['reciprocal_referer'];
                            $links_only_new[$k]['reciprocal_referer'] = $old_referer + 1;
                        }

                        $k++;
                    }

                    // go and update data
                    $old_data['links'] = serialize($links_only_new);

                    // save it all
                    global $wpdb;
                    $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('links' => $old_data['links']), array('id' => $kw['row_id']));


                    // set sesssion to prevent more checking
                    $_SESSION['ops_referer'] = 1;

                    // end                   
                    return;
                }
            }
        }
    }

}
