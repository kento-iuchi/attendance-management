<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Attendance_Db.php');

$attendanceDb = new AttendanceDb();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $res = $attendanceDb->post();
        header('Content-Type: application/json');
        echo json_encode($res);//複数の値をjsonで返す
        exit;
    } catch (Exception $e) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error!!', true, 500);
        echo $e->getMessage();
        exit;
    }
}
