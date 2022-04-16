<?php
class Accounts{

private $helper;

public function __construct($help){
$this->helper = $help;
}

public function readTypes($used = true){
$response = array();   
$stmt = $this->helper->runQuery("SELECT * FROM account_types WHERE USE_ACC = $used");
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
extract($row);
$results = array(
"account_type_id" => $this->helper->encryptID($ACC_TYPE_ID),
"account_type_name" => $ACC_TYPE_NAME
);
array_push($response, $results);
}
return $response;
}


public function readTypesById($accountid, $used = true){
    $response = array();   
    $stmt = $this->helper->runQuery("SELECT * FROM account_types WHERE 	ACC_TYPE_ID = :uaccid AND USE_ACC = :uused");
    $stmt->bindParam(":uaccid", $accountid);
    $stmt->bindParam(":uused", $used);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $results = array(
    "account_type_id" => $this->helper->encryptID($ACC_TYPE_ID),
    "account_type_name" => $ACC_TYPE_NAME
    );
    array_push($response, $results);
    }
    return $response;
    }

    public function verifyAccount($userid, $used){  
        $used = ($used == "true") ? true: false;
        if($used){
        $stmt = $this->helper->runQuery("UPDATE accounts SET IS_VERIFIED=true WHERE ACC_ID = :uuserid");
        $stmt->bindParam(":uuserid", $userid);
        if($stmt->execute()){
        return true;
        }else{
        return false;
        }
    }else{
        $stmt = $this->helper->runQuery("UPDATE accounts SET IS_VERIFIED=false WHERE ACC_ID = :uuserid");
        $stmt->bindParam(":uuserid", $userid);
        if($stmt->execute()){
        return true;
        }else{
        return false;
        }
    }
    echo $userid;
    }

