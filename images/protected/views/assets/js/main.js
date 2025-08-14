(function(){

    'use strict';

    var Message, fancyboxSettings;

    Message = function(type, message, title){
        $.notification({timeout: 5000, title: title, content: message, type: type, img: ""});
    };

    fancyboxSettings = {
        padding: 0
    };


    $(function(){

        // Chosen
        if($.isFunction($.fn.chosen)) {
            $('select:not(.not-chosen)').chosen({
                disable_search_threshold: 10
            });
        }

        // Tooltip
        if($.isFunction($.fn.tooltip)) {
            $('[rel=tooltip]').tooltip();
        }

        // jScrollPane
        if($.isFunction($.fn.jScrollPane)) {
            $('.scroll-pane').jScrollPane();
        }

        // Fancybox
        if($.isFunction($.fn.fancybox)) {
            $('.fancybox').fancybox(fancyboxSettings);
        }

    });

})();