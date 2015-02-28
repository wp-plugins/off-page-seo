<?php

class OPS_Settings {

    /**
     * Initialization of Settings Class
     * */
    public function __construct() {

        // if we are saving data from form
        if (isset($_POST['null'])) {
            $this->ops_save_settings();
        }

        // display message that settings was updated
        if (isset($_GET['saved']) && $_GET['saved'] == true) {
            ?>
            <div class="updated" style="padding: 8px 20px;">
                <?php if (isset($_GET['signed']) && $_GET['signed'] == 'true'): ?>
                    You should be able to browse <a href="admin.php?page=ops_backlinks">guest posting</a> network now!
                <?php else: ?>
                    Settings were updated.
                <?php endif; ?>
            </div> 
            <?php
        }

        // check for gust posting
        if (isset($_GET['signed']) && $_GET['signed'] == 'true') {
            $settings = Off_Page_SEO::ops_get_settings();
            $settings['last_check_site_info'] = 0;
            Off_Page_SEO::ops_update_option('ops_settings', serialize($settings));
        }

        // renders settings form
        $this->ops_render_settings_form();
    }

    /**
     * Receive $_POST and saves it as serialized array into database
     * @global type $wpdb
     */
    public function ops_save_settings() {
        // perform test
        if (isset($_POST['ops-clear-date']) && $_POST['ops-clear-date'] == 'on') {
            $_POST['last_check'] = 0;
        }

        // update post meta
        // secure graphs
        if (isset($_POST['graphs'])) {
            $_POST['graphs'] = $this->ops_sanatize_graphs_inputs($_POST['graphs']);
        }
        if (isset($_POST['guest_posting'])) {
            $_POST['guest_posting'] = $this->ops_sanatize_guest_posting_inputs($_POST['guest_posting']);
        }

        $data = serialize($_POST);
        Off_Page_SEO::ops_update_option('ops_settings', $data);

        // update database report
        global $wpdb;

        // go through loop of Graphs
        if (isset($_POST['graphs'])) {
            foreach ($_POST['graphs'] as $graph) {
                $db_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'ops_rank_report WHERE url = "' . trim($graph['url']) . '" AND keyword = "' . trim($graph['keyword']) . '"', ARRAY_A);
                if (!$db_results) {
                    // if there are no rows in wp_ops_rank_report, add one
                    $data = array(
                        'url' => trim($graph['url']),
                        'keyword' => trim($graph['keyword']),
                        'positions' => serialize(array()),
                        'post_id' => url_to_postid(trim($graph['url'])),
                        'active' => 1
                    );
                    $wpdb->insert($wpdb->base_prefix . 'ops_rank_report', $data);
                }
            }
        }

        // if we want to delete records
        if (isset($_POST['ops-delete-reports']) && $_POST['ops-delete-reports'] == 'on') {
            $ops_delete = serialize(array());
            $wpdb->query("UPDATE " . $wpdb->base_prefix . "ops_rank_report SET positions = '" . $ops_delete . "'");
        }
        ?>

