<?php

namespace common;

include( "../common/LoadData.php" );
include( "../common/DBConnection.php" );

use common\LoadData as LoadData;
use common\DBConnection as DBConnection;

/*error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
*/

if(isset( $_POST['user_id'] )) {
	$loadData = new LoadData();
     $result = $loadData->getSeries($_POST['user_id']);
     echo json_encode($result);
}