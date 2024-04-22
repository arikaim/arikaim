'use strict';

arikaim.component.onLoaded(function() {    
    $('.relations-field-dropdown').dropdown({
        onAdd: function(value, text, $choice) {
            var extension = $(this).attr('extension');
            var model = $(this).attr('model');
            var type = $(this).attr('type');           
            var relationId = $(this).attr('relation-id');

            relations.add(model,extension,value,type,relationId,function(result) {
            });
        },
        onRemove: function(value, text, $choice) {
            var extension = $(this).attr('extension');
            var model = $(this).attr('model');
            var type = $(this).attr('type');           
            var relationId = $(this).attr('relation-id');

            relations.deleteRelation(model,extension,value,type,relationId,function(result) {
            });
        }
    });
});