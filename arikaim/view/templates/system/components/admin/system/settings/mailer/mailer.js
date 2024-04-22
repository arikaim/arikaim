/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function MailerSettings() {
   
    this.init = function() {
        $('#drivers_dropdown').dropdown({
            onChange: function(value) {                    
                options.saveConfigOption('settings/mailerDriver',value);
            }
        });

        arikaim.ui.button('.mailer-settings-button',function() {
            return arikaim.page.loadContent({
                id: 'mailer_config',
                component: 'system:admin.system.settings.mailer.settings',  
            });
        });

        arikaim.events.on('driver.config',function(element,name,category) {      
            drivers.loadConfig(name,'mailer_config',null,'sixteen wide');
        },'driversList',self)

        arikaim.ui.button('#send_button',function() {
            return mailerSettings.sendTestEmail();
        });
    };

    this.sendTestEmail = function() {
        $('#mailer_config').html('');

        return arikaim.get('/core/api/mailer/test/email',function(result) {     
            return arikaim.page.loadContent({
                id: 'mailer_config',
                params: {
                    message: result.message           
                },
                component: 'system:admin.system.settings.mailer.test',  
            });  
        },function(errors) {    
            return arikaim.page.loadContent({
                id: 'mailer_config',             
                component: 'system:admin.system.settings.mailer.test',  
            });                               
        });
    };
}

var mailerSettings = new MailerSettings();

arikaim.component.onLoaded(function() {    
    mailerSettings.init();
});