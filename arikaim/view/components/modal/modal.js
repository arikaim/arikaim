/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Modal() { 
    var self = this;
  
    this.alert = function(parameters) {
        return this.confirm(parameters,null,null,'alert_dialog');
    };

    this.confirm = function(parameters, onApprove, onDeny, element) {
        element = getDefaultValue(element,'confirm_dialog');
        parameters = getDefaultValue(parameters,{});

        var deferred = new $.Deferred();

        if (isEmpty(parameters.title) == false) {
            $('#' + element + ' .modal-title').html(parameters.title);
        }
        if (isEmpty(parameters.description) == false) {
            $('#' + element + ' .modal-description').html(parameters.description);
        }
        if (isEmpty(parameters.confirm) == false) {
            $('#confirm_button').html(parameters.confirm);
        }
        if (isEmpty(parameters.icon) == false) {
            $('#' + element + ' .modal-icon').attr('class','icon modal-icon ' + parameters.icon);           
        }
        if (isEmpty(parameters.icon_hide) == false) {
            $('#' + element + ' .modal-icon').hide();
        }
        if (isEmpty(parameters.component) == false) {          
            arikaim.page.loadContent({
                id: element + '_content',
                component: parameters.component.name,
                params: getDefaultValue(parameters.component.params,null)
            });
        }

        $('#' + element).modal({
            onDeny : function() {
                deferred.reject(parameters);  
                callFunction(onDeny,parameters);                
            },
            onApprove : function() {
                deferred.resolve(parameters);      
                callFunction(onApprove,parameters);                                        
            }
        }).modal('show');

        return deferred.promise();
    };

    this.confirmDelete = function(parameters, onApprove, onDeny) {
        return this.confirm(parameters,onApprove,onDeny,'delete_dialog');
    }

    this.init = function() {
        arikaim.component.loadContent('components:modal',function(result) {
            $('body').append(result.html);
        }); 
    };
}

var modal = new Modal();
modal.init();
