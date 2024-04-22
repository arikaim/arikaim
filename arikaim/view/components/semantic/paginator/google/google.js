/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function GoogleApiPaginator() {
    var self = this;    
    this.options = {};
    this.buttons = {};
    this.pageToken = null;

    this.init = function(rowsId, component, namespace, buttons, initButtons) {      
        initButtons = getDefaultValue(initButtons,true);
        component = getDefaultValue(component,$('#' + rowsId).attr('component'));
        namespace = getDefaultValue(namespace,$('#' + rowsId).attr('namespace'));
        if (isEmpty(buttons) == true) {
            this.buttons.selector = 'paginator';
            this.buttons.component = 'semantic~paginator';
            this.buttons.params = {};
        } else {
            this.buttons = buttons;
        }
        this.setOptions(rowsId,component,namespace);
        if (initButtons == true) {
            this.initButtons();
        }
      
        $('.page-size-menu').dropdown({
            onChange: function(value) {               
                self.setPageSize(value,self.getOptions().namespace,function(pageSize) {
                    arikaim.events.emit('paginator.page.size.changed',pageSize);
                    self.setPage(1,self.getOptions().namespace,function(result) {               
                        arikaim.events.emit('paginator.load.page',result);                                                     
                        self.loadRows(function(result) {
                            self.reload();  
                        });  
                    });                                     
                });                           
            }
        });
    };

    this.initButtons = function(pageToken) {
        pageToken = (isEmpty(pageToken) == true) ? this.pageToken : pageToken;

        arikaim.ui.button('.page-link',function(element) {
            var pageToken = $(element).attr('page-token'); 
            return self.setPage(pageToken,self.getOptions().namespace,function(result) {            
                arikaim.events.emit('paginator.load.page',result);
                self.initButtons();                                
                self.loadRows();  
            }); 
        });
    };

    this.setParams = function(params) {
        this.options.params = params;
    };

    this.setOptions = function(rowsId, component, namespace) {
        namespace = getDefaultValue(namespace,'');
        this.options = { 
            id: rowsId,           
            namespace: namespace       
        };      
        this.options.component = (isString(component) == true) ? component : component.name;
        this.options.params = (isObject(component.params) == true) ? component.params : {};
    };

    this.getOptions = function() {      
        return this.options;
    }

    this.getCurrentPage = function(namespace, onSuccess, onError) {
        var deferred = new $.Deferred();
        namespace = getDefaultValue(namespace,'');

        arikaim.get('/core/api/ui/paginator/' + namespace,function(result) {
            deferred.resolve(result.page);
            callFunction(onSuccess,result.page); 
        },function(error) {
            deferred.reject(error);
            callFunction(onError,error);  
        });

        return deferred.promise();
    }

    this.setPageSize = function(pageSize, namespace, onSuccess, onError) {
        var deferred = new $.Deferred();

        pageSize = (isEmpty(pageSize) == true) ? 1 : pageSize;
        namespace = getDefaultValue(namespace,"");
        var data = { 
            page_size: pageSize,
            namespace: namespace 
        };

        arikaim.put('/core/api/ui/paginator/page-size',data,function(result) {
            deferred.resolve(result.page_size);  
            callFunction(onSuccess,result.page_size);      
        },function(error) {
            deferred.reject(error);  
            callFunction(onError,error);  
        });

        return deferred.promise();
    };

    this.clear = function(namespace, onSuccess, onError) {
        return arikaim.delete('/core/api/ui/paginator/' + namespace,onSuccess,onError);
    };

    this.setPage = function(pageToken, namespace, onSuccess, onError) {
        var deferred = new $.Deferred();    
        namespace = getDefaultValue(namespace,'');

        var data = { 
            page: pageToken,
            namespace: namespace 
        };
        arikaim.put('/core/api/ui/paginator/page',data,function(result) {
            self.pageToken = result.page;
            deferred.resolve(result.page);  
            callFunction(onSuccess,result);      
        },function(error) {
            deferred.reject(error);  
            callFunction(onError,error);  
        });

        return deferred.promise();
    };

    this.loadRows = function(onSuccess) {  
        return arikaim.page.loadContent({
            id: self.options.id,
            component: self.options.component,
            params: (isEmpty(self.options.params) == true) ? {} : self.options.params
        },function(result) {
            callFunction(onSuccess,result);
        });
    };

    this.reload = function(selector, component) {
        selector = getDefaultValue(selector,this.buttons.selector);
        component = getDefaultValue(component,this.buttons.component);
        this.buttons.params.namespace = self.options.namespace;
      
        return arikaim.page.loadContent({
            id: selector,
            component: component,
            params: self.buttons.params
        },function(result) {            
            self.initButtons();            
        });
    }
}

if (isEmpty(googleApiPaginator) == true) {
    var googleApiPaginator = new GoogleApiPaginator();
}