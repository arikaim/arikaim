/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function callFunction(function_name,params) {
    if (isFunction(function_name) == true) {
        return function_name(params);
    }
    return null;
}

function isJSON(json_string){
    try {
        var json_string = JSON.stringify(json_string);
        var json = JSON.parse(json_string);
        if(typeof(json_string) == 'string')
            if(json_string.length == 0) return false;
    }
    catch(e){
        return false;
    }
    return true;
}

function getObjectProperty(path, obj) {
    return path.split('.').reduce(function(prev, curr) {
        return prev ? prev[curr] : null
    }, obj || self)
}

function getValue(path,obj,default_value) {
    var val = getObjectProperty(path,obj);
    if (val == null) {
        val = default_value;
    }
    return val;
}

function getDefaultValue(variable,default_value) {
    if (isEmpty(variable) == true) {
        return default_value;
    }
    return variable;
}

function isEmpty(variable) {
    if (variable === undefined) return true;
    if (variable === null) return true;
    if (variable === "") return true;
    return false;
}

function isFunction(variable) {
    if (typeof variable === 'function') return true;    
    return false;
}

function isObject(variable) {
    return (typeof variable === 'object');
}

function isString(variable) {
    if (typeof variable === 'string' || variable instanceof String) {
        return true;
    }
    return false;
}

function isArray(variable) {
    if (isEmpty(variable) == true) return false;
    return (variable.constructor === Array);
}

function Component(prop) {
    
    var properties = {};

    this.getProperty = function(name) {
        return getObjectProperty(name,properties);
    };

    this.getProperties = function() {
        return properties;
    };

    this.setProperties = function(json_data) {
        if (isJSON(json_data) == true) {
            properties = JSON.parse(json_data);
            return true;
        }
        return false;
    };

    if (isEmpty(prop) == false) {
        this.setProperties(prop);
    }
}

/**
 * @class Form
 * Manage html forms
 */
function Form() {
    var message_element = "#message";
    var self = this;

    this.onSubmit = function(form_element,onSuccess,onError) {
        $(form_element).off();       
        $(form_element).submit(function(e) {
            e.preventDefault();     
            self.disable(form_element);       
            if (self.validate(form_element) == true) {
                self.enable(form_element);
                callFunction(onSuccess);
            } else {
                self.showValidationErrors(form_element);
                self.enable(form_element);
                callFunction(onError);
            }
            return false;
        });
    };

    this.addFieldErrors = function(form_element,errors) {
        errors.forEach(function(error) {
            $(form_element).form('add prompt',error.field_name,error.message);
        });
    };
    
    this.disable = function(form_element) {
        $(form_element + " :input").prop("disabled", true);
    };
    
    this.enable = function(form_element) {
        $(form_element + " :input").prop("disabled", false);
    };

    this.addRules = function(form_element,rules,form_settings) {
        $(form_element).form(rules);
        $(form_element + ' :input').on('focus',function() {        
            self.clearErrors(form_element);
        });
    };

    this.validate = function(form_element) {
        $(form_element).form('validate form');
        var is_valid = $(form_element).form('is valid');
        return is_valid; 
    };

    this.showValidationErrors = function(form_element,element) {
        var errors_element = getDefaultValue(element,'.form-errors');
        $(form_element).find('.form-errors').show();
    };

    this.showMessage = function(options) {
        var element = getValue('element',options,message_element);     
        var msg = getValue('msg',options,''); 
        var auto_hide = getValue('auto_hide',options,0);     
        $(element).removeClass('hidden');
        $(element).show();
        if (auto_hide > 0) {
            $(element).delay(auto_hide).fadeOut('slow');
        } 
        if ($(element).find('.header') != false) {
            $(element).find('.header').html(msg);
        } else {
            $(element).html(msg);
        }       
    };

    this.clearErrors = function(form_element) {
        $(form_element).find('.form-errors').html('');    
        $(form_element).find('.form-errors').hide();
        $(form_element).find('.error').removeClass('error').find('.prompt').remove();    
    };   

    this.showErrors = function(errors,element,component) {
        var element = getDefaultValue(element,'.form-errors');

        $(element).html("");
        $(element).show();
        if (isArray(errors) == true) {
            for (var index = 0; index < errors.length; index++) {
                var error = errors[index];              
                if (isObject(error) == true) {
                    var error_label = "";
                    if (isObject(component) == true) {                     
                        error_label = component.getProperty(error.field_name + ".label");
                    }
                    error = "<span>" + error_label + "</span> " + error.message;
                }
                $(element).append("<li>" + error + "</li>");
            }
        } else {
            $(element).append("<li>" + errors + "</li>");
        }
    };
}

