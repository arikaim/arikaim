/**
 *  Arikaim
 *  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function callFunction(functionName, params) {
    return (isFunction(functionName) == true) ? functionName(params) : null
}

function safeCall(objName, callback, showError, showErrorDetails) {
    showError = getDefaultValue(showError,false);
    showErrorDetails = getDefaultValue(showErrorDetails,false);
    var obj = (isObject(this[objName]) == true) ? this[objName] : null;

    if (isObject(obj) == true) {
        if (isFunction(callback) == true) {
            var call = function(obj,callback) {
                try {
                    return callback(obj);
                } catch (error) {
                    if (showError) {
                        console.warn('Warning: ' + error.message);
                        if (showErrorDetails) {
                            console.log(error);
                        }                   
                    }
                }
            }
        }
        return call(obj,callback);
    } else {
        if (showError) {
            console.warn('Warning: ' + objName + ' is not valid object');
        }
    }

    return false;
}

function isJSON(json){
    try {
        JSON.parse(json);
    }
    catch(e) {
        return false;
    }
    return true;
}

function getObjectProperty(path, obj) {
    if (isObject(obj) == false) {
        obj = {};
    }
    return path.split('.').reduce(function(prev, curr) {
        return prev ? prev[curr] : null
    }, obj || self)
}

function getValue(path, obj, defaultValue) {
    var value = getObjectProperty(path,obj);
    return (value == null) ? defaultValue : value;      
}

function getDefaultValue(variable, defaultValue) {
    return (isEmpty(variable) == true) ? defaultValue : variable;     
}

function isFunction(variable) {
    if (typeof variable === 'function') return true;    
    return false;
}

function isArray(variable) {
    return (isObject(variable) == false) ? false : (variable.constructor === Array);
}

function supportUpload() {
    return (typeof(window.FileReader) != 'undefined');
}

function isEmpty(variable) {
    if (variable === undefined) return true;
    if (variable === null) return true;
    if (variable === '') return true;
    if (isObject(variable) == true) {
        return $.isEmptyObject(variable);
    }
    if (isArray(variable) == true) {
        return (variable.length == 0)
    }

    return false;
}

function inArray(value, array) {
    return array.indexOf(value) > -1;
}

function isPromise(variable) {
    return (isObject(variable) == false) ? false : (typeof variable.then === 'function');   
}

function isObject(variable) {
    return (variable === null) ? false : (typeof variable === 'object');   
}

function isString(variable) {
    return (typeof variable === 'string' || variable instanceof String) ? true : false;
}

function createVariable(name, value) {
    window[name] = value;   
    return !isEmpty(name);
}

function getElementAttributes(selector, exclude) {
    exclude = getDefaultValue(exclude,['id','type','src','class']);
    var attributes = {};
    $(selector).each(function() {
        $.each(this.attributes, function() {           
            if (this.specified) {
                if (inArray(this.name,exclude) == false) {                   
                    attributes[this.name] = this.value;                   
                }           
            }
        });
    });

    return attributes;   
}

function resolveLibrayrParams(selector) {
    var exclude = ['id','type','src'];
    $(selector).each(function() {
        $.each(this.attributes, function() {           
            if (this.specified) {
                if (inArray(this.name,exclude) == false) {                   
                    createVariable(this.name,this.value);                   
                }           
            }
        });
    });   
}

/**
 * @class Events
 * Events Emitter
 */
