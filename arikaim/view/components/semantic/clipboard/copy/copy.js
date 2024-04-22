'use strict';

arikaim.component.onLoaded(function(component) {
    $('.clipboard-copy').on('click',function() {
        var type = $(this).attr('content-type');
        var selector = $(this).attr('content-selector');
        var escape = $(this).attr('escape-content');
        var options = $(this).attr('content-options');
        var focusElement = $(this).attr('focus-element');
      
        clipboardCopy(selector,type,escape,options,focusElement);        
    });

    function clipboardCopy(selector, type, escape, options, focusElement) {
        var value = (type == 'element') ? $(selector).html() : $(selector).val();
        focusElement = (isEmpty(focusElement) == true) ? selector : focusElement;
        if (options == 'trim') {
            value = value.trim();
        }

        if (isObject(navigator.clipboard) == true) {
            navigator.clipboard.writeText(value).then(function() {
                $(focusElement).show();  
                $(focusElement).focus();         
                $(focusElement)[0].scrollIntoView(false);  
                return true;
            },function() {
               
            });
        } else {           
            var $input = $('<input>');
            $('body').append($input);
            if (escape == true && isEmpty(value) == false) {
                var doc = new DOMParser().parseFromString(value,'text/html');
                value = doc.documentElement.textContent;
            }
           
            $input.val(value);
            $input.focus();
            $input.select();
            document.execCommand('copy');
            $input.remove();    
        }

        $(focusElement).show();  
        $(focusElement).focus();         
        $(focusElement)[0].scrollIntoView(false);                    
    }
});