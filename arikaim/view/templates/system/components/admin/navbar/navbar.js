$(document).ready(function() {
    $('#language_dropdown').dropdown({
        onChange: function(value) {               
            arikaim.setLanguage(value);
        }
    });
});