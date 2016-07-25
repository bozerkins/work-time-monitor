<?php

$connectionRoute = __DIR__ . '/var/database.sqlite';
if (!file_exists($connectionRoute) || !is_writable($connectionRoute)) {
    touch($connectionRoute);
}
$connection = new PDO('sqlite:' . $connectionRoute);
$connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$connection->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );


function secondsToHumanReadable($secs)
{
    $secs = $secs - $secs % 60;

    $units = array(
        "week" => 7*24*3600,
        "day"  =>   24*3600,
        "hour" =>      3600,
        "min"  =>        60,
        "sec"  =>         1,
    );
    if ($secs === '0' || $secs === 0) {
        return "0 min";
    }
    $secs = (int)$secs;
    if ($secs == 0) {
        return "-";
    }
    $string = "";
    foreach ($units as $name => $divisor) {
        if ($quot = intval($secs / $divisor)) {
            $string .= $quot . ' ' . $name . ' ';
            $secs -= $quot * $divisor;
        }
    }

    return trim($string);
}