<?php

require_once('../../../../../wp-load.php');
require_once('../ops.php');
require_once('../ops-rank-reporter.php');

if ($_POST['doCron'] == 1) {
    OPS_Rank_Reporter::ops_control_reciprocal();
}