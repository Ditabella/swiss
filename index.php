<?php

session_start();

$GLOBALS['user_id'] = 'user3213';

require_once 'common/DBConnection.php';
require_once 'common/LoadData.php';

use common\LoadData as LoadData;
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

$loadData = new LoadData();

$cook_val = $loadData->getBanksStart($GLOBALS['user_id']);

if(!isset($_COOKIE['start_balance'])) {
     setcookie('start_balance', serialize($cook_val));
} else {
    $_COOKIE['start_balance'] = $loadData->getNewBanksBalance($_COOKIE['start_balance'], 'Revolut', 29);
}

?>



<!DOCTYPE html>
<html lang="ru-RU">
<head>
  <meta charset="UTF-8">
  <title>Swiss Franc Tables</title>
  <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>

  <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
  <link rel="stylesheet" href="https://code.highcharts.com/css/highcharts.css">

  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>

  <script type="text/javascript" src="/js/moment.js"></script>


      <!-- <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.1/js/dataTables.searchBuilder.min.js"></script> -->
      <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
      <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-moment.js"></script>


  <script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script>
  <script src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>


  <script src="/js/dataTables.editor.min.js"></script>


  <link href="https://fonts.googleapis.com/css?family=PT+Sans&amp;subset=cyrillic,latin-ext" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=PT+Sans+Caption&amp;subset=cyrillic,latin-ext" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/solid.css"
        integrity="sha384-wnAC7ln+XN0UKdcPvJvtqIH3jOjs9pnKnq9qX68ImXvOGz2JuFoEiCjT8jyZQX2z" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css"
        integrity="sha384-HbmWTHay9psM8qyzEKPc8odH4DsOuzdejtnr+OFtDmOcIVnhgReQ4GZBH7uwcjf6" crossorigin="anonymous">
 <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/select/1.4.0/css/select.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css" />
  <link rel="stylesheet" href="/css/editor.dataTables.min.css" />
  <link rel="stylesheet" href="/css/style.css" />

</head>
<body>
<div id="ru">
  <div class="top">
    <header>
    </header>
    <main>
      <div class="container text-center">
        <div class="row justify-content-start mb-3  mt-3">
          <div class="col-7">
           <!--file add form - sart -->
            <div class="example exampleForm">
              <fieldset>
              <legend>Upload</legend>
            <!--<form id="upload" onsubmit="return false" method="POST" enctype="multipart/form-data">
                <fieldset>
              <legend>Upload</legend>
              <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000">
              <div>
                  <label for="fileselect">Browse files</label>
                  <input type="file" id="fileselect" name="fileselect[]" multiple="multiple">
                  <div id="filedrag" style="display: block;" class="">Click the file & drop it here (drag & drop)</div>
              </div>

                    <div id="messages"></div>
              <div id="submitbutton" style="display: none;">
                  <button type="submit">Загрузить файлы</button>
              </div>
                </fieldset>
            </form>-->

           <form action="common/code.php" method="POST" enctype="multipart/form-data">

              <input type="file" name="import_file" class="form-control" />
              <input type="hidden" name="user" value="<?=$GLOBALS['user_id']?>"/>
              <button type="submit" name="save_excel_data" class="btn btn-primary mt-3">Import</button>

            </form>
            <?php
              if(isset($_SESSION['message'])){
                  echo "<h4>".$_SESSION['message']."</h4>";
                  unset($_SESSION['message']);
              }
            ?></fieldset>
            </div>
               <!-- file add form - end -->

          </div>
          <div class="col-5">
            <div id="content">
              <?=$loadData->getRun($GLOBALS['user_id'])?>
            </div>
            </div>

          </div>
          <div class="row justify-content-start mb-3">
            <div class="col-12">
              <?=$loadData->getBanks($GLOBALS['user_id'], $_COOKIE['start_balance'])?>
            </div>
          </div>
          <div class="row justify-content-start mb-3">
            <div class="col-12">
              <div id="container"></div>              
            </div>
          </div>
          <div class="row justify-content-start mb-3">
            <div class="col-12">
                <?=$loadData->getTrans($GLOBALS['user_id'])?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <hr>
  <div class="bottom">
    <footer>
      
    </footer>
  </div>
</div>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<!-- <script type="text/javascript" src="/js/add_file.js"></script> -->
<!-- <script type="text/javascript" src="/js/scripts.js"></script> -->

<script type="text/javascript">
  var colors = ['#64BAEF', '#61DC67', '#db629c', '#f4c25d', '#f4f45d'];
  var highcharts = Highcharts.chart('container', {
    title: {
      text: 'Cash forecast'
    },
        xAxis: {
            type: 'datetime'
        },
        yAxis: {
          title: {
              text: 'Amount'
          }
        },
        plotOptions: {
          series: {
              pointStart: Date.UTC(<?=$loadData->getStartDate($GLOBALS['user_id'],'y')?>, <?=$loadData->getStartDate($GLOBALS['user_id'],'m')?>, <?=$loadData->getStartDate($GLOBALS['user_id'],'d')?>, <?=$loadData->getStartDate($GLOBALS['user_id'],'h')?>, <?=$loadData->getStartDate($GLOBALS['user_id'],'i')?>, <?=$loadData->getStartDate($GLOBALS['user_id'],'s')?>),
            pointIntervalUnit: 'day'
          },
          spline: {
            lineWidth: 4,
            states: {
                hover: {
                    lineWidth: 5
                }
            },
            marker: {
                enabled: false
            }
        }
      },
      colors:colors,
      series: [<?=$loadData->getSeries($GLOBALS['user_id'])?>],
});

