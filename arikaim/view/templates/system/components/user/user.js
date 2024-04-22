/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function User() {
    this.login = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/user/login',formId,onSuccess,onError);       
    };

    this.logout = function(onSuccess, onError) {      
        return arikaim.get('/core/api/user/logout',onSuccess,onError);          
    };
};

var user = new User();
