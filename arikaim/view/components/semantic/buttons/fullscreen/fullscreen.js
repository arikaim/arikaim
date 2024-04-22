'use strict';

arikaim.component.onLoaded(function(component) {
    $('.full-screen').on('click',function() {
        var targetId = $(this).attr('iframe-id');
        var el = $('#' + targetId);
        var doc = (isEmpty(el[0]) == true) ? document : el[0];
         
        var fullScreen = doc.requestFullscreen
        || doc.webkitRequestFullScreen
        || doc.mozRequestFullScreen
        || doc.msRequestFullscreen;

        if (isEmpty(fullScreen) == false) {
            fullScreen.call(doc);
        }
    });
});