var editorBank;
var editorTrans;

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

$(document).ready(function () {
/*  var start =  getCookie("start_balance");


$.ajax({
          url: "/common/reload_banks.php",
          method: "POST",
          data: { "startBalance": start,  "user_id": "<?=$GLOBALS['user_id']?>"},
          success: function(data) {
             console.log(data);
          }
});*/


  
    editorTrans = new $.fn.dataTable.Editor( {
        ajax:   "/common/ChangeData.php?table=table_<?=$GLOBALS['user_id']?>",
        table: "#transactions",
        fields: [ {
                label: "Transaction No:",
                name: "Transaction_No"
            }, {
                label: "Amount:",
                name: "Amount"
            }, {
                label: "Date:",
                name: "Date",
                type:  'datetime',
                //format:  'Y-m-d H:i:s',
                format:  'YYYY-MM-DD h:mm:ss'
            },
            {
                label: "Account:",
                name:  "Account",
                type:  "select",
                options: [<?=$loadData->getBankList($GLOBALS['user_id'])?>]
            },
        ]
    } );

    /*editor.on( 'preSubmit', function ( e, data, action ) {
  if ( action === 'edit' ) {
    data.data.stock += data.data.addStock;
  }
} );
    */

    /*editorTrans.field('Account').update( [
      'Mr', 'Ms', 'Mrs', 'Miss', 'Dr', 'Captain'
  ] );*/

    editorBank = new $.fn.dataTable.Editor( {
        ajax:   "/common/ChangeBank.php?table=table_<?=$GLOBALS['user_id']?>",
        table: "#banks_list",
        fields: [ {
                label: "Banks:",
                name: "Account"
            }, {
                label: "Currency:",
                name: "Currency"
            }/*, {
                label: "Starting balance:",
                name: "start_balance"
            }*/
        ]
    } );

    editorTrans.on('submitSuccess', function (e, json, data, action) {
      if (action === "edit") {
          console.log('edit_row');          
      }

      var start =  getCookie("start_balance");
      $.ajax({
        url: "/common/reload_chart.php", 
        type: "POST",
        dataType: 'json',
        data: {user_id: '<?=$GLOBALS['user_id']?>'},
        success:function(response) {
          var jsonData = JSON.parse("[" + response + "]");
          var all_series = [];
          $.each(jsonData, function(key,value) {
            var series = { data: []};
            $.each(value, function(key,val) {
                if (key == 'name') {
                    series.name = val;
                }else{
                    $.each(val, function(key,valu) {
                      series.data.push(valu);
                    });
                }
            });
            all_series.push(series);
          });
          highcharts.update({series: all_series}); 
        }
      });
      //dataTableBanks.draw();
      setTimeout( function () {
          //dataTableBanks.ajax.reload(null, false);
          dataTableBanks.clear().draw();
          console.log(dataTableBanks);
          console.log('bingo!');
      }, 5000 );

      /*setInterval( function () {
          dataTableBanks.ajax.reload();
      }, 30000 );*/

      /*dataTableBanks.clear().draw();
       dataTableBanks.rows.add(NewlyCreatedData); // Add new data
       dataTableBanks.columns.adjust().draw(); // Redraw the DataTable*/


    });

    $('#banks_list').on( 'click', 'tbody td.editable', function (e) {
        editorBank.inline( this );
    } );

    $('#transactions').on( 'click', 'tbody td.editable', function (e) {
        editorTrans.inline( this );
    } );

    $('#transactions').on('click', 'tbody td.row-remove', function (e) {
      editorTrans.remove(this.parentNode, {
          title: 'Delete record',
          message: 'Are you sure you wish to delete this record?',
          buttons: 'Delete'
      });
      console.log('delete_row');
    } );


    var start =  getCookie("start_balance");

    var dataTableBanks = $('#banks_list').DataTable({
      //document.cookie = "username=Arnold
        /*ajax: {
          url: "/common/reload_banks.php",
          type: "post",
          data: { "startBalance": start,  "user_id": "<?=$GLOBALS['user_id']?>"},
          dataSrc: function (json) {
             return json.data;
          }
        },
        serverSide: true,
        dataType: 'json',*/
        drawCallback: function( settings ) {
            //alert( 'DataTables has redrawn the table' );
        },
        paging: false,
        searching: false,
        info: false,
        columns: [
            { data: 'Account', className: 'editable' },
            { data: 'Currency', className: 'editable' },
            { data: 'start_balance', className: 'editable'  },
            { data: 'end_balance' },
            { data: 'end_balance_chf' }
        ]
    });

    var dataTableTranc = $('#transactions').DataTable({
        pagingType: 'simple_numbers',
        searching: false,
        dom: 'lBftrip',
        info: true,
        buttons: [
            'excel', 'pdf',
            { extend: "create", editor: editorTrans }
        ],
        columns: [
          { data: 'Account' },
          { data: 'Transaction_No', className: 'editable' },
          { data: 'Amount', className: 'editable'  },
          { data: 'Currency' },
          { data: 'Date' , className: 'editable'},
          {
              orderable: false,
                data: null,
                className: 'row-remove',
                defaultContent: '<span class="fa fa-trash"></span>'
            }
        ]
    });
});

$.getJSON("https://cdn.cur.su/api/latest.json", function(json) {});

</script>

</body>
</html>