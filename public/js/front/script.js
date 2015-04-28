(function($) {
    $.fn.pinkStreaming = function(options) {
        var settings = $.extend({
            idAttr: 'data-id',
            complete: null
        }, options);

        return this.each(function() {            
            $(this).click(function() {
                console.log($(this).attr(settings.idAttr));
            });

            if ($.isFunction(settings.complete)) {
                settings.complete.call(this);
            }
        });
    }
})(jQuery)