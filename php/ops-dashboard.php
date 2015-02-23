<?php

class OPS_Dashboard {

    /**
     * Initialization of Dashboard Class
     * */
    public function __construct() {
        ?>
        <div class="wrap" id="ops-dashboard">
            <?php $settings = Off_Page_SEO::ops_get_settings(); ?>
            <h2 class="ops-h2">Off Page SEO - Dashboard</h2>
            
            <div class="left-col" id="ops-rank-reporter">
                <!--GRAPHS-->
                <?php new OPS_Rank_Reporter(); ?>
            </div>
            <div class="right-col">
                <!--SITE INFO-->
                <?php $this->ops_render_site_info($settings) ?>

                <!--DONATE BOX-->
                <?php $this->ops_render_donate_box($settings) ?>
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
            <div class="inside ops-padding">

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



                <?php if (isset($settings['show']['facebook']) && $settings['show']['facebook'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Facebook Shares
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['facebook'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['twitter']) && $settings['show']['twitter'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Twitter Shares
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['twitter'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['google']) && $settings['show']['google'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Google Shares
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['google'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['pinterest']) && $settings['show']['pinterest'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Pinterest
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['pinterest'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['stumbleupon']) && $settings['show']['stumbleupon'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Stumbleupon
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['stumbleupon'] ?>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if (isset($settings['show']['delicious']) && $settings['show']['delicious'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Delicious
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['delicious'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['reddit']) && $settings['show']['reddit'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Reddit
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['reddit'] ?>
                        </div>
                    </div>
                <?php endif; ?>



                <?php if (isset($settings['show']['linkedin']) && $settings['show']['linkedin'] == 'on'): ?>
                    <div class="ops-line">
                        <div class="left-col">
                            Linkedin
                        </div>
                        <div class="right-col">
                            <?php echo $settings['site_info']['linkedin'] ?>
                        </div>
                    </div>
                <?php endif; ?>


                <div class="site-lang">
                    Site language: <b><?php echo Off_Page_SEO::ops_get_language($settings['lang']) ?></b><br/>
                    Next scheduled check: <strong><?php echo date('F d, Y H:i:s', $settings['last_check'] + 259200); ?></strong>
                </div>
            </div>

        </div>
        <?php
    }
    
    /**
     * Renders donation box
     * @param type $settings
     */
    public function ops_render_donate_box($settings) {
        if (isset($settings['donate']) && $settings['donate'] == 'on'):
            ?>
            <div id="ops-donate">
                <div class="enjoying">
                    Enjoying this plugin?
                </div>
                <div class="help">
                    We want your <a href="mailto:info@offpageseoplugin.com">feedback</a>!<br/>
                    Help us buy a new spaceship.
                </div>
                <div class="dont-bother">
                    <a href="admin.php?page=ops_settings">Don't bother me.</a>
                </div>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="business" value="pago2@seznam.cz">
                    <input type="hidden" name="lc" value="US">
                    <input type="hidden" name="item_name" value="Off Page SEO Plugin">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
            <?php
        endif;
    }

}
