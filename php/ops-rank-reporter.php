<?php

class OPS_Rank_Reporter {

    /**
     * Initialization of Rank Reporter Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();

        if (isset($settings['graphs'])) {
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
        <?php if (isset($positions[0]['positions'][0])): ?>
            <div class="postbox">
                <div id="master-graph" style="width: 95%; height: 400px;"></div>
            </div>


            <script type = "text/javascript" src = "https://www.google.com/jsapi"></script>
            <script type="text/javascript">

                google.load("visualization", "1.1", {packages: ["corechart"]});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable
                            ([['Date' <?php
            foreach ($positions as $position) {
                echo ",'" . $position['keyword'] . "'";
            }
            ?>],
            <?php $i = 0; ?>
            <?php foreach ($positions[0]['positions'] as $position): ?>

                <?php $time = date('Y, m, d, H, i', $position['time']); ?>
                                [ new Date(<?php echo $time ?>) <?php
                foreach ($positions as $position) {
                    if (isset($position['positions'][$i]['position'])) {
                        echo "," . $position['positions'][$i]['position'];
                    } else {
                        echo ", 100 ";
                    }
                }
                $i++;
                ?>],
                <?php
                if ($i == 15) {
                    break;
                }
                ?>
            <?php endforeach; ?>
                            ]);
                            var options = {
                                vAxis: {
                                    title: "Position",
                                    viewWindowMode: 'explicit',
                                    viewWindow: {
                                        min: 1
                                    },
                                    direction: -1
                                },
                                pointSize: 10,
                                legend: {position: 'bottom'},
                                height: 400,
                                chartArea: {left: 80, top: 20, 'width': '90%', 'height': '70%'},
                                pointShape: 'circle'};
                    var chart = new google.visualization.LineChart(document.getElementById('master-graph'));
                    chart.draw(data, options);
                }
            </script>
        <?php endif; ?>
        <?php
    }

    /**
     * Display all graphs we have set up
     * @param type $settings
     */
    public function ops_render_positions($settings) {
        ?>
        <div class = "postbox">
            <h3 class = "ops-h3">Rank Checker</h3>
            <script type = "text/javascript" src = "https://www.google.com/jsapi"></script>
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


                    $('body').on('click', '.ops-add-new-link', function (e) {
                        e.preventDefault();
                        var allId = $(this).closest('form').data('count');
                        var newId = allId++;
                        $(this).closest('form').data('count', allId);
                        $(this).parent().prepend('<div class="ops-link-wrap"><input type="text" name="links[' + allId + '][url]" class="ops-url" placeholder="URL" /><select name="links[' + allId + '][type]" class="ops-type"><option value="backlink">Backlink</option><option value="article">Article</option><option value="comment">Comment</option></select><input type="text" name="links[' + allId + '][price]" class="ops-price" placeholder="Price" /><select name="links[' + allId + '][date]" class="ops-date" ><option value="today">Today</option><option value="yesterday">Yesterday</option><option value="three-days-ago">3 days ago</option><option value="week-ago">Week ago</option></select><button class="ops-delete-link">x</button></div>');
                    });



                    $('body').on('click', 'button.ops-delete-link', function (e) {
                        e.preventDefault();
                        $(this).parent().empty();
                    });

                    $('.ops-show-backlinks').click(function (e) {
                        e.preventDefault();
                        if ($(this).parent().find('.ops-backlinks-list').hasClass('ops-open') === false) {
                            $(this).parent().find('.ops-backlinks-list').addClass('ops-open').slideDown();
                        } else {
                            $(this).parent().find('.ops-backlinks-list').removeClass('ops-open').slideUp();
                        }
                    });

                });</script>
            <?php
            foreach ($settings['graphs'] as $graph) {

                // set positions
                $positions = $this->ops_get_positions($graph['url'], $graph['keyword']);
                $row_id = $this->ops_get_row_id($graph['url'], $graph['keyword']);
                ?>

                <div class="ops-kw-graph-wrapper">

                    <div class="ops-kw-wrapper ops-padding">
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
                            <div class="ops-show-backlinks">
                                <a href="#" class="button">Backlinks (<?php echo $this->ops_count_backlinks($row_id)  ?>)</a> 
                            </div>
                            <div class="ops-backlinks-list" style="display:none;">
                                <?php $this->ops_render_backlinks($row_id); ?>
                                <a href="#" class="ops-dash-edit-mode" data-rowid="<?php echo $row_id ?>">Edit mode</a>
                            </div>
                        </div>
                        <div class="right-col">

                            <?php if (isset($positions[0]['position'])): ?>
                                <div class="ops-show-graph">
                                    <a href="" class="button button-primary">Show Graph</a>
                                </div>
                            <?php else : ?>
                                <div class="ops-show-graph">
                                    No data, next scheduled check: <strong><?php echo date('F d, Y H:i:s', $settings['last_check'] + 259200); ?></strong> or you can force test in Settings.
                                </div>
                            <?php endif; ?>


                            <!--NOW-->
                            <?php if (isset($positions[0]['position'])): ?>
                                <div class="position">
                                    <span>Now: </span> <?php echo $positions[0]['position'] ?>
                                </div>
                            <?php endif; ?>

                            <!--WEEK AGO-->
                            <?php if (isset($positions[1]['position'])): ?>
                                <div class="position">
                                    <span>Last time: </span> <?php echo $positions[1]['position'] ?>
                                </div>
                            <?php endif; ?>

                            <!--MONTH AGO-->
                            <?php if (isset($positions[2]['position'])): ?>
                                <div class="position">
                                    <span>2 ago: </span> <?php echo $positions[2]['position'] ?>
                                </div>
                            <?php endif; ?>

                        </div>

                        <?php if ($positions): ?>

                            <!--SCRIPT-->
                            <script type="text/javascript">

                                google.load("visualization", "1.1", {packages: ["corechart"]});
                                google.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable
                                            ([['Date', 'Position'],
                <?php $i = 0; ?>
                <?php foreach ($positions as $position): ?>
                    <?php $time = date('Y, m, d, H, i', $position['time']); ?>
                                                [new Date(<?php echo $time ?>), <?php echo $position['position'] ?>],
                    <?php
                    $i++;
                    if ($i == 15) {
                        break;
                    }
                    ?>
                <?php endforeach; ?>
                                            ]);
                                            var options = {
                                                legend: 'none',
                                                vAxis: {
                                                    title: "Position",
                                                    viewWindowMode: 'explicit',
                                                    viewWindow: {
                                                        min: 1
                                                    },
                                                    direction: -1
                                                },
                                                pointSize: 10,
                                                height: 400,
                                                chartArea: {left: 80, top: 20, 'width': '90%', 'height': '70%'},
                                                pointShape: 'circle'};
                                    var chart = new google.visualization.LineChart(document.getElementById('<?php echo $graph['keyword'] . $graph['url'] ?>'));
                                    chart.draw(data, options);
                                }
                            </script>
                        <?php endif; ?>
                    </div>

                    <div class="ops-graph-wrapper <?php echo (isset($graph['open']) && $graph['open'] == 'on') ? "ops-show" : ""; ?>">
                        <div class="ops-graph" id="<?php echo $graph['keyword'] . $graph['url'] ?>"></div>
                    </div>

                </div>
            <?php } ?>
        </div>

        <a href="admin.php?page=ops_settings" class="button button-primary ops-add-new-kw">Add new</a>
        <?php
    }

    /**
     * Returns position of URL in Google Search Results
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

            // check if its the position
            $len_parsed = strlen($search_this);
            $len_found = strlen($results[$n]['link']);
            $len_diff = abs($len_found - $len_parsed);
            $results[$n]['similarity'] = similar_text($search_this, $results[$n]['link']);
            $results[$n]['len_diff'] = $len_diff;
            $results[$n]['position'] = $n;

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

    public static function ops_update_backlinks_db($data, $id) {
        global $wpdb;
        $wpdb->update($wpdb->base_prefix . "ops_rank_report", array('links' => $data), array('id' => $id));
    }

    public static function ops_render_backlinks($id) {
        $data = self::ops_get_row_data($id);
        $uns_data = unserialize($data['links']);
        if (is_array($uns_data)):
            $uns_data = unserialize($data['links']);
            foreach ($uns_data as $link):
                ?>
                <div class="single-backlink">
                    <div class="link">
                        <a href="<?php echo $link['url'] ?>" target="blank"><?php echo $link['url'] ?></a>&nbsp;
                    </div>
                    <div class="type">
                        <?php echo $link['type'] ?>
                    </div>
                    <div class="price">
                        <?php echo $link['price'] ?>&nbsp;
                    </div>
                    <div class="date">
                        <?php echo date('F d, Y', $link['date']) ?>
                    </div>
                </div>
                <?php
            endforeach;
        endif;
    }

}
