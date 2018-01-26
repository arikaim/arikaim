
arikaim.onPageReady(function() {
    $('#number_format').dropdown({
        onChange: function(value) {          
            settings.save('number.format',value);
        }
    });
});
