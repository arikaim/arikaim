/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function CookieDialog() { 
    var self = this;
    this.interval = null;

    this.init = function(type, options) {     
        this.interval = getValue('interval',options,$('#cookie_policy_modal').attr('interval'));
        var position = getValue('position',options,'bottom right');

        if (isEmpty(this.interval) == true) {
            this.interval = 7;
        }

        if (this.isApproved() == true) return;
    
        if (type == 'dialog') {
            $('#cookie_policy_modal').modal({           
                closable: true,              
                blurring: false,
                onVisible: function() {               
                    self.setApproved();
                },
                onHidden: function() {               
                    self.setApproved();
                }
            }).modal('show');            
        } else {
            $('#cookie_policy_modal').toast({
                displayTime: 0,
                position: position,
                onDeny: function(){
                    self.setApproved();
                },
                onApprove: function() {
                    self.setApproved();
                }
            });
        }           
    };

    this.setApproved = function(interval) {
        interval = getDefaultValue(interval,this.interval);     
        arikaim.storage.setCookie('privacy-policy',1,interval);
    };

    this.isApproved = function() {
        var show = arikaim.storage.getCookie('privacy-policy'); 
        return (show == 1 || show == '1') ? true : false;   
    };   
}