/**
 * @class HtmlComponents
 * Container for all html components loaded 
 */
function HtmlComponent() {
    
    var self = this;
    var components = {};
    var events = {};

    this.get = function(component_name) {
        if (isEmpty(components[component_name]) == false) {
            return components[component_name];
        } 
        return false;
    };

    this.getAll = function() {
        return components;
    };

    this.set = function(component_name,component_properties) {
        var component = new Component(component_properties);
        components[component_name] = component;
    };

    this.onLoaded = function(component_name,callback) {
        events[component_name] = callback;
    };

    this.getEvent = function(component_name) {
        if (isFunction(events[component_name]) == true) {
            return events[component_name];
        } 
        return false;
    };

    this.triggerEvent = function(component_name) {
        callback = this.getEvent(component_name);
        if (callback != false) {
            return callFunction(callback);
        }
        return false;
    };

    this.getUrl = function(component_name,params) {
        var url = '/api/ui/component/' + component_name;
        if (isEmpty(params) == true) {
            return url;
        }
        if (isArray(params) == true) {
            for (var index = 0; index < params.length; ++index) {
                url = url + '/' + params[index];
            }            
        }
        return url;
    };

    this.load = function(component_name,onSuccess,onError,params,use_header) {       
        if (isEmpty(component_name) == true) {
            arikaim.log('Error: Not valid component name: ' + component_name);
            return false;
        }     
        if (use_header == true) {
            var url = this.getUrl(component_name,null);
        } else {
            var url = this.getUrl(component_name,params);
        }
        arikaim.apiCall(url,'GET',params,function(result) {
            arikaim.component.set(component_name,result.properties);
            callFunction(onSuccess,result);
            self.includeFiles(result,function(files_loaded) {            
                arikaim.component.triggerEvent(component_name);
                arikaim.log('component ' + component_name + ' loaded!');           
            });
        },function(errors) {
            arikaim.log('Error loading component ' + component_name);
            callFunction(onError,errors);
        },"session");
    };

    this.includeFiles = function(response,onSuccess) {
        var js_files  = response.js_files;
        var css_files = response.css_files;
        
        var files_count = 0;
        var loaded_files = 0;
    
        if (css_files != false) {
            files_count = files_count + css_files.length;
            css_files.forEach(function(file_url) {              
                arikaim.includeCSSFile(file_url);
                loaded_files++;
                if (loaded_files == files_count) {
                    callFunction(onSuccess(loaded_files));
                } 
            }, this);
        }

        if (js_files != false) {
            files_count = files_count + js_files.length;
            js_files.forEach(function(file_url) {              
               arikaim.includeScript(file_url,function() {
                    loaded_files++;
                    if (loaded_files == files_count) {
                        callFunction(onSuccess(loaded_files));
                    }               
               });
            }, this);
        }
        if (loaded_files == files_count) {
            callFunction(onSuccess(loaded_files));
        }
    };
}

/**
 *  @class Page
 *  
 */
