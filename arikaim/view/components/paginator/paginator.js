/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Paginator() {
    var self = this;    
    this.options = {};
    
    this.init = function(rowsId, component, namespace) {             
        component = getDefaultValue(component,$('#' + rowsId).attr('component'));
        namespace = getDefaultValue(namespace,$('#' + rowsId).attr('namespace'));
   
        this.setOptions(rowsId,component,namespace);

        arikaim.ui.button('.page-link',function(element) {
            var page = $(element).attr('page'); 
            return self.setPage(page,self.getOptions().namespace,function(result) {               
                arikaim.events.emit('paginator.load.page',result);
                self.initButtons(result.page);                                
                self.loadRows(self.getOptions());  
            }); 
        });
       
        $('.page-size-menu').dropdown({
            onChange: function(value) {               
                self.setPageSize(value,self.getOptions().namespace,function(pageSize) {
                    arikaim.events.emit('paginator.page.size.changed',pageSize);
                    self.loadRows(self.getOptions());
                });                           
            }
        });
    };

    this.initButtons = function(page) {
        page = parseInt(page);
        $('.page-link').removeClass('active');       
        $('.page-' + page).addClass('active');    
        
        var fromPage = parseInt($('.paginator').attr('from-page'));
        var toPage = parseInt($('.paginator').attr('to-page'));
        var lastPage = parseInt($('.paginator').attr('last-page'));

        if (page > 1) {
            $('.first-page').removeClass('disabled');
            $('.prev-page').removeClass('disabled');
        }
        if (page == 1) {
            $('.first-page').addClass('disabled');
            $('.prev-page').addClass('disabled');
        }     
        if (page == lastPage) {
            $('.next-page').addClass('disabled');
            $('.last-page').addClass('disabled');
        } else {
            $('.next-page').removeClass('disabled');
            $('.last-page').removeClass('disabled');
        }

        if (isNaN(fromPage) == false) {
            if ((page > toPage) || (page < fromPage)) {                       
                this.reload();
            }
        }
    };

    this.setOptions = function(rowsId, component, namespace) {
        namespace = getDefaultValue(namespace,"");
        this.options = { 
            id: rowsId,           
            namespace: namespace       
        };
        if (isString(component) == true) {
            this.options.component = component;
        } else {
            this.options.component = component.name;
            this.options.params = component.params;
        }
    };

    this.getOptions = function() {      
        return this.options;
    }

    this.getCurrentPage = function(namespace, onSuccess, onError) {
        var deferred = new $.Deferred();
        namespace = getDefaultValue(namespace,"");

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
        var data = { page_size: pageSize, namespace: namespace };

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
            deferred.resolve(result.page);  
            callFunction(onSuccess,result);      
        },function(error) {
            deferred.reject(error);  
            callFunction(onError,error);  
        });

        return deferred.promise();
    };

    this.loadRows = function(options) { 
        if (isObject(options) == false) {
            return false;
        }
        
        return arikaim.page.loadContent({
            id: options.id,
            component: options.component,
            params: options.params
        });
    };

    this.reload = function(selector) {
        selector = getDefaultValue(selector,'paginator');
        return arikaim.page.loadContent({
            id: selector,
            component: 'components:paginator',
            params: { namespace: self.options.namespace }
        },function(result) {
            self.init(self.options.id,self.options.component,self.options.namespace)
        });
    }
}

if (isEmpty(paginator) == true) {
    var paginator = new Paginator();
}
