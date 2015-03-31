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
            <p>Social share counts are displayed bellow. </p>
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
        }

        $args = array(
            'post_type' => $post_types,
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 1000,
            'meta_query' => array(
                array(
                    'key' => 'ops_shares',
                    'compare' => 'EXISTS',
                ),
            ),
        );
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
//                        delete_post_meta(get_the_ID(), 'ops_shares');
                        ?>
                        <tr> 
                            <td><?php the_time('F d, Y') ?></td> 
                            <td><a href="<?php the_permalink()?>" target="_blank"><?php the_title() ?></a><a href="post.php?post=<?php echo get_the_ID() ?>&action=edit" target="_blank" class="edit">Edit</a></td> 
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
        <?php else: ?>
            Please set up <a href="admin.php?page=ops_settings">post types</a> you want to track and let the people browse your site. They will automatically trigger the scripts that will count the shares from the social APIs.
        <?php endif; ?>


        <?php
    }

}
