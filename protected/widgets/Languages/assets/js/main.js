(function(){ 'use strict';

    $(function(){
        $('.languages a').on('click', function(e){
            e.preventDefault();

            var lang = $(this).data('lang');

            if($.inArray(lang, languages) !== -1) {
                $.cookie('lang', lang, {
                    expires: 365,
                    path: '/'
                });

                window.location.reload();
            }
        });
    });

})();
