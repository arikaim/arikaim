

arikaim.onPageReady(function() {
    $('#time_zone').dropdown({
        onChange: function(value) {          
            settings.save('time.zone',value);
        }
    });
    $('#date_format').dropdown({
        onChange: function(value) {          
            settings.save('date.format',value);
        }
    });
    $('#time_format').dropdown({
        onChange: function(value) {          
            settings.save('time.format',value);
        }
    });
});