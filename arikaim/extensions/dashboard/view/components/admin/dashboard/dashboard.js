'use strict';

arikaim.component.onLoaded(function(component) {
    component.init = function() {
        arikaim.ui.button('.dashboard-settings',function(element) {
            var state = $(element).attr('state');
            if (state == 'on') {
                $('#dashboard_settings_content').fadeOut(500);     
                $(element).attr('state','off');
                $(element).addClass('primary').removeClass('green');
            } else {
                $('#dashboard_settings_content').fadeIn(500);               
                arikaim.page.loadContent({
                    id: 'dashboard_settings_content',
                    component: 'dashboard::admin.settings'           
                }); 
                $(element).attr('state','on');
                $(element).addClass('green').removeClass('primary');
            }           
        });
    };

    component.init();

    return component;
});