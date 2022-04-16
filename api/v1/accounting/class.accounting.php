<?php
require_once("../config/helper.class.php");

class Accounting{

private $helper;

public function __construct(){
$this->helper = new Helper();
}

public function createFiscalYear($start_date, $enddate){
// open fiscal year
}

public function getAccountTypes(){

}

public function createCustomAccount($acccounttype, $accountname){

}

public function getJournal(){
$response = array();
//un adjusted trial balance
$stmt = $this->helper->runQuery("SELECT id, account_id, sum(dr) AS dr, sum(cr) AS cr FROM journal GROUP BY account_id");
if($stmt->execute()){
    while($row = $stmt->fetchObject()){
        $account =  $this->readAccountById($row->account_id);
        $row->account_id =  $account;
        if($account->account_type->type_code_name == "ADEX"){
         //ADEX
         //  increasing: DR
         // decreasing: CR
         $row->bd = ($row->dr-$row->cr);
        }else{
        //LER
        // increasing: CR
        // decreasing: DR
        $row->bd = ($row->dr + $row->cr);
        }
array_push($response, $row);
    }
}
return $response;
}

public function getTrailBalance(){
    $response = new stdClass;
    //un adjusted trial balance
    $response->enteries = array();
    //totals
    $dr_total = 0;
    $cr_total = 0;
    $stmt = $this->helper->runQuery("SELECT id, account_id, sum(dr) AS dr, sum(cr) AS cr FROM journal GROUP BY account_id");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
            $account =  $this->readAccountById($row->account_id);
            $row->account_id =  $account;
            if($account->account_type->type_code_name == "ADEX"){
             //ADEX
             //  increasing: DR
             // decreasing: CR
             $row->bd = ($row->dr-$row->cr);
            }else{
            //LER
            // increasing: CR
            // decreasing: DR
            $row->bd = ($row->dr + $row->cr);
            }
            // 
            $dr_total += $row->dr;
            $cr_total += $row->cr;
           
    //print_r($row);
    array_push($response->enteries, $row);
        }
}
$response->total = array(
    "total_dr" => $dr_total,
    "total_cr" => $cr_total
);
return $response;
}

public function getSummarizedBalanceSheet(){
    // Cash Account
    $cash = $this->getCashAccount();
   //print_r($cash)
    // bd inventory
    $inventory = $this->getTotalInventory();
    // bd Capital Account (EQuity)
    $capital = $this->getOwnersEquity();
    // bd returns(Net Profit)
    $returns = $this->getNetProfit();
    //print_r( $returns);

    $total_dr = $cash->bd + $inventory->bd;
    $total_cr = $capital->bd +  $returns->gross_profit;

    $response = array(
      "dr" => [
       $cash,
       $inventory
      ],

      "cr" => [
          $capital,
          $returns

      ],

      "totals" => [
          "dr" => $total_dr,
          "cr" => $total_cr
      ]

    );
    return $response;
}
////////

public function getNetProfit(){
    $response = new stdClass;
    $revenue = $this->getTotalRevenue();
    $expenses = $this->getTotalExpenses();
    $sales_returns = $this->getTotalSalesReturns();
    if(!empty($revenue->bd)){
    $netprofits = $revenue->bd - $expenses->bd;
    $gross_ratio = ($revenue->bd - $expenses->bd) /$revenue->bd;
    //$response = new stdClass;
    $response = $this->readAccountByCodeName("returns");
    // print_r($returns);
    $response->total_revenue = $revenue->bd;
    $response->total_expenses = $expenses->bd;
    $response->gross_profit = $netprofits;
    $response->gross_ratio = $gross_ratio;
    // Account Details
    // Create returns Account
    $response->dr =  0;
    $response->cr = $netprofits;
    $response->bd = $netprofits;
    }
  return $response;
}

public function getTotalRevenue(){
$revenue = $this->readAccountByCodeName("revenue");
$account = $this->getAccountEntry($revenue->id);
$sales_returns = $this->getTotalSalesReturns();
// subtract sales returns from revenue
if(!empty($account->bd) && !empty($sales_returns->bd)){
$account->bd = $account->bd-$sales_returns->bd;
}
//print_r($account);
return $account;
}
////////

public function getTotalSalesReturns(){
    $sales_returns = $this->readAccountByCodeName("sales_returns");
    $account = $this->getAccountEntry($sales_returns->id);
    //print_r($account);
    return $account;
}
    ////////

