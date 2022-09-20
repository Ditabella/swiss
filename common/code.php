<?
session_start();
$_SESSION['message'] = 'файл загрузили';

$dbConnection =   new mysqli("localhost","ivanovr6_db","020&VhUM","ivanovr6_db");
global $dbConnection;

require_once '../common/SimpleXLSX.php';
use common\SimpleXLSX as SimpleXLSX;

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

if(isset($_POST['save_excel_data']))
{
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['xls','csv','xlsx'];
    $result = '';

    if(in_array($file_ext, $allowed_ext))
    {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];
        $sql = '';
        $fields_title = '';
        $fields_title_create = '';

        
        if ( $xlsx = SimpleXLSX::parse($inputFileNamePath) ) {
          $rows = $xlsx->rows();
          foreach($rows as $key => $fields) { 
            if ($key == 0){
                $fields_title = $fields[0].', ' . str_replace(' ', '_', $fields[1]) . ', ' . $fields[2] . ', ' . $fields[3] . ', ' . $fields[4];

                $fields_title_create = $fields[0].' varchar(255) NOT NULL,
                          '.str_replace(' ', '_', $fields[1]).' varchar(255) NOT NULL,
                          '.$fields[2].' float,
                          '.$fields[3].' varchar(255),
                          '.$fields[4].' datetime, ';

            }else{
                $sql .= "INSERT INTO table_".$_POST['user']." (" . $fields_title . ") VALUES ('" .$fields[0]."', '" . $fields[1] . "', '" . $fields[2] . "', '" . $fields[3] . "', '" . $fields[4] . "');";
            }
            $result .= $sql;

        
            } 


        } else {
          $result = SimpleXLSX::parseError();
        }
        // Check connection
        if (mysqli_connect_errno()) {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
          exit();
        }
        $msg_text = '';
        if(isset($_POST['user'])){

            $query_check = "CREATE TABLE IF NOT EXISTS `table_".$_POST['user']."` (
                          id int(11) AUTO_INCREMENT,
                          ".$fields_title_create."
                          PRIMARY KEY  (id)
                          )";
            if (!$dbConnection) {
               die("Connection failed: " . mysqli_connect_error());
             }else{
                $result_create = mysqli_query($dbConnection, $query_check);
                mysqli_query($dbConnection, "DELETE FROM table_".$_POST['user']);
                mysqli_query($dbConnection, "ALTER TABLE table_".$_POST['user']." AUTO_INCREMENT=1");

                if ($dbConnection->multi_query($sql) === TRUE) {
                  $msg_text = "New records created successfully";
                } else {
                  $msg_text = "Error: " . $sql . "<br>" . $dbConnection->error;
                }
                $dbConnection->close();
             }
        }
        
        $msg = true;

        if(isset($msg))
        {
            $_SESSION['message'] = "Successfully Imported";
            header('Location: /');
            exit(0);
        }
        else
        {
            $_SESSION['message'] = "Not Imported";
            header('Location: /');
            exit(0);
        }
    }
    else
    {
        $_SESSION['message'] = "Invalid File";
        header('Location: /');
        exit(0);
    }
}
?>