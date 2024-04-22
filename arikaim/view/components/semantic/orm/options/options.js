/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OrmOptions(saveOptionsUrl, saveOptionUrl) {
    var self = this;
    this.saveOptionsUrl = saveOptionsUrl;
    this.saveOptionUrl = saveOptionUrl;

    this.update = function(formId, onSuccess, onError) {       
        return arikaim.put(this.saveOptionsUrl,formId,onSuccess,onError);          
    };
    
    this.saveOption = function(referenceId, key, value, onSuccess, onError) {  
        var data = {
            id: referenceId,
            key: key,
            value: value
        }     

        return arikaim.put(this.saveOptionUrl,data,onSuccess,onError);          
    };

    this.initCheckboxFields = function() {
        $('.checkbox').checkbox({
            onChecked: function() {
                var field = $(this).closest('.checkbox').find('.option-field');
                field.val(1);  
                var referenceId = field.attr('reference-id');
                var key = field.attr('option-key');
                self.saveOption(referenceId,key,1);      
            },
            onUnchecked: function() {
                var field = $(this).closest('.checkbox').find('.option-field');
                field.val(0);  
                var referenceId = field.attr('reference-id');
                var key = field.attr('option-key');
                self.saveOption(referenceId,key,0);
            }
        });
    };
}
