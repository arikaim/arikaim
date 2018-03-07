/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function AlertDialog() {
    
    var params = {};

    this.show = function(parameters) {
        if (isEmpty(parameters) == true) {
            params = {};
        } else {
            params = parameters;
            if (isEmpty(params.title) == false) {
                $('#alert_dialog_title').html(params.title);
            }
            if (isEmpty(params.description) == false) {
                $('#alert_dialog_description').html(params.description);
            }
        }
        $('#alert_dialog').modal('show');
    };
 
    this.getParams = function() {
        return params;
    }
}

var alertDialog = new AlertDialog();