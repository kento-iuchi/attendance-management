<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Attendance_Db.php');

//create reference to database
$attendanceDb = new \MyApp\AttendanceDb();
$members = $attendanceDb->getMembers();
$types = $attendanceDb->getTypes();
$histories = $attendanceDb->getHistories();


?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>勤怠履歴の検索</title>
        <link rel="stylesheet" href="css/search-histories.css">
    </head>
    <body>
        <fieldset>
            <legend>検索条件指定</legend>
            <form action="" id="search-option-form">
                名前：
                <select name = "member_id">
                        <option value="all_member">指定しない</option>
                    <?php foreach ($members as $member) : ?>
                        <option value="<?= h($member->id);?>"><?= h($member->name);?></option>
                    <?php endforeach; ?>
                </select>
                内容：
                <select name = "type_id">
                    <option value="all_type">指定しない</option>
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= h($type->id);?>"><?= h($type->name);?></option>
                    <?php endforeach; ?>
                </select>
                <p>
                    <input type="date" name = "date_range_first"/>から
                    <input type="date" name = "date_range-last"/>まで
                </p>
                <input type="submit" value=" 検索する " />
            </form>
        </fieldset>
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/search-histories.js"></script>
    </body>
</html>
