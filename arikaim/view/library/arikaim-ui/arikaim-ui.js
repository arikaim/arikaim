/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/

if (typeof arikaim !== 'object') {
    throw new Error('Arikaim library not loaded!');   
}

function isEmptyElement(selector) {
    if (isEmpty(selector) == true) {
        return true;
    }

    return ($(selector).html().toString().trim() == '');
}

/**
 * Text helpers
 * @class Text
 */
function Text() {
    
    this.createSlug = function(string) {
        return string
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s+/g, "-")
            .replace(/[^\w\-]+/g, "")
            .replace(/\-\-+/g, "-")
            .replace(/^-+/, "")
            .replace(/-+$/, "");
    }
}

/**
 * @class Table
 *
 */
function Table() {
    var self = this;

    this.removeRow = function(rowId, emptyLabel, onEmpty) {
        emptyLabel = getDefaultValue(emptyLabel,'..');
        var parent = $(rowId).parent();
        $(rowId).remove();
        
        if (isEmptyElement(parent) == true) {
            var result = callFunction(onEmpty,parent);
            if (result !== false) {
                $(parent).append('<tr><td>' + emptyLabel + '</td></tr>');
            }           
        }
    };

    this.removeSelectedRows = function(selected) {
        if (isArray(selected) == false) {
            return false;
        }
        $.each(selected,function(index,value) {
            self.removeRow('#' + value);
        });
    };
}

/**
 *  @class TemplateEngine
 *  Simple template engine parse tags <% var name %>  
 */
function TemplateEngine() {    
    var tags = ['<%','%>'];

    var parseTemplateVariable = function(name) {        
        var regexp = new RegExp('[' + tags[0] + '][' + tags[1] + ']','gi');
        return name.replace(regexp,'').trim();      
    };

    this.render = function(text, data) {
        var value = '';
        var regexp = new RegExp(tags[0] + '([^' + tags[1] + ']+?)' + tags[1],'gi');
        var result = text.match(regexp);

        if (isArray(result) == false) {
            return text;
        }
        for (var i = 0; i < result.length; i++) {                      
            var templateVariable = parseTemplateVariable(result[0]);
            if (templateVariable != false) {
                value = getValue(templateVariable,data,'');
            }
            text = text.replace(result[0],value);
        }

        return text;
    };
}

/**
 * @class Form
 * Manage html forms
 */
