<?php
class Category{
private $helper;

public function __construct($help){
$this->helper = $help;
}

public function getCategories($lang_iso_code){
$response = array();
$stmt = $this->helper->getAllRows("category");
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$unit = array(
"cat_id" => $this->helper->encryptID($row["cat_id"]),
"cat_name" => $row[$lang_iso_code."_cat_name"],
"cat_icon_image" => $row["cat_icon_image"]
);
array_push($response, $unit);
}
return $response;
}

public function readOneCategory($lang_iso_code, $catid){
    $stmt = $this->helper->runQuery("SELECT * FROM category WHERE cat_id = :ucatid");
    $stmt->bindParam(":ucatid", $catid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $category = array(
    "cat_id" => $this->helper->encryptID($row["cat_id"]),
    "cat_name" => $row[$lang_iso_code."_cat_name"],
    "cat_icon_image" => $row["cat_icon_image"]
    );
    return $category;
    }

}

?>