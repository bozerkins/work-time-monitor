<?php

require_once 'bootstrap.php';

header('Content-Type: application/json');

$event = array_key_exists('event', $_REQUEST) ? $_REQUEST['event'] : null;

if (!$event) {
    echo json_encode(array('status' => false, 'result' => 'no event received'));
    return;
}

if ($event === 'session_start') {
    $session = $connection->query("SELECT * FROM work_session WHERE time_end IS NULL LIMIT 1")->fetch();
    if ($session) {
        echo json_encode(array('status' => false, 'result' => 'there is currently active session'));
        return;
    }
    $record = array();
    $record['time_start'] = time();
    $record['work_date'] = date('Y-m-d');
    $connection->exec("
        INSERT INTO work_session (time_start, work_date) VALUES (
          {$record['time_start']}, '{$record['work_date']}'
        )
    ");
    echo json_encode(array('status' => true, 'result' => 'session started'));
    return;
}
if ($event === 'session_stop') {
    $session = $connection->query("SELECT * FROM work_session WHERE time_end IS NULL LIMIT 1")->fetch();
    if (!$session) {
        echo json_encode(array('status' => false, 'result' => 'no active session found'));
        return;
    }
    $record = array();
    $record['time_end'] = time();
    // close all events
    $connection->exec("
        UPDATE work_event
        SET event_time_end = {$record['time_end']}
        WHERE event_time_end IS NULL AND session_id = {$session['id']};
    ");
    // close session
    $connection->exec("
        UPDATE work_session
        SET time_end = {$record['time_end']}
        WHERE time_end IS NULL;
    ");
    echo json_encode(array('status' => true, 'result' => 'session ended'));
    return;
}
if ($event === 'event_start') {
    $session = $connection->query("SELECT * FROM work_session WHERE time_end IS NULL LIMIT 1")->fetch();
    if (!$session) {
        echo json_encode(array('status' => false, 'result' => 'no active session found'));
        return;
    }
    $record = array();
    $record['event_name'] = array_key_exists('event_name', $_REQUEST) ? $connection->quote($_REQUEST['event_name']) : null;
    $record['event_time_start'] = time();
    if (!$record['event_name']) {
        echo json_encode(array('status' => false, 'result' => 'no event name provided'));
        return;
    }
    // close all active events
    $connection->exec("
        UPDATE work_event
        SET event_time_end = {$record['event_time_start']}
        WHERE event_time_end IS NULL AND session_id = {$session['id']};
    ");
    // create new event
    $connection->exec("
        INSERT INTO work_event (session_id, event_name, event_time_start) VALUES (
          {$session['id']}, {$record['event_name']}, {$record['event_time_start']}
        )
    ");
    echo json_encode(array('status' => true, 'result' => 'event created'));
    return;
}
if ($event === 'get_current_session') {
    $sessionId = array_key_exists('session_id', $_REQUEST) ? intval($_REQUEST['session_id']) : null;
    $session = null;
    if (!$sessionId) {
        $session = $connection->query("SELECT * FROM work_session WHERE time_end IS NULL LIMIT 1")->fetch();
    } else {
        $session = $connection->query("SELECT * FROM work_session WHERE id = {$sessionId} LIMIT 1")->fetch();
    }

    if (!$session) {
        echo json_encode(array('status' => true, 'result' => null));
        return;
    }
    $session['time_start'] = date('Y-m-d H:i', $session['time_start']);
    $session['summary'] = array();
    $session['summary']['work'] = $connection->query("
        SELECT SUM(CASE  WHEN event_time_end is null THEN strftime('%s', 'now') ELSE event_time_end END - event_time_start) as seconds 
        FROM work_event 
        WHERE session_id = {$session['id']} AND event_name LIKE '%work%'
    ")->fetchColumn(0);
    $session['summary']['work'] = secondsToHumanReadable($session['summary']['work']);
    $session['summary']['break'] = $connection->query("
        SELECT SUM(CASE  WHEN event_time_end is null THEN strftime('%s', 'now') ELSE event_time_end END - event_time_start) as seconds 
        FROM work_event 
        WHERE session_id = {$session['id']} AND event_name LIKE '%break%'
    ")->fetchColumn(0);
    $session['summary']['break'] = secondsToHumanReadable($session['summary']['break']);
    $session['summary']['total'] = $connection->query("
        SELECT SUM(CASE  WHEN event_time_end is null THEN strftime('%s', 'now') ELSE event_time_end END - event_time_start) as seconds 
        FROM work_event 
        WHERE session_id = {$session['id']}
    ")->fetchColumn(0);
    $session['summary']['total'] = secondsToHumanReadable($session['summary']['total']);
    $session['events'] = array_map(function($event) {
        $event['event_time_total'] = secondsToHumanReadable(($event['event_time_end'] ?: time()) - $event['event_time_start']);
        $event['event_time_start'] = date('H:i', $event['event_time_start']);
        if ($event['event_time_end']) {
            $event['event_time_end'] = date('H:i', $event['event_time_end']);
        }
        return $event;
    }, $connection->query("SELECT * FROM work_event WHERE session_id = {$session['id']}")->fetchAll());
    echo json_encode(array('status' => true, 'result' => $session));
    return;
}
if ($event === 'get_session_list') {
    $sessions = $connection->query("SELECT * FROM work_session WHERE time_end IS NOT NULL")->fetchAll();
    echo json_encode(array('status' => true, 'result' => $sessions));
    return;
}