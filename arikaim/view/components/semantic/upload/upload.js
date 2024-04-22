/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function FileUpload(formId, options) {      
    var defaultLabel = "Drag & Drop file or <span class='filepond--label-action'> Browse </span>";
    var filepondId;

    this.options = {};

    this.setDefaults = function(options) {
        $.fn.filepond.setDefaults(options);
    };
    
    this.registerPlugin = function(plugIn) {
        if (isEmpty(plugIn) == false && isObject(FilePond) == true) {
            $.fn.filepond.registerPlugin(plugIn)
            return true;
        }
        return false;
    };

    this.reset = function() {      
        var files = $(filepondId).filepond('getFiles');
        
        files.forEach(function(file) {
            $(filepondId).filepond('removeFile',file.id);            
        });
    };

    this.init = function(formId, options) {
        var maxFiles = getValue('maxFiles',options,1);
        var label = getValue('label',options,defaultLabel);
        var acceptedFileTypes = getValue('acceptedFileTypes',options,["*"]);
        var instantUpload = getValue('instantUpload',options,false);
        var url = getValue('url',options,'/api/storage/admin/upload');
        var formFields = getValue('formFields',options,{});         
        var maxFileSize = getValue('maxFileSize',options,"10MB");        
        var allowMultiple = getValue('allowMultiple',options,false);       
        var onSuccess = getValue('onSuccess',options,null);
        var onError = getValue('onError',options,null);
        
        filepondId = getValue('filepondId',options,'#file');

        this.options = options;
        //File type validatin plugin
        this.registerPlugin(FilePondPluginFileValidateType);
       
        $(filepondId).filepond({          
            maxFiles: maxFiles,
            allowMultiple: allowMultiple,
            labelIdle: label,
            maxFileSize: maxFileSize,
            acceptedFileTypes: acceptedFileTypes,
            instantUpload: instantUpload,
            onremovefile: function(file) {
               $('.errors').hide();
            },
            onaddfilestart: function(file) {
                callFunction(options.onAddFileStart,file);
            },
            onprocessfilestart: function(file) {
                callFunction(options.onStart,file);
            },
            server: {
                process: {
                    url: arikaim.getBaseUrl() + url,
                    method: 'POST',
                    onload: function(response) {   
                        arikaim.ui.form.enable(formId);                    
                        var response = new ApiResponse(response);                          
                        callFunction(onSuccess,response.getResult());
                    },
                    onerror: function(response) {  
                        arikaim.ui.form.enable(formId);   
                        var response = new ApiResponse(response);
                        arikaim.ui.form.enable(formId);  
                        var submitButton = arikaim.ui.form.findSubmitButton(formId);
                        arikaim.ui.enableButton(submitButton);

                        callFunction(onError,response.getErrors());
                    },
                    ondata: (data) => {                           
                        Object.keys(formFields).forEach(function(key) {    
                            var fieldValue = $(formFields[key]).val();  
                            data.append(key,fieldValue);                               
                        });                                    
                        return data;
                    }
                },
                fetch: null,
                revert: null
            }
        });     

        var onFormError = (isFunction(onError) == true) ? null : function(error) {
            arikaim.ui.form.enable(formId);   
        };

        arikaim.ui.form.onSubmit(formId,function() {                    
            return $(filepondId).filepond('processFiles');             
        },function(result) {
            arikaim.ui.form.enable(formId);   
        },onFormError);
    };

    this.init(formId,options);
}