public function getOwnersEquity(){
    $capital = $this->readAccountByCodeName("capital");
    $account = $this->getAccountEntry($capital->id);
    //print_r($account);
    return $account;
}
////////

public function getCashAccount(){
    $cashacc = $this->readAccountByCodeName("cash_account");
    $cashacc = $this->getAccountEntry($cashacc->id);
    //print_r($account);
    return $cashacc;
}
    ////////

public function getTotalExpenses(){
    $expenses = $this->readAccountByCodeName("expenses");
    $account = $this->getAccountEntry($expenses->id);
    //print_r($account);
    return  $account;
}
////////

public function getTotalInventory(){
    $inventory = $this->readAccountByCodeName("inventory");
    $account = $this->getAccountEntry($inventory->id);
   // print_r($account);
   return $account;
}
////////
    
public function getAccountEntry($account_id){
    $row = new stdClass;
    // Get Account Entry
    $stmt = $this->helper->runQuery("SELECT id, account_id, sum(dr) AS dr , sum(cr) AS cr FROM journal WHERE account_id = :uuid GROUP BY account_id");
    $stmt->bindParam(":uuid", $account_id);
    $stmt->execute();
    if($stmt->rowCount() > 0){
    $row = $stmt->fetchObject();
            $account =  $this->readAccountById($row->account_id);
            $row->account_id =  $account;
            if($account->account_type->type_code_name == "ADEX"){
             //ADEX
             //  increasing: DR
             // decreasing: CR
             $row->bd = ($row->dr-$row->cr);
            }else{
            //LER
            // increasing: CR
            // decreasing: DR
            $row->bd = ($row->dr + $row->cr);
            }
        }
    return $row;  
}

public function postSalesReturnsToAccounts(){
    // offseting Sales returns from accounts by adding a contra revenue accout of (Sales Return Account)
    // This requires ad cash account which is decreasing and  sales returns account which is increasing
    // we need to debit(DR) the Sales return account and credit(CR) the cash account
    // since we are using a perpetual Inventory Method we also to put back the return inventory back in the books 
    // by Debiting(DR) inventory with original cost price and Credit(CR) the (Cost of goods) the same amount
    // Sales Return  DR  - increasing  LER
    $sales_return_account =  $this->readAccountByCodeName("sales_returns"); // Sales Return DR
    // cash CR  - decreasing ADEX
    $cash_account = $this->readAccountByCodeName("cash_account"); // Cash Account - CR
    // print_r($sales_return_account);
    $journal_category = $this->readJournalCategory("sales");// Sales
    $fiscal_year = $this->readActiveFiscalYear(); // current fiscal ye
    $stmt = $this->helper->runQuery("SELECT * FROM sales_refunds WHERE account_posting = false");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
            $journal_date = explode(" ", $row->created)[0];
            $order_item = $this->readOrderItemById($row->id);
             // 
            //print_r($order_item);
            $item = $order_item->item_id;
            $qtyReturned = $row->qty;
            $unit_cost_price = $item->unit_cost_price;
            $cost_of_goods_returned = $qtyReturned * $unit_cost_price;
            $cogs_narration = $row->narration;
            $transaction_type = $order_item->orderID->transaction_type_id;
            //print_r($transaction_type);
             //Cost of Goods
             // by Debiting(DR) inventory with original cost price and Credit(CR) the (Cost of goods) the same amount
           $this->updateCostOfGoodsAccount($journal_date, $fiscal_year, $cogs_narration, 0,  $cost_of_goods_returned);
            $unit_selling_price = $item->unit_selling_price;
            $price_of_goods_returned = $qtyReturned * $unit_selling_price;
            $journal_no = "JRN".rand(1000, 1000000);
            //echo $sellingprice."\n";
            // DR
            $narration = $row->narration;
            // cash Account -- increasing DR
            $this->saveEntry($sales_return_account->id, $journal_category->id, $fiscal_year->id, $narration,  $price_of_goods_returned, 0, $journal_no, $journal_date);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //revenue -- increasing CR
            // Cash Account decreases 
            $this->saveEntry($cash_account->id, $journal_category->id, $fiscal_year->id, $narration, 0,  $price_of_goods_returned, $journal_no, $journal_date);
            //CR
            //print_r($item);
           $this->updateSalesRefundsToAccountPosting($row->id);
        }
    }
}

