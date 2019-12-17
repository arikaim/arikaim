$(document).ready(function() {  
    packageRepository.onInstalled = function(result) {
        libraries.showLibraryDetails(result.name,function(result) {
            
        });
    };
});