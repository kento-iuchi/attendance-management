$(function(){
    'use strict';

    //データベースから検索　＆　表示
    $('#search-option-form').submit(function() {

        var search_form_inputs = $('#search-option-form').serialize();

        $.post('_ajax.php', {
            search_conditions: search_form_inputs,
            mode: 'search'
        }, function(res){
            $('.added-search-result').remove();
            $('#num-results').html(res.length);

            //検索結果を表示する要素の作成
            $.each(res, function($results_id, $results_content){
                console.log($results_content);
                var $tr = $('#search-result-template').clone();
                $tr.attr('id', 'result_' + $results_content.id)
                $tr.attr('class', 'added-search-result')
                .find('.result-member-name')
                .html($results_content.member_name)
                $tr.find('.result-type-name')
                .html($results_content.type_name);
                $tr.find('.result-apply-date')
                .html($results_content.apply_date);
                $tr.find('.result-arrival-time')
                .html($results_content.arrival_time);
                $tr.find('.result-leaving-time')
                .html($results_content.leaving_time);
                $tr.find('.result-comment')
                .html($results_content.reason);
                console.log($tr);
                $('#search-results > tbody').append($tr);
            });

        });
        return false;
    });


    //指定期間の全休、半休、半半休をCSVに出力
    $('#csv-export-form').submit(function() {
        console.log("通信開始");
        $.post('_ajax.php', {
            export_conditions: $('#csv-export-form').serialize(),
            mode: 'export',
        }, function(res){
            //ダウンロードリンクを作る 本当はリンクを作らず即ダウンロードさせたい
            $('#csv-download-button').removeClass("hidden");
            //resにはcsvファイルのpathが入っている
            var $csvfilepath = res;
            var $downloadlink = $('#csv-download-button');
            var $csvfilename = $csvfilepath.split('/');
            $csvfilename = $csvfilename[$csvfilename.length -1];// スラッシュ区切りの最後がファイル名
            $csvfilename = $csvfilename.split('$')[0] + '.csv';//$以下のランダム文字列を消す

            $downloadlink.attr({
                download: $csvfilename,
                href: $csvfilepath
            });

        }, "json").fail(function(XMLHttpRequest, textStatus, errorThrown){
            console.log("ajax通信に失敗しました");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus     : " + textStatus);
            console.log("errorThrown    : " + errorThrown.message);
        });
        return false;
    });

});
