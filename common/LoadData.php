<?php

namespace common;
use mysqli;

class LoadData
{
    private $mysqli;

    public function __construct() {
        $this->mysqli = DBConnection::getInstance();
    }

    public function calcToCHF($currency)
    {
        if ($currency == 'CHF') return 1;
        $json = file_get_contents('https://cdn.cur.su/api/latest.json');
        $curArray = json_decode($json)->rates; // get Currencies from json file
        $chf = $curArray->CHF;
        $curVal = ($currency != 'USD') ? ($curArray->$currency / $chf) : (1 / $chf);
        return round($curVal, 4);        
    }

   public function getRun($user_id)
    {
        $fileCurArray = [];
        $sql = "SELECT DISTINCT Currency FROM table_" . $user_id;
        $Currency = $this->mysqli->query($sql);
        if (mysqli_num_rows($Currency) > 0) {
            while($row = mysqli_fetch_assoc($Currency)) {
                $fileCurArray[] = $row["Currency"];
              }
        }
        try {
            $json = file_get_contents('https://cdn.cur.su/api/latest.json');
            $curArray = json_decode($json)->rates; // get Currencies from json file
            $chf = $curArray->CHF;
            $curTable = '<table class="table">
            <tr><th>Currency</th><th>FX Rate</th></tr>';
            foreach ($fileCurArray as $cur) {
                if ($cur != 'CHF') {
                    $curVal = ($cur != 'USD') ? ($curArray->$cur / $chf) : (1 / $chf);
                    $curTable .= '<tr><td>' . $cur . '</td><td>' . round($curVal, 4) . '</td</tr>';
                }
            }
            $curTable .= '</table>';
            return $curTable;
        } catch (Exception $e) {
            echo 'Error: ',  $e->getMessage(), "\n";
        }
    }

    public function getStartDate($user_id, $type)
    {
        $firstDateText = '';
        $firstDateResult = [];
        $sql = "SELECT Date FROM table_" . $user_id . " ORDER BY Date ASC LIMIT 1";
        $firstDate = $this->mysqli->query($sql);
        if (mysqli_num_rows($firstDate) > 0) {
            $firstDateText = mysqli_fetch_assoc($firstDate);
        }
        $firstDateResult['y'] = date("Y",strtotime($firstDateText['Date']));
        $firstDateResult['m'] = date("m",strtotime($firstDateText['Date']));
        $firstDateResult['d'] = date("d",strtotime($firstDateText['Date']));
        $firstDateResult['h'] = date("H",strtotime($firstDateText['Date']));
        $firstDateResult['i'] = date("i",strtotime($firstDateText['Date']));
        $firstDateResult['s'] = date("s",strtotime($firstDateText['Date']));

        return $firstDateResult[$type];
    }

    public function getSeries($user_id){
        $series = '';
        $seriesList = [];
        $banksList = [];
        $sql = "SELECT DISTINCT Account FROM table_" . $user_id;
        $banks = $this->mysqli->query($sql);
        if (mysqli_num_rows($banks) > 0) {
            while($row = mysqli_fetch_assoc($banks)) {
                $banksList[] = $row["Account"];
              }
        }
        foreach ($banksList as $bank) {
            if ($bank){
                $seriesItem = '{"name": "' . $bank . '", "data": [';
                $sql = "SELECT Amount FROM table_" . $user_id ." WHERE Account ='" . $bank . "' ORDER BY Date ASC";
                $get_bank = $this->mysqli->query($sql);
                $amount = '';
                if (mysqli_num_rows($get_bank) > 0) {
                    $amounts = [];
                    while($row = mysqli_fetch_assoc($get_bank)) {
                        $amounts[] = (float)$row['Amount'];
                      }
                    $amount = implode(", ", $amounts);
                }
                $seriesItem .= $amount . "]}";
                $seriesList[] = $seriesItem;
            }
        }
        $series = implode(", ", $seriesList);
        return $series;        
    }

    public function getBanksStart($user_id)
    {
        $sql = "SELECT DISTINCT Account FROM table_" . $user_id;
        $banks = $this->mysqli->query($sql);
        if (mysqli_num_rows($banks) > 0) {
            while($row = mysqli_fetch_assoc($banks)) {
                $banksList[$row["Account"]] = 0;
              }
        }
        if(isset($banksList)) return $banksList;
    }


    public function getNewBanksBalance($cookie, $bank = '', $balance = 0)
    {
        $getCookBalance = unserialize($cookie);
        $getCookBalance[$bank] = $balance;
        return serialize($getCookBalance);
    }

