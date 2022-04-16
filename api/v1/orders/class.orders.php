<?php
require_once("../products/class.products.php");
class ORDERS{

private $helper;

private $currency;

private $config;

private $accounts;

private $category;

private $product;

public function __construct($help){
$this->helper = $help;
$this->currency = new CURRENCY($this->helper); 
$this->config = new CONFIG();
$this->accounts = new Accounts($help);
$this->category = new Category($help);
$this->product = new Products($help);
}


public function orderItem($farmer_id, $product_id, $stock_units_required, $order_description, $trader_id){
    $stmt = $this->helper->runQuery("
    INSERT INTO orders SET
    farmer_id = :ufarmid,
    product_id = :uprodid,
    stock_units_required = :ustockunits,
    order_description = :uorderdesc,
    trader_id = :utradeid,
    seen = false,
    confirmed = false");
    $stmt->bindParam(":ufarmid", $farmer_id);
    $stmt->bindParam(":uprodid", $product_id);
    $stmt->bindParam(":ustockunits", $stock_units_required);
    $stmt->bindParam(":uorderdesc", $order_description);
    $stmt->bindParam(":utradeid", $trader_id);
    if($stmt->execute()){
    return true;
    }else{
    return false;
    }
}

public function confirmOrder($orderid){
    $stmt = $this->helper->runQuery("UPDATE orders SET confirmed = true WHERE order_id = :uorderid");
    $stmt->bindParam(":uorderid", $orderid);
    if($stmt->execute()){
    return true;
    }else{
    return false;
    }
}

public function deleteOrder($orderid){
    $stmt = $this->helper->runQuery("DELETE FROM orders WHERE order_id = :uorderid AND confirmed = false");
    $stmt->bindParam(":uorderid", $orderid);
    if($stmt->execute()){
    return true;
    }else{
    return false;
    }
}


public function getOneOrder($orderid, $lang){
    $stmt = $this->helper->runQuery("SELECT * FROM orders WHERE order_id = :uorderid");
    $stmt->bindParam(":uorderid", $orderid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    return array(  
        "order_id" => $this->helper->encryptID($order_id),
        "farmer_id" => $this->accounts->getUserDetails($farmer_id, $this->config, $lang),
        "product_id" => $this->product->readOnProduct($product_id, $lang),
        "stock_units_required" => $stock_units_required,
        "order_description" => $order_description,
        "trader_id" => $this->accounts->getUserDetails($trader_id, $this->config, $lang),
        "seen" => $seen,
        "order_date_time" => $order_date_time,
        "confirmed" => $confirmed
    );   
}

public function getMyTraderOrders($lang_code, $trader_id, $offset, $per_page = 0){
    $results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page; 
    $orders = $this-> getTraderOrders($trader_id, $lang_code);
    $totalOrders = count($orders["orders"]);
    $PagesRequired = $this->getPagesRequired($totalOrders, $results_per_page);
    $showNext = false;
    $showPrevious = false;
    //offet safty
    $page = 1;
    if($offset <= 1){
    $page = 1;
    $showNext = true;
    $showPrevious = false;
    }
    
    if(($offset >= 2) && ($offset <= $PagesRequired)){
    $page = $offset;
    $showNext = true;
    $showPrevious = true;
    }
    
    if($offset >= $PagesRequired){
    $page =  $PagesRequired; 
    $showNext = false;
    $showPrevious = true;
    }
    
    $limit  = $this->getLimit($page, $results_per_page);
    $next_records = $this->getNextRecords($totalOrders, $results_per_page,  $page);
    $response =  $this->getTraderOrders($trader_id, $lang_code, $limit);
    $response["controls"] = array();
    $controls = array(
    "total_pages" => $PagesRequired,
    "current_page" => $page,
    "total_records" => $totalOrders,
    "next_records" => $next_records,
    "show_next" => $showNext,
    "show_previous" => $showPrevious
    );
    array_push($response["controls"], $controls);
    return $response;
    }

public function getTraderOrders($trader_id, $lang, $limit = ""){
  $response = array();
  $response["orders"] = array();
  $stmt = $this->helper->runQuery("SELECT * FROM orders WHERE trader_id = :utraderid ORDER BY order_id DESC $limit");
  $stmt->bindParam(":utraderid", $trader_id);
  $stmt->execute();
  $product_counter = $stmt->rowCount();
  if($product_counter > 0){
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  extract($row);
  $order_item = array(  
      "order_id" => $this->helper->encryptID($order_id),
      "farmer_id" => $this->accounts->getUserDetails($farmer_id, $this->config, $lang),
      "product_id" => $this->product->readOnProduct($product_id, $lang),
      "stock_units_required" => $stock_units_required,
      "order_description" => $order_description,
      "trader_id" => $this->accounts->getUserDetails($trader_id, $this->config, $lang),
      "seen" => $seen,
      "order_date_time" => $order_date_time,
      "confirmed" => $confirmed
  );
  array_push($response["orders"], $order_item);
  }
  }
  return $response;
}


public function getMyFarmerOrders($lang_code, $farmer_id, $offset, $per_page = 0){
    $results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page; 
    $orders = $this->getFarmerOrders($farmer_id, $lang_code);
    $totalOrders = count($orders["orders"]);
    $PagesRequired = $this->getPagesRequired($totalOrders, $results_per_page);
    $showNext = false;
    $showPrevious = false;
    //offet safty
    $page = 1;
    if($offset <= 1){
    $page = 1;
    $showNext = true;
    $showPrevious = false;
    }
    
    if(($offset >= 2) && ($offset <= $PagesRequired)){
    $page = $offset;
    $showNext = true;
    $showPrevious = true;
    }
    
    if($offset >= $PagesRequired){
    $page =  $PagesRequired; 
    $showNext = false;
    $showPrevious = true;
    }
    
    $limit  = $this->getLimit($page, $results_per_page);
    $next_records = $this->getNextRecords($totalOrders, $results_per_page,  $page);
    $response =  $this->getFarmerOrders($farmer_id, $lang_code, $limit);
    $response["controls"] = array();
    $controls = array(
    "total_pages" => $PagesRequired,
    "current_page" => $page,
    "total_records" => $totalOrders,
    "next_records" => $next_records,
    "show_next" => $showNext,
    "show_previous" => $showPrevious
    );
    array_push($response["controls"], $controls);
    return $response;
    }


public function getFarmerOrders($farmer_id, $lang, $limit = ""){
    $response = array();
    $response["orders"] = array();
    $stmt = $this->helper->runQuery("SELECT * FROM orders WHERE farmer_id = :ufarmerid ORDER BY order_id DESC $limit");
    $stmt->bindParam(":ufarmerid", $farmer_id);
    $stmt->execute();
    $product_counter = $stmt->rowCount();
    if($product_counter > 0){
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $order_item = array(  
        "order_id" => $this->helper->encryptID($order_id),
        "farmer_id" => $this->accounts->getUserDetails($farmer_id, $this->config, $lang),
        "product_id" => $this->product->readOnProduct($product_id, $lang),
        "stock_units_required" => $stock_units_required,
        "order_description" => $order_description,
        "trader_id" => $this->accounts->getUserDetails($trader_id, $this->config, $lang),
        "seen" => $seen,
        "order_date_time" => $order_date_time,
        "confirmed" => $confirmed
    );
    array_push($response["orders"], $order_item);
    }
    }
    return $response;
  }


public function getLimit($pageno, $results_per_page){
    $limit = 'LIMIT '. ($pageno-1) * $results_per_page  .','.$results_per_page;
    return $limit;
    }
    
    public function getPagesRequired($totalrows, $results_per_page){
    $no_pages_required = ceil($totalrows/$results_per_page);    
    return $no_pages_required;
    }
  
public function getNextRecords($totalrows, $results_per_page,  $page){
    $PagesRequired = $this->getPagesRequired($totalrows, $results_per_page);
    if((($totalrows % $results_per_page) == 0) && ($page >= $PagesRequired)){
    return 0; 
    }else if((($totalrows % $results_per_page) > 0) && ($page >= $PagesRequired)){
    return 0;
    }else if((($totalrows % $results_per_page) > 0) && ($page >= ($PagesRequired-1))){
    return $totalrows % $results_per_page;   
    }else if((($totalrows % $results_per_page) == 0) && ($page < $PagesRequired)){
    return $results_per_page;  
    }else if((($totalrows % $results_per_page) > 0) && ($page < $PagesRequired)){
    return $results_per_page;
    }else{
    return 0;
    }
    }
  


}
?>