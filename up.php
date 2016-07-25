<?php

require_once 'bootstrap.php';

$sqls = array();
$sqls[] = "
CREATE TABLE IF NOT EXISTS work_session (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    time_start INTEGER NOT NULL,
    time_end INTEGER, 
    work_date DATE NOT NULL
);
";
$sqls[] = "
CREATE TABLE IF NOT EXISTS work_event (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  session_id INTEGER NOT NULL,
  event_time_start INTEGER NOT NULL,
  event_time_end INTEGER,
  event_name VARCHAR NOT NULL
);
";

foreach($sqls as $sql) {
    $connection->exec($sql);
}