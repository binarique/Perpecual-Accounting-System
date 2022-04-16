<?php
require_once("../currency/class.currency.php");
require_once("../config/config.php");
class SEARCH{

private $helper;

private $currency;

private $config;

private $results_per_page = 20;

public function __construct($help){
$this->helper = $help;  
$this->currency = new CURRENCY($help);
$this->config = new CONFIG(); 
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

public function generalSearch($key_word, $client, $category, $lang, $offset=1, $per_page=0){
    switch($client){
        case "android":
        return $this->webAppSearch($key_word, $client, $category, $lang, $offset, $per_page);
        //android
        break;
        case "iphone":
        return $this->webAppSearch($key_word, $client, $category, $lang, $offset, $per_page);
        //iphone
        break;
        case "webapp":
        return $this->webAppSearch($key_word, $client, $category, $lang, $offset, $per_page);
        //webapp
        break;
        default:
        return $this->webAppSearch($key_word, $client, $category, $lang, $offset, $per_page);
        //asume web app
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

//PRODUCTS SEARCH ONLY
public function productSearchOnly($key_word, $lang, $limit = ""){
$response = array();
$response["products"] = array();
$productname = $lang."_product_name";
$stmt_product = $this->helper->runQuery("SELECT * FROM products WHERE $productname LIKE '%$key_word%' AND show_product=true $limit");
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

//PRODUCTS CATEGORY ONLY
public function categorySearchOnly($key_word, $lang, $limit = ""){
$response = array();
$response["products"] = array();
$description = $lang."_product_description";
$productname = $lang."_product_name";
//CATEGORIES
$catname = $lang."_cat_name";
$stmt_category = $this->helper->runQuery("
SELECT
category.cat_id,
products.product_id,
products.product_no,
products.ACC_ID,
products.$productname,
products.cat_id,
products.sold_in_units_id,
products.stock_units,
products.price_per_unit,
products.min_unit_order,
products.currency_id,
products.$description,
products.product_thumbnail,
products.creation_date,
products.last_modification_date,
products.show_product,
category.$catname FROM category
INNER JOIN products ON category.cat_id = products.cat_id WHERE
category.$catname LIKE '%$key_word%' AND products.show_product=true $limit");
$stmt_category->execute();
$category_counter = $stmt_category->rowCount();
if($category_counter > 0){
while($row = $stmt_category->fetch(PDO::FETCH_ASSOC)){   
    extract($row);
    $product_item = array(  
        "product_id" => $this->helper->encryptID($product_id),
        "product_no" => $product_no,
        "ACC_ID" => $ACC_ID,
        "product_name" => $row[$lang."_product_name"],
        "cat_id" => $cat_id,
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

//PRODUCTS CATEGORY ONLY
public function categorySearchByID($key_word, $category_id, $lang, $limit = ""){
    $response = array();
    $response["products"] = array();
    $description = $lang."_product_description";
    $productname = $lang."_product_name";
    //CATEGORIES
    $catname = $lang."_cat_name";
    $stmt_category = $this->helper->runQuery("
    SELECT * FROM products WHERE cat_id = $category_id AND $productname LIKE '%$key_word%' AND show_product = true $limit");
    $stmt_category->execute();
    $category_counter = $stmt_category->rowCount();
    if($category_counter > 0){
    while($row = $stmt_category->fetch(PDO::FETCH_ASSOC)){   
        extract($row);
        $product_item = array(  
            "product_id" => $this->helper->encryptID($product_id),
            "product_no" => $product_no,
            "ACC_ID" => $ACC_ID,
            "product_name" => $row[$lang."_product_name"],
            "cat_id" => $cat_id,
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

public function resultRanking($productConter, $categoryCounter){
if($categoryCounter > $productConter){
return "CATEGORY";
}else{
return "PRODUCT";
} 
}

//CONDUCT WEB SEARCH FORMAT
public function webAppSearch($key_word, $client, $category, $lang, $offset, $per_page = 0){
$results_per_page = (($per_page > 0) && is_numeric($per_page)) ? $per_page : $this->results_per_page;
$key_word =  $this->helper->clean($key_word);
if(empty($category)){

//PRODUCTS ONLY
$products = $this->productSearchOnly($key_word, $lang);
$productsTotal = count($products["products"]);


//PRODUCTS CATEGORY ONLY
$category = $this->categorySearchOnly($key_word, $lang);
$categoryTotal = count($category["products"]);

//ranking results
$ranking = $this->resultRanking($productsTotal, $categoryTotal);

//limit
switch($ranking){
case "CATEGORY":
//CATEGORY RESPONSE  
$PagesRequired = $this->getPagesRequired($categoryTotal, $results_per_page);
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
$next_records = $this->getNextRecords($categoryTotal, $results_per_page,  $page);
$response =  $this->categorySearchOnly($key_word, $lang, $limit);
$response["controls"] = array();

$controls = array(
"total_pages" => $PagesRequired,
"current_page" => $page,
"total_records" => $categoryTotal,
"next_records" => $next_records,
"show_next" => $showNext,
"show_previous" => $showPrevious
);
array_push($response["controls"], $controls);
return $response;
break;
default:  
$PagesRequired = $this->getPagesRequired($productsTotal, $results_per_page); 
//GENERAL PRODUCTS RESPONSE  
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
$next_records = $this->getNextRecords($productsTotal, $results_per_page,  $page);
$response =  $this->productSearchOnly($key_word, $lang, $limit);
$response["controls"] = array();

$controls = array(
"total_pages" => $PagesRequired,
"current_page" => $page,
"total_records" => $productsTotal,
"next_records" => $next_records,
"show_next" => $showNext,
"show_previous" => $showPrevious
);
array_push($response["controls"], $controls);
return $response;
}
}else{
//CATEGORIES ONLY
//CATEGORIES
$TotalIDCategory = count($this->categorySearchByID($key_word, $category, $lang)["products"]);
$PagesRequired = $this->getPagesRequired($TotalIDCategory, $results_per_page);
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
$next_records = $this->getNextRecords($TotalIDCategory, $results_per_page,  $page);
$response = $this->categorySearchByID($key_word, $category, $lang, $limit);
$response["controls"] = array();

$controls = array(
"total_pages" => $PagesRequired,
"current_page" => $page,
"total_records" => $TotalIDCategory,
"next_records" => $next_records,
"show_next" => $showNext,
"show_previous" => $showPrevious
);
array_push($response["controls"], $controls);
return $response;
}
}

}
?>