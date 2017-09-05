<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Attendance_Db.php');

//create reference to database
$attendanceDb = new AttendanceDb();
$members = $attendanceDb->getMembers();
$types = $attendanceDb->getTypes();
$histories = $attendanceDb->getHistories();


?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>勤怠履歴の検索</title>
        <link rel="stylesheet" href="css/search-or-export-histories.css">
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/search-or-export-histories.js"></script>
    </head>
    <body>
        <table>
            <tr><td>
                <fieldset>
                    <legend>検索条件指定</legend>
                    <form action="" id="search-option-form">
                        名前：
                        <select name="member_id">
                                <option value="all_member">指定しない</option>
                            <?php foreach ($members as $member) : ?>
                                <option value="<?= h($member->id);?>"><?= h($member->name);?></option>
                            <?php endforeach; ?>
                        </select>
                        内容：
                        <select name="type_id">
                            <option value="all_type">指定しない</option>
                            <?php foreach ($types as $type) : ?>
                                <option value="<?= h($type->id);?>"><?= h($type->name);?></option>
                            <?php endforeach; ?>
                        </select>
                        <p>
                            <input type="date" name="date_range_first" value="<?php echo date('Y-m-01', strtotime(date('Y-m-1') . '-1 month'));?>"/>から
                            <input type="date" name="date_range_last" value="<?php echo date('Y-m-t', strtotime(date('Y-m-1') . '-1 month'));?>"/>まで
                        </p>
                        <input type="submit" value=" 検索する " />
                    </form>
                </fieldset>
            </td><td>
                <fieldset>
                    <legend>有給取得状況をCSVファイルに出力</legend>
                    <form action="" id="csv-export-form">
                        <p>
                            <input type="date" name="date_range_first" value="<?php echo date('Y-m-01', strtotime(date('Y-m-1') . '-1 month'));?>"/>から
                            <input type="date" name="date_range_last" value="<?php echo date('Y-m-t', strtotime(date('Y-m-1') . '-1 month'));?>"/>まで
                        </p>
                            <input type="submit" value="　出力する　" />
                    </form>
                    <p>
                        <a id="csv-download-button" class="hidden">ダウンロードする</div>
                    </p>
                </fieldset>
            </td>
            </tr>
        </table>
        <table id="search-results">
            <thead>
                <tr><td>
                    ヒット数：
                    <span id="num-results"></span>
                </td></tr>
            </thead>
            <tbody id="results-part">
                <tr id="search-result-template">
                    <td>
                    <hr>
                    <ul>
                        <li class="result-member-name"></li>
                        <li class="result-type-name"></li>
                        <li class="result-apply-date"></li>
                        <li class="result-arrival-time"></li>
                        <li class="result-leaving-time"></li>
                        <li class="result-comment"></li>
                    </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
