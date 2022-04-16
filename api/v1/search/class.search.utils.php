<?php
class UNTILS{

public function __construct(){   
}

    public function deepSearchQuery($key_word){
        $STRING = "";
        $stringArray = explode(" ",$key_word);
        for($i = 0; $i < count($stringArray); $i++){
        if(strlen($stringArray[$i]) > 1){
        $STRING .= "File_NAME LIKE"."'%$stringArray[$i]%'"." "."OR"." ";
        }
        }
        $query2 = trim($STRING,"OR ");
        $final_query = trim($query2);
        return $final_query;
        }
            
        public function wrapTag($Text){
        return '<b>'.$Text.'</b>';
        }
        
        public function highlightString($myKeyWord, $mixedArray){
        $splitArrays = explode(" ",$myKeyWord);
        $replace = array_map('wrapTag', $splitArrays);
        $myText = str_ireplace($splitArrays, $replace, $mixedArray);
        return $myText;
        }


}
?>