        <!--REDIRECTS-->
        <script type="text/javascript">
            window.location.href = "<?php echo get_home_url() . '/wp-admin/admin.php?page=ops_settings&saved=true'; ?>";
        </script>
        <?php
        exit;
    }

    /**
     * Sanatize strings given by user
     * @param type $graphs
     * @return array
     */
    public function ops_sanatize_graphs_inputs($graphs) {
        $n = 0;
        foreach ($graphs as $graph) {
            $sanatized[$n]['keyword'] = sanitize_text_field($graph['keyword']);
            $sanatized[$n]['url'] = sanitize_text_field($graph['url']);
            $sanatized[$n]['master'] = (isset($graph['master']) ? $graph['master'] : "" );
            $sanatized[$n]['volume'] = (isset($graph['volume']) ? $graph['volume'] : "" );
            $n++;
        }
        return $sanatized;
    }

    /**
     * Sanatize strings given by user
     * @param type $graphs
     * @return array
     */
    public function ops_sanatize_guest_posting_inputs($guest_posting) {
        $sanatized['email_subject'] = sanitize_text_field($guest_posting['email_subject']);
        $sanatized['email_reply'] = sanitize_text_field($guest_posting['email_reply']);
        $sanatized['email_content'] = $guest_posting['email_content'];
        return $sanatized;
    }

    /**
     * Render Main Settings Form
     */
    public function ops_render_settings_form() {
        ?>
        <div class="wrap" id="ops-settings">
            <?php $settings = Off_Page_SEO::ops_get_settings(); ?>
            <h2 class="ops-h2">Settings</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li>Settings</li>
                </ul>
            </div>
            <form method="post" action="">
                <!--HIDDEN FIELD-->
                <input type="hidden" value="yes" name="null" />
                <input type="hidden" value="<?php echo $settings['last_check'] ?>" name="last_check" />
                <input type="hidden" value="<?php echo $settings['last_check_site_info'] ?>" name="last_check_site_info" />
                <input type="hidden" value="<?php echo $settings['site_info']['page_rank'] ?>" name="site_info[page_rank]" />
                <input type="hidden" value="<?php echo $settings['site_info']['alexa_rank'] ?>" name="site_info[alexa_rank]" />
                <input type="hidden" value="<?php echo $settings['site_info']['facebook'] ?>" name="site_info[facebook]" />
                <input type="hidden" value="<?php echo $settings['site_info']['twitter'] ?>" name="site_info[twitter]" />
                <input type="hidden" value="<?php echo $settings['site_info']['google'] ?>" name="site_info[google]" />
                <input type="hidden" value="<?php echo $settings['site_info']['pinterest'] ?>" name="site_info[pinterest]" />
                <input type="hidden" value="<?php echo $settings['site_info']['stumbleupon'] ?>" name="site_info[stumbleupon]" />
                <input type="hidden" value="<?php echo $settings['site_info']['delicious'] ?>" name="site_info[delicious]" />
                <input type="hidden" value="<?php echo $settings['site_info']['reddit'] ?>" name="site_info[reddit]" />
                <input type="hidden" value="<?php echo $settings['site_info']['linkedin'] ?>" name="site_info[linkedin]" />
                <input type="hidden" value="<?php echo $settings['site_info']['guest_posting'] ?>" name="site_info[guest_posting]" />



                <!--LEFT COL-->
                <div class="left-col">

                    <div class="postbox">
                        <h3 class="ops-h3">Supported languages</h3>
                        <div class="ops-padding">
                            <p>Please select your language.</p>
                            <select name="lang">
                                <?php $languages = Off_Page_SEO::ops_lang_array() ?>
                                <?php foreach ($languages as $key => $value): ?>
                                    <option value="<?php echo $key ?>" <?php echo ($key == $settings['lang']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br/><br/>
                            <p>Please select the Google domain you want to search in.</p>
                            <select name="google_domain" class="select2">
                                <?php $google_domains = Off_Page_SEO::ops_google_domains_array() ?>
                                <?php foreach ($google_domains as $key => $value): ?>
                                    <option value="<?php echo $key ?>" <?php echo ($key == $settings['google_domain']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="postbox" id="ops-rank-reporter-settings">
                        <h3 class="ops-h3">Rank Reporter</h3>
                        <div class="ops-padding">
                            <p>Please enter the keywords and URLs that you want the report on.</p>
                            <?php
                            if (isset($settings['graphs'])) {
                                $number = count($settings['graphs']);
                            } else {
                                $number = 0;
                            }
                            ?>

                            <div class="ops-wrapper" data-number="<?php echo $number ?>">
                                <?php $n = 0; ?>
                                <?php if (isset($settings['graphs'])): ?>
                                    <?php foreach ($settings['graphs'] as $graph) : ?>
                                        <div class="ops-new-kw-wrapper">
                                            <div class="row">
                                                <a href="" class="delete-kw">Delete</a>
                                                <input type="text" name="graphs[<?php echo $n ?>][keyword]" placeholder="Keyword" value="<?php echo $graph['keyword'] ?>" />
                                                <input type="url" name="graphs[<?php echo $n ?>][url]" placeholder="URL" value="<?php echo $graph['url'] ?>" />

                                            </div>
                                            <div class="options">
                                                Show in master graph:
                                                <input type="checkbox" name="graphs[<?php echo $n ?>][master]" <?php echo (isset($graph['master']) && $graph['master'] == 'on') ? "checked" : ""; ?> />
                                                Search volume:
                                                <input type="number" name="graphs[<?php echo $n ?>][volume]" placeholder="Volume" value="<?php echo (isset($graph['volume'])) ? $graph['volume'] : ""; ?>" class="ops-volume" />
                                            </div>
                                        </div>
                                        <?php $n++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <a href="#" class="button add-new-kw">Add new</a>
                            <div class="ops-delete">
                                <input type="checkbox" name="ops-delete-reports" /> Delete all reports.
                            </div>
                            <div class="ops-delete">
                                <input type="checkbox" name="ops-clear-date" /> Perform ranking test next time the user visits your site <i>(don't use frequently)</i>. 
                            </div>
                            <div class="max-exec">
                                Max server execution time : <b><?php echo ini_get('max_execution_time'); ?>s</b>, max number of keywords should be <b><?php echo $this->ops_get_recommended_kws(ini_get('max_execution_time')) ?></b> depending on your ranking in SERP (use more, if higher).
                            </div>
                        </div>
                    </div>


                    <div class="postbox" id="ops-donate-box-settings">
                        <h3 class="ops-h3">Donate Box</h3>
                        <div class="ops-padding">
                            <p>If you don't want to support us or you have already (thank you!), you can hide the donation message.</p>
                            <input type="checkbox" name="donate" <?php echo (isset($settings['donate']) && $settings['donate'] == 'on') ? "checked='checked'" : ""; ?>/>
                        </div>
                    </div>

                    <input type="submit" class="button button-primary" value="Save" />

                </div>


                <!--RIGHT COL-->
                <div class="right-col">

                    <div class="postbox" id="ops-social-metrics-settings">
                        <h3 class="ops-h3">Social metrics</h3>
                        <div class="ops-padding">
                            <div class="row">
                                <input type="checkbox" name="show[page_rank]" <?php echo (isset($settings['show']['page_rank']) && $settings['show']['page_rank'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Page Rank
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[alexa_rank]" <?php echo (isset($settings['show']['alexa_rank']) && $settings['show']['alexa_rank'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Alexa Rank
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[facebook]" <?php echo (isset($settings['show']['facebook']) && $settings['show']['facebook'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Facebook
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[twitter]" <?php echo (isset($settings['show']['twitter']) && $settings['show']['twitter'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Twitter
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[google]" <?php echo (isset($settings['show']['google']) && $settings['show']['google'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Google
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[pinterest]" <?php echo (isset($settings['show']['pinterest']) && $settings['show']['pinterest'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Pinterest
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[stumbleupon]" <?php echo (isset($settings['show']['stumbleupon']) && $settings['show']['stumbleupon'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Stumbleupon
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[delicious]" <?php echo (isset($settings['show']['delicious']) && $settings['show']['delicious'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Delicious
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[reddit]" <?php echo (isset($settings['show']['reddit']) && $settings['show']['reddit'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Reddit
                            </div>
                            <div class="row">
                                <input type="checkbox" name="show[linkedin]" <?php echo (isset($settings['show']['linkedin']) && $settings['show']['linkedin'] == 'on') ? "checked='checked'" : ""; ?>/>
                                Linkedin
                            </div>

                        </div>
                    </div>


                    <div class="postbox" id="ops-guest-blog-outreach">
                        <h3 class="ops-h3">Guest Posting Outreach</h3>
                        <div class="ops-padding">
                            This email will be sent automatically to the owner of the blog you want to reach.
                            <div class="row">
                                <label for="guest_posting[email_subject]">
                                    Email Subject:
                                </label>
                                <input name="guest_posting[email_subject]" type="text" value="<?php echo (isset($settings['guest_posting']['email_subject'])) ? $settings['guest_posting']['email_subject'] : ""; ?>" />
                            </div>
                            <div class="row">
                                <label for="guest_posting[email_reply]">
                                    Reply to:
                                </label>
                                <input name="guest_posting[email_reply]" type="email" value="<?php echo (isset($settings['guest_posting']['email_reply'])) ? $settings['guest_posting']['email_reply'] : ""; ?>" />
                            </div>
                            <div class="row">
                                <label for="guest_posting[email_content]" class="top">
                                    Contents of email:
                                </label>
                                <textarea name="guest_posting[email_content]"><?php echo (isset($settings['guest_posting']['email_content'])) ? $settings['guest_posting']['email_content'] : ""; ?></textarea>
                            </div>

                        </div>
                    </div>

                </div>



            </form>
        </div> 

        <script>
            jQuery(document).ready(function ($) {

                $('select[name=lang], .select2').select2();

                $('body').on('click', '#ops-rank-reporter-settings .delete-kw', function (e) {
                    e.preventDefault();
                    $(this).closest('.ops-new-kw-wrapper').remove();

                });
                $('#ops-rank-reporter-settings .add-new-kw').click(function (e) {
                    e.preventDefault();
                    // get numbers
                    var numberGraphs = $('#ops-rank-reporter-settings .ops-wrapper').data('number');
                    var newNumberGraphs = numberGraphs + 1;

                    // set new number
                    var numberGraphs = $('#ops-rank-reporter-settings .ops-wrapper').data('number', newNumberGraphs);

                    // append
                    $('#ops-rank-reporter-settings .ops-wrapper').append('<div class="ops-new-kw-wrapper"><div class="row"><a href="" class="delete-kw">Delete</a><input type="text" name="graphs[' + newNumberGraphs + '][keyword]" placeholder="Keyword" /><input type="text" name="graphs[' + newNumberGraphs + '][url]" placeholder="URL" /></div>Show in master graph<input type="checkbox" name="graphs[' + newNumberGraphs + '][master]" />Search volume:<input type="text" name="graphs[' + newNumberGraphs + '][volume]" placeholder="Volume" value="" class="ops-volume" /></div></div>');
                });
            });
        </script>
        <?php
    }

    public function ops_get_recommended_kws($time) {
        if ($time < 45) {
            $return = "7-12";
        } elseif ($time < 80) {
            $return = "10-15";
        } elseif ($time < 120) {
            $return = "14-20";
        } else {
            $return = "> 20";
        }
        return $return;
    }

}
