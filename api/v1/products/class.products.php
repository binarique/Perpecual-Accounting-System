<?php
require_once("../currency/class.currency.php");
require_once("../config/config.php");
require_once("../accounts/class.accounts.php");
require_once("../category/class.category.php");

class Products{

private $helper;

private $currency;

private $config;

private $accounts;

private $category;

private $results_per_page = 20;

public function __construct($help){
$this->helper = $help;
$this->currency = new CURRENCY($this->helper); 
$this->config = new CONFIG();
$this->accounts = new Accounts($help);
$this->category = new Category($help);
}

public function getUnits($lang_iso_code){
$response = array();
$stmt = $this->helper->getAllRows("product_units");
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$unit = array(
"unit_id" => $this->helper->encryptID($row["unit_id"]),
"unit_name" => $row[$lang_iso_code."_unit_name"],
"unit_symbol" => $row["unit_symbol"]
);
array_push($response, $unit);
}
return $response;
}


public function getUnitsByID($lang_iso_code, $unitid){
  $stmt = $this->helper->runQuery("SELECT * FROM product_units WHERE unit_id = :uuid");
  $stmt->bindParam(":uuid", $unitid);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $unit = array(
  "unit_id" => $this->helper->encryptID($row["unit_id"]),
  "unit_name" => $row[$lang_iso_code."_unit_name"],
  "unit_symbol" => $row["unit_symbol"]
  );
  return $unit;
  }

public function createProductNo(){
return "PR-".$this->helper->getRandom();
}

public function productExist($product_no){
$stmt = $this->helper->runQuery("SELECT * FROM products WHERE product_no = :uproductno");
$stmt->bindParam(":uproductno", $product_no);
$stmt->execute();
$count = $stmt->rowCount();
if($count > 0){
return true;
}else{
return false;
}
}


