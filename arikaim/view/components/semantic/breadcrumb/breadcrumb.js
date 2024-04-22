/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Breadcrumb() {
    var self = this;

    this.selector = null;
    this.items = []; 
    this.separator = '/';
    this.onSelect = null;
    this.disableLinks = false;
    this.separatorClass = 'divider';
    this.pathItemClass = 'section';
    this.homePath = '';

    this.init = function(options) {
        this.selector = getValue('selector',options,'#breadcrumb');
        this.onSelect = getValue('onSelect',options,null);
        this.disableLinks = getValue('disableLinks',options,false);
        this.separator = getValue('separator',options,$(this.selector).attr('separator'));
        this.separatorClass = getValue('separatorClass',options,'divider');
        this.pathItemClass = getValue('pathItemClass',options,'section');
        this.homePath = getValue('homePath',options,$(this.selector).attr('home-path'));

        var path = getValue('path',options,$(this.selector).attr('current-path'));
        this.items = path.split(this.separator);

        this.initLinks();       
    };

    this.initLinks = function() {
        arikaim.ui.button('.breadcrumb-item',function(element) {
            var path = $(element).attr('path');          
            self.setPath(path);         
            $(element).addClass('active');
          
            callFunction(self.onSelect,path,self);
        });
    };

    this.getCurrentPath = function() {
        return $(this.selector).attr('current-path');       
    };

    this.getPath = function(pathIndex) {
        if (isEmpty(pathIndex) == true) {
            return this.items.join(this.separator);
        }
        var path = '';
        for (var index = 0; index <= pathIndex; index++) {
            path += (path.slice(-1) == this.separator) ? this.items[index] : this.separator + this.items[index];            
        }

        return path;
    };

    this.addPathItem = function(item, index) {      
        var tag = (this.disableLinks == true) ? 'div' : 'a';
        var itemClass = (this.disableLinks == true) ? this.pathItemClass : this.pathItemClass + ' breadcrumb-item';
        var path = this.getPath(index);     
        var code = '<' + tag + ' class="' + itemClass + '" path="' + path + '" >' + item + '</' + tag + '>';
        
        $('.breadcrumb-items').append(code);    
    };

    this.addSeparator = function(separatorClass) {
        separatorClass = getDefaultValue(separatorClass,this.separatorClass);
        $('.breadcrumb-items').append('<div class="' + separatorClass + '">' + this.separator + '</div>');
    };

    this.setCurrentPath = function(path) {
        path = (isEmpty(path) == true) ? '' : path;
        this.items = path.split(this.separator);
        $(this.selector).attr('current-path',path);
    };

    this.setPath = function(path) {
        this.setCurrentPath(path);
        $('.breadcrumb-items').empty();
      
        this.items.forEach(function(item,index) {
            if (isEmpty(item) == false) {
                self.addPathItem(item,index);
                self.addSeparator();
            }            
        });
        this.initLinks();     
    };
}

if (isEmpty(breadcrumb) == true) {
    var breadcrumb = new Breadcrumb();
}