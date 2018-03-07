/**
 *  Arikaim Widget Library
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ArikaimWidget() {

    if (typeof ArikaimWidget.instance === 'object') {
        return ArikaimWidget.instance;
    }

    this.laodLibrary = function() {

    };

    this.loadScript = function(url,onDone) {
        var script = document.createElement('script');
        script.setAttribute("type","text/javascript");
        script.setAttribute("src",url);
        if (script.readyState) {
            script.onreadystatechange = function () { // For old versions of IE
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    onDone();
                }
            }
        } else {
            script.onload = onDone;
        }
        if (document.getElementsByTagName("head") != null) {
            document.getElementsByTagName("head").appendChild(script);
        } else {
            document.documentElement.appendChild(script);
        }
      

    }

    ArikaimWidget.instance = this;
}

var arikaimWidget = new ArikaimWidget();