public function getLimit($pageno, $results_per_page){
  $limit = 'LIMIT '. ($pageno-1) * $results_per_page  .','.$results_per_page;
  return $limit;
  }
  
  public function getPagesRequired($totalrows, $results_per_page){
  $no_pages_required = ceil($totalrows/$results_per_page);    
  return $no_pages_required;
  }

  public function readProductImages($product_no){
  $response = array();
  $stmt = $this->helper->runQuery("SELECT * FROM product_images WHERE product_no = :uprodno");
  $stmt->bindParam(":uprodno", $product_no);
  $stmt->execute();
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  extract($row);
  array_push($response, $this->helper->getHostURL().$this->config->PRODUCT_THUMBNAILS_FOLDER.$image_name);
  }
  return $response;
  }


  public function readProductDetails($product_no, $lang){
    $response = array();
    $stmt = $this->helper->runQuery("SELECT * FROM product_details WHERE product_no = :uprodno");
    $stmt->bindParam(":uprodno", $product_no);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    array_push($response, $row[$lang."_product_details"]);
    }
    return $response;
    }
  
  public function readOnProduct($productid, $lang){
    $stmt_product = $this->helper->runQuery("SELECT * FROM products WHERE product_id = :uprodid AND show_product=true");
    $stmt_product->bindParam(":uprodid", $productid);
    $stmt_product->execute();
    $row = $stmt_product->fetch(PDO::FETCH_ASSOC);
    extract($row);
    $product_item = array(  
      "product_id" => $this->helper->encryptID($product_id),
      "product_no" => $product_no,
      "ACC_ID" => $this->accounts->getUserDetails($ACC_ID, $this->config, $lang),
      "product_name" => $row[$lang."_product_name"],
      "product_images" => $this->readProductImages($product_no),
      "cat_id" => $this->category->readOneCategory($lang, $cat_id),
      "sold_in_units_id" => $this->getUnitsByID($lang, $sold_in_units_id),
      "product_stock" => $stock_units,
      "price_per_unit" => $price_per_unit,
      "min_unit_order" => $min_unit_order,
      "currency" => $this->currency-> getCurrencyById($currency_id),
      "product_description" => $row[$lang."_product_description"],
      "product_details" => $this->readProductDetails($product_no, $lang),
      "product_thumbnail" => $this->helper->getHostURL().$this->config->PRODUCT_THUMBNAILS_FOLDER.$product_thumbnail,
      "creation_date" => $creation_date,
      "modification_date" => $last_modification_date 
  );
  return $product_item;
  }

  public function getProductsRelated($lang, $cat_id, $product_id, $limit = ""){
    $response = array();
    $response["products"] = array();
    $stmt_product = $this->helper->runQuery("
    SELECT * FROM products WHERE cat_id = :ucatid AND product_id != :uprodid AND show_product=true
    UNION ALL
    SELECT * FROM products WHERE cat_id != :ucatid AND product_id != :uprodid AND show_product=true $limit");
    $stmt_product->bindParam(":ucatid", $cat_id);
    $stmt_product->bindParam(":uprodid", $product_id);
    $stmt_product->execute();
    $product_counter = $stmt_product->rowCount();
    if($product_counter > 0){
    while($row = $stmt_product->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $product_item = array(  
        "product_id" => $this->helper->encryptID($product_id),
        "product_no" => $product_no,
        "ACC_ID" => $this->accounts->getUserDetails($ACC_ID, $this->config, $lang),
        "product_name" => $row[$lang."_product_name"],
        "cat_id" => $this->helper->encryptID($cat_id),
        "sold_in_units_id" => $this->getUnitsByID($lang, $sold_in_units_id),
        "product_stock" => $stock_units,
        "price_per_unit" => $price_per_unit,
        "min_unit_order" => $min_unit_order,
        "currency" => $this->currency-> getCurrencyById($currency_id),
        "product_description" => $row[$lang."_product_description"],
        "product_thumbnail" => $this->helper->getHostURL().$this->config->PRODUCT_THUMBNAILS_FOLDER.$product_thumbnail,
        "creation_date" => $creation_date,
        "modification_date" => $last_modification_date 
    );
    array_push($response["products"], $product_item);
    }
    }
    return $response;
  }


  public function getRelatedProducts($lang_code, $cat_id, $product_id, $offset, $per_page = 0){
    $results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page; 
    $products = $this->getProductsRelated($lang_code, $cat_id, $product_id);
    $totalProducts = count($products["products"]);
    $PagesRequired = $this->getPagesRequired($totalProducts, $results_per_page);
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
    $next_records = $this->getNextRecords($totalProducts, $results_per_page,  $page);
    $response =  $this->getProductsRelated($lang_code, $cat_id, $product_id, $limit);
    $response["controls"] = array();
    $controls = array(
    "total_pages" => $PagesRequired,
    "current_page" => $page,
    "total_records" => $totalProducts,
    "next_records" => $next_records,
    "show_next" => $showNext,
    "show_previous" => $showPrevious
    );
    array_push($response["controls"], $controls);
    return $response;
    }
  

public function getProducts($lang_code, $lang, $limit = ""){
  $response = array();
  $response["products"] = array();
  $stmt_product = $this->helper->runQuery("SELECT * FROM products WHERE show_product=true ORDER BY product_id DESC $limit");
  $stmt_product->execute();
  $product_counter = $stmt_product->rowCount();
  if($product_counter > 0){
  while($row = $stmt_product->fetch(PDO::FETCH_ASSOC)){
  extract($row);
  $product_item = array(  
      "product_id" => $this->helper->encryptID($product_id),
      "product_no" => $product_no,
      "ACC_ID" => $this->accounts->getUserDetails($ACC_ID, $this->config, $lang),
      "product_name" => $row[$lang_code."_product_name"],
      "cat_id" => $this->helper->encryptID($cat_id),
      "sold_in_units_id" => $this->getUnitsByID($lang_code, $sold_in_units_id),
      "product_stock" => $stock_units,
      "price_per_unit" => $price_per_unit,
      "min_unit_order" => $min_unit_order,
      "currency" => $this->currency-> getCurrencyById($currency_id),
      "product_description" => $row[$lang_code."_product_description"],
      "product_thumbnail" => $this->helper->getHostURL().$this->config->PRODUCT_THUMBNAILS_FOLDER.$product_thumbnail,
      "creation_date" => $creation_date,
      "modification_date" => $last_modification_date 
  );
  array_push($response["products"], $product_item);
  }
  }
  return $response;
}

public function getUserProducts($lang, $userid, $limit = ""){
  $response = array();
  $response["products"] = array();
  $stmt_product = $this->helper->runQuery("SELECT * FROM products WHERE ACC_ID = :uuserid AND show_product = true ORDER BY product_id DESC $limit");
  $stmt_product->bindParam(":uuserid", $userid);
  $stmt_product->execute();
  $product_counter = $stmt_product->rowCount();
  if($product_counter > 0){
  while($row = $stmt_product->fetch(PDO::FETCH_ASSOC)){
  extract($row);
  $product_item = array(  
      "product_id" => $this->helper->encryptID($product_id),
      "product_no" => $product_no,
      "ACC_ID" => $this->helper->encryptID($ACC_ID),
      "product_name" => $row[$lang."_product_name"],
      "cat_id" => $this->helper->encryptID($cat_id),
      "sold_in_units_id" => $this->getUnitsByID($lang, $sold_in_units_id),
      "product_stock" => $stock_units,
      "price_per_unit" => $price_per_unit,
      "min_unit_order" => $min_unit_order,
      "currency" => $this->currency->getCurrencyById($currency_id),
      "product_description" => $row[$lang."_product_description"],
      "product_thumbnail" => $this->helper->getHostURL().$this->config->PRODUCT_THUMBNAILS_FOLDER.$product_thumbnail,
      "creation_date" => $creation_date,
      "modification_date" => $last_modification_date 
  );
  array_push($response["products"], $product_item);
  }
  }
  return $response;
}

public function getMyProducts($lang_code, $userid, $offset, $per_page = 0){
  $results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page; 
  $products = $this->getUserProducts($lang_code, $userid);
  $totalProducts = count($products["products"]);
  $PagesRequired = $this->getPagesRequired($totalProducts, $results_per_page);
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
  $next_records = $this->getNextRecords($totalProducts, $results_per_page,  $page);
  $response =  $this->getUserProducts($lang_code, $userid, $limit);
  $response["controls"] = array();
  $controls = array(
  "total_pages" => $PagesRequired,
  "current_page" => $page,
  "total_records" => $totalProducts,
  "next_records" => $next_records,
  "show_next" => $showNext,
  "show_previous" => $showPrevious
  );
  array_push($response["controls"], $controls);
  return $response;
  }

public function getNewProducts($lang_code, $lang, $offset, $per_page = 0){
$results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page; 
$products = $this->getProducts($lang_code, $lang);
$totalProducts = count($products["products"]);
$PagesRequired = $this->getPagesRequired($totalProducts, $results_per_page);
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
$next_records = $this->getNextRecords($totalProducts, $results_per_page,  $page);
$response =  $this->getProducts($lang_code, $lang, $limit);
$response["controls"] = array();
$controls = array(
"total_pages" => $PagesRequired,
"current_page" => $page,
"total_records" => $totalProducts,
"next_records" => $next_records,
"show_next" => $showNext,
"show_previous" => $showPrevious
);
array_push($response["controls"], $controls);
return $response;
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

public function productDetailsExist($product_no){
    $stmt1 = $this->helper->runQuery("SELECT * FROM product_details WHERE product_no = :uproductno");
    $stmt1->bindParam(":uproductno", $product_no);
    $stmt1->execute();
    $count = $stmt1->rowCount();
    if($count > 0){
    return true;
    }else{
    return false;
    }
    }
    
    
    public function productImagesExist($product_no){
    $stmt1 = $this->helper->runQuery("SELECT * FROM product_images WHERE product_no = :uproductno");
    $stmt1->bindParam(":uproductno", $product_no);
    $stmt1->execute();
    $count = $stmt1->rowCount();
    if($count > 0){
    return true;
    }else{
    return false;
    }
    }


public function saveProductImages($images, $product_no){
for($i = 0; $i < count($images); $i++){
$myimages = $images[$i];
$query = "INSERT INTO product_images SET product_no = :uproductno, 	image_name = :uimages";
$stmt = $this->helper->runQuery($query);
$stmt->bindParam(":uproductno", $product_no);
$stmt->bindParam(":uimages", $myimages);
$stmt->execute();
}
}

public function saveDetails($product_details, $product_no){
    $details = $product_details;
    $product_lang_detail = $this->helper->createColumn("product_details", $details);
    $keys_details = array_keys($details);
    
    $query = "INSERT INTO product_details SET product_no = :uproductno, ".$product_lang_detail;
    $stmt = $this->helper->runQuery($query);
    $stmt->bindParam(":uproductno", $product_no);
    
    for($i = 0; $i < count($keys_details); $i++){
    $colum_key = ":u".$keys_details[$i];
    $stmt->bindParam($colum_key, $details[$keys_details[$i]]);
    }
    $stmt->execute();
    }

public function createProduct($data, $lang){
$product_no = $this->helper->clean($data["product_no"]);
$userid = $this->helper->decryptID($this->helper->clean($data["userid"]));
$product_name = $data["product_name"];
$cat_id = $this->helper->decryptID($this->helper->clean($data["cat_id"]));
$units_id = $this->helper->decryptID($this->helper->clean($data["units_id"]));
$sold_units = $this->helper->clean($data["stock_units"]);
$price_per_unit = $this->helper->clean($data["price_per_unit"]);
$min_unit_order = $this->helper->clean($data["min_unit_order"]);
$currency_id = $this->helper->decryptID($this->helper->clean($data["currency_id"]));
$product_description = $data["product_description"];
$product_thumbnail = $this->helper->clean($data["product_thumbnail"]);
$images = $data["images"];
$product_details = $data["product_details"];
$product_name_keys = array_keys($product_name);
$product_description_keys = array_keys($product_description);
$product_details_keys = array_keys($product_details);
$product_lang_name = $this->helper->createColumn2("product_name", $product_name, "pname");
$product_lang_description  = $this->helper->createColumn2("product_description", $product_description, "pdesc");
if(!$this->productExist($product_no)){
$query = "INSERT INTO products SET
 product_no = :uproductno,
 ACC_ID = :uuserid,
 ".$product_lang_name.",
 cat_id = :ucatid,
 sold_in_units_id = :usoldunits,
 stock_units = :ustock,
 price_per_unit = :upriceperunit,
 min_unit_order = :uminorder,
 currency_id = :ucurrency,
 ".$product_lang_description.",
 product_thumbnail = :uproductthumb,
 creation_date = Now(),
 last_modification_date = Now(),
 show_product = true";
$stmt = $this->helper->runQuery($query);
$stmt->bindParam(":uproductno", $product_no);
//product name bindings
for($i = 0; $i < count($product_name_keys); $i++){
$column_key =  ":upname".$product_name_keys[$i];
$stmt->bindParam($column_key, $product_name[$product_name_keys[$i]]);
}
$stmt->bindParam(":ucatid", $cat_id);
$stmt->bindParam(":usoldunits", $units_id);
$stmt->bindParam(":uuserid", $userid);
$stmt->bindParam(":ustock", $sold_units);
$stmt->bindParam(":upriceperunit", $price_per_unit);
$stmt->bindParam(":uminorder", $min_unit_order);
$stmt->bindParam(":ucurrency", $currency_id);
//product description bindings
for($i = 0; $i < count($product_description_keys); $i++){
$column_key =  ":updesc".$product_description_keys[$i];
$stmt->bindParam($column_key, $product_description[$product_description_keys[$i]]);
}
$stmt->bindParam(":uproductthumb", $product_thumbnail);
if($stmt->execute()){
$this->saveProductImages($images, $product_no);
$this->saveDetails($product_details, $product_no);
http_response_code(200);
return array(
  "message" => $lang["PRODUCT_CREATED_SUCCESS"],
  "status" => "success"
); 
}
}else{
http_response_code(400);
return array(
  "message" => $lang["PRODUCT_CREATE_EXISTS_ERROR"],
  "status" => "failed"
);  
}
}

}

?>