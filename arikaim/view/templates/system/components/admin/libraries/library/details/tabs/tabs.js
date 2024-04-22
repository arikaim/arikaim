'use strict';

arikaim.component.onLoaded(function() {    
    packageRepository.onInstalled = function(result) {
        libraries.showLibraryDetails(result.name,function(result) {            
        });
    };
});