function Form() {
    var self = this;

    this.clear = function(selector) {
        $(selector)[0].reset();
        $(selector).trigger('reset');
        $(selector).each(function() {  
            this.reset();
        }); 
    };

    this.populate = function (selector, data) {
        $.each(data,function (key, value) {
            $('[name="' + key + '"]',selector).val(value);
        });
    };

    this.serialize = function(selector) {
        var form = $(selector);
        return (form.length == 0) ? false : JSON.stringify(form.serializeArray());       
    };

    this.findSubmitButton = function(selector) {
        var button = $('input[type=submit]',selector);

        if ($(button).length == 0) {
            button = $(selector).find('.save-button');
        }
        if ($(button).length == 0) {
            button = $(selector).find('.submit-button');
        }

        return button;
    };

    this.onSubmit = function(selector, action, onSuccess, onError, submitButton) {
        var deferred = new $.Deferred();

        if (isEmpty(submitButton) == true) {
            var submitButton = this.findSubmitButton(selector);
        }

        $(selector).off();     
        $(selector).unbind();
        $(selector).on('submit',function(event) {
            self.clearErrors(selector);
            event.preventDefault();   
            self.disable(selector);
            arikaim.ui.disableButton(submitButton);
            var data = self.serialize(selector);

            if (self.validate(selector) == false) {    
                arikaim.ui.enableButton(submitButton);   
                self.enable(selector);  
                self.showValidationErrors(selector);
                deferred.reject();  
                callFunction(onError);               
            } else {
                // form is valid
                var actionResult = callFunction(action,data);
             
                if (isPromise(actionResult) == true) {                   
                    actionResult.done(function(result) {
                        arikaim.ui.enableButton(submitButton);                        
                        self.enable(selector); 
                        callFunction(onSuccess,result);
                        deferred.resolve(result); 
                    }).fail(function(errors) { 
                        arikaim.ui.enableButton(submitButton);                          
                        self.enable(selector);                     
                        self.addFieldErrors(selector,errors);
                        self.showErrors(errors);
                        deferred.reject(errors); 
                        callFunction(onError,errors); 
                    });
                } else {
                    arikaim.ui.enableButton(submitButton);                      
                    if (actionResult === true) {
                        deferred.resolve(data);  
                        callFunction(onSuccess,data);
                    }
                }
            }
        });

        return deferred.promise();
    };

    this.toObject = function(array) {        
        var result = {};
        if (isArray(array) == false) {
            return result;
        }
        for (var i = 0; i < array.length; i++){
            result[array[i]['name']] = array[i]['value'];
        }

        return result;
    };

    this.addFieldErrors = function(selector, errors) {
        if (isArray(errors) == false) {
            return false;
        }
        errors.forEach(function(error) {
            $(selector).form('add prompt',error.field_name,error.message);
        });
    };
    
    this.disable = function(selector, cssClass) {
        cssClass = getDefaultValue(cssClass,'disabled'); 
        $(selector).children().addClass(cssClass);
    };
    
    this.enable = function(selector, cssClass) {
        cssClass = getDefaultValue(cssClass,'disabled'); 
        $(selector).children().removeClass(cssClass);
    };

    this.addRules = function(selector, rules) {
        $(selector).form(rules);
        $(selector + ' :input').on('focus',function() {        
            self.clearErrors(selector);
        });
    };

    this.validate = function(selector) {
        $(selector).form('validate form');
        return $(selector).form('is valid');
    };

    this.showValidationErrors = function(selector) {       
        var message = $(selector).find('.errors.message');
        if (isObject(message) == true) {
            arikaim.ui.show(message);
        }
    };

    this.showMessage = function(options) {
        if (isObject(options) == false) {
            options = { message: options };
        }
        var selector = getValue('selector',options,null); 
        var cssClass = getValue('class',options,null);   
        var removeClass = getValue('removeClass',options,'error');

        if (isEmpty(selector) == true) {
            selector = $('form').find('.success');
        }
        var message = getValue('message',options,''); 
        var hide = getValue('hide',options,2000);  

        if (cssClass != null) {
            $(selector).addClass(cssClass).removeClass(removeClass);
        }
    
        arikaim.ui.show(selector);
        if (hide > 0) {
            $(selector).delay(hide).animate({ opacity: 0 }, 200);
        } 
        if ($(selector).find('.header').length != 0) {
            $(selector).find('.header').html(message);
        } else {
            $(selector).html(message);
        }       
    };

    this.clearErrors = function(selector) {      
        $(selector).find('.errors').html('');    
        $(selector).find('.errors').hide();
        $(selector).find('.error').find('.prompt').remove();
    };   

    this.showErrors = function(errors, selector, component) {
        if (isEmpty(selector) == true) {
            selector = $('form').find('.errors');
        }       
        var message = ''; 
        if (isArray(errors) == true) {
            for (var index = 0; index < errors.length; index++) {
                var error = errors[index];
              
                if (isObject(error) == true) {
                    var errorLabel = '';
                    if (isObject(component) == true) {                     
                        errorLabel = component.getProperty(error.field_name + '.label');
                    }
                    error = '<span>' + errorLabel + '</span> ' + error.message;                 
                }
                if (isString(error) == true) {
                    message += '<li>' + error + '</li>';
                }
            }
        } else {
            message = '<li>' + errors + '</li>';           
        }
      
        this.showMessage({
            selector: selector,
            message: message,
            hide: 0
        });        
    };
}

/**
 * @class ArikaimUI 
 * UI helpers
 */
