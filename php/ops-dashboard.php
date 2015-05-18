<?php

class OPS_Dashboard {

    /**
     * Initialization of Dashboard Class
     * */
    public function __construct() {
        ?>
        <div class="wrap" id="ops-dashboard">
            <?php $settings = Off_Page_SEO::ops_get_settings(); ?>
            <?php $premium = Off_Page_SEO::ops_get_option('ops_premium'); ?>
            <h2 class="ops-h2">Off Page SEO - Dashboard</h2>

            <div class="left-col" id="ops-rank-reporter">
                <?php if (isset($premium['before_premium']) && $premium['before_premium'] == 1 && Off_Page_SEO::ops_is_premium() == 0): ?>
                    <div class="ops-before-premium">
                        It seems you have been using this plugin before turning into premium. You can <a href="mailto:info@offpageseoplugin.com?subject=<?php echo 'Licence Code Redeem ' . get_home_url() ?>&body=<?php echo rawurlencode('Please send me licence code for my site :' . get_home_url()) ?>">redeem free licence code</a> for your website and 2 others for 1 year.
                    </div>
                <?php endif; ?>
                <!--GRAPHS-->
                <?php new OPS_Rank_Reporter(); ?>
            </div>
            <div class="right-col">
                <!--SITE INFO-->
                <?php $this->ops_render_site_info($settings) ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renders Site Informations
     */
    public function ops_render_site_info($settings) {
        ?>
        <div class="postbox">
            <h3 class="ops-h3">Your site's information</h3>
            <div class="inside ops-padding-sides">
                <?php if (isset($settings['show']['page_rank']) && $settings['show']['page_rank'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            PageRank
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['page_rank'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['alexa_rank']) && $settings['show']['alexa_rank'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            AlexaRank
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['alexa_rank'] ?>
                        </div>
                    </div>
                <?php endif; ?>


                <div class="ops-line">
                    <div class="left-col">
                        Total Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_total'])) ? $settings['site_info']['shares_total'] : ""; ?>
                    </div>
                </div>

                <div class="ops-line">
                    <div class="left-col">
                        Facebook Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_facebook'])) ? $settings['site_info']['shares_facebook'] : ""; ?>
                    </div>
                </div>

                <div class="ops-line">
                    <div class="left-col">
                        Twitter Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_twitter'])) ? $settings['site_info']['shares_twitter'] : ""; ?>
                    </div>
                </div>

                <div class="ops-line">
                    <div class="left-col">
                        Google+ Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_googleplus'])) ? $settings['site_info']['shares_googleplus'] : ""; ?>
                    </div>
                </div>

                <div class="ops-line">
                    <div class="left-col">
                        Pocket Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_pocket'])) ? $settings['site_info']['shares_pocket'] : ""; ?>
                    </div>
                </div>

                <div class="ops-line">
                    <div class="left-col">
                        Pinterest Shares
                    </div>
                    <div class="right-col">
                        <?php echo (isset($settings['site_info']['shares_pinterest'])) ? $settings['site_info']['shares_pinterest'] : ""; ?>
                    </div>
                </div>


                <div class="site-lang">
                    The stats above summarize data for the whole website. For specific pages, visit <a href="admin.php?page=ops_social_networks">Social Networks</a> page.<br/><br/>
                    Site language: <b><?php echo Off_Page_SEO::ops_get_language($settings['lang']) ?></b><br/>
                    Last check: <strong><?php echo date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check']); ?></strong><br/>
                    Next scheduled check: <strong><?php echo date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check'] + 259200); ?></strong>
                </div>
            </div>

        </div>
        <?php
    }

}
