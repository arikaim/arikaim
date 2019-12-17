$(document).ready(function() {  
    packageRepository.onInstalled = function(result) {      
        templates.showDetailsPage(result.name);
    };
});