function ArikaimUI() {
    var self = this;
    
    this.form = new Form();
    this.template = new TemplateEngine();
    this.table = new Table();

    this.button = function(selector, action, onSuccess, onError) {      
        $(selector).off();
        $(selector).on('click',function(event) {
            event.stopPropagation();
            var button = this;
            self.disableButton(button);
          
            var result = callFunction(action,this);
            if (isPromise(result) == true) {
                result.then(function(result) {
                    self.enableButton(button);                  
                    callFunction(onSuccess,result);
                }).catch(function(error) {
                    self.enableButton(button);                   
                    callFunction(onError,result);
                });
            } else {
                self.enableButton(button);              
                if (result !== false) {
                    callFunction(onSuccess,result);
                } else {
                    callFunction(onError,result);
                }
            }
        });
    };

    this.initImageLoader = function() {
        $.each($('img'),function() {
            var dataSrc = $(this).attr('data-src');
            if (dataSrc) {
                $(this).attr('src',dataSrc);
            }
        });
    };

    this.viewPasswordButton = function(selector, fieldSelector, toggleClass) {
        toggleClass = getDefaultValue(toggleClass,'slash');
        fieldSelector = getDefaultValue(fieldSelector,'.password-field');
        
        this.button(selector,function(element) {
            $(element).find('.icon').toggleClass(toggleClass);
            $(fieldSelector).attr('type',function(index, attr) {
                return (attr == 'text') ? 'password' : 'text';
            });
        });
    };

    this.getAttr = function(selector, name, defaultValue) {
        var value = $(selector).attr(name);
        return (isEmpty(value) == true) ? defaultValue : value;
    };

    this.menu = function(itemSelector,cssClass) {
        itemSelector = getDefaultValue(itemSelector,'.menu .item');
        cssClass = getDefaultValue(cssClass,'active');
        
        $(itemSelector).on('click',function() {
            $(itemSelector).removeClass(cssClass);
            $(this).addClass(cssClass);
        }); 
    };

    this.tab = function(selector, contentSelector, paramsList) {
        paramsList = getDefaultValue(paramsList,[]);
        paramsList.push('language'); 
        paramsList.push('uuid');
        paramsList.push('extension');
        selector = getDefaultValue(selector,'.tab-item');
        contentSelector = getDefaultValue(contentSelector,'tab_content');

        this.button(selector,function(element) {
            var component = $(element).attr('component');
            var params = {};
            if (isArray(paramsList) == true) {
                paramsList.forEach(function(value) {
                    var attr = self.getAttr(element,value,null);
                    if (attr != null) {
                        params[value] = attr;
                    }
                });
            }          
            self.setActiveTab(element,selector);
            return arikaim.page.loadContent({
                id: contentSelector,
                component: component,
                params: params
            });   
        });
    };

    this.setActiveTab = function(selector, itemsSelector) {      
        itemsSelector = getDefaultValue(itemsSelector,'.tab-item');
        $(itemsSelector).removeClass('active');
        $(selector).addClass('active');     
    };

    this.enableButton = function(element) {       
        $(element).removeClass('disabled loading');
    };

    this.disableButton = function(element, loadingOnly) {   
        loadingOnly = getDefaultValue(loadingOnly,false);

        if (loadingOnly == true) {
            $(element).addClass('loading');
        } else {          
            $(element).addClass('disabled loading');
        }            
    };

    this.show = function(element) {
        $(element).show();
        $(element).removeClass('hidden');
        $(element).removeClass('invisible');     
        $(element).css('visibility','visible');
        $(element).css('opacity','1');
    };

    this.hide = function(element,placeholder) {
        if (placeholder == true) {
            $(element).css('opacity','0');
        } else {
            $(element).hide();
            $(element).addClass('hidden');
            $(element).removeClass('visible');
            $(element).css('visibility','hidden');
        }
    };
    
    this.getChecked = function(selector) {
        var selected = [];
        $(selector + ':checked').each(function(index) {
            if (isEmpty($(this).val()) == false) {
                selected.push($(this).val());
            }           
        });     

        return { selected: selected };
    };

    this.cssClass = function(selector, value, cssClass) {
        if (value == true) {
            $(selector).addClass(cssClass);
        } else {
            $(selector).removeClass(cssClass);
        }
    };

    this.selectAll = function(element, itemClass, iconSelector) {
        itemClass = getDefaultValue(itemClass,'.selected-row');
        iconSelector = getDefaultValue(iconSelector,'#all_icon');

        var value = $(element).attr('data-value');
        if (value == 'select') {
            $(itemClass).prop('checked',true);               
            $(element).attr('data-value','unselect');
            $(iconSelector).addClass('check');
        } else{
            $(iconSelector).removeClass('check');               
            $(itemClass).prop('checked',false);
            $(element).attr('data-value','select');
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
    var name = null;
    var onContentReady = null;
    var defaultLoader = '<div class="ui active blue centered loader"></div>';  
    var language;

    this.loader = '';

    this.toastMessage = function(message) {
        if (isObject(message) == false) {
            message = { 
                class: 'success',
                message: message,
                position: 'bottom right'
            };
        } 
        message.position = getDefaultValue(message.position,'bottom right');
        $('body').toast(message);
    };

    this.setLoader = function(loaderHtml) {
        loader = loaderHtml
    };

    this.getLoader = function(code) {     
        var code = ((isEmpty(code) == true) && (isEmpty(this.loader) == true)) ? defaultLoader : this.loader;

        return $(code);
    };

    this.onContentReady = function(callback) {
        onContentReady = callback;
    };

    this.onReady = function(callback) {        
        $(document).ready(callback);
    };

    this.reload = function() {
        location.reload(true);
    };

    this.getProperty = function(property_name) {
        var data = components[name];
        return (isJSON(data) == true) ? JSON.parse(components[name]) : false;        
    };

    this.getPageName = function() {
        return name;
    };

    this.hasLib = function(libraryName) {
        return (properties.library.indexOf(libraryName) > 0) ? true : false;           
    };

    this.setProperties = function(params) {            
        name = params.name;
        properties = params; 
        language = params.language;

        return true;
    };

    this.removeLoader = function(selector) {
        selector = getDefaultValue(selector,'#loader');
        $(selector).remove();
    };

    this.showLoader = function(selector, loader, append) {
        append = getDefaultValue(append,false);
        loader = getDefaultValue(loader,this.getLoader());   
    
        if (append == true) {
            $(selector).append(loader);
        } else {
            $(selector).html(loader);
        }
      
        $('#loader').dimmer({});
    };

    this.setContent = function(element, content) {
        $(element).html(content);
    };

    this.replaceContent = function(element, content) {
        $(element).replaceWith(content);
    };
    
    this.loadProperties = function(name, onSuccess, onError) {
        name = getDefaultValue(name,'');  
        arikaim.log('Load page properties: ' + name);
        arikaim.get('/core/api/ui/page/properties/' + name,function(result) {          
            self.setProperties(result.properties);                     
            self.setLoader(getValue('properties.loader',result,''));
            arikaim.log('Page properties loaded!');
            callFunction(onSuccess,result);
        },function(errors) {
            arikaim.log('Error loading page properties: ' + name);
            callFunction(onError,errors);
        });
    };

    this.loadContent = function(params, onSuccess, onError) {       
        var componentName = getValue('component',params,'no-name');       
        var componentParams = getValue('params',params,'');
        var elementId = getValue('id',params);
        var element = getValue('element',params);
        var loaderCode = getValue('loader',params,null);
        var loaderClass = getValue('loaderClass',params,'');
        var replace = getValue('replace',params,false);
        var useHeader = getValue('useHeader',params,false);
        var includeFiles = getValue('includeFiles',params,true);

        if (isEmpty(elementId) == false) {
            element = '#' + elementId;
        }
        var loader = this.getLoader(loaderCode);
        if (isEmpty(loaderClass) == false) {
            loader.attr('class',loaderClass);
        }
        this.showLoader(element,loader);
    
        arikaim.component.load(componentName,function(result) { 
            self.removeLoader(); 
            if (replace == false) {
                arikaim.page.setContent(element,result.html);
            } else {
                $(element).replaceWith(result.html);
            }
            callFunction(onSuccess,result);                       
        },function(errors) {
            // errors load component
            self.removeLoader();
            self.showErrorMessage(params,errors);
            callFunction(onError,errors);   
        },componentParams,useHeader,includeFiles);
    };

    this.showErrorMessage = function(params,errors) {
        var elementId = getValue('id',params);
        var element = getValue('element',params);
        var message = { message: errors[0] };
        
        if (isEmpty(elementId) == false) {
            element = '#' + elementId;
        }      
        arikaim.component.load('components:message.error',function(result) { 
            arikaim.page.setContent(element,result.html); 
        },null,message);
    };

    this.showSystemErrors = function(errors,element) {
        element = getDefaultValue(element,'.error');
        $(element + ' ul').html('');
        this.show(element);
        if (isObject(errors) == true) {
            $.each(errors, function(key,value) {
                $(element + ' ul').append('<li>' + value + '</li>');
            });  
        } else {
            $(element).append('<li>' + errors + '</li>');
        }
    };

    this.loadPage = function(name,onSuccess,onError) {    
        this.log('Load page: ' + name);
        this.get('/core/api/ui/page/' + name,function(result) {
            arikiam.log('Page ' + name + ' loaded!'); 
            callFunction(onSuccess,result);     
        }, function(errors) {
            arikiam.log('Error loading page: ' + name);
            callFunction(onError,errors);       
        });        
    };
} 

/**
 * @class Component
 * @param {*} prop 
 */
function Component(prop) {
    
    var properties = {};

    this.getProperty = function(name) {
        return getObjectProperty(name,properties);
    };

    this.getProperties = function() {
        return properties;
    };

    this.setProperties = function(json) {
        if (isJSON(json) == true) {
            properties = JSON.parse(json);
            return true;
        }

        return false;
    };

    if (isEmpty(prop) == false) {
        this.setProperties(prop);
    }
}

/**
 * @class HtmlComponents
 * Container for all html components loaded 
 */
function HtmlComponent() {
    
    var self = this;
    var components = {};
   
    this.get = function(name) {
        return (isEmpty(components[name]) == false) ? components[name] : false;          
    };

    this.getAll = function() {
        return components;
    };

    this.set = function(name, properties) {
        var component = new Component(properties);
        components[name] = component;
    };

    this.resolveUrl = function(name, params) {
        var url = '/core/api/ui/component/' + name;
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

    this.loadContent = function(name, onSuccess, onError, params, useHeader) {
        return this.load(name,onSuccess,onError,params,useHeader,false);
    };
    
    this.loadProperties = function(name, params, onSuccess, onError) {        
        return arikaim.apiCall('/core/api/ui/component/properties/' + name,onSuccess,onError,params);      
    };

    this.loadDetails = function(name, params, onSuccess, onError) {    
        return arikaim.apiCall('/core/api/ui/component/details/' + name,onSuccess,onError,params);      
    };

    this.load = function(name, onSuccess, onError, params, useHeader, includeFiles) {  
        if (isEmpty(includeFiles) == true) {
            includeFiles = true;
        }               
        var url = (useHeader == true) ? this.resolveUrl(name,params) : this.resolveUrl(name,null);
       
        return arikaim.apiCall(url,'GET',params,function(result) {
            arikaim.component.set(name,result.properties);
            callFunction(onSuccess,result);
            if (includeFiles == true) {
                self.includeFiles(result,function(filesLoaded) {   
                    // event
                    arikaim.log('component ' + name + ' loaded!');           
                },function(url) {
                    var name = url.split('/').pop();                   
                    // event
                });
            }
        },function(errors) {
            arikaim.log('Error loading component ' + name);
            callFunction(onError,errors);
        });
    };

    this.includeFiles = function(response,onSuccess,onFileLoaded) {
        var jsFiles  = response.js_files;
        var cssFiles = response.css_files;
        var filesCount = 0;
        var loadedFiles = 0;
    
        if (cssFiles != false) {
            filesCount = filesCount + cssFiles.length;
            cssFiles.forEach(function(file) {              
                arikaim.includeCSSFile(file.url);
                loadedFiles++;
                if (loadedFiles == filesCount) {
                    callFunction(onSuccess,loadedFiles);
                } 
            }, this);
        }

        if (isEmpty(jsFiles) == false) {
            var files = Object.values(jsFiles);
            filesCount = filesCount + files.length;
            
            files.forEach(function(file) {  
                if (isEmpty(file.params) == false) {
                    if (arikaim.findScript(file.url) == false) {                      
                        var async = (file.params.indexOf('async') > -1) ? true : false;
                        var crossorigin = (file.params.indexOf('crossorigin') > -1) ? 'anonymous' : null;
                        arikaim.loadScript(file.url,async,crossorigin);
                    }        
                } else {
                    arikaim.includeScript(file.url,function() {
                        loadedFiles++;
                        callFunction(onFileLoaded,file.url);
                        if (loadedFiles == filesCount) {
                            callFunction(onSuccess,loadedFiles);
                        }               
                    });
                }                                            
            }, this);
        }
        if (loadedFiles == filesCount) {
            callFunction(onSuccess,loadedFiles);
        }
    };   
}

Object.assign(arikaim,{ text: new Text() });
Object.assign(arikaim,{ ui: new ArikaimUI() });
Object.assign(arikaim,{ page: new Page() });
Object.assign(arikaim,{ component: new HtmlComponent() });