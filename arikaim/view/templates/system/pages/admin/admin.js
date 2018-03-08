/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ControlPanel() {
    this.setToolIcon = function(icon_class) {       
        $('#tool_icon').removeClass();
        $('#tool_icon').addClass('mini icon ' + icon_class);
    };

    this.setToolTitle = function(title) { 
        $('#tool_title').html(title);
    };

    this.init = function() {
        arikaim.component.load('system:confirm-dialog',function(result) {
            $('body').append(result.html);
        },function(errors) {

        });
    };
}

var controlPanel = new ControlPanel();

arikaim.page.onReady(function() {      
    arikaim.session.init(true);
    controlPanel.init();
});