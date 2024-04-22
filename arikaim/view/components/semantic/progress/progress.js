/**
 *  Arikaim  
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function TaskProgress() {
    var self = this;
    
    this.responseLen = 0;

    this.post = function(url, data, onProgress, onSuccess, onError) {
        return this.request('POST',url,data,onProgress,onSuccess,onError);    
    };

    this.put = function(url, data, onProgress, onSuccess, onError) {
        return this.request('PUT',url,data,onProgress,onSuccess,onError);       
    };

    this.get = function(url, onProgress, onSuccess, onError, data) {
        return this.request('GET',url,data,onProgress,onSuccess,onError);       
    };

    this.request = function(method, url, data, onProgress, onSuccess, onError) {
        this.responseLen = 0;

        var handleProgress = (isEmpty(onProgress) == false) ? this.getHandleProgress(onProgress,onSuccess,onError) : null
        var handleSuccess = (isEmpty(onSuccess) == false) ? this.getHandleSuccess(onSuccess,onError) : null

        switch (method) {
            case "GET":              
                return arikaim.get(url,handleSuccess,onError,data,null,handleProgress);
            case 'POST': 
                return arikaim.post(url,data,handleSuccess,onError,null,handleProgress);
            case 'PUT': 
                return arikaim.put(url,data,handleSuccess,onError,null,handleProgress)
        }
        
        return false;
    };

    this.getHandleSuccess = function(onSuccess, onError) { 
         
        return function(result) {  
         
            if (isJSON(result) == true) {
                if (self.isProgressResponse(result) == false) {
                    callFunction(onSuccess,result);
                } else {
                    var dataItems = JSON.parse(result);                     
                    var lastItem = dataItems[dataItems.length - 1];   
                    var response = new ApiResponse(lastItem);  
                    if (response.hasError() == false) {
                        callFunction(onSuccess,response.getResult())
                    } else {
                        callFunction(onError,result.getErrors());
                    }    
                }                         
            }
        };                                                                                
    };

    this.getHandleProgress = function(onProgress, onSuccess, onError) { 
         
        return function(event) {
            if (isEmpty(event.currentTarget.responseText) == true) {            
                return;
            }
                    
            var data = event.currentTarget.responseText.substr(self.responseLen);
            if (data.charAt(data.length - 1) == ',') {
                data = data.slice(0,-1);
            }
      
            data = '[' + data + ']';
            self.responseLen = event.currentTarget.responseText.length;
                     
            if (isJSON(data) == true) {
                var dataItems = JSON.parse(data);   
                var lastItem = dataItems[dataItems.length - 1];            
                var response = new ApiResponse(lastItem);  
                if (response.hasError() == true) {                                                 
                    callFunction(onError,response.getErrors());                        
                } else {                   
                    var result = response.getResult();
                    if (result.progress_end == true) {
                        callFunction(onSuccess,result);       
                    } else {
                        callFunction(onProgress,result);       
                    }
                   
                }     
            }         
        };                                                                                       
    }; 

    this.isProgressResponse = function(apiResult) {      
        if (isObject(apiResult) == false) {
            return false;
        }

        return (apiResult.progress == true || apiResult.progress == 'true'); 
    }
}

var taskProgress = new TaskProgress();
