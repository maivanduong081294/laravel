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
    if($value && trim($value)) {
        $value = trim($value);
        return " $key=\"$value\"";
    }
    return "";
}
?>