


function Update() {

    this.checkForNewVersion = function(onDone) {
        arikaim.get('/admin/api/update/check',onDone,onError);
    };

    this.update = function(onDone,onError) {
        arikaim.get('/admin/api/update/',onDone,onError);
    };
}

var update = new Update();

arikaim.onPageReady(function() {
    $('#update_button').off();
    $('#update_button').on('click',function() {
        update.update(function(result) {
            
        });
    });
});
