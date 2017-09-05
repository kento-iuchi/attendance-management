$(function(){
    'use strict';

    var $csvfilepath;

    //search from database
    $('#search-option-form').submit(function() {
        var search_form_inputs = $('#search-option-form').serialize();

        console.log(search_form_inputs);

        $.post('_ajax.php', {
            search_conditions: search_form_inputs,
            mode: 'search'
        }, function(res){
            $('.added-search-result').remove();
            $('#num-results').html(res.length);
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
            //ダウンロードリンクを作る
            //resにはcsvファイルのpathが入っている
            $csvfilepath = res;
            $('#csv-download-button').text(res);
            $('#csv-download-button').removeClass("hidden");
        }, "json").fail(function(XMLHttpRequest, textStatus, errorThrown){
            console.log('だめでした');
            console.log("ajax通信に失敗しました");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus     : " + textStatus);
            console.log("errorThrown    : " + errorThrown.message);
        });
        return false;
    });

    $("#csv-download-button").on("click", function(e){
        var $target = $(e.target);
        $target.attr({
            download: "file.csv",
            href:  $csvfilepath
        });
    });
});
