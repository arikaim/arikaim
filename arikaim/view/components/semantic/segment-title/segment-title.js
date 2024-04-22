'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.close-button',function(element) {
        var contentId = $(element).attr('content-id');
        $('#' + contentId).html('');
        $('#' + contentId).addClass('hidden');
    });
});