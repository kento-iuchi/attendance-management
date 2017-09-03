$(function(){
    'use strict';

    //休みを選んだら時間をインアクティブに
    $('select[name = type_id]').change(function(){
        var $selected_type = $('option:selected',this);
        if($selected_type.text() != '休み'){
            $('input[name = arrival_time]').prop('readonly', false);
            $('input[name = leaving_time]').prop('readonly', false);
        }else {
            $('input[name = arrival_time]').prop('readonly', true);
            $('input[name = leaving_time]').prop('readonly', true);
        }
    });

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
        $('#preview-arrival-time').html($('input[name = arrival_time]').val());
        $('#preview-leaving-time').html($('input[name = leaving_time]').val());
        $('#preview-comment').html($('textarea[name = comment]').val());
        if($('input[name = superior_checked]').prop('checked')){
                $('#preview-superior-checked').text("確認済み");
            }else {
                $('#preview-superior-checked').text("いいえ");
            }
    });

    //leave on histories
    $('#attendance-form').submit(function() {
        //フォームの中身を取得
        var form_inputs = $('#attendance-form').serialize();
        console.log(form_inputs);
        $.post('_ajax.php', {
            input_data: form_inputs,
            mode: 'leave'
        }, function(res){
            $('#apply_content_preview').fadeOut(700);
            console.log('mode: [leave] return from _ajax.php : %s',JSON.stringify(res));

            if(res.superior_checked == 1){
                res.superior_checked = "確認済み";
            }
            else{
                res.superior_checked = "いいえ";
            }
            var $tr = $('#history_template').clone();
            $tr.attr('id', 'history_' + res.id)
            .find('.history-member-name')
            .html(res.member_name)
            $tr.find('.history-type-name')
            .html(res.type_name);
            $tr.find('.history-apply-date')
            .html(res.apply_date);
            $tr.find('.history-arrival-time')
            .html(res.arrival_time);
            $tr.find('.history-leaving-time')
            .html(res.leaving_time);
            $tr.find('.history-comment')
            .html(res.comment);
            $tr.find('.history-superior-checked')
            .html(res.superior_checked);
            console.log($tr);
            $('#histories > tbody').prepend($tr);
        });
        return false;
    });

});
