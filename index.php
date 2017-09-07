<?php
$cwd = getcwd();
require_once($cwd . '/setup.php');

// データベースへの参照
$attendanceDb = new AttendanceDb();
$departments = $attendanceDb->getDepartments();
$members = $attendanceDb->getMembers();
$types = $attendanceDb->getTypes();
$histories = $attendanceDb->getHistories();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/index.js"></script>
    <link rel="stylesheet" href="css/index.css">
    <meta charset="utf-8">
    <title> 勤怠管理 </title>
</head>
<body>
    <div id="container">
        <div class="clearfix">
            <div id="head-menu-left"><a href="search-or-export-histories.php">勤怠履歴の検索／CSVの出力</a></div>
            <div id="head-menu-center"></div>
            <div id="head-menu-right">データベースの管理</div>
        </div>

        <form action="" id="attendance-form">
            <!-- 送信内容入力部 -->
            <div id="input-part">
            <div id="input-part-top-stair">
                部署：
                <select name="department_id">
                    <?php foreach ($departments as $department) : ?>
                        <option value="<?= h($department->id);?>"><?= h($department->name);?></option>
                    <?php endforeach; ?>
                </select>
                名前：
                <select name="member_id">
                    <?php foreach ($members as $member) : ?>
                        <option value="<?= h($member->id);?>"><?= h($member->name);?></option>
                    <?php endforeach; ?>
                </select>
                内容：
                <select name="type_id">
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= h($type->id);?>"><?= h($type->name);?></option>
                    <?php endforeach; ?>
                </select>
                対象日：
                <input type="date" name="apply_date" value="<?php echo date('Y-m-d');?>"/>
                出社時間:
                <input type="time" name="arrival_time" value="10:00" readonly/>
                退社時間:
                <input type="time" name="leaving_time" value="19:00" readonly/>
            </div>
            <div>
                コメント：
                <textarea rows="3" cols="120" placeholder="理由などを入力してください" name="comment"></textarea>
            </div>
            <div>
                上長確認済みならチェック
                <!-- 下の行は未チェック時disable扱いされSUBMITで送信されないのを防ぐため -->
                <input type="hidden"   name="superior_checked" value="0">
                <input type="checkbox" name="superior_checked"/>
            </div>
            </div>
            <!-- 送信内容入力部 -->

            <div>
                <button type="button" id="confirm-input">送信内容を確認する</button>
            </div>

            <!-- 送信内容確認 -->
            <div>
                <fieldset id="post_content_preview" class="hidden">
                    <legend>送信内容確認</legend>
                    <div><table>
                        <tr>
                            <td class="left-title-column">部署</td><td id="preview-department"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">名前</td><td id="preview-name"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">内容</td><td id="preview-type"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">対象日</td><td id="preview-date"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">出社時間</td><td id="preview-arrival-time"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">退社時間</td><td id="preview-leaving-time"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">コメント</td><td id="preview-comment"></td>
                        </tr>
                        <tr>
                            <td class="left-title-column">上長確認</td><td id="preview-superior-checked"></td>
                        </tr>
                    </table></div>
                    <div id="apply"><input type="submit" id="apply-button" value="送信する" /></div>
                </fieldset>
            </div>
            <!-- 送信内容確認 -->

        </form>
        <!-- 送信履歴 -->
        <table id="histories">
            <?php $history_show_count =0; ?>
            <?php foreach (array_reverse($histories) as $history) :?>
            <tr id="history_<?= h($history->id); ?>">
                <td>
                <hr>
                    <table class="history_box">
                        <tr>
                            <td class="left-title-column">部署</td><td class="history-department-name"><?= h($history->department_name); ?></td>
                        </tr><tr>
                            <td class="left-title-column">名前</td><td class="history-member-name"><?= h($history->member_name); ?></td>
                        </tr><tr>
                            <td class="left-title-column">内容</td><td class="history-type-name"><?= h($history->type_name); ?></td>
                        </tr><tr>
                            <td class="left-title-column">対象日</td><td class="history-apply-date"><?= h($history->apply_date); ?></td>
                        </tr><tr>
                            <td class="left-title-column">出社時間</td><td class="history-arrival-time"><?= h($history->arrival_time); ?></td>
                        </tr><tr>
                            <td class="left-title-column">退社時間</td><td class="history-leaving-time"><?= h($history->leaving_time); ?></td>
                        </tr><tr>
                            <td class="left-title-column">コメント</td><td class="history-comment"><?= h($history->reason); ?></td>
                        </tr><tr>
                            <td class="left-title-column">上長確認</td><td class="history-superior-checked"
                                <?php if($history->superior_checked == 1){echo ">確認済み";} else {echo " class=\"not-checked\">いいえ";}?>
                            </td>
                        </tr>
                    </table>
                    <?php $history_show_count++; ?>
                    <?php if( $history_show_count != 0 and $history_show_count % 10 == 0):?>
                        <div class="history-more">もっとみる</div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach;?>
            <tr id="history_template">
                <td>
                <hr>
                <table class="history_box">
                    <tr>
                        <td class="left-title-column">部署</td><td class="history-department-name"></td>
                    </tr><tr>
                        <td class="left-title-column">名前</td><td class="history-member-name"></td>
                    </tr><tr>
                        <td class="left-title-column">内容</td><td class="history-type-name"></td>
                    </tr><tr>
                        <td class="left-title-column">対象日</td><td class="history-apply-date"></td>
                    </tr><tr>
                        <td class="left-title-column">出社時間</td><td class="history-arrival-time"></td>
                    </tr><tr>
                        <td class="left-title-column">退社時間</td><td class="history-leaving-time"></td>
                    </tr><tr>
                        <td class="left-title-column">コメント</td><td class="history-comment"></td>
                    </tr><tr>
                        <td class="left-title-column">上長確認</td><td class="history-superior-checked"></td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        <!-- 送信履歴 -->
    </div>
</body>
</html>
