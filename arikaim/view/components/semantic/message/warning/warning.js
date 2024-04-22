'use strict';

arikaim.component.onLoaded(function(component) {
    $('.warning .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
});