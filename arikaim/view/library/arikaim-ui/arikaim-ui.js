/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

if (typeof arikaim !== 'object') {
    throw new Error('Arikaim library not loaded!');   
}

function isEmptyElement(selector) {
    if (isEmpty(selector) == true) {
        return true;
    }

    return ($(selector).length == 0 || $(selector).html().toString().trim() == '');
}

/**
 * Text helpers
 * @class Text
 */
function Text() {
      
    this.hexNumber = function(number, length) {
        var result = number.toString(16).toUpperCase();
        while(result.length < length) {
            result = '0' + result;
        }
           
        return result;
    };
   
    this.unicodeText = function(text) {
        var i;
        var result = '';
        for(i = 0; i < text.length; ++i) {           
            result += (this.isASCII(text,i) == true) ? text[i] : "\\u" + this.hexNumber(text.charCodeAt(i),4);              
        }

        return result;
    }

    this.isASCII = function(text, index) {
        index = getDefaultValue(index,0);
        return !(text.charCodeAt(index) > 126 || text.charCodeAt(index) < 32); 
    };

    this.replaceUmlautChars = function(text) {
        return text
            .toLowerCase()
            .replace(/ä/g,'ae')
            .replace(/æ/g,'ae')
            .replace(/å/g,'aa')
            .replace(/ö/g,'oe')
            .replace(/ø/g,'oe')
            .replace(/ü/g,'ue')
            .replace(/ß/g,'ss')
            .replace(/é/g,'e')
            .replace(/è/g,'e')
            .replace(/ó/g,'o');       
    };

    this.parseVersion = function(version) {
        version = getDefaultValue(version,'0.0.0');
        var tokens = version.split('.');

        return {
            major:  (isEmpty(tokens[0]) == true) ? 0 : parseInt(tokens[0]),
            minor:  (isEmpty(tokens[1]) == true) ? 0 : parseInt(tokens[1]),
            patch:  (isEmpty(tokens[2]) == true) ? 0 : parseInt(tokens[2])
        }       
    }

    this.versionCompare = function(version1, version2) {
        version1 = this.parseVersion(version1);
        version2 = this.parseVersion(version2);
       
        if (version1.major > version2.major) return true;                   
        if (version1.major == version2.major) {
            
            if (version1.minor > version2.minor) return true;
            if (version1.minor == version2.minor) {
                if (version1.patch > version2.patch) return true;
            }
        } 

        return false;
    };

    this.createSlug = function(text) {
        if (isEmpty(text) == true) {
            return '';
        }
        var text = text.toString().trim().toLowerCase();
        text = this.replaceUmlautChars(text);

        return text.replace(/\s+/g,'-').replace(/\-\-+/g,'-');
    }

    this.escapeHtml = function(html) {
        return html
            .replace(/&/g,"&amp;")
            .replace(/</g,"&lt;")
            .replace(/>/g,"&gt;")
            .replace(/"/g,"&quot;")
            .replace(/'/g,"&#039;");
    };

    this.htmlDecode = function(text) {
        var doc = new DOMParser().parseFromString(text,'text/html');
        return doc.documentElement.textContent;
    };
}

/**
 * @class Table
 *
 */
function Table() {
    var self = this;

    this.emptyRowCode = '<tr class="empty-row"><td colspan="<% colspan %>"><% empytLabel %></td></tr>';

    this.getEmptyRowHmtlCode = function(params) {
        return arikaim.ui.template.render(this.emptyRowCode,params);       
    };

    this.removeRow = function(rowId, emptyLabel, onEmpty, colSpan) {
        emptyLabel = getDefaultValue(emptyLabel,'..');
        colSpan = getDefaultValue(colSpan,1);
        var parent = $(rowId).parent();
        $(rowId).remove();
        
        if (isEmptyElement(parent) == true) {
            var result = callFunction(onEmpty,parent);
            if (result !== false) {
                $(parent).append(this.getEmptyRowHmtlCode({ 
                    colspan: colSpan,
                    empytLabel: emptyLabel
                }));
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
            var templateVariable = parseTemplateVariable(result[i]);
            if (templateVariable !== false) {
                value = getValue(templateVariable,data,'');
            }
            text = text.replace(result[i],value);
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

    this.getSettings = function() {
        return (isEmpty($.fn.form.settings) == true) ? null : $.fn.form.settings;        
    };

    this.onError = function(callback) {
        if (isEmpty($.fn.form.settings) == false) {
            $.fn.form.settings.onFailure = callback;
        }
    };

    this.clear = function(selector) {
        $(selector)[0].reset();
        $(selector).trigger('reset');
        $(selector).each(function() {  
            this.reset();
        }); 
        this.clearErrors(selector);
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

    this.serialize = function(selector, replaceFields) {
        var data = $(selector).serializeArray();
        if (isEmpty(replaceFields) == true) {
            return data;
        }
        Object.entries(replaceFields).forEach(function(item) {           
            data = self.replaceValue(data,item[0],item[1]);
        });

        return data;
    };

    this.replaceValue = function(formData, fieldName, fieldValue) {
        formData.forEach(function(item,index) {
            if (item.name == fieldName) {
                formData[index].value = fieldValue
            }
        });

        return formData;
    };

    this.onSubmit = function(selector, action, onSuccess, onError, submitButton) {
        if (isEmpty(submitButton) == true) {
            var submitButton = this.findSubmitButton(selector);
        }
        $(selector).off();     
        $(selector).unbind();

        $(selector).on('submit',function(event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (window.event && window.event.keyCode == 13) {
                // prevent default form submit 
                return false;
            }
             
            self.clearErrors(selector);
            self.disable(selector);
            arikaim.ui.disableButton(submitButton);
            var data = self.serialize(selector);

            if (self.validate(selector) == false) {    
                arikaim.ui.enableButton(submitButton);   
                self.enable(selector);  
                self.showValidationErrors(selector);
                callFunction(onError);               
            } else {
                // form is valid
                var actionResult = callFunction(action,data);
             
                if (isPromise(actionResult) == true) {                      
                    actionResult.then(function(result) {
                        arikaim.ui.enableButton(submitButton);                        
                        self.enable(selector); 
                        callFunction(onSuccess,result);
                    }).catch(function(errors) {                                          
                        if (isObject(errors) == true && isArray(errors) == false) {
                            errors = '';                         
                        }

                        arikaim.ui.enableButton(submitButton);                          
                        self.enable(selector);                     
                        self.addFieldErrors(selector,errors);
                        
                        if (isEmpty(errors) == false) {
                            self.showErrors(errors);
                        }
                      
                        callFunction(onError,errors); 
                    });
                } else {
                    if (actionResult === false) {
                        arikaim.ui.enableButton(submitButton);    
                        callFunction(onError,data);
                    }                                    
                    if (actionResult === true || isEmpty(actionResult) == true) {
                        arikaim.ui.enableButton(submitButton);    
                        callFunction(onSuccess,data);
                    }
                }
            }
        });
    };

    this.toObject = function(array) {        
        var result = {};
        if (isArray(array) == false) {
            return result;
        }
        for (var i = 0; i < array.length; i++) {
            result[array[i]['name']] = array[i]['value'];
        }

        return result;
    };

    this.addFieldErrors = function(selector, errors) {
        if (isArray(errors) == false) {
            return false;
        }
    
        errors.forEach(function(error) {
            if (isEmpty(error.field_name) == false) {
                $(selector).form('add prompt',error.field_name,error.message);
            }
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

    this.buildRules = function(selector, rules) {       
        var fields = $(selector).find('input, textarea, select');
        if (isEmpty(rules) == true) {
            rules = {   
                fields: {},
                inline: false,
            };
        }
    
        $.each(fields,function(index,field) {
            var fieldName = $(field).attr('name');
            if (isEmpty(fieldName) == false) {
                if (isEmpty(rules.fields[fieldName]) == true) {
                    rules.fields[fieldName] = self.createRule(field);
                }  
            }                     
        });
    
        return rules;
    };

    this.createRule = function(field) {
        var rule = $(field).attr('rule');
        var ruleValue = $(field).attr('rule-value');
        var optional = $(field).attr('optional');
        var fieldId = $(field).attr('id');  
        var name = $(field).attr('name');  
        var errorPrompt = $(field).attr('error-prompt');
        errorPrompt = (isEmpty(errorPrompt) == false) ? errorPrompt.split(',') : null;
           
        var result = {
            identifier: (isEmpty(fieldId) == true) ? name : fieldId
        };
        if (isEmpty(rule) == true) {
            result.optional = true;
            return result;
        }
        var items = rule.split(',');
        var rules = [];
        $.each(items,function(index,item) {
            var ruleItem = {
                type: item
            };
           
            if (isEmpty(errorPrompt) == false) {
                ruleItem.prompt = errorPrompt[index];
            }
            if (isEmpty(ruleValue) == false) {
                ruleItem.value = ruleValue;
            }
            rules.push(ruleItem);           
        });
        result.rules = rules;
        result.optional = (optional == 'true') ? true : false;
                      
        return result;
    };

    this.addValidationRule = function(name, callback) {
        if (isFunction($.fn.form.settings.rules[name]) == false) {
            $.fn.form.settings.rules[name] = callback;
        }
    };

    this.addRules = function(selector, rules) {
        // custom rules 
        this.addValidationRule('scriptTag',function(value) {
            var regexp = /<script\b[^>]*>([\s\S]*?)/gmi          
            return !value.match(regexp);
        });
        this.addValidationRule('htmlTags',function(value) {
            var regexp = /<[a-z][\s\S]*>/i       
            return !value.match(regexp);
        });

        rules = this.buildRules(selector,rules);
       
        if (isEmpty(rules.onFailure) == true) {
            rules.onInvalid = function(error) {
                var message = $(selector).find('.errors.message');
                if ($(message).is(':empty') == true) {
                    $(selector).form('add prompt',$(this).attr('name'),error);              
                }
            }
        };        

        $(selector).form(rules);
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
        var selector = getValue('selector',options,'form'); 
        var cssClass = getValue('class',options,null);   
        var removeClass = getValue('removeClass',options,'error');
        var message = getValue('message',options,''); 
        var error = getValue('error',options,false); 
        var hide = getValue('hide',options,2000);  

        if (error == true) {
            selector = $(selector).find('.error.message');
        } else {
            selector = $(selector).find('.success.message');
        }
       
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
        $(selector).find('.errors.message').html('');
        $(selector).find('.errors').html('');    
        $(selector).find('.errors.list').remove();
        $(selector).find('.errors').hide();
        $(selector).find('.error').find('.prompt').remove();
    };   

    this.showErrors = function(errors, selector, component) {
        if (isEmpty(selector) == true) {
            selector = 'form';
        }       
        var message = '<ul>'; 
        if (isArray(errors) == true) {
            for (var index = 0; index < errors.length; index++) {
                var error = errors[index];
              
                if (isObject(error) == true) {
                    var errorLabel = '';                  
                    error = '<span>' + errorLabel + '</span> ' + error.message;                 
                }
                if (isString(error) == true) {
                    message += '<li>' + error + '</li>';
                }
            }
        } else {
            if (isEmpty(errors) == false) {
                message = '<li>' + errors + '</li>';      
            }                
        }
        message += '</ul>';

        if (isEmpty(message) == false) {
            this.showMessage({
                selector: selector,
                message: message,
                error: true,
                hide: 0
            });        
        }
    };   
}

/**
 * @class ArikaimUI 
 * UI helpers
 */
function ArikaimUI() {
    var self = this;
    var version = '1.4.24';

    this.form = new Form();
    this.template = new TemplateEngine();
    this.table = new Table();

    this.getVersion = function() {
        return version;
    } 

    this.getAttributes = function(element) {
        var attrs = {};
        $.each(element.attributes, function (index,attribute) {
            attrs[attribute.name] = (isEmpty(attribute.value) == true) ? null : attribute.value;
        });       
        return attrs;
    };

    this.loadComponent = function(params, onSuccess, onError) {
        return arikaim.page.loadContent(params, onSuccess, onError);
    };

    this.subscribe = function(componentId, variableName, callback) {
        return arikaim.component.subscribe(componentId,variableName,callback);
    }

    this.loadLibrary = function(name, onSuccess, onError) {
        return arikaim.component.loadLibrary(name,onSuccess,onError);
    }

    this.getComponent = function(id) {
        return arikaim.component.get(id);
    }

    this.withComponent = function(id, callback) {
        var component = this.getComponent(id);
        if (isObject(component) == false) {
            return false;
        }

        return callFunction(callback,component);
    };

    this.getComponents = function() {
        return arikaim.component.getAll();
    }

    this.loadComponentButton = function(selector, action, onSuccess, onError) {
        this.button(selector,function(button) {
            var props = self.getAttributes(button);

            if (isEmpty(props['params']) == false) {
                var params = {};
                var itemValue;
                var items = props['params'].split(',');              
                items.forEach(function(item) { 
                    var param = item.split(':');
                    itemValue = (param[1] === 'false') ? false : param[1];
                    itemValue = (itemValue === 'true') ? true : itemValue;
                    params[param[0]] = itemValue;
                });
                props['params'] = params;
            }
            callFunction(action,button);

            return self.loadComponent(props,onSuccess,onError);
        });
    };

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

    this.isActive = function(selector) {
        if ($(selector).hasClass('active') == true) {
            return true;
        }
        if ($(selector).attr('active') == 'true') {
            return true;
        }
        return false;
    };
 
    this.setActiveButton = function(selector, groupSelector) {
        var group = $(groupSelector).children();
        if (group.length > 0) {
            $.each(group,function(index,button) {
                $(button).removeClass('active');
                $(button).attr('active','false');
            });
        }
        $(selector).addClass('active');
        $(selector).attr('active','true');
    };

    this.setButtonGroupInactive = function(groupSelector) {
        var group = $(groupSelector).children();
        if (group.length > 0) {
            $.each(group,function(index,button) {
                $(button).removeClass('active');
                $(button).attr('active','false');
            });
        }
    };

    this.toggleButton = function(options, onSuccess, onError) {
        var selector = (isObject(options) == true) ? options.selector : options;
        var groupSelector = getValue('groupSelector',options,null);  
        var action = getValue('action',options,null);

        this.button(selector,function(button) {
            if (self.isActive(selector) == false) {         
                self.setActiveButton(selector,groupSelector)                
            } else {              
                $(selector).removeClass('active');
                $(selector).attr('active','false');
            }
            callFunction(action,$(selector));
        },onSuccess,onError);
    }

    this.setEmptyImageOnError = function(selector, imageSrc) {
        $(selector).on('error',function() {
            if (isString(imageSrc) == true) {
                $(this).attr('src',imageSrc);
            } 
            callFunction(imageSrc,this);          
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

    this.loadImage = function(url, onSuccess, onError) {
        var image = new Image();
        image.onload = function(image) {
            callFunction(onSuccess,image);
        };
        image.onerror = function(image) {
            callFunction(onError,image);
        };
        image.src = url;
        
        return image;
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

    this.enableButton = function(element, loaderIconSelector) {    
        loaderIconSelector = getDefaultValue(loaderIconSelector,'.loader-icon');  
        $(element).find(loaderIconSelector).hide();
    
        $(element).removeClass('disabled loading');
        $(element).removeAttr('disabled');
    };
     
    this.disableButton = function(element, loadingOnly, loaderIconSelector) {   
        loaderIconSelector = getDefaultValue(loaderIconSelector,'.loader-icon'); 
        $(element).find(loaderIconSelector).show();

        loadingOnly = getDefaultValue(loadingOnly,false);

        if (loadingOnly == true) {
            $(element).addClass('loading');
        } else {          
            $(element).attr("disabled", true);
            $(element).addClass('disabled loading');
        }            
    };

    this.show = function(selector, options, removeClasses) {
        removeClasses = getDefaultValue(removeClasses,['hidden','invisible'])
        $(selector).show(options);
        $(selector).removeClass(removeClasses);      
        $(selector).css('visibility','visible');
        $(selector).css('opacity','1');
    };

    this.toggle = function(selector, options, removeClasses, placeholder) {
        var element = (isObject(selector) == true) ? selector.selector : selector;
        var value = getValue('value',selector,this.isHidden(element));

        if (value == true) {
            this.show(element,options,removeClasses);
        } else {
            this.hide(element,placeholder,options);
        }
    };

    this.isHidden = function(selector) {
        if ($(selector).css('display') == 'none' || isEmpty($(selector).css('display') == true)) {
            return true;
        } 
        if ($(selector).is(':visible') == false) {
            return true;
        }
        if ($(selector).is(':hidden') == true) {
            return true;
        }             
        if ($(selector).hasClass('hidden') == true) {
            return true;
        }
        if ($(selector).attr('opacity') == '0') {
            return true;
        }

        return false;
    };

    this.hide = function(selector, placeholder, options) {
        if (placeholder == true) {
            $(selector).css('opacity','0');
        } else {
            $(selector).hide(options);
            $(selector).addClass('hidden');
            $(selector).removeClass('visible');
            $(selector).css('visibility','hidden');
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
 * 
 *  @class Page 
 */
function Page() {
    var self = this;
    var properties = {};  
    var defaultLoader = '<div class="ui active blue centered loader" id="loader"></div>';  
   
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

    this.setLoader = function(code) {
        this.loader = code;
    };

    this.getPageComponents = function() {
        var components = [];
        $('.component-file').each(function(index, item) {
            var name = $(item).attr('component-name');
            var id = $(item).attr('component-id');
            var type = $(item).attr('component-type');

            if (isEmpty(name) == false) {
                components.push({
                    name: name,
                    id: id,
                    type: type
                }); 
            }          
        });
        
        return components;
    };

    this.createComponentInstance = function(id, name, type) {
        // init onLoaded callback
        var component = arikaim.component.create(id,name,type);  
        var callback = arikaim.component.getLoadedListener(name);

        arikaim.component.setLoadedListener(id,callback);

        var result = callFunction(callback,component);            
        component = (result instanceof ArikaimComponent) ? result : component;
         // add component object      
        if (isObject(component) == true) {   
            arikaim.component.add(component);
        }

        arikaim.log('Component instance:' + component.getName() + ' type:' + component.getType() + ' id:' + component.getId());
    }

    this.initComponentInstances = function() {
        $('.component-instance').each(function(index, item) {
            var name = $(item).attr('component-name');
            var id = $(item).attr('component-id');
            var type = $(item).attr('component-type');

            self.createComponentInstance(id,name,type);
        });        
    };

    this.init = function() {
        console.log('Arikaim UI version ' + arikaim.ui.getVersion());      
        arikaim.component.dispatchLoadedEvent(this.getPageComponents(),{});

        this.initComponentInstances();
    };

    this.getLoader = function(code) {     
        if (isEmpty(code) == false) {
            return code;
        } 
        if (isEmpty(this.loader) == false) {
            return this.loader;
        }
        code = $('#loader-code').html();

        return (isEmpty(code) == true) ? defaultLoader : code;            
    };

    this.getLoaderOptions = function() {
        var loader = $('#loader-code')[0];
       
        if (isEmpty(loader) == false) {
            return arikaim.ui.getAttributes(loader);
        }
        return {};
    };
    
    this.reload = function() {
        location.reload();
    };

    this.hasLib = function(libraryName) {
        return (properties.library.indexOf(libraryName) > 0) ? true : false;           
    };

    this.setProperties = function(params) {            
        properties = params; 
    };

    this.removeLoader = function(selector) {
        selector = getDefaultValue(selector,'#loader');
        $(selector).remove();
    };

    this.showLoader = function(selector, loader, append) {
        var options = this.getLoaderOptions();
        append = getDefaultValue(append,options.append);
        loader = getDefaultValue(loader,this.getLoader());   
        
        if (append == true || append == 'true') {
            $(selector).append(loader);
        } else {
            $(selector).html(loader);
        }     
    };

    this.loadContent = function(params, onSuccess, onError) {   
        // component name to load  
        var componentName = getValue('component',params,null);
        componentName = (isEmpty(componentName) == true) ? getValue('name',params,null) : componentName;                   
        // mount element id (parent element id)
        var elementId = getValue('id',params);
        elementId = (isEmpty(elementId) == true) ? getValue('mountTo',params,null) : elementId;  
        elementId = (isEmpty(elementId) == true) ? getValue('mountto',params,null) : elementId;     
        // options
        var componentParams = getValue('params',params,'');
        var element = getValue('element',params);
        var loaderCode = getValue('loader',params,null);
        var loaderClass = getValue('loaderClass',params,'');
        var replace = getValue('replace',params,false);
        var append = getValue('append',params,false);
        var prepend = getValue('prepend',params,false);
        var focus = getValue('focus',params,false);
        var hideLoader = getValue('hideLoader',params,false);
        var useHeader = getValue('useHeader',params,false);
        var method = getValue('method',params,'GET');
        var includeFiles = getValue('includeFiles',params,true);
        var disableRedirect = getValue('disableRedirect',params,false);
       
        replace = (replace === 'true') ? true : replace;
        append = (append === 'true') ? true : append;
        prepend = (prepend === 'true') ? true : prepend;

        if (isObject(elementId) == true) {
            element = elementId;
        }
        
        if (isEmpty(elementId) == false && isObject(elementId) == false) {
            element = '#' + elementId;
        }

        if (isEmpty(loaderClass) == false) {
            $('#loader').attr('class',loaderClass);
        }

        if (append !== true && hideLoader !== true && prepend != true) {
            this.showLoader(element,loaderCode);
        }

        arikaim.component.load(componentName,function(result) { 
            self.removeLoader(); 
            if (append == true) {              
                $(element).append(result.html);                   
            } else if (replace == true) {
                $(element).replaceWith(result.html);      
            } else if (prepend == true) {
                $(element).prepend(result.html); 
            } else {
                $(element).html(result.html);
            }          
            
            if (focus == true) {
                // set focus
                $(element).attr('tabindex',0).focus();
            }

            if (isArray(result.component_instances) == true) {
                // create component instances
                result.component_instances.forEach(function(item) {
                    arikaim.page.createComponentInstance(item.id,item.name,item.type);
                }); 
            }

            // dispatch load components
            arikaim.component.dispatchLoadedEvent(result.components,result,onSuccess); 
           
        },function(errors,options) {
            // errors load component
            self.removeLoader();
            self.showErrorMessage(params,errors);
            if (disableRedirect == false && isEmpty(options.redirect) == false) {
                // redirect to error or login page
                arikaim.loadUrl(options.redirect,true);
            } 
          
            callFunction(onError,errors,null,options);   
        },componentParams,useHeader,includeFiles,method);
    };

    this.showErrorMessage = function(params, errors) {
        var elementId = getValue('id',params);
        var element = getValue('element',params);
        var message = { message: errors[0] };
        
        if (isEmpty(elementId) == false) {
            element = '#' + elementId;
        }      
        arikaim.component.load('semantic~message.error',function(result) { 
            $(element).html(result.html);            
        },null,message);
    };

    this.showSystemErrors = function(errors, element) {
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
} 

/**
 * Arikaim component proxy handler
 */
function ArikaimComponentProxy() {
    this.set = function(target, property, value, receiver) {
        if (typeof value === 'function') {           
            target[property] = value;
            return true;
        }
        target.set(property,value);  
        return true;             
    };

    this.get = function(target, property, receiver) {          
        if (typeof target[property] === 'undefined') {
            return target.get(property);            
        }  
        if (typeof target[property] === 'function') {           
            return function() {
                return target[property].apply(target,arguments);
            }
        };

        return target.get(property);               
    };

    this.apply = function(target, thisArg, argumentsList) {              
        return target(argumentsList[0],argumentsList[1],argumentsList[2],argumentsList[3]);
    };

    this.getPrototypeOf = function(target) {
        return ArikaimComponent.prototype;
    }
}

/**
 * Arikaim component
 * @class ArikaimComponent
 */
function ArikaimComponent(id, name, type, parentId, props) {       
    var id = getDefaultValue(id,null);
    var name = getDefaultValue(name,null);
    var type = getDefaultValue(type,null);
    var props = getDefaultValue(props,[]);
    var vars = getDefaultValue(vars,[]);
    var element = (isEmpty(id) == true) ? null : document.getElementById(id);
    // parent node
    var parentId = getDefaultValue(parentId,(isObject(element) == true) ? element.parentNode.id : null);
    var parent = (isEmpty(parentId) == true) ? null : document.getElementById(parentId);

    this.reload = function(onSuccess) { 
        if (isEmpty(this.getParentId()) == true) {
            console.error('Parent node id is empty');
            return;
        }

        return arikaim.ui.loadComponent({
            mountTo: this.getParentId(),
            component: this.getName(),
            params: arikaim.ui.getAttributes(this.getElement())
        },function(result) {
            callFunction(onSuccess,result);
        });
    };

    this.hasParent = function() { 
        return (parent instanceof Element) ? true : false;         
    };

    this.isValid = function() {      
        if (element instanceof Element) {
            return true;            
        }

        console.warn('Not valid component Id: ' + id);          
        return false;
    }

    this.getName = function() {    
        return name;
    };

    this.getId = function() {
        return id;
    };

    this.getParentId = function() {
        return parentId;
    };

    this.getParent = function() {
        return parent;
    };

    this.getElement = function() {
        return element;
    };

    this.getType = function() {
        return type;
    };

    this.get = function(name) {
        if (this.isValid() == false) {
            return getDefaultValue(vars[name],null);
        }

        return element.getAttribute(name);
    }
    
    this.set = function(name, value) {
        if (this.isValid() == false) {
            vars[name] = value;
        } else {
            element.setAttribute(name,value);
        }
       
        // emit event 
        arikaim.component.emitEvent(this.getId(),name,value);         
    }

    this.html = function(html) {
        if (this.isValid() == false) {
            return false;
        }

        if (isEmpty(html) == true) {
            return element.innerHTML;
        }

        element.innerHTML = html;
    };

    this.getVar = function(name) {
        return getDefaultValue(vars[name],null);
    };

    this.setVar = function(name, value) {
        vars[name] = value;
        // emit event 
        arikaim.component.emitEvent(this.getId(),name,value);
    };

    this.on = function(name, callback) {
        if (this.isValid() == false || isEmpty(name) == true) {
            return false;
        }
        
        $(element).off(name,'**');
        $(element).on(name,callback);
    }

    this.off = function(name, callback) {
        if (this.isValid() == false || isEmpty(name) == true) {
            return false;
        }
        
        callback = getDefaultValue(callback,'**');
        $(element).off(name,callback);
    }

    this.onChange = function(variableName, callback) {
        this.subscribe(this.getId(),variableName,callback);
    };

    this.subscribe = function(componentId, name, callback) {
        arikaim.component.subscribe(componentId,name,callback,this.getId());        
    };

    this.remove = function() {
        arikaim.component.remove(this.getId());   
    };
}

/**
 * @class HtmlComponents
 * Container for all html components loaded 
 */
function HtmlComponents() {
    var self = this; 

    this.events = new Events();
    this.componentsList = [];
    this.loadedScripts = [];
    this.loadedListeners = [];

    this.emitEvent = function(componentId, variableName, value) {
        this.events.emit(getEventName(componentId,variableName),value);
    };

    this.subscribe = function(componentId, variableName, callback, subscriberId) {
        if (isFunction(callback) == false) {
            return false;
        }
        if (isObject(componentId) == true) {
            componentId = componentId.getId();
        }
        
        subscriberId = (isEmpty(subscriberId) == true) ? componentId : subscriberId;
        var eventName = getEventName(componentId,variableName);
        var subscriberName = getSubscriberName(subscriberId,variableName);

        // add listener
        this.events.addListener(eventName,callback,subscriberName);
    };

    function getEventName(componentId, name) {       
        return 'ui-component:' + componentId + ':' + name;
    }

    function getSubscriberName(componentId, name) {       
        return 'ui-component-subscriber-' + componentId + '-' + name;
    }

    this.clear = function() {
        this.loadedScripts = [];
        this.loadedListeners = [];
        this.componentsList = [];
    };

    this.create = function(id, name, type, parentId, props) { 
        return new Proxy(new ArikaimComponent(id,name,type,parentId,props),new ArikaimComponentProxy());
    };

    this.getAll = function() {
        return this.componentsList;
    }

    this.get = function(id) {
        var component = this.componentsList[id];
        return (isObject(component) == true) ? component : null;
    }

    this.add = function(component) {
        if (component instanceof ArikaimComponent) {
            this.componentsList[component.getId()] = component;
            return true;
        }

        return false;
    }

    this.remove = function(id) {
        delete this.componentsList[id];
        $('#' + id).remove();
    }
   
    this.createComponentId = function(name) {
        var pos = name.indexOf(':');
        if (pos > -1) {
            name = name.substr(pos + 1);
        }
        pos = name.indexOf('>');
        if (pos > -1) {
            name = name.substr(pos + 1);
        }
        pos = name.indexOf('::');
        if (pos > -1) {
            name = name.substr(pos + 1);
        }

        name = name.replace('~','-');
      
        return name;
    };

    this.getCurrentComponent = function() {
   
        if (isEmpty(document.currentScript) == false) {
            var name = $(document.currentScript).attr('component-name');
            var id = $(document.currentScript).attr('component-id');
            var type = $(document.currentScript).attr('component-type');  
            if (isEmpty(name) == false) {
                return this.create(id,name,type);  
            }           
        }
      
        return (isObject(window['arikaimComponent']) == true) ? window['arikaimComponent'] : this.create();
    };

    this.getLoadedListener = function(key) {
        return this.loadedListeners[key];
    };

    this.setLoadedListener = function(key, callback) {
        this.loadedListeners[key] = callback;
    } 

    this.onLoaded = function(callback, component) {       
        component = (isEmpty(component) == true) ? this.getCurrentComponent() : component;        
       
        if (isEmpty(component.getName()) == false && isFunction(callback) == true) {          
            this.loadedListeners[component.getName()] = callback;                  
        }
    };

    this.dispatchLoadedEvent = function(components, params, onSuccess) {

        components.forEach(function(item) {         
            var component = self.create(
                getValue('id',item,''),
                getValue('name',item,null),                
                getValue('type',item,'arikaim')
            );
        
            var callback = self.loadedListeners[component.getId()];
            if (isEmpty(callback) == true) {
                callback = self.loadedListeners[item.name];
            }

            if (isFunction(callback) == false) {
                callback = self.loadedListeners[component.getName()]
            }

            var result = callFunction(callback,component);    
            component = (result instanceof ArikaimComponent) ? result : component;
            // add component object         
            self.add(component);

            arikaim.log('Component loaded:' + component.getName() + ' type:' + component.getType() + ' id:' + component.getId());
        });
        
        callFunction(onSuccess,components,params);
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

    this.loadContent = function(name, onSuccess, onError, params, useHeader, method) {
        return this.load(name,onSuccess,onError,params,useHeader,false,method);
    };
    
    this.loadProperties = function(name, params, onSuccess, onError) {        
        return arikaim.apiCall('/core/api/ui/component/properties/' + name,onSuccess,onError,params);      
    };

    this.loadDetails = function(name, params, onSuccess, onError) {    
        return arikaim.apiCall('/core/api/ui/component/details/' + name,onSuccess,onError,params);      
    };

    this.loadLibraryFiles = function(name, onSuccess, onError) {
        arikaim.get('/core/api/ui/library/' + name,function(requestResult) { 
            callFunction(onSuccess,requestResult);                      
        },onError);
    };

    this.loadLibrary = function(name, onSuccess, onError) {
        arikaim.get('/core/api/ui/library/' + name,function(requestResult) {           
            self.includeFiles(requestResult,function(result) {  
                arikaim.log('Library ' + name + ' loaded.');             
                callFunction(onSuccess,requestResult);
            });
        },onError);
    };

    this.load = function(name, onSuccess, onError, params, useHeader, includeFiles, method, options) {  
        options = getDefaultValue(options,{});
        includeFiles = (isEmpty(includeFiles) == true) ? true : includeFiles;                     
        method = getDefaultValue(method,'GET');
        if (method.toUpperCase() != 'GET') {
            useHeader = false;
        }
        var url = (useHeader == true) ? this.resolveUrl(name,params) : this.resolveUrl(name,null);
        return arikaim.apiCall(url,method,params,function(result) {                   
            if (includeFiles == true) {
                self.includeFiles(result,function() {   
                    callFunction(onSuccess,result);                                       
                });
            } else {
                callFunction(onSuccess,result);                          
            }
        },function(errors,options) {
            arikaim.log('Error loading component ' + name);
            callFunction(onError,errors,null,options);
        });
    };

    this.loadScripts = function(files) {
        var deferred = jQuery.Deferred();
        
        function loadScript(i) {
            if (i >= files.length) {
                deferred.resolve();
                return;
            }
            if (self.loadedScripts.indexOf(files[i].url) !== -1) {
                // script is loaded load next               
                loadScript(i + 1);
                return;
            }

            window['arikaimComponent'] = self.create(
                getDefaultValue(files[i].component_id,null),
                getDefaultValue(files[i].component_name,null),
                getDefaultValue(files[i].component_type,null)
            )
               
            if (isEmpty(files[i].params) == false) {
                var async = (files[i].params.indexOf('async') > -1) ? true : false;
                var crossorigin = (files[i].params.indexOf('crossorigin') > -1) ? 'anonymous' : null;  
            
                arikaim.loadScript(files[i].url,async,crossorigin);
                self.loadedScripts.push(files[i].url);                
                loadScript(i + 1);
            } else {
                arikaim.includeScript(files[i].url,function() {  
                    self.loadedScripts.push(files[i].url);                          
                    loadScript(i + 1);
                });    
            } 
        }
        loadScript(0);

        return deferred;    
    };

    this.includeFiles = function(response, onSuccess, onError) {
        var loadedFiles = 0;

        // load css files
        if (response.css.length > 0) {           
            response.css.forEach(function(file) {       
                if (self.loadedScripts.indexOf(file.url) !== -1) {
                    return true;
                }
                arikaim.includeCSSFile(file.url);
                self.loadedScripts.push(file.url);   
            }, this);
        }
        
        // load js files
        var files = Object.values(response.js);
        if (files.length == 0) {
            callFunction(onSuccess,loadedFiles);
            return;
        }

        this.loadScripts(files).done(function() {
            callFunction(onSuccess,loadedFiles);
        }).catch(function() {
            callFunction(onError);
        });
    };   
}

Object.assign(arikaim,{ text: new Text() });
Object.assign(arikaim,{ page: new Page() });
Object.assign(arikaim,{ component: new HtmlComponents() });
Object.assign(arikaim,{ ui: new ArikaimUI() });

// init
$(window).on('load',function() {
    arikaim.page.init();
});
