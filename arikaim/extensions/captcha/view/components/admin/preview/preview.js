$(document).ready(function() {
    $('#drivers_dropdown').dropdown({
        onChange: function(name) {              
            captcha.loadCaptcha(name,'preview_captcha');
        }
    });   
});