/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ConfirmDialog() {
    var params = {};

    this.show = function(parameters,onApprove,onDeny) {
        if (isEmpty(parameters) == true) {
            params = {};
        } else {
            params = parameters;
            if (isEmpty(params.title) == false) {
                $('.confirm-dialog-title').html(params.title);
            }
            if (isEmpty(params.description) == false) {
                $('.confirm-dialog-description').html(params.description);
            }
        }
        $('#confirm_dialog').modal({
            onDeny : function() {
                callFunction(onDeny,params);                
            },
            onApprove : function() {
                callFunction(onApprove,params);                                        
            }
        }).modal('show');
    };
 
    this.getParams = function() {
        return params;
    };
}

var confirmDialog = new ConfirmDialog();
