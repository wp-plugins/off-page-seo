<?php

class OPS_Email {

    public static function ops_send_rank_report() {

        $settings = Off_Page_SEO::ops_get_settings();
        
        // if we don't have email address in settings, don't send email
        if(strlen($settings['notification_email']) < 3){
            return;
        }
        
        $n = 0;
        foreach ($settings['graphs'] as $graph) {
            $positions[$n]['url'] = $graph['url'];
            $positions[$n]['keyword'] = $graph['keyword'];

            $row_data = OPS_Rank_Reporter::ops_get_row_by_url_and_keyword($graph['url'], $graph['keyword']);
            $links = unserialize($row_data['links']);
            if (is_array($links)) {
                $count = count($links);
            } else {
                $count = 0;
            }
            $positions[$n]['backlinks'] = $count;

            $positions[$n]['positions'] = OPS_Rank_Reporter::ops_get_positions($graph['url'], $graph['keyword']);

            $n++;
        }
        
        // set email data
        $to = $settings['notification_email'];
        $subject = 'Rank Report - ' . get_bloginfo('name');
        $body = self::ops_get_email_body_rank_report($positions, $settings);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // send mail
        $m = wp_mail($to, $subject, $body, $headers);
        
        if($m != true){ // try php mail
            mail($to, $subject, $body, $headers);
        }
    }

    private static function ops_get_email_body_rank_report($data, $settings) {
        $output = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//CS" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns:fb = "http://www.facebook.com/2008/fbml" xmlns:og = "http://opengraph.org/schema/">
            <head>
                <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8" />
                <meta name = "viewport" content = "width=device-width, initial-scale=1.0" />
                <title>Rank Report</title>
                <!--....................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................... -->

                <style type = "text/css">
                    body{
                        height:100%!important;
                        margin:0;
                        padding:0;
                        width: 100%;
                        background-color:#f1f1f1;
                    }
                    table{
                        width: 100%;
                        margin-top:10px;
                        margin-bottom:10px;
                        border-collapse:collapse;
                    }
                    img, a img{
                        border:0;
                        outline:none;
                        text-decoration:none;
                    }
                    h1, h2, h3, h4, h5, h6{
                        margin:0;
                        padding:0;
                    }
                    p{
                        margin:0;
                    }
                    a{
                        word-wrap:break-word;
                    }
                    table, td{
                        mso-table-lspace:0pt;
                        mso-table-rspace:0pt;
                    }
                    table th{
                        background-color:#f0f0f0;
                        border-top: 1px solid #d7d7d7;
                        border-bottom: 1px solid #d7d7d7;
                    }
                    table tr:hover{
                        background-color:#f9f9f9;
                    }
                    table tr{
                        border-bottom: 1px solid #ececec;
                    }
                    table td, table th{
                        padding: 3px 10px;
                    }
                    img{
                        -ms-interpolation-mode:bicubic;
                    }
                    body, table, td, p, a, li, blockquote{
                        -ms-text-size-adjust:100%;
                        -webkit-text-size-adjust:100%;
                    }
                    .inner-email{
                        padding: 20px;
                        background-color:#ffffff;
                        margin: 20px auto;
                        max-width: 1100px;
                    }
                    p{
                        padding: 0px 0 10px;
                    }
                    h2{
                        font-size: 28px;
                        font-weight: 500;
                        padding:0;
                        margin: 0 0 10px;
                    }
                    .check-info{
                        margin-top: 20px;
                        font-size: 12px;
                    }
                    .check-info p{
                        padding-bottom: 5px;
                    }
                    .unsubscribe{
                        margin-top: 20px;
                        font-size: 12px;
                        text-align:center;
                    }
                    .text-center{
                        text-align:center;
                    }
                    .good{
                        color:green;
                    }
                    .bad{
                        color:red;
                    }
                    .wp-button{
                        background: #2ea2cc;
                        border: 1px solid #0074a2;
                        color: #fff;
                        text-decoration: none;
                        border-radius: 3px;
                        font-size: 16px;
                        padding: 0 15px;
                        line-height: 26px;
                        height: 28px;
                    }
                    .float-right{
                        float:right;
                        margin: 15px 0px 0 0;
                    }
                    .free-version{
                        padding: 10px;
                        background-color: #6cbf66;
                    }
                </style>
            </head>
            <body>
                <div class="inner-email">
                <h2>Rank Report</h2>
                <p>Current ranking in Google search results.</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>URL</th>
                            <th>Current position</th>
                            <th>Change</th>
                            <th># of backlinks</th>
                        </tr>
                    </thead>
                    <tbody>';
        $o = 0;
        foreach ($data as $kw) {
            $output .= '<tr>';

            // keyword
            $output .= '<td>' . $kw['keyword'] . '</td>';

            // url
            $dots = (strlen($kw['url']) > 45) ? "..." : "";
            $output .= '<td><a href="' . $kw['url'] . '">' . mb_substr(strip_tags($kw['url']), 0, 45, 'UTF-8') . $dots . '</a></td>';

            // new position
            $output .= '<td class="text-center">' . $kw['positions'][0]['position'] . '</td>';

            // change
            if(!isset($kw['positions'][1]['position'])){
                $kw['positions'][1]['position'] = 100;
            }
            
            $diff = $kw['positions'][1]['position'] - $kw['positions'][0]['position'];
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
            $output .= '<td class="' . $class . ' text-center"">' . $diff . $arrow . ' </td>';

            // # of backlinks
            $output .= '<td class="text-center">' . $kw['backlinks'] . '</td>';

            $output .= '</tr>';
            
            $o++;
            if($o == 2 && Off_Page_SEO::ops_is_premium() == 0){
                $output .= '<tr><td colspan="5" class="free-version">You are using the free version. For a full report and other cool tools please <a href="http://www.offpageseoplugin.com">purchase a licence key</a>.</td></tr>';
                break;
            }
        }


        $output .= '
                    </tbody>
                </table>
                    <div class="check-info">
                    <a href="' . get_admin_url() . 'admin.php?page=ops" class="wp-button float-right">Take to me the Dashboard</a>
                    <p>
                        Last check: <strong> ' . date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check']) . '</strong><br/>
                    </p>
                    <p>
                        Next scheduled check: <strong> ' . date(Off_Page_SEO::ops_get_date_format() . ' H:i:s', $settings['last_check'] + 259200) . '</strong>
                    </p>
                    <p>
                        This report was generated by <a href="http://www.offpageseoplugin.com">Off Page SEO Plugin</a>.
                    </p>
                    </div>
                </div>
                <div class="unsubscribe">
                    <a href="' . get_admin_url() . 'admin.php?page=ops_settings" target="_blank">Do not receive these emails.</a>
                </div>
            </body>
        </html>
        ';

        return $output;
    }

}