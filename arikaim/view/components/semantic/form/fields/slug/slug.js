'use strict';

arikaim.component.onLoaded(function(component) {
    var slugSource = $('#slug').attr('slug-source');  
    var editable = $('#slug').attr('editable');
    var disableUpdate = $('#slug').attr('disable-update');
   
    if (disableUpdate == true || disableUpdate == 'true') {
        return;
    }
    var value = $('#' + slugSource).val();
    $('#slug').html(arikaim.text.createSlug(value));  
    
    $('#' + slugSource).keyup(function() {
        var text = $(this).val();   
        var slug = arikaim.text.createSlug(text);
        slug = slug.trim();
        if (editable == '1') {
            $('#slug').val(slug); 
        } else {
            slug = (slug == '') ? '&nbsp;' : slug;            
            $('#slug').html(slug);    
        }
    });
});