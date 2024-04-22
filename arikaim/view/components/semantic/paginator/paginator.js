/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Paginator() {
    var self = this;    
    this.options = {};
    this.buttons = {};
  
    this.init = function(rowsId, component, namespace, buttons, initButtons) {   
        if (isEmpty(arguments[0]) == true) {
            rowsId = $('.paginator').attr('rows-selector')
        } 
        var component = getDefaultValue(arguments[1],$('#' + rowsId).attr('component'));
        component = (isEmpty(component) == true) ? $('.paginator').attr('component') : component;
        
        var namespace = getDefaultValue(arguments[2],$('#' + rowsId).attr('namespace'));
        namespace = (isEmpty(namespace) == true) ? $('.paginator').attr('namespace') : namespace;

        initButtons = getDefaultValue(initButtons,true);
              
        if (isEmpty(buttons) == true) {
            this.buttons.selector = 'paginator';
            this.buttons.component = 'semantic~paginator';
            this.buttons.params = {};
        } else {
            this.buttons = buttons;
        }
        var currentPage = this.resolveCurrentPage();
       
        this.setOptions(rowsId,component,namespace);
        if (initButtons == true) {
            this.initButtons();
            this.setButtonsStates(currentPage);
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


    this.resolveCurrentPage = function() {
        var el = $('#current_page');      
        var currentPage = (el.length > 0) ? el.html().trim() : null;
        if (isEmpty(currentPage) == true) {
            var el = $('.paginator');
            currentPage = (el.length > 0) ? el.attr('current-page').trim() : 1;
        }

        return currentPage;
    };

    this.disableButton = function(selector) {   
        $(selector).addClass('disabled');
        $(selector).state('disable');       
    }

    this.enableButton = function(selector) {      
        $(selector).removeClass('disabled');
    }

    this.setButtonsStates = function(page, lastPage) {
        page = (isEmpty(page) == true) ? this.resolveCurrentPage() : parseInt(page);
        var lastPage = getDefaultValue(arguments[1],parseInt($('.paginator').attr('last-page')));

        $('.page-link').removeClass('active');       
        $('.page-' + page).addClass('active');    
        
        var fromPage = parseInt($('.paginator').attr('from-page'));
        var toPage = parseInt($('.paginator').attr('to-page'));
    
        if (page == 1) {                 
            this.disableButton('.first-page');
            this.disableButton('.prev-page');    
        } else {           
            this.enableButton('.first-page');
            this.enableButton('.prev-page');              
        }
           
        if (page == lastPage) {          
            this.disableButton('.next-page');
            this.disableButton('.last-page');          
        } else {
            this.enableButton('.next-page'); 
            this.enableButton('.last-page'); 
        } 
        
        if (isNaN(fromPage) == false) {
            if ((page > toPage) || (page < fromPage)) {                 
                this.reload();
            }
        }
    };

    this.initButtons = function() {     
        arikaim.ui.button('.page-link',function(element) {
            var page = $(element).attr('page'); 
          
            return self.setPage(page,self.getOptions().namespace,function(result) {   
                // set current page              
                $('#current_page').html(result.page);
                $('.paginator').attr('current-page',result.page);                         
                arikaim.events.emit('paginator.load.page',result);                                                      
                self.loadRows();  
            }); 
        },function(result) {
            self.setButtonsStates(result.page,result.last_page);
        });
    };

    this.setParams = function(params) {
        this.options.params = params;
    };

    this.setOptions = function(rowsId, component, namespace) {
        var component = getDefaultValue(arguments[1],{});
        var namespace = getDefaultValue(arguments[2],'');
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
        namespace = getDefaultValue(namespace,'');
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

    this.setPage = function(page, namespace, onSuccess, onError) {
        var deferred = new $.Deferred();

        page = (isEmpty(page) == true) ? 1 : page;
        namespace = getDefaultValue(namespace,"");

        var data = { 
            page: page,
            namespace: namespace 
        };
        arikaim.put('/core/api/ui/paginator/page',data,function(result) {
            self.currentPage = result.page;
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

if (isEmpty(paginator) == true) {
    var paginator = new Paginator();
    arikaim.events.emit('paginator.loaded',paginator);
}