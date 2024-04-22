'use strict';

arikaim.component.onLoaded(function(component) {
    $('.message .close').on('click',function() {
        $(this).closest('.message').transition('fade');
    });
});