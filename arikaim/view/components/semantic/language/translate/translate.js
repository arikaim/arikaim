/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function LanguageTranslate() {

    this.translateText = function(text, sourceLanguage, targetLanguage, onSuccess) {
        sourceLanguage = getDefaultValue(sourceLanguage,'auto');
        text = encodeURI(text);
        var serviceUrl =  "https://translate.googleapis.com/translate_a/";
        var url = serviceUrl + "single?client=gtx&sl=" + sourceLanguage + "&tl=" + targetLanguage + "&dt=t&q=" + text;
          
        $.getJSON(url,function(data) {
            var result = data[0][0][0];
            callFunction(onSuccess,result);
        });
    };

    this.translateField = function(sourceFeildSelector, targetFieldSelector, sourceLanguage, targetLanguage, onSuccess) {
        var text = $(sourceFeildSelector).val();
       
        this.translateText(text,sourceLanguage,targetLanguage,function(result) {
            $(targetFieldSelector).val(result);           
            callFunction(onSuccess,result);
        });
    };
};

var translate = new LanguageTranslate();