    public function getBanks($user_id, $startBalance)
    {
        $getCookBalance = unserialize($startBalance);
        $banksList[] = '';
        $sql = "SELECT DISTINCT Account FROM table_" . $user_id;
        $banks = $this->mysqli->query($sql);
        if (mysqli_num_rows($banks) > 0) {
            while($row = mysqli_fetch_assoc($banks)) {
                $banksList[] = $row["Account"];
              }
        }
        $banksTable = '<table class="display" id="banks_list" style="width:100%">
         <thead>
            <tr>
                <th>Banks</th>
                <th>Currency</th>
                <th>Starting balance</th>
                <th>End balance</th>
                <th>End balance (CHF)</th>
            </tr>
        </thead>
        <tbody>';
        //setcookie('some_cookie_name', serialize($cook_val));

        //$get_cook = unserialize($_COOKIE['some_cookie_name']);

        //echo $get_cook['cook_one'];

        foreach ($banksList as $bank) {
            if ($bank){
                //setcookie($bank, 0, time() + 3600);// срок действия - 1 час (3600 секунд)
                $sql = "SELECT * FROM table_" . $user_id ." WHERE Account ='" . $bank . "' ORDER BY Date ASC";
                $get_bank = $this->mysqli->query($sql);
                $currency = '';
                $startBalance = $getCookBalance[$bank];
                $balance = $startBalance;
                $id = 1;
                if (mysqli_num_rows($get_bank) > 0) {
                    while($row = mysqli_fetch_assoc($get_bank)) {
                        $amount = (float)$row['Amount'];
                        $currency = $row['Currency'];
                        $id = $row['id'];
                        $balance += $amount;
                      }
                }
                $new_cur = $this->calcToCHF($currency);
                $balance_in_chf = $balance * $new_cur;
                $banksTable .= '
                <tr id="row_' . $id . '">
                    <td>'.$bank.'</td>
                    <td>'.$currency.'</td>
                    <td>'.$startBalance.'</td>
                    <td>'.$balance.'</td>
                    <td>'.round($balance_in_chf, 2).'</td>
                </tr>';

            }
        }
        $banksTable .= '</tbody></table>';
        return $banksTable;
    }

    public function getBanksRows($user_id, $startBalance)
    {
        $getCookBalance = unserialize($startBalance);
        $banksList[] = '';
        $sql = "SELECT DISTINCT Account FROM table_" . $user_id;
        $banks = $this->mysqli->query($sql);
        if (mysqli_num_rows($banks) > 0) {
            while($row = mysqli_fetch_assoc($banks)) {
                $banksList[] = $row["Account"];
              }
        }
        $banksData = array();
        foreach ($banksList as $bank) {
            if ($bank){
                $sql = "SELECT * FROM table_" . $user_id ." WHERE Account ='" . $bank . "' ORDER BY Date ASC";
                $get_bank = $this->mysqli->query($sql);
                $currency = '';
                $startBalanceRow = $getCookBalance[$bank];
                $balance = $startBalanceRow;
                $id = 1;
                $recordsTotal = mysqli_num_rows($get_bank);
                if (mysqli_num_rows($get_bank) > 0) {
                    while($row = mysqli_fetch_assoc($get_bank)) {
                        $amount = (float)$row['Amount'];
                        $currency = $row['Currency'];
                        $id = $row['id'];
                        $balance += $amount;
                      }
                }
                $new_cur = $this->calcToCHF($currency);
                $balance_in_chf = $balance * $new_cur;
                $banksData[] = array("DT_RowId"=>"row_" . $id,"Account"=>$bank,"Currency"=>$currency,"start_balance"=>$startBalanceRow,"end_balance"=>round($balance, 2),"end_balance_chf"=>round($balance_in_chf, 2));
                //array_push($banksData, $data_row);
            }
        }

        $tableData = array(/*"draw" => 1,"recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsTotal, */"data" => $banksData);
        return $tableData;
    }

    public function getBankList($user_id)
    {
        $sql = "SELECT DISTINCT Account FROM table_" . $user_id;
        $banks = $this->mysqli->query($sql);
        $banks_options = '';
        if (mysqli_num_rows($banks) > 0) {
            while($row = mysqli_fetch_assoc($banks)) {
                if ($row["Account"]) {
                    $banksList[] = '{ label: "' . $row["Account"] . '", value: "' . $row["Account"] . '" }';
                }
            }
        }
        if (isset($banksList)){
            $banks_options = implode(", ", $banksList);
            return $banks_options;
        }else{
            return false;
        }
    }

    public function getCurrencyOfBank($user_id, $bank)
    {
        $sql = "SELECT Currency FROM table_" . $user_id . " WHERE Account ='" . $bank . "' LIMIT 1";
        $Currency_query = $this->mysqli->query($sql);
        $Currency = '';
        if (mysqli_num_rows($Currency_query) > 0) {
            while($row = mysqli_fetch_assoc($Currency_query)) {
                if ($row["Currency"] != '') {
                    $Currency = $row["Currency"];
                }
            }
        }
        return $Currency;
    }

    public function getTrans($user_id)
    {
        $transList[] = '';
        $sql = "SELECT * FROM table_" . $user_id . " ORDER BY Date DESC";
        $trans = $this->mysqli->query($sql);
        $transTable = '<table class="display" id="transactions" style="width:100%">
         <thead>
            <tr>
                <th>Account</th>
                <th>Transactions No</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Date</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>';
        if (mysqli_num_rows($trans) > 0) {
            while($row = mysqli_fetch_assoc($trans)) {
                $transTable .= '<tr id="row_' . $row['id'] . '">
                <td>'.$row['Account'].'</td>
                <td>'.$row['Transaction_No'].'</td>
                <td>'.round((float)$row['Amount'], 2).'</td>
                <td>'.$row['Currency'].'</td>
                <td>'.$row['Date'].'</td>
                <td></td>
                </tr>';
            }
        }
        $transTable .= '</tbody></table>';
        return $transTable;
    }

    public function __destruct() {
        //Close the Connection
        $this->mysqli->close();
    }
}