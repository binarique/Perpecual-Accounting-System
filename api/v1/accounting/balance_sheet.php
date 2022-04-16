<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class.accounting.php");
$accounting = new Accounting();
$summarized_balancesheet = $accounting->getSummarizedBalanceSheet();
echo json_encode($summarized_balancesheet);
?>