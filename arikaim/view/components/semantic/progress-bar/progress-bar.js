/**
 *  Arikaim
 *  Progress bar component
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ProgressBar() {
    var self = this;

    var selector = "#progress";       
    var defautInterval = 1000;    
    var onComplete = null;
    var onBeforeComplete = null;
    var progressValue = 95;
    var onError = null;

    this.doIncrement = null;

    this.hide = function(placeholder) {
        this.reset();
        if (placeholder == true) {
            $(selector).css('opacity','0');
        } else {
            $(selector).addClass('hidden');     
            $(selector).hide();
        }
    };

    this.show = function() {
        $(selector).css('opacity','1');
        $(selector).css({'visibility': 'visible'});
        $(selector).removeClass('hidden');        
        $(selector).show();
    };

    this.start = function(options) {
        this.reset();
        this.show();
        if (isObject(options) == true) {
            onComplete = getValue('onComplete',options,null);
            onBeforeComplete = getValue('onBeforeComplete',options,null);
        }
        var interval = getValue('interval',options,defautInterval);
                    
        if (isEmpty(interval) == true) {
            var interval = defautInterval; 
        }
        
        this.doIncrement = setInterval(this.increment,interval);
    };

    this.increment = function() {
        var val = $(selector).progress('get value');

        if (isFunction(onBeforeComplete) == true) {
            if (val >= progressValue) {
                var result = onBeforeComplete();
                if (result === null) {
                    return;
                }
                if (result === false) {
                    // show error
                    clearInterval(this.doIncrement);
                    callFunction(onError);                    
                    return;
                }
            }
        }

        $(selector).progress('increment'); 
        var isComplete = $(selector).progress('is complete');
       
        if (isComplete == true) { 
            clearInterval(this.doIncrement);
            callFunction(onComplete);               
        }
    };

    this.reset = function() {
        clearInterval(this.doIncrement);
        $(selector).progress('reset');
    };

    this.init = function() {
        $(selector).progress({
            duration : 200,
            total    : 100
        });
    };

    this.setLabel = function(label) {
        $(selector).progress('set label',label);
    };
}

var progressBar = new ProgressBar();
