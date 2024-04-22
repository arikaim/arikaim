/**
 *  Arikaim
 *  @copyright  Copyright (c) Intersoft Ltd <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
*/
'use strict';

function ArikaimClient(url, key) {
    var endpoint = url;
    var apiKey = key;
    var version = '1.0.0';
    var self = this;
    
    var headers = {
        'Content-Type': 'application/json',
        'Authorization' : apiKey
    };

    this.get = function(url) {
        return this.request('POST',url);         
    };

    this.post = function(url, data) {
        return this.request('POST',url,data);        
    };

    this.put = function(url, data) {
        return this.request('PUT',url,data);        
    };

    this.delete = function(url) {
        return this.request('DELETE',url);        
    };

    this.request = function(method, url, data) {
        return new Promise(function(resolve, reject) {
            console.log(endpoint + url);
            fetch(endpoint + url,self.getOptions(method,data))
                .then(async function(response) {
                    var json = await response.json();
                    return (response.ok == true) ? resolve(json) : reject(json);   
                })
                .catch(function(error) {
                    reject(error);
                });
        });  
    };

    this.getOptions = function(method, data) {
        var options = {
            method: method,
            mode: 'cors',
            headers: headers 
        }

        if (typeof data === 'object') {
            options['body'] = JSON.stringify(data);
        }

        return options;
    };

    console.log('Arikaim Client v' + version);
}