function Events() {
    var self = this;
    var events = {};

    this.addListener = function(event, callback, name, context) {
        context = (isEmpty(context) == true) ? this : context;
        name = (isEmpty(name) == true) ? null : name;

        var listener = { 
            callback: callback,
            context: context, 
            name: name 
        };

        if (isEmpty(events[event]) == true) {
            events[event] = [];
        } 
    
        if (name !== null) {
            if (this.hasListener(event,name) === true) {
                return false;
            }       
        }
        events[event].push(listener);
    
        return true; 
    };

    this.getListeners = function(event) {
        return events[event];
    };

    this.on = function(event, callback, name, context) {
        context = (isEmpty(context) == true) ? this : context;
        return this.addListener(event,callback,name,context);
    };

    this.emit = function(event, param1, param2, param3, param4) {
        if (isEmpty(events[event]) == true) {
            return false;
        }
        
        events[event].forEach(function(item) {
            switch (arguments.length) {
                case 1: return item.callback.call(item.context), true;
                case 2: return item.callback.call(item.context,param1), true;
                case 3: return item.callback.call(item.context,param1,param2), true;
                case 4: return item.callback.call(item.context,param1,param2,param3), true;
                case 5: return item.callback.call(item.context,param1,param2,param3,param4), true;
            }
        });
    };

    this.hasListener = function(event, name) {
        if (isEmpty(events[event]) == true) {
            return false;
        }

        for (var i = 0; i < events[event].length ; i++) {
            var item = events[event][i];
            if (item.name == name) {
                return true;
            }
        }

        return false;
    };

    this.removeListener = function(event, name) {
        if (isEmpty(events[event]) == true) {
            return false;
        }
        events[event].forEach(function(item) {
            if (item.name == name) {
                var index = events[event].indexOf(item);
                events[event].array.splice(index, 1);
            }
        });
    };

    this.removeAllListeners = function(event) {
        events[event] = null;
    };
} 

/**
 *  @class ApiResponse
 *  Api response object
 *  Api calls returns ApiResponse object
 */
function ApiResponse(response) {
      
    var status = 'ok';
    var errors = [];
    var result = '';

    this.createEmpty = function() {
        status = 'ok';
        errors = [];
        result = '';
    };

    this.init = function(response) {

        if (isEmpty(response) == true) {
            data = this.createEmpty();
            return;
        }
        if (isObject(response) == true) {
            data = response;
        } else {
            if (isJSON(response) == false) {
                result = response;
                status = 'ok';
                errors = [];
                return;
            }
            data = JSON.parse(response);
        }
      
        if (isEmpty(data.status) == false) {
            status = data.status;
        }
        if (isEmpty(data.errors) == false) {
            errors = data.errors;
        }
        if (isEmpty(data.result) == false) {
            result = data.result;
        }
    };

    var data = this.init(response);

    this.getResult = function() {        
        return result;
    };

    this.getErrors = function() {
        return errors;
    };

    this.getError = function(callback) {
        for (var index = 0; index < errors.length; ++index) {
            callFunction(callback,errors[index])        
            if (isNaN(callback) == false ) {
                return (isEmpty(errors[callback]) == false) ? errors[callback] : false;              
            }
        }

        return true;
    };

    this.getStatus = function() { 
        return status;
    };

    this.addError = function(error) {
        errors.push(error);
    };

    this.hasError = function() { 
        return (isEmpty(errors) == false);
    };
}

/**
 *  @class Storage
 *  Cookie, localStorage, sessionStorage class
 */
function Storage() {

    var type = 'cookie';

    this.set = function(name, value, time) {
        switch (type) {
            case 'cookie': {
                this.setCookie(name,value,time);
                break;
            }
            default:{
                this.setCookie(name,value,time);
                break;
            }
        }
    };
    
    this.get = function(name) {
        switch (type) {
            case 'cookie':{
                return this.getCookie(name);             
            }
            default:{
                return this.getCookie(name);              
            }
        }
    };

    this.setCookie = function(name, value, days) {
        var expires = '';
        if (isEmpty(days) == false) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = ';expires=' + date.toGMTString();           
        } 
        if (isArray(value) == true) {
            value = JSON.stringify(value);
        }
        document.cookie = name + '=' + value + expires + ';path=/';
    };

    this.getCookie = function(name) {
        var fieldName = name + '=';
        cookie = document.cookie.split(';');
        var item = '';
        for(var i = 0;i < cookie.length; i++) {
            item = cookie[i];          
            while (item.charAt(0) == ' ') item = item.substring(1,item.length);
            if (item.indexOf(fieldName) == 0) {
              return item.substring(fieldName.length,item.length);
            }
        }
        return null;
    }

    this.setSession = function(name,value) {
        if (isObject(sessionStorage) == true) {
            sessionStorage.setItem(name,value);
            return true;            
        } 

        return false;
    };

    this.getSession = function(name) {
        if (isObject(sessionStorage) == true) {
            return sessionStorage.getItem(name);                    
        } 

        return false;
    };

    this.clearSession = function() {
        if (isObject(sessionStorage) == true) {
            sessionStorage.clear();
            return true;
        }

        return false;
    }; 
    
    this.removeSession = function(name) {
        if (isObject(sessionStorage) == true) {
            sessionStorage.removeItem(name);
            return true;
        }

        return false;
    };
}

/**
 *  @class Arikaim
 *  Main Arikaim CMS object
 * 
 */
function Arikaim() {
    var self = this; 

    if (isObject(Arikaim.instance) == true) {
        return Arikaim.instance;
    }
  
    var host     = window.location.origin;
    var devMode  = true;
    var jwtToken = '';
    var services = [];  
    var baseUrl  = '';
    var version  = '1.0.8';

    this.storage    = new Storage();       

    this.getBaseUrl = function() {
       return (isEmpty(baseUrl) == true) ? '' : baseUrl         
    };

    this.setBaseUrl = function(url) {       
        baseUrl = (isEmpty(url) == true) ? this.resolveBaseUrl() : url;
    }; 

    this.resolveBaseUrl = function() {
       return (isEmpty(arikaim_base_url) == true) ? window.location.protocol + '//' + window.location.host : arikaim_base_url;   
    };

    this.init = function(url) {
        resolveLibrayrParams('#library_arikaim');
        this.setBaseUrl(url);    
        // check for jquery 
        window.onload = function() {
            if (isEmpty(window.jQuery) == true) {  
                console.log('Error: jQuery library missing.');
            } 
        }       
        this.log('\nArikaim CMS v' + this.getVersion());  
    };

    this.getLanguagePath = function(language) {
        var url = this.getUrl();
        language = getDefaultValue(language,'en');

        if (isEmpty(language) == true) {
            return url;
        }
        if (url.substr(-3,1) == '/' || url.substr(-4,1) == '/') {
            url = url.slice(0,-3);         
        }

        return (url.slice(-1) == '/') ? url + language + '/' : url + '/' + language + '/';         
    };

    this.getUrl = function() {
        return window.location.href;
    }

    this.loadUrl = function(url, relative) { 
        relative = getDefaultValue(relative,false);
        url = (relative == true) ? arikaim.getBaseUrl() + url : url;
        document.location.href = url;
    };

    this.setLanguage = function(language) {       
        language = getDefaultValue(language,'en');          
        var url = this.getLanguagePath(language);
     
        this.storage.setCookie('language',language,30);
        this.storage.setSession('language',language);
        this.loadUrl(url);
    };

    this.getLanguage = function() {
        var language = this.storage.getSession('language');
        if (isEmpty(language) == true ) {
            language = this.storage.getCookie('language');
        }

        return getDefaultValue(language,'en');         
    };
    
    this.setToken = function(token, save) {
        jwtToken = token;
        if (save == true) {
            this.storage.set('token',token);
        }       
    };

    this.clearToken = function() {
        jwtToken = '';
        this.storage.set('token','');
    };

    this.getToken = function() {
        return (isEmpty(jwtToken) == true) ? this.storage.get('token','') : jwtToken;
    };

    this.log = function(msg) { 
        if (devMode == true) {
            console.log(msg);
        }
    };

    this.setDevMode = function(mode) { 
        devMode = mode;
        if (mode == true) {
            this.log('Development mode.\n');
        }
    };

    this.getVersion = function() { 
        return version;
    };

    this.getHost = function() { 
        return host;
    };

    this.setHost = function(url) { 
        host = url;
    };

    this.getPath = function() { 
        return window.location.pathname;
    };

    this.post = function(url, data, onSuccess, onError) {
        if (isString(data) == true) {
            if ($(data).length > 0) {
                data = $(data).serialize();
            } 
        }

        return this.apiCall(url,'POST',data,onSuccess,onError);
    };

    this.get = function(url, onSuccess, onError, data) {   
        data = getDefaultValue(data,null);
        return this.apiCall(url,'GET',data,onSuccess,onError);
    };

    this.delete = function(url, onSuccess, onError) {   
        return this.apiCall(url,'DELETE',null,onSuccess,onError);
    };

    this.put = function(url, data, onSuccess, onError) {
        if (isString(data) == true) {
            data = $(data).serialize();
        }   

        return this.apiCall(url,'PUT',data,onSuccess,onError);
    };

    this.patch = function(url, data, onSuccess, onError) {  
        if (isString(data) == true) {
            data = $(data).serialize();
        } 

        return this.apiCall(url,'PATCH',data,onSuccess,onError);
    };

    this.register = function(name, service) {
        this.log('Register service: ' + name);   
        services[name] = service;
    };  

    this.call = function(name,args) {
        this.log('Call Service: ' + name);
        return services[name].apply(null, args || []); 
    };  

    this.includeCSSFile = function(url) {
        $('<link>').appendTo('head').attr({
            type: 'text/css', 
            rel: 'stylesheet',
            href: url
        });
    };

    this.includeScript = function(url, onSuccess, onError) {
        $.getScript(url).done(function(script, status) {
            callFunction(onSuccess,status);
        }).fail(function(jqxhr, settings, exception) {
            callFunction(onError,exception);
        });       
    }; 

    this.findScript = function(url) {
        var search = document.querySelector('script[src="' + url + '"]');
        return !isEmpty(search);
    };

    this.loadScript = function(url, async, crossorigin, id) {
        var script = document.createElement('script');
        script.src = url;

        if (async == true) {
            script.setAttribute('async');
        }
        if (isEmpty(crossorigin) == false) {
            script.setAttribute('crossorigin',crossorigin);
        }
        if (isEmpty(id) == false) {
            script.setAttribute('id',id);
        }
        document.getElementsByTagName('body')[0].appendChild(script);       
    };

    this.getAuthHeader = function() {
        var token = this.getToken();       
        return (isEmpty(token) == false) ? 'Bearer ' + token : '';          
    };

    this.open = function(method, url, data, onSuccess, onError) {
        this.request(url,method,data,onSuccess,onError,null,'none',true);
    };

    this.request = function(url, method, requestData, onSuccess, onError, onProgress, crossDomain) {    
        var deferred = new $.Deferred();

        crossDomain = getDefaultValue(crossDomain,false); 
        requestData = getDefaultValue(requestData,null);  

        if (crossDomain == false) {
            url = this.getBaseUrl() + url;
        }
        var authHeader = this.getAuthHeader();  
        var headerData = null;

        if ((method == 'GET') && (isObject(requestData) == true)) {     
            headerData = JSON.stringify(requestData);
            requestData = null;
        }
          
        var progress = function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress',function(event) {
                callFunction(onProgress,event);
            }, false);
            return xhr;
        };
        
        $.ajax({
            url: url,
            method: method,          
            data: requestData,
            xhr: progress,
            crossDomain: crossDomain,
            beforeSend: function(request) {
                request.setRequestHeader('Accept','application/json; charset=utf-8');
                if (authHeader != null) {
                    request.setRequestHeader('Authorization',authHeader);
                }
                if (isEmpty(headerData) == false) {         
                    request.setRequestHeader('Params',headerData);
                }
            },
            success: function (data) {   
                var response = new ApiResponse(data);  
                deferred.resolve(response);  
                callFunction(onSuccess,response);            
            },
            error: function (xhr, status, error) {             
                arikaim.log('Error\n');
                arikaim.log('Request url: ' + url + '\n');
                arikaim.log('Error details: ' + error);
                arikaim.log('Response: ' + xhr.responseText);
                response = new ApiResponse(xhr.responseText);               
                deferred.reject(response.getErrors());
        
                callFunction(onError,response.getErrors());
            }
        });   

        return deferred.promise();
    };

    this.apiCall = function(url, method, requestData, onSuccess, onError, onProgress) {
        var deferred = new $.Deferred();

        this.request(url,method,requestData,function(response) {      
            if (response.hasError() == false) {  
                deferred.resolve(response.getResult());  
                callFunction(onSuccess,response.getResult());                         
            } else {
                deferred.reject(response.getErrors());  
                callFunction(onError,response.getErrors());                  
            }
        },function(errors) {
            deferred.reject(errors);
            callFunction(onError,errors);
        },onProgress);

        return deferred.promise();
    };

    // Singleton
    Arikaim.instance = this;

    this.init();
}

// Create Arikaim object 
var arikaim = new Arikaim();

Object.assign(arikaim,{ events: new Events() });