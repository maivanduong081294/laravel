<?php
use App\Models\Media;

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

function getImageSourceById(int $id,string $type='thumbnail') {
    $media = new Media();
    return $media->getImageSourceById($id, $type);
}

function getImageById(int $id,string $type='thumbnail') {
    $media = new Media();
    $detail = $media->getDetailById($id);
    $html = '';
    if($detail) {
        $souce = $media->getImageSourceById($id, $type);
        $html = '<img src="'.$souce.'" alt="'.$detail->name.'"/>';
    }
    return $html;
}

function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   
    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}