public function accountExists($user_email, $phone_no){
$stmt = $this->helper->runQuery("SELECT * FROM accounts WHERE 
USER_EMAIL = :uemail OR PHONE_NO = :uphoneno");
$stmt->bindParam(":uemail", $user_email);
$stmt->bindParam(":uphoneno", $phone_no);
$stmt->execute();
$count = $stmt->rowCount();
if($count == 0){
return false;
}else{
return true;
}
}

public function getAdminDetails($admin_id, $config, $lang){
    $stmt = $this->helper->runQuery("SELECT * FROM adminstrator WHERE
    ADMIN_ID = :uadminid");
    $stmt->bindParam(":uadminid", $admin_id);
    $stmt->execute();
    $counter = $stmt->rowCount();
    if($counter == 1){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    return array(
    "ADMIN_ID" =>  $this->helper->encryptID($row["ADMIN_ID"]),
    "FIRST_NAME" => $row["FIRST_NAME"],
    "SECOND_NAME" => $row["SECOND_NAME"],
    "USER_NAME" => $row["USER_NAME"],
    "ADMIN_EMAIL" => $row["ADMIN_EMAIL"]
    );
    }else{
    http_response_code(400);
    return array(
    "message" => $lang["GET_USER_ERROR_FIELDS"],
    "status" => "failed"
    );
    }
}


public function getAllUsers($config, $lang){
    $response = array();
    $stmt = $this->helper->runQuery("SELECT * FROM accounts");
    $stmt->execute();
    $counter = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $type = $this->readTypeByID($row["ACC_TYPE_ID"]);
    http_response_code(200);
    $user = array(
    "ACC_ID" => $this->helper->encryptID($row["ACC_ID"]),
    "FIRST_NAME" =>  $row["FIRST_NAME"],
    "FIRST_NAME" =>  $row["FIRST_NAME"],
    "SECOND_NAME" => $row["SECOND_NAME"],
    "USER_EMAIL" => $row["USER_EMAIL"],
    "BASE_CURRENCY_ID" => $this->helper->encryptID($row["BASE_CURRENCY_ID"]),
    "COUNTRY_ID" => $this->helper->encryptID($row["COUNTRY_ID"]),
    "PHONE_NO" => $row["PHONE_NO"],
    "USER_ADDRESS" => $row["USER_ADDRESS"],
    "BUSSINESS_NAME" => $row["BUSSINESS_NAME"],
    "NIN_NO" => $row["NIN_NO"],
    "PROFILE_PICTURE" => $this->helper->getHostURL().$config->PROFILE_PHOTO_FOLDER.$row["PROFILE_PICTURE"],
    "COVER_PHOTO" => $this->helper->getHostURL().$config->COVER_PHOTO_FOLDER.$row["COVER_PHOTO"],
    "REG_DATE" => $row["REG_DATE"],
    "USER_IP" => $row["USER_IP"],
    "TYPE_NAME" => $type["ACC_TYPE_NAME"],
    "USER_ID" => $this->helper->encryptID($row["ACC_ID"]),
    "ACC_TYPE_ID" => $type,
    "IS_TERMINATED" => $row["IS_TERMINATED"],
    "IS_VERIFIED" => $row["IS_VERIFIED"],
    "IS_EMAIL_VALIDATED" => $row["IS_EMAIL_VALIDATED"],
    "message" => "",
    "status" => "success"
    );
    array_push($response, $user);
    }
return $response;
}

public function getUserDetails($userid, $config, $lang){
    // $userid = (int)$userid;
    $stmt = $this->helper->runQuery("SELECT * FROM accounts WHERE
    ACC_ID = :uuserid");
    $stmt->bindParam(":uuserid", $userid);
    $stmt->execute();
    $counter = $stmt->rowCount();
    if($counter > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $type = $this->readTypeByID($row["ACC_TYPE_ID"]);
    http_response_code(200);
    return array(
        "userid" => $userid,
    "ACC_ID" => $this->helper->encryptID($row["ACC_ID"]),
    "FIRST_NAME" =>  $row["FIRST_NAME"],
    "FIRST_NAME" =>  $row["FIRST_NAME"],
    "SECOND_NAME" => $row["SECOND_NAME"],
    "USER_EMAIL" => $row["USER_EMAIL"],
    "BASE_CURRENCY_ID" => $this->helper->encryptID($row["BASE_CURRENCY_ID"]),
    "COUNTRY_ID" => $this->helper->encryptID($row["COUNTRY_ID"]),
    "PHONE_NO" => $row["PHONE_NO"],
    "USER_ADDRESS" => $row["USER_ADDRESS"],
    "BUSSINESS_NAME" => $row["BUSSINESS_NAME"],
    "ID_FRONT_PIC" => $row["ID_FRONT_PIC"],
    "ID_BACK_PIC" => $row["ID_BACK_PIC"],
    "NIN_NO" => $row["NIN_NO"],
    "PROFILE_PICTURE" => $this->helper->getHostURL().$config->PROFILE_PHOTO_FOLDER.$row["PROFILE_PICTURE"],
    "COVER_PHOTO" => $this->helper->getHostURL().$config->COVER_PHOTO_FOLDER.$row["COVER_PHOTO"],
    "REG_DATE" => $row["REG_DATE"],
    "USER_IP" => $row["USER_IP"],
    "TYPE_NAME" => $type["ACC_TYPE_NAME"],
    "USER_ID" => $this->helper->encryptID($row["ACC_ID"]),
    "ACC_TYPE_ID" => $type,
    "IS_TERMINATED" => $row["IS_TERMINATED"],
    "IS_VERIFIED" => $row["IS_VERIFIED"],
    "IS_EMAIL_VALIDATED" => $row["IS_EMAIL_VALIDATED"],
    "message" => "",
    "status" => "success"
    );
    }else{
    http_response_code(400);
    return array(
    "id" => $userid,
    "message" => $lang["GET_USER_ERROR_FIELDS"],
    "status" => "failed"
    );
    }
}

public function isBussinessnameExist($bussinessname, $config, $lang){
    $stmt = $this->helper->runQuery("SELECT * FROM accounts WHERE 
    BUSSINESS_NAME = :ubname");
    $stmt->bindParam(":ubname", $bussinessname);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($count == 0){
    return false;
    }else{
    return true;
    }
}

public function registerUser(
$first_name,
$second_name,
$user_email,
$country_id,
$phone_no,
$user_address,
$user_password,
$account_type_id,
$base_currency_id,
$bussiness_name,
$nin_no,
$id_front_pic,
$id_back_pic,
$cover_photo,
$profile_picture,
$lang 
){
$user_password = $this->helper->passwordEncrypt($user_password);
$user_ip = $this->helper->getUserIp();
if(!$this->accountExists($user_email, $phone_no)){
$stmt = $this->helper->runQuery("
INSERT INTO accounts SET
FIRST_NAME = :ufname,
SECOND_NAME = :usname,
USER_EMAIL = :uemail,
BASE_CURRENCY_ID = :ubaseid,
COUNTRY_ID = :ucountryid,
PHONE_NO = :uphoneno,
USER_ADDRESS = :uuseradress,
BUSSINESS_NAME = :ubname,
USER_PASSWORD = :upass,
NIN_NO = :unino,
ID_FRONT_PIC = :uidfront,
ID_BACK_PIC = :uidback,
PROFILE_PICTURE = :upropic,
COVER_PHOTO = :ucover,
REG_DATE = NOW(),
USER_IP = :uip,
ACC_TYPE_ID = :uaccid,
IS_TERMINATED = false,
IS_VERIFIED = false,
IS_EMAIL_VALIDATED = false
");
$stmt->bindParam(":ufname", $first_name);
$stmt->bindParam(":usname", $second_name);
$stmt->bindParam(":uemail", $user_email);
$stmt->bindParam(":ucountryid", $country_id);
$stmt->bindParam(":uphoneno", $phone_no);
$stmt->bindParam(":uuseradress", $user_address);
$stmt->bindParam(":upass", $user_password);
$stmt->bindParam(":uaccid", $account_type_id);
$stmt->bindParam(":ubaseid", $base_currency_id);
$stmt->bindParam(":ubname", $bussiness_name);
$stmt->bindParam(":unino", $nin_no);
$stmt->bindParam(":uidfront", $id_front_pic);
$stmt->bindParam(":uidback", $id_back_pic);
$stmt->bindParam(":ucover", $cover_photo);
$stmt->bindParam(":upropic", $profile_picture);
$stmt->bindParam(":uip", $user_ip);
if($stmt->execute()){
http_response_code(201);
return array(
"message" => $lang["REG_SUCCESSFUL_MSG"],
"status" => "success"
);
}else{
http_response_code(503);
return array(
    "message" => $lang["REG_SERVICE_ERROR"],
    "status" => "failed"
);
}
}else{
http_response_code(400);
return array(
        "message" => $lang["REG_ACC_EXISTS_ERROR"],
        "status" => "failed"
);    
}
}
public function readTypeByID($typeid){
$stmt = $this->helper->runQuery("SELECT * FROM account_types WHERE ACC_TYPE_ID = :utypeid");
$stmt->bindParam(":utypeid", $typeid);
$stmt->execute(); 
$row = $stmt->fetch(PDO::FETCH_ASSOC);
return $row;
}
    
public function isAdminLogin($email, $password, $lang){
$email = $this->helper->clean($email);
$password = $this->helper->passwordEncrypt($this->helper->clean($password));
$stmt = $this->helper->runQuery("SELECT * FROM adminstrator WHERE
 USER_NAME = :uemail OR ADMIN_EMAIL = :uemail AND ADMIN_PASSWORD = :upassword");
$stmt->bindParam(":uemail", $email);
$stmt->bindParam(":upassword", $password);
$stmt->execute();
$counter = $stmt->rowCount();
if($counter == 1){
$row = $stmt->fetch(PDO::FETCH_ASSOC);
return array(
"user_id" => $this->helper->encryptID($row["ADMIN_ID"]),
"account_type" => "admin",
"message" => "",
"status" => "success"
);
}else{
return array(
"message" => $lang["LOGIN_ERROR_FIELDS"],
"status" => "failed"
);
}
}

public function isAccountTerminated($email, $password){ 
$stmt1 = $this->helper->runQuery("SELECT * FROM accounts WHERE
USER_EMAIL = :uemail AND USER_PASSWORD = :upassword AND IS_TERMINATED = true");
$stmt1->bindParam(":uemail", $email);
$stmt1->bindParam(":upassword", $password);
$stmt1->execute();
$counter1 = $stmt1->rowCount();
if($counter1  == 1){
return true;
}else{
return false;
}
}

public function isAccountVerified($email, $password){ 
    $stmt1 = $this->helper->runQuery("SELECT * FROM accounts WHERE
    USER_EMAIL = :uemail AND USER_PASSWORD = :upassword AND IS_VERIFIED = true");
    $stmt1->bindParam(":uemail", $email);
    $stmt1->bindParam(":upassword", $password);
    $stmt1->execute();
    $counter1 = $stmt1->rowCount();
    if($counter1  == 1){
    return true;
    }else{
    return false;
    }
    }

public function isUserLogin($email, $password, $lang){
    $email = $this->helper->clean($email);
    $password = $this->helper->passwordEncrypt($this->helper->clean($password));
    $stmt = $this->helper->runQuery("SELECT * FROM accounts WHERE
    USER_EMAIL = :uemail AND USER_PASSWORD = :upassword");
    $stmt->bindParam(":uemail", $email);
    $stmt->bindParam(":upassword", $password);
    $stmt->execute();
    $counter = $stmt->rowCount();
    if($counter == 1){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $type = $this->readTypeByID($row["ACC_TYPE_ID"]);
    return array(
    "type_name" => $type["ACC_TYPE_NAME"],
    "user_id" => $this->helper->encryptID($row["ACC_ID"]),
    "type_id" => $this->helper->encryptID($type["ACC_TYPE_ID"]),
    "message" => "",
    "status" => "success"
    );
    }else{
    return array(
    "message" => $lang["LOGIN_ERROR_FIELDS"],
    "status" => "failed"
    );
    }
    }

}
?>