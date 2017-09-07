<?php
$cwd = getcwd();
require_once($cwd . '/setup.php');

// データベースへの参照
$attDb = new AttendanceDb();

$date_range_first = $_POST['date_range_first'];
$date_range_last = $_POST['date_range_last'];

//指定した期間の有給情報をDBに問い合わせ、得られた情報をまとめたCSVのパスを得る。
$csv_filepath = $attDb->_exportHistoriesToCsv($date_range_first,  $date_range_last);


$csv_filesize = filesize($csv_filepath);
$read_csv = file_get_contents($csv_filepath);
header("Content-disposition: attachment; filename=$csv_filepath");
header("Content-Length:$csv_filesize");
header("Content-type: application/octet-stream; name=$csv_filepath");

readfile($csv_filepath);
unlink($csv_filepath);
