$(function(){
    'use strict';

    //入力修正時に確認画面閉じる
    $('#input-part').click(function(){
        $('#apply_content_preview').fadeOut(200);
    });

    //show input preview
    $('#confirm-input').click(function(){
        $('#apply_content_preview').fadeIn(700);
        $('#preview-name').html($('select[name = member_id] :selected').text());
        $('#preview-type').html($('select[name = type_id] :selected').text());
        $('#preview-date').html($('input[name = apply_date]').val());
        $('#preview-time').html($('input[name = arrive_time]').val());
        $('#preview-comment').html($('textarea[name = comment]').val());
    });

    //leave on histories
    $('#attendance-form').submit(function() {
        //フォームの中身を取得
        var form_inputs = $('#attendance-form').serialize();

        $.post('_ajax.php', {
            input_data: form_inputs,
            mode: 'leave'
        }, function(res){
            $('#apply_content_preview').fadeOut(700);
            console.log('mode: [leave] return from _ajax.php : %s',JSON.stringify(res));
            var $tr = $('#history_template').clone();
            $tr.attr('id', 'history_' + res.id)
            .find('.history-member-name')
            .html(res.member_name)
            $tr.find('.history-type-name')
            .html(res.type_name);
            $tr.find('.history-apply-date')
            .html(res.apply_date);
            $tr.find('.history-arrive-time')
            .html(res.arrive_time);
            $tr.find('.history-comment')
            .html(res.comment);
            console.log($tr);
            $('#histories > tbody').prepend($tr);
        });
        return false;
    });

});
