<?php
function getFavicon() {
    return asset('favicon.png');
}
function getLogo() {
    return asset('assets/logo.svg');
}

function listShowItemsNumber($json = false) {
    $values =  [
        5,10,20,30,50
    ];
    if($json) {
        return json_encode($values);
    }
    return $values;
}

function setShowItemsNumber($number) {
    $numbers = listShowItemsNumber();
    $max = max($numbers);
    $min = min($numbers);
    if($number > $max) {
        $number = $max;
    }
    elseif($number < $min) {
        $number = $min;
    }
    return $number;
}

function checkSortingTable($name) {
    $orderBy = request()->orderBy;
    $orderType = request()->orderType;
    $respone = '';
    if($name == $orderBy) {
        $respone = $orderType == strtolower("DESC")?'desc':'asc';
    }
    return $respone;
}