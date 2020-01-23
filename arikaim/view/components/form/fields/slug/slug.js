$(document).ready(function() {
    var slugSource = $('#slug').attr('slug-source');
    var value = $('#' + slugSource).val();
    $('#slug').html(arikaim.text.createSlug(value));  

    $('#' + slugSource).keyup(function() {
        var slug = arikaim.text.createSlug($(this).val());
        if (slug.trim() == '') {
            slug = '&nbsp;';
        }
        $('#slug').html(slug);      
    });
});