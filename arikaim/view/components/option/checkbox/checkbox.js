$(document).ready(function() {
    $('.option-checkbox').checkbox({
        onChecked: function() {   
            var name = $(this).attr('name');                     
            options.save(name,1);
        },
        onUnchecked: function() {  
            var name = $(this).attr('name');                  
            options.save(name,0);
        }
    });
});