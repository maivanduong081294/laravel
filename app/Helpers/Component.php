<?php
function setComponentAttributes($attributes = []) {
    $strAttribute = '';
    if(!empty($attributes)) {
        foreach($attributes as $key=>$value) {
            $strAttribute.= setComponentAttribute($key,$value);
        }
    }
    return $strAttribute;
}

function setComponentAttribute($key,$value=null) {
    $value = trim($value);
    if($value != "" || $value != null) {
        $value = trim($value);
        return " $key=\"$value\"";
    }
    return "";
}
?>