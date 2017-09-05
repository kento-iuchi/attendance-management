<?php
//パス
$fpath = './a.jpg';
//ファイル名
$fname = '';

header('Content-Type: application/force-download');
header('Content-Length: '.filesize($fpath));
header('Content-disposition: attachment; filename="'.$fname.'"');
readfile($fpath);
