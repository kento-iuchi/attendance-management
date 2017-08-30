$(function(){
    'use strict';

    //search from database
    $('#search-option-form').submit(function() {
        var search_form_inputs = $('#search-option-form').serialize();

        $.post('_ajax.php', {
            search_conditions: search_form_inputs,
            mode: 'search'
        }, function(res){

        });
        return false;
});
