<?php
class CONFIG{

public $DEFAULT_TIME_ZONE =  "Africa/Kampala";

public $DB_HOST =  "localhost";

public $DB_USER =  "root";

public $DB_PASSWORD =  "";

public $DB_DATABASE = "eazzey_pos";

public $API_URL = "eazzeypos/api/v1/";

/******************************
SETTINGS
 ******************************/

public $DEFAULT_PROFILE_PHOTO  = "default_profile.jpg";

public $DEFAULT_COVER_PHOTO = "default_cover.png";

/******************************
GEOPLUGIN API
 ******************************/
	
//the default language
public $LANG = 'en';

//user ip address
public $USER_IP;

//custom ip
PUBLIC $EG_IP = "102.84.26.92";

/******************************
ENCRYPTION CONFIGURATION
 ******************************/

public $ENCRYPTION_METHOD = "AES-256-CBC";//DON'T CHANGE THIS VALUE IF YOU DON'T KNOW WHAT YOUR DOING

public $SECRET_KEY = "KZP-Q2G-H67-RTP-QG4-ZPQ";

public $SECRET_PASSWORD = "4RT56D87S67GHG90F";


public $LANGUAGES_FOLDER = "../config/languages/lang/";

/******************************
PRODUCT CONFIGURATION
 ******************************/
public $RECORDS_PER_PAGE = 10;

public function __construct(){
$this->USER_IP = $_SERVER['REMOTE_ADDR'];
}

public function getUserIp(){
return $this->USER_IP;   
}

}
?>