<?php
$cwd = getcwd();
require_once($cwd . '/config.php');
require_once($cwd . '/Attendance_Db.php');

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
