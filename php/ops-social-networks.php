<?php

class OPS_Social_Networks {

    /**
     * Initialization of Dashboard Class
     * */
    public function __construct() {
        $settings = Off_Page_SEO::ops_get_settings();
        ?>
        <div class="wrap" id="ops-social-networks">
            <?php ?>
            <h2 class="ops-h2">Social Networks</h2>
            <div class="ops-breadcrumbs">
                <ul>
                    <li><a href="admin.php?page=ops">Dashboard</a> &#8658;</li>
                    <li>Social Networks</li>
                </ul>
            </div>
            <?php if(isset($settings['control_shares']) && $settings['control_shares'] == 'on'):?>
            <?php else : ?>
            <p><strong style='font-size: 20px;'>Social share control is turned off. You can turn it on in Settings, but be aware of your hosting limits as this feature can be a bit expensive.</strong></p>
            <p>If the plugin stops reporting the rankings (reports positions of 100), first thing you should do is turn off this feature.</p>
            <?php endif; ?>
            <?php $this->ops_render_shares($settings); ?>
        </div>
        <?php
    }

    /**
     * Renders Site Informations
     */
    public function ops_render_shares($settings) {
        $post_types = Off_Page_SEO::ops_get_post_types();
        if (!isset($post_types) || !is_array($post_types)) {
            echo "Please specify post types you want to count shares for in Settings.";
            return;
        }

        $args = array(
            'post_type' => $post_types,
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1
        );

        if (isset($_GET['show']) && $_GET['show'] == 'all') {
            $args['meta_query'] = array(
                array(
                    'key' => 'ops_shares',
                    'compare' => 'EXISTS',
                ),
            );
        } else {
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'ops_shares_total',
                    'value' => 0,
                    'type' => 'numeric',
                    'compare' => '>'
                ),
                array(
                    'key' => 'ops_shares',
                    'compare' => 'EXISTS',
                )
            );
        }
        $wp_query = new WP_Query($args);
        ?>
        <?php if ($wp_query->have_posts()) : ?>

            <script type='text/javascript' src='<?php echo plugins_url('off-page-seo/js/jquery.thfloat.min.js') ?>'></script>
            <script type='text/javascript' src='<?php echo plugins_url('off-page-seo/js/jquery.tablesorter.min.js') ?>'></script>
            <style>
                #thfloathead-ops-share-table{
                    margin-top: 30px !important;
                }
            </style>
            <script>
                jQuery(document).ready(function ($) {
                    $("#ops-share-table").tablesorter();
                    $("#ops-share-table").thfloat();
                });
            </script>
            <table id="ops-share-table">
                <thead> 
                    <tr> 
                        <th>Published</th> 
                        <th>Title</th>
                        <th class="soc-fb">FB</th>
                        <th class="soc-tw">Tw</th>
                        <th class="soc-go">Google</th>
                        <th class="soc-poc">Pocket</th>
                        <th class="soc-pin">Pinterest</th>
                        <th>Total</th>
                    </tr> 
                </thead> 
                <tbody> 
                    <?php while ($wp_query->have_posts()): $wp_query->the_post(); ?>
                        <?php
                        $meta = get_post_meta(get_the_ID());
                        $shares = unserialize($meta['ops_shares'][0]);
                        $shares_total = $meta['ops_shares_total'][0];
                        ?>
                        <tr> 
                            <td><?php the_time(Off_Page_SEO::ops_get_date_format()) ?></td> 
                            <td><a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a></td> 
                            <td class="ops-center"><?php echo $shares['count']['facebook'] ?></td> 
                            <td class="ops-center"><?php echo $shares['count']['twitter'] ?></td> 
                            <td class="ops-center"><?php echo $shares['count']['googleplus'] ?></td> 
                            <td class="ops-center"><?php echo $shares['count']['pocket'] ?></td> 
                            <td class="ops-center"><?php echo $shares['count']['pinterest'] ?></td>
                            <td class="ops-center"><b><?php echo $shares_total; ?></b></td>
                        </tr> 

                    <?php endwhile; ?>
                </tbody> 
            </table>

            <?php
            if (!isset($_GET['show'])) {
                $args_all = array(
                    'post_type' => $post_types,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'posts_per_page' => -1
                );

                $args_all['meta_query'] = array(
                    'relation' => 'AND',
                    array(
                        'key' => 'ops_shares_total',
                        'value' => 0,
                        'type' => 'numeric',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'ops_shares',
                        'compare' => 'EXISTS',
                    )
                );
                $wp_query_all = new WP_Query($args_all);
                
                echo '<div class="ops-shares-others"><b>' . $wp_query_all->found_posts . "</b> other posts on your website does not have any shares. <a href='admin.php?page=ops_social_networks&show=all'>Show them all.</a></div>";
            }
            ?>
        <?php else: ?>
            You don't have any shares or shares was not counted yet. Browse your website and come back later.
        <?php endif; ?>


        <?php
    }

}
