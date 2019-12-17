
arikaim.page.onReady(function() {
    arikaim.ui.button('.details-button',function(element) {
        var name = $(element).attr('library');      
        arikaim.ui.setActiveTab('#details_button');

        libraries.showDetails(name);
    });
});