<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Attendance_Db.php');

error_reporting(E_ALL);
ini_set( 'error_reporting', E_ALL );

$attendanceDb = new AttendanceDb();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $res = $attendanceDb->post();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    } catch (Exception $e) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error!!', true, 500);
        echo $e->getMessage();
        exit;
    }
}
