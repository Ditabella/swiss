<?php

namespace common;

include( "../lib/DataTables.php" );
include( "../common/LoadData.php" );

// Alias Editor classes so they are easy to use

use common\LoadData as LoadData;

use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

$sql_table = $db->quote( $_GET['table'] );
$sql_table = str_replace("'", "", $sql_table);

/*
$sql = "
  
    SELECT g.*, tra.region AS team_region, ora.region AS opponent_region,
  
    FROM
      
        " . $sql_table . " g
      
    WHERE g.Account=". $db->quote( $_GET['Account'] ) ."
  
";*/


//$Currency = $loadData->getCurrencyOfBank($sql_table, 'Revolut');

//'table_user3213'

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, $sql_table, 'id')
    ->fields(
        Field::inst( 'Account' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Account is required' ) 
            ) ),
        Field::inst( 'Currency' )
            ->setValue('EUR')
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'Currency' )  
            ) )        
    )
    //->debug(true)
    

    //->where( 'Account', $_POST['Account'] )
    // Get the view
    //->readTable($sql)


    ->process( $_POST )
    ->json();