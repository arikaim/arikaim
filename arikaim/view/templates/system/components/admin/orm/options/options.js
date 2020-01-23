/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function OrmOptions() {
    var self = this;

    this.update = function(formId, onSuccess, onError) {       
        return arikaim.put('/core/api/orm/options',formId,onSuccess,onError);          
    };
    
    this.initEditForm = function(formId) {
        formId = getDefaultValue(formId,'#options_form');
        arikaim.ui.form.onSubmit(formId,function() {       
            return self.update(formId);
        },function(result) {      
            arikaim.ui.form.showMessage(result.message);
        });
    };
}

var ormOptions = new OrmOptions();
