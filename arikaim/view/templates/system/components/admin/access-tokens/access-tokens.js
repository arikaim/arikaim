/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function AccessTokens() {

    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/core/api/tokens/delete/' + uuid,onSuccess,onError);          
    };

    this.deleteExpired = function(uuid, onSuccess, onError) {
        return arikaim.delete('/core/api/tokens/delete/expired/' + uuid,onSuccess,onError);          
    };
}

var accessTokens = new AccessTokens();
