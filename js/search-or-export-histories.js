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
                $tr.attr('id', 'result_' + $results_content.id);
                $tr.attr('class', 'added-search-result');
                $tr.find('.result-department-name')
                .html($results_content.department_name)
                $tr.find('.result-member-name')
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

});
