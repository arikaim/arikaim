/**
 *  Arikaim
 *  Progress bar component
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ProgressBar() {

    var progress_element_id = "#progress"; 
    var self = this;
    var doIncrement = null;
    var default_interval = 20;
    var label = "";
    var on_complete = null;
    var on_before_complete = null;
    var on_before_complete_value = 95;
    var on_error = null;

    this.hide = function() {
        this.reset();
        $(progress_element_id).addClass('hidden');     
        $(progress_element_id).hide();
    };

    this.show = function() {
        $(progress_element_id).removeClass('hidden');        
        $(progress_element_id).show();
    };

    this.start = function(options) {
        on_complete = getValue('onComplete',options,null);
        on_before_complete = getValue('onBeforeComplete',options,null);
        var interval = getValue('interval',options,default_interval);

        this.reset();              
        if (isEmpty(interval) == true) {
            var interval = default_interval; 
        }
        doIncrement = setInterval(this.increment,interval);
    };

    this.increment = function() {
        var val = $(progress_element_id).progress('get value');
        if (isFunction(on_before_complete) == true) {
            if (val >= on_before_complete_value) {
                var result = on_before_complete();
                if (result === null) {
                    return;
                }
                if (result === false) {
                    // show error
                    clearInterval(doIncrement);
                    callFunction(on_error);                    
                    return;
                }
            }
        }

        $(progress_element_id).progress('increment'); 
        var is_complete = $(progress_element_id).progress('is complete');
       
        if (is_complete == true) { 
            clearInterval(doIncrement);
            callFunction(on_complete);               
        }
    };

    this.reset = function() {
        clearInterval(doIncrement);
        $(progress_element_id).progress('reset');
    };

    this.init = function() {
        $('.ui.progress').progress({
            duration : 200,
            total    : 100
        });
    };
}

var progressBar = new ProgressBar();
