<?php

require_once("../config/config.php");
require_once("../accounts/class.accounts.php");
class MESSAGER{

    private $helper;

    private $config;

    private $accounts;

    public function __construct($help){
    $this->helper = $help;
    $this->config = new CONFIG();
    $this->accounts = new Accounts($help);
    }


    public function sendMSG($order_id, $user1, $user2, $message){
        $stmt = $this->helper->runQuery("
        INSERT INTO messages SET
        order_id = :uorderid,
        user1 = :uuser1id,
        user2 = :uuser2id,
        message = :umsg,
        msg_read = false");
        $stmt->bindParam(":uorderid", $order_id);
        $stmt->bindParam(":uuser1id", $user1);
        $stmt->bindParam(":uuser2id", $user2);
        $stmt->bindParam(":umsg", $message);
        if($stmt->execute()){
        return true;
        }else{
        return false;
        }    
    }


    public function getMessages($order_id, $user1_id, $user2_id, $lang){
        $response = array();
        $stmt = $this->helper->runQuery("
        SELECT * FROM messages WHERE
        ((user1=:uuser1id AND user2=:uuser2id)
        OR
        (user1=:uuser2id AND user2=:uuser1id)) AND order_id = :uorderid");
        $stmt->bindParam(":uorderid", $order_id);
        $stmt->bindParam(":uuser1id", $user1_id);
        $stmt->bindParam(":uuser2id", $user2_id);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        if($user1 == $user1_id){
        $message = array(
         "msgid" => $this->helper->encryptID($msgid),
         "owner" => "me",
         "user" => $this->accounts->getUserDetails($user1, $this->config, $lang),
         "message" => $message,
         "msg_datetime" => $msg_datetime,
         "msg_read" => $msg_read
        );
        array_push($response, $message);
        }else{
            $message = array(
                "msgid" => $this->helper->encryptID($msgid),
                "owner" => "them",
                "user" => $this->accounts->getUserDetails($user2, $this->config, $lang),
                "message" => $message,
                "msg_datetime" => $msg_datetime,
                "msg_read" => $msg_read
               );
              
         array_push($response, $message);

        }
        
        }
        return $response;

    }

    
}
?>