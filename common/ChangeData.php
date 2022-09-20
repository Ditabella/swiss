<?php

namespace common;

include( "../lib/DataTables.php" );

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


//$Currency = $loadData->getCurrencyOfBank($sql_table, 'Revolut');

//'table_user3213'

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, $sql_table, 'id')
    ->fields(
        Field::inst( 'Account' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Account is required' ) 
            ) ),
        Field::inst( 'Transaction_No' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Transactions No is required' )  
            ) ),
        Field::inst( 'Amount' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Amount is required' )  
            ) ),
        Field::inst( 'Currency' )
            ->setValue('EUR')
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'Currency' )  
            ) ),
        Field::inst( 'Date' )
            ->validator( Validate::dateFormat( 'Y-m-d H:i:s' ) )
            ->getFormatter( Format::dateSqlToFormat( 'Y-m-d H:i:s' ) )
            ->setFormatter( Format::dateFormatToSql('Y-m-d H:i:s' ) )
    )
    ->debug(true)
    ->process( $_POST )
    ->json();