function Page() {
    
    var self = this;
    var properties = {};
    var ui_type;
    var page_name = "";
    var on_content_ready = null;
    var loader = "";

    this.setLoader = function(loader_code) {
        loader = loader_code
    };
    
    this.getLoader = function() {
        return loader;
    };

    this.onContentReady = function(callback) {
        on_content_ready = callback;
    };

    this.onReady = function(callback) {        
        $(document).ready(callback);
    };

    this.load = function(url) { 
        document.location.href = url;
    };

    this.reload = function() {
        location.reload(true);
    };

    this.getProperty = function(property_name) {
        var data =  components[component_name];
        if (isJSON(data) == true) {
            return JSON.parse(components[component_name]);
        }
        return false;
    };

    this.getPageName = function() {
        return page_name;
    }

    this.hasLib = function(lib_name) {
        if (properties.libraries.indexOf(lib_name) > 0) {
            return true;
        } 
        return false;
    } 

    this.setProperties = function(params) {            
        page_name  = params.page_name;
        properties = params;    
        return true;
    };

    this.showLoader = function(element,loader) {
        if (isEmpty(loader) == true) {
            loader = this.getLoader();
        }        
        $(element).html(loader);    
        return false;
    };

    this.showContent = function(element) {
        $(element).show();
        $(element).css('visibility', 'visible');
    };

    this.clearContent = function(element) {
        $(element).html("");
    };

    this.hideContent = function(element) {
        $(element).hide();
        $(element).css('visibility', 'hidden');
    };

    this.setContent = function(element,content) {
        $(element).html(content);
    };

    this.replaceContent = function(element,content) {
        $(element).replaceWith(content);
    };
    
    this.loadProperties = function(page_name,onSuccess,onError) {
        arikaim.log('Load page properties: ' + page_name);
        arikaim.get('/api/ui/page/properties/' + page_name,function(result) {          
            self.setProperties(result.properties);           
            self.version = getValue('properties.version',result,'1.0');
            self.setLoader(getValue('properties.loader',result,''));

            arikaim.log('Page properties loaded!');
            callFunction(onSuccess,result);
        },function(errors) {
            arikaim.log('Error loading page properties: ' + page_name);
            arikaim.setErrors(errors);
            callFunction(onError,errors);
        },"session");
    };

    this.loadContent = function(params,onSuccess,onError) {       
        var component_name = getValue('component',params,'no-name');       
        var component_params = getValue('params',params,'');
        var element_id = getValue('id',params);
        var element = getValue('element',params);
        var loader = getValue('loader',params,null);
        var replace = getValue('replace',params,false);
        var extension = getValue('extension',params,null);
        var use_header = getValue('use_header',params,false);
       
        if (isEmpty(element_id) == false) {
            element = '#' + element_id;
        }
        if (isEmpty(extension) == false) {
            component_name = extension + ":" + component_name;
        }
        this.showLoader(element,loader);
    
        arikaim.component.load(component_name,function(result) {  
            if (replace == false) {
                arikaim.page.setContent(element,result.html);
            } else {
                $(element).replaceWith(result.html);
            }
            callFunction(onSuccess,result);                       
        },function(errors) {
            // errors load component
            callFunction(onError,errors);   
        },component_params,use_header);
    };

    this.showSystemErrors = function(errors,element) {
        if (isEmpty(element) == true) {
            var element = '.error';
        }
        $(element + " ul").html("");
        this.show(element);
        if (isObject(errors) == true) {
            $.each(errors, function(key,value) {
                $(element + " ul").append("<li>" + value + "</li>");
            });  
        } else {
            $(element).append("<li>" + errors + "</li>");
        }
    };

    this.show = function(element) {
        $(element).show();
        $(element).removeClass('hidden');
    };

    this.loadPage = function(page_name,onSuccess,onError) {    
        this.log('Load page: ' + page_name);
        this.get('/api/ui/page/' + page_name,function(result) {
            self.log('Page ' + page_name + ' loaded!'); 
            callFunction(onSuccess,result);     
        }, function(errors) {
            self.log('Error loading page: ' + page_name);
            callFunction(onError,errors);       
        },"session");        
    };
} 

/**
 *  @class ApiResponse
 *  Api response object
 *  Api calls returns ApiResponse object
 */
