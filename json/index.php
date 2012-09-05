<?php

require('../php/class.php');

$eq = new earthquake();
//
//$eq->add_epoch();
//die();
$eq->get_earthquakes();
$json = array('feed' => array(
        'count' => count($eq->earthquake_data)
        , 'datetime' => date("Y-m-d h:i:s")
        , 'latest_datetime' => $eq->latest['datetime']
        , 'largest_magnutide' => $eq->largest['magnitude']
        , 'latest' => $eq->latest
        , 'largest' => $eq->largest
        , 'earthquakes' => array_reverse($eq->earthquake_data)
    )
);
echo json_encode($json);

