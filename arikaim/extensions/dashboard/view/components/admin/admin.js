/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
 'use strict';

 function DashboardControlPanel() {
    var self = this;
 
    this.hidePanel = function(name, onSuccess, onError) {
        return arikaim.put('/api/admin/dashboard/hide',{
            component_name: name
        },onSuccess,onError);          
    };
 
    this.showPanel = function(name, onSuccess, onError) { 
        return arikaim.put('/api/admin/dashboard/show',{ 
            component_name: name
        },onSuccess,onError);           
    };   
 
    this.initSettings = function() {
        arikaim.ui.button('.view-panel-button',function(element) {
            var visible = $(element).attr('data-visible');
            var icon = $(element).children('.icon');
            var componentName = $(element).attr('component-name');

            if (visible == true) {
                $(element).attr('data-visible',0);
                self.hidePanel(componentName,function(result) {
                    $(icon).addClass('slash');
                    self.loadDashboard();
                });
            } else {
                $(element).attr('data-visible',1);
                self.showPanel(componentName,function(result) {
                    $(icon).removeClass('slash');
                    self.loadDashboard();
                });
            }
        });
    };
 
    this.loadDashboard = function() {
        return arikaim.page.loadContent({
            id: 'dashboard_content',
            component: 'dashboard::admin.dashboard.items'           
        }); 
    };
}
 
var dashboardControlPanel = new DashboardControlPanel();