function ApiResponse(response) {
      
    var status = "ok";
    var errors = [];
    var result = "";

    this.createEmpty = function() {
        status = "ok";
        errors = [];
        result = "";
    };

    this.init = function(response) {

        if (isEmpty(response) == true) {
            data = this.createEmpty();
            return;
        } else {
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
                if (isEmpty(errors[callback]) == false) {
                    return errors[callback];
                } else {
                    return false;
                }
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
        if (errors.length > 0) {
            return true;
        } 
        return false;
    };
}

/**
 *  @class Storage
 *  Cookie, localStorage, sessionStorage class
 */
function Storage() {

    var type = "cookie";
    
    this.set = function(name, value, time) {
        switch (type) {
            case "cookie": {
                this.setCookie(name, value, time);
                break;
            }
            default:{
                this.setCookie(name, value, time);
                break;
            }
        }
    };
    
    this.get = function(name) {
        switch (type) {
            case "cookie":{
                return this.getCookie(name);
                break;
            }
            default:{
                return this.getCookie(name);
                break;
            }
        }
    };

    this.setCookie = function(name, value, days) {
        var expires = "";
        if (isEmpty(days) == false) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = ";expires=" + date.toGMTString();           
        } 
        if (isArray(value) == true) {
            value = JSON.stringify(value);
        }
        document.cookie = name + "=" + value + expires + ";path=/";
    };

    this.getCookie = function(name) {
        var field_name = name + "=";
        cookie = document.cookie.split(';');
        var item = "";
        for(var i = 0;i < cookie.length; i++) {
            item = cookie[i];          
            while (item.charAt(0) == ' ') item = item.substring(1,item.length);
            if (item.indexOf(field_name) == 0) {
              return item.substring(field_name.length,item.length);
            }
        }
        return null;
    }

    this.setSession = function(name,value) {
        if (isObject(sessionStorage) == true) {
            sessionStorage.setItem(name,value);
            return true;            
        } else {
            return false;
        }
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
 *  @class Session
 *  Manage session
 * 
 */
function Session(id,recreate_interval) {

    var session_id = id;
    var recreate_handler = null;
    var self = this;

    this.getInfo = function(onDone,onError) {
        arikaim.get('/api/session/',function(result) {
            callFunction(onDone,result);
        },function(errors) {
            callFunction(onError,errors);
        },'session');
    };

    this.set = function(key,value,onDone) {
        var data = {'key': key,'value': value };
        arikaim.put('/api/session/',data,function(result) {
            callFunction(onDone,result);            
        });
    };

    this.getId = function() { 
        return session_id;
    };

    this.setRecreateInterval = function(interval_seconds) {
        recreate_handler = setInterval(this.recreate,interval_seconds * 1000);
    };

    this.removeRecreateInterval = function() {
        clearInterval(recreate_handler);
    };

    this.recreate = function(onDone) {
        arikaim.get('/api/session/restart/',function(result) {
            callFunction(onDone,result);     
        });
    };

    this.getInterval = function(lifetime) {
        var interval = lifetime  - 240;
        if (interval < 3600000) {
            interval = 36000000;
        }
        return interval;
    };

    this.init = function(recreate) {
        this.getInfo(function(result) {
            if (recreate == true) {
                var interval = self.getInterval(result.lifetime);
                arikaim.log('session recreate interval: ' + interval);
                self.setRecreateInterval(interval);
            }
        });
    };

    if (isNaN(recreate_interval) == false) {
        this.setRecreateInterval(recreate_interval);
    };    
}

/**
 *  @class Arikaim
 *  Main Arikaim CMS object
 * 
 */
function Arikaim() {
   
    if (isObject(Arikaim.instance) == true) {
        return Arikaim.instance;
    }

    var storage     = new Storage();         
    var host        = window.location.origin;
    var dev_mode    = true;
    var jwt_token   = storage.get('token');
    var services    = [];
    var default_language = "en";
    var errors = {};
    var self = this;    
    
    this.component  = new HtmlComponent(); 
    this.page       = new Page();
    this.form       = new Form(); 
    this.session    = new Session(storage.get("PHPSESSID"));
    this.version    = "1.0";

    this.init = function() {
        // check for jquery 
        window.onload = function() {
            if (isEmpty(window.jQuery) == true) {  
                console.log("Error: jQuery library missing.");
            } 
        }
        // load current page properties
        this.page.loadProperties("",function() {         
            self.log("\nArikaim CMS v" + self.version + "\n");
        },function() {
            self.log("Error loading page properties.");
        });        
    };

    this.connect = function(host,key,secret,onSuccess,onError) {
        this.setHost(host);
        var data = { api_key: key, api_secret: secret };
        this.post('/api/create/token/',data,function(result) {
            setToken(result);
            callFunction(onSuccess,result);
        },function(error) {
            console.log(error);
            callFunction(onError,error);
        });
    };

    this.getErrors = function() {
        return errors;
    };

    this.setErrors = function(errors_lits) {
        if (isObject(errors_lits) == true) {
            errors = errors_lits;
            return true;
        }
        return false;
    };

    this.clearErrors = function() {
        errors = {};
    }

    this.hasError = function() {
        if ($.isEmptyObject(errors) == true) {
            return false;
        }
        return true;
    };

    this.setLanguage = function(language_code) {
        if (isEmpty(language_code) == true) {
            var language_code = "en";
        }
        storage.setCookie('language',language_code,30);
        storage.setSession('language',language_code);
        this.page.reload();
    };

    this.getLanguage = function() {
        var language = storage.getSession('language');
        if (isEmpty(language) == true ) {
            language = storage.getCookie('language');
        }
        if (isEmpty(language) == true) {
            return default_language;
        }
        return language;
    };
    
    this.setToken = function(token) {
        jwt_token = token;
        storage.set('token',token);
    };

    this.clearToken = function() {
        jwt_token = '';
        storage.set('token','');
    };

    this.isValidToken = function() {
        if (jwt_token == false) return false;
        if (jwt_token == null)  return false;
        if (jwt_token == "")  return false;
        return true;
    };

    this.getToken = function() {
        return jwt_token;
    };

    this.log = function(msg) { 
        if (dev_mode == true) {
            console.log(msg);
        }
    };

    this.getBaseUrl = function() {
        return host + "/" + this.getPath().split('/')[1];
    };

    this.setDevMode = function(mode) { 
        dev_mode = mode;
        if (mode == true) {
            this.log("Development mode.\n");
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

    this.post = function(url,form_elament,onSuccess,onError,auth_type) {
        data = $(form_elament).serialize();
        this.apiCall(url,'POST',data,onSuccess,onError,auth_type);
    };

    this.get = function(url,onSuccess,onError,auth_type) {   
        this.apiCall(url,'GET',null,onSuccess,onError,auth_type);
    };

    this.delete = function(url,onSuccess,onError,auth_type) {   
        this.apiCall(url,'DELETE',null,onSuccess,onError,auth_type);
    };

    this.put = function(url,data,onSuccess,onError,auth_type) {   
        this.apiCall(url,'PUT',data,onSuccess,onError,auth_type);
    };

    this.patch = function(url,data,onSuccess,onError,auth_type) {   
        this.apiCall(url,'PATCH',data,onSuccess,onError,auth_type);
    };

    this.register = function(name,service_def) {
        this.log('Register service: ' + name);   
        this.services[name] = service_def;
    };  

    this.call = function(name,args) {
        this.log("Call Service: " + name);
        return this.services[name].apply(null, args || []); 
    };  

    this.includeCSSFile = function(file_url) {
        $('<link>').appendTo('head').attr({
            type: 'text/css', 
            rel: 'stylesheet',
            href: file_url
        });
    };

    this.includeScript = function(url,onSuccess,onError) {
        $.getScript(url).done(function(script,status) {
            callFunction(onSuccess,status);
        }).fail(function(jqxhr,settings,exception) {
            callFunction(onError,exception);
        });       
    }; 

    this.getComponent = function(component_name) {
        return this.component.get(component_name);
    };

    this.onComponentLoaded = function(component_name,callback) {
        return this.component.onLoaded(component_name,callback);
    };

    // auth_type - session , jwt
    this.getAuthHeader = function(auth_type) {

        var token = this.session.getId();
        if (isEmpty(jwt_token) == false) {
            token = jwt_token;
        }
        // default jwt
        var header = "Bearer " + jwt_token;
               
        switch (auth_type) {
            case "session":
                header = "Bearer " + token;
                break;
            case "jwt":
                header = "Bearer " + jwt_token;
                break;           
        }
        return header;
    };

    /**
     * ajax call
     * @param {*} url Request relative URL
     * @param {*} method Request method (GET,POST,PUT,DELETE,PATCH)
     * @param {*} request_data Ajax Request data 
     * @param {*} callback  Response handler function 
     * @param {*} auth_type Authentication type (session, jwt)
     */
    this.request = function(url,method,request_data,onSuccess,onError,auth_type) {
        var url = this.getBaseUrl() + url;
        var auth_header = this.getAuthHeader(auth_type);  
        if (isEmpty(request_data) == true) {
            request_data = null;
        }
        var header_data = null;
        if ((method == "GET") && (isObject(request_data) == true)) {     
            header_data = JSON.stringify(request_data);
            request_data = null;
        }

        this.log(method + ' request url: ' + url);    
        $.ajax({
            url: url,
            method: method,          
            data: request_data,
            beforeSend: function(request) {
                request.setRequestHeader('Authorization',auth_header);
                if (isEmpty(header_data) == false) {         
                    request.setRequestHeader('Params',header_data);
                }
            },
            success: function (data) {     
                callFunction(onSuccess,new ApiResponse(data));
            },
            error: function (xhr, status, error) {             
                arikaim.log("Error\n");
                arikaim.log("Request url: " + url + "\n");
                arikaim.log("Error details: " + error);
                arikaim.log("Response: " + xhr.responseText);
                response = new ApiResponse();
                response.addError(error);
                callFunction(onError,response.getErrors());
            }
        });   
    };

    this.apiCall = function(url,method,request_data,onSuccess,onError,auth_type) {
        this.request(url,method,request_data,function(response) {      
            if (response.hasError() == false) {  
                callFunction(onSuccess,response.getResult());                         
            } else {
                callFunction(onError,response.getErrors());                  
            }
        },function(errors) {
            callFunction(onError,errors);
        },auth_type);
    };

    // Singleton
    Arikaim.instance = this;
}

// Create Arikaim object 
var arikaim = new Arikaim();
arikaim.init();