public function postSalesToAccounts(){
    $revenue_account =  $this->readAccountById(3); // Revenue the money that contains my profits and capital 
    $journal_category = $this->readJournalCategory("sales");// Sales
    $fiscal_year = $this->readActiveFiscalYear(); // current fiscal year
    $stmt = $this->helper->runQuery("SELECT * FROM orders WHERE isSettled=true AND account_posting = false");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
          $trx_type_id = $row->transaction_type_id;
          // cash or credit
          $transaction_type = $this->readTransactionType($trx_type_id);
          // if the transaction is cash=CashAccount or if its a Credit then Accounts receiver(Debtor client)
          $account_id = $transaction_type->trx_code_name == "cash" ? 1 : 6;
          // get account to  affected
          $account = $this->readAccountById($account_id);
          //print_r($row);
          // order
          $orderID = $row->orderID;
          //////////////////////////////
          if($transaction_type->trx_code_name == "cash"){
            // cash sales
            // $narration = "Cash sale of ".$qty." ".$row->item_name;
            $journal_date = explode(" ", $row->orderDate)[0];
            //echo $journal_date;
            // get order items
            $stmt2 = $this->helper->runQuery("SELECT * FROM order_items WHERE orderID = :uorderid");
            $stmt2->bindParam(":uorderid", $orderID);
            $stmt2->execute();
            while($row2 = $stmt2->fetchObject()){
             $item = $this->readInventoryItemById($row2->item_id);
             $qtySold = $row2->qty;
             $unit_cost_price = $item->unit_cost_price;
             $cost_of_goods_sold = $qtySold * $unit_cost_price;
             $cogs_narration = "Cost of goods for ".$qtySold." ".$item->item_name;
              //Cost of Goods
              $this->updateCostOfGoodsAccount($journal_date, $fiscal_year, $cogs_narration, $cost_of_goods_sold, 0);
             $unit_selling_price = $item->unit_selling_price;
             $price_of_goods_sold = $qtySold * $unit_selling_price;

             $journal_no = "JRN".rand(1000, 1000000);
             //echo $sellingprice."\n";
             // DR
             $narration = "Cash sale of ".$qtySold." ".$item->item_name;
             // cash Account -- increasing DR
             $this->saveEntry($account_id, $journal_category->id, $fiscal_year->id, $narration,  $price_of_goods_sold, 0, $journal_no, $journal_date);
             ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
             //revenue -- increasing CR
             // Cash Account decreases 
             $this->saveEntry($revenue_account->id, $journal_category->id, $fiscal_year->id, $narration, 0,  $price_of_goods_sold, $journal_no, $journal_date);
             //CR
           //  print_r($item);
            }
          }else{
              // credit sales
              //Accounts recievable(debtors)

          }
          $this->updateSalesToAccountPosting($orderID);
        }

    }
}


public function updateCostOfGoodsAccount($journal_date, $fiscal_year, $narration, $entry1, $entry2){
    // Cost of goods sold(Expenses) posting the money spent on paying goods sold and how they affect the inventory
    // Expenses DR  - increasing ADEX ( we are getTing the money back that we spent on buying the inventory)
    $expenses = $this->readAccountByCodeName("expenses"); // cost of goods sold (COGS)
    // Inventory  - decreasing (Purchase Account) ADEX
    //WHEN WE INCCUR COST OF GOODS (INVENTORY REDUCES SO WE CR)
    $purchase_account =  $this->readAccountById(2); // inventory /purcahse
    $journal_category = $this->readJournalCategory("inventory");// inventory
    // new journal numbers for cost of goods and inventory journal pair
    $journal_no = "JRN".rand(1000, 1000000);
    // Debit the cash account with cost of goods
    //  Expenses -  DR  - increasing  ADEX
    $this->saveEntry($expenses->id, $journal_category->id, $fiscal_year->id, $narration, $entry1, $entry2, $journal_no, $journal_date);
    // Inventory  - CR -  decreasing (Purchase Account) ADEX
    $this->saveEntry($purchase_account->id, $journal_category->id, $fiscal_year->id, $narration, $entry2, $entry1, $journal_no, $journal_date);
    }


// public function offsetInventory($amount){
//    // offset inventory
//    $journal_category = $this->readJournalCategory("inventory");// inventory
// }

public function DepositToCashAndCapitalAccount(){
//Cash From Inventory Affects The Cash And Capital Account
//Deposting available inventory to cash and capital account if it was bought using cash
// cash  DR  - increasing  ADEX
$cash_account =  $this->readAccountById(1); // Cash Account DR
// capital CR  - increasing LER
$capital_account = $this->readAccountByCodeName("capital");
$journal_category = $this->readJournalCategory("capital_and_cash");// Default(System Account)
$fiscal_year = $this->readActiveFiscalYear(); // current fiscal year
$transaction_type = $this->readTransactionTypeByCodeName("cash");
$journal_no = "JRN".rand(1000, 1000000);
$stmt = $this->helper->runQuery("SELECT *  FROM inventory WHERE trx_type_id=:uutrxid AND account_posting = false");
$stmt->bindParam(":uutrxid", $transaction_type->id);
$stmt->execute();
while($row = $stmt->fetchObject()){
$cost_of_goods = $row->qty * $row->unit_cost_price;
$narration = "Deposit of ".$cost_of_goods." in cash to capital account";
$journal_date = explode(" ", $row->created)[0];
// Debit the cash account with cost of goods
$journal_category = $this->readJournalCategory("capital_and_cash");// Default(System Account)
// cash  DR  - increasing  ADEX
$this->saveEntry($cash_account->id, $journal_category->id, $fiscal_year->id, $narration, $cost_of_goods, 0, $journal_no, $journal_date);
// capital CR  - increasing LER
$this->saveEntry($capital_account->id, $journal_category->id, $fiscal_year->id, $narration, 0, $cost_of_goods, $journal_no, $journal_date);
}
}



public function postInventoryToAccounts(){
    $this->DepositToCashAndCapitalAccount();
    $purchase_account =  $this->readAccountById(2); // inventory /purcahse
    $journal_category = $this->readJournalCategory("inventory");// inventory
    $fiscal_year = $this->readActiveFiscalYear(); // current fiscal year
    // get inventory
    $stmt = $this->helper->runQuery("SELECT * FROM inventory WHERE 	account_posting = false");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
            $trx_type_id = $row->trx_type_id;
            // cash or credit
            $transaction_type = $this->readTransactionType($trx_type_id);
            // if the transaction is cash=CashAccount or if its a Credit then Accounts payable(Creditors)
            $account_id = $transaction_type->trx_code_name == "cash" ? 1 : 5;
            // get account to  affected
            $account = $this->readAccountById($account_id);
            $qty = $row->qty;
            $unit_cost_price = $row->unit_cost_price;
            $unit_selling_price = $row->unit_selling_price;
            $total = $qty * $unit_cost_price;
            $journal_no = "JRN".rand(1000, 1000000);
            $journal_date = explode(" ", $row->created)[0];
            //echo $journal_no;
            // journal category
            //  DR - CR
            if($transaction_type->trx_code_name == "cash"){
                // cash purchase
                $narration = empty($row->narration) ? "Cash purchase of ".$qty." ".$row->item_name : $row->narration;
                // cash puscharse
                // inventory increases (we debt) DR
                $this->saveEntry($purchase_account->id, $journal_category->id, $fiscal_year->id, $narration, $total, 0, $journal_no, $journal_date);
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Cash Account reduces, inventory increases
               //- Cash Account decreases(we credit) CR
                $this->saveEntry($account_id, $journal_category->id, $fiscal_year->id, $narration, 0, $total, $journal_no, $journal_date);
            }else{
                // Accounts payable increase CR, inventory increases(creditors) DR
                $creditor = $this->readSupplier($row->supplier_id);
               // print_r($creditor);
                $narration = empty($row->narration) ? "Credit purchase of ".$qty." ".$row->item_name." from ".$creditor->supplier_name : $row->narration;
               // echo $narration;
                // inventory increases (we debt) DR ADEX
                $this->saveEntry($purchase_account->id, $journal_category->id, $fiscal_year->id, $narration, $total, 0, $journal_no, $journal_date);
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //Accounts Account reduces, inventory increases
               //- Accounts payable is increasing (we credit) CR LER
               $this->saveEntry($account_id, $journal_category->id, $fiscal_year->id, $narration, 0, $total, $journal_no, $journal_date);
            }
           $this->updateInventoryAccountPosting($row->id);
           echo "success";
        }
    }

}

// Update Sales Accounting
public function updateSalesRefundsToAccountPosting($refund_id){
    $stmt = $this->helper->runQuery("UPDATE sales_refunds SET account_posting = true WHERE id=:uuid");
    $stmt->bindParam(":uuid", $refund_id);
    if($stmt->execute()){
     return true;
    }else{
     return false;
    }
}

// Update Sales Accounting
public function updateSalesToAccountPosting($order_id){
    $stmt = $this->helper->runQuery("UPDATE orders SET account_posting = true WHERE orderID=:uorderid");
    $stmt->bindParam(":uorderid", $order_id);
    if($stmt->execute()){
     return true;
    }else{
     return false;
    }
}

// Update Inventory Accounting
public function updateInventoryAccountPosting($inv_id){
    $stmt = $this->helper->runQuery("UPDATE inventory SET account_posting = true WHERE id=:uinvid");
    $stmt->bindParam(":uinvid", $inv_id);
    if($stmt->execute()){
     return true;
    }else{
     return false;
    }
}

public function saveEntry($account, $journal_category, $fiscal_year, $narration, $dr, $cr, $journo, $journal_date){
    $query = "INSERT INTO  journal SET
     account_id = :uaccount,
     journal_cat_id	 = :ucategory,
     fiscal_year_id = :ufiscal,
     dr = :udr,
     cr = :ucr,
     journal_no = :ujourno,
     narration = :unarration,
     is_open = true,
     journal_date = :ujournodate";
    $stmt = $this->helper->runQuery($query);
    $stmt->bindParam(":uaccount", $account);
    $stmt->bindParam(":ucategory", $journal_category);
    $stmt->bindParam(":ufiscal", $fiscal_year);
    $stmt->bindParam(":udr", $dr);
    $stmt->bindParam(":ucr", $cr);
    $stmt->bindParam(":ujourno", $journo);
    $stmt->bindParam(":unarration", $narration);
    $stmt->bindParam(":ujournodate", $journal_date);
    if($stmt->execute()){
      return true;
    }else{
      return false;
    }
}

public function readSupplier($supplier_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM supplier WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $supplier_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readCustomer($customer_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM customers WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $customer_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readOrderItemById($order_item_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM order_items WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $order_item_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    $row2->orderID = $this->readOrderById($row2->orderID);
    $row2->item_id = $this->readInventoryItemById($row2->item_id);
    return $row2;
}


public function readOrderById($order_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM orders WHERE orderID = :uuid");
    $stmt2->bindParam(":uuid", $order_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    $row2->transaction_type_id = $this->readTransactionType($row2->transaction_type_id);
    return $row2;
}

public function readInventoryItemById($item_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM inventory WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $item_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readAccountByCodeName($revenue){
    $stmt2 = $this->helper->runQuery("SELECT * FROM accounts WHERE account_code_name = :uucodename");
    $stmt2->bindParam(":uucodename", $revenue);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    //ADEX - LER items
    $row2->account_group = $this->readAccountGroupById($row2->account_group);
    //Adex - LER types
    $row2->account_type = $this->readAccountType($row2->account_type);
    return $row2;
}

public function readAccountById($account_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM accounts WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $account_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    //ADEX - LER items
    $row2->account_group = $this->readAccountGroupById($row2->account_group);
    //Adex - LER types
    $row2->account_type = $this->readAccountType($row2->account_type);
    return $row2;
}

public function readAccountGroupById($account_group_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM account_groups WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $account_group_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    $row2->account_type_id = $this->readAccountType($row2->account_type_id);
    return $row2;
}

public function readAccountType($account_type_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM account_types WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $account_type_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readActiveFiscalYear(){
    $stmt2 = $this->helper->runQuery("SELECT * FROM fiscal_year WHERE closed = false");
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}


public function readTransactionType($trx_type_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM  transaction_types WHERE id = :uuid");
    $stmt2->bindParam(":uuid", $trx_type_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readTransactionTypeByCodeName($code_name){
    $stmt2 = $this->helper->runQuery("SELECT * FROM  transaction_types WHERE trx_code_name = :ucodename");
    $stmt2->bindParam(":ucodename", $code_name);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}

public function readJournalCategory($code_name){
    $stmt2 = $this->helper->runQuery("SELECT * FROM  journal_categories WHERE journal_cat_code_name = :ucodename");
    $stmt2->bindParam(":ucodename", $code_name);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}



}
?>