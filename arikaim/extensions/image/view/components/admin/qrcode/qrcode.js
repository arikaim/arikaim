'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.addRules("#qr_code_form");

    arikaim.ui.form.onSubmit("#qr_code_form",function() {  
        return arikaim.post('/api/admin/image/qrcode/generate','#qr_code_form',function(result) {
            $('#qrcode_image').attr('src',result.image);    
        });
    });
});