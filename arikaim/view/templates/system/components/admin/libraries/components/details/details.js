'use strict';

arikaim.component.onLoaded(function() {  
    $('#libraries_dropdown').dropdown({
        onChange: function(name) {              
            componentsLibraryView.loadDetails(name);
        }
    });   
});