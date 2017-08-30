<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/Attendance_Db.php');

//create reference to database
$attendanceDb = new \MyApp\AttendanceDb();
$members = $attendanceDb->getMembers();
$types = $attendanceDb->getTypes();
$histories = $attendanceDb->getHistories();

//var_dump($members);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <a href="search-histories.php">勤怠履歴を検索する</a>
    <div id="container">
        <form action="" id="attendance-form">
            <div id = "input-part">
            <p>
                名前：
                <select name = "member_id">
                    <?php foreach ($members as $member) : ?>
                        <option value="<?= h($member->id);?>"><?= h($member->name);?></option>
                    <?php endforeach; ?>
                </select>
                内容：
                <select name = "type_id">
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= h($type->id);?>"><?= h($type->name);?></option>
                    <?php endforeach; ?>
                </select>
                申請したい日：
                <input type="date" name = "apply_date"/>
                出社時間:
                <input type="time" name = "arrive_time"/>
            </p>
            <p>
                コメント：
                <textarea rows="3" cols="120" placeholder="理由などを入力してください" name = "comment"></textarea>
            </p>
            </div>
            <p>
                <button type="button" id="confirm-input">申請内容を確認する</button>
            </p>
            <p>
                <fieldset id="apply_content_preview" class="hidden">
                    <legend>申請内容確認</legend>
                    <p><table>
                        <tr>
                            <td>名前</td><td id="preview-name"></td>
                        </tr>
                        <tr>
                            <td>内容</td><td id="preview-type"></td>
                        </tr>
                        <tr>
                            <td>申請したい日</td><td id="preview-date"></td>
                        </tr>
                        <tr>
                            <td>出社時間</td><td id="preview-time"></td>
                        </tr>
                        <tr>
                            <td>コメント</td><td id="preview-comment"></td>
                        </tr>
                    </table></p>
                    <input type="submit" id="apply-button" value="申請する" />
                </fieldset>
            </p>
        </form>
        <table id="histories">
            <?php foreach (array_reverse($histories) as $history) :?>
            <tr id = "history_<?= h($history->id); ?>" data-id = "history_<?= h($history->id); ?>">
                <td>
                    <ul class = "history_box">
                        <li class = "history-member-name"><?= h($history->member_name); ?></li>
                        <li class = "history-type-name"><?= h($history->type_name); ?></li>
                        <li class = "history_apply_date"><?= h($history->apply_date); ?></li>
                        <li class = "history-arrive_time"><?= h($history->arrive_time); ?></li>
                        <li class = "history-comment"><?= h($history->reason); ?></li>
                    </ul>
                </td>
            </tr>
            <?php endforeach;?>
            <tr id="history_template">
                <td>
                <hr>
                <ul class = "history_box1">
                    <li class = "history-member-name"></li>
                    <li class = "history-type-name"></li>
                    <li class = "history-apply-date"></li>
                    <li class = "history-arrive-time"></li>
                    <li class = "history-comment"></li>
                </ul>
                </td>
            </tr>
        </table>
    </div>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
