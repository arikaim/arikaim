/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ControlPanel() {

    this.setPageIcon = function(iconClass, selector) {     
        selector = getDefaultValue(selector,'#page_icon');
        $(selector).removeClass().addClass('icon inverted pl-6 ' + iconClass);    
    };

    this.setPageTitle = function(title, selector) { 
        selector = getDefaultValue(selector,'#page_title');
        $(selector).html(title);
    };
}

var controlPanel = new ControlPanel();
