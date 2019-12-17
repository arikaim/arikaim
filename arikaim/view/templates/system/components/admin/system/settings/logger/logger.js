arikaim.page.onReady(function() {
    $('.change-option').checkbox({
        onChecked: function() {
            var optionName = $(this).attr('name');
            options.save(optionName,true);
        },
        onUnchecked: function() {
            var optionName = $(this).attr('name');
            options.save(optionName,false);
        }
    });        
});