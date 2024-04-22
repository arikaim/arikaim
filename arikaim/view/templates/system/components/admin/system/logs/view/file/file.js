/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
 'use strict';

 function FileLogsView() {
     var self = this;
    
     this.init = function() {
        paginator.init('logs_rows',"system:admin.system.logs.view.file.rows",'logs.file');    
     };   
 
     this.initRows = function() {
     };
 }
 
var fileLogsView = createObject(FileLogsView,ControlPanelView);
 
arikaim.component.onLoaded(function() {    
    fileLogsView.init();
    fileLogsView.initRows();
});