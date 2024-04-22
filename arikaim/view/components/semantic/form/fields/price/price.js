'use strict';
arikaim.component.onLoaded(function(component) {
    arikaim.ui.button('.clear-price',function(element) {
        var priceField = $(element).attr('price-field');
        $('#' + priceField).val("");
        $('#' + priceField).focus();
    });
});