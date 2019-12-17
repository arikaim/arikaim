/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/

function CategoryView() {
    var self = this;

    this.init = function() {
        paginator.init('category_rows',{
            name: 'category::admin.view.items',
            params: {
                namespace: 'category'
            }
        });      
        $('#branch').dropdown({
            onChange: function(branch, text, choice) { 
                var language = $('#choose_language').dropdown('get value');
                category.loadList('category_rows',null,null,language,branch,function(result) {                   
                    self.initRows();  
                    paginator.clear('category',function() {
                        paginator.init('category_rows',{
                            name: 'category::admin.view.items',
                            params: {
                                namespace: 'category',
                                branch: branch
                            }
                        });     
                        paginator.reload();     
                    });   
                });
            }
        });
    };

    this.initRows = function() {
        var component = arikaim.component.get('category::admin');
        var removeMessage = component.getProperty('messages.remove.content');
        $('.actions-dropdown').dropdown();
        
        arikaim.ui.button('.add-button',function(element) {
            var parent_id = $(element).attr('parent-id');
            var language = $(element).attr('language');
            category.loadAddCategory(parent_id,language); 
        });
      
        arikaim.ui.button('.edit-button',function(element) {
            var uuid = $(element).attr('uuid');
            var language = $(element).attr('language');
            category.loadEditCategory(uuid,language);     
        });
    
        arikaim.ui.button('.delete-button',function(element) {
            var uuid = $(element).attr('uuid');
            var title = $(element).attr('data-title');

            var message = arikaim.ui.template.render(removeMessage,{ title: title });
            modal.confirmDelete({ 
                title: component.getProperty('messages.remove.title'),
                description: message
            },function() {
                category.delete(uuid,function(result) {
                    $('#' + uuid).remove();
                    $('.class-' + uuid).remove();                   
                });
            });
        });
      
        arikaim.ui.button('.disable-button',function(element) {
            var uuid = $(element).attr('uuid');
            var parentUuid = $(element).attr('parent-uuid');
            var parentId = $(element).attr('parent-id');
            var language = $(element).attr('language');
            var branch = $(element).attr('branch');

            category.setStatus(uuid,0,function(result) {
                category.loadList(parentUuid,parentId,uuid,language,branch,function(result) {                  
                    self.init();                  
                });
            });
        });   
        
        arikaim.ui.button('.enable-button',function(element) {
            var uuid = $(element).attr('uuid');
            var parentUuid = $(element).attr('parent-uuid');
            var parentId = $(element).attr('parent-id');
            var language = $(element).attr('language');
            var branch = $(element).attr('branch');

            category.setStatus(uuid,1,function(result) {
                category.loadList(parentUuid,parentId,uuid,language,branch,function(result) {                   
                    self.init();                    
                });
            });
        }); 
        
        arikaim.ui.button('.relations-button',function(element) {
            var uuid = $(element).attr('uuid');
            var language = $(element).attr('language');
            category.loadCategoryRelations(uuid,language);     
        });

        this.initAccordion();
    };

    this.initAccordion = function(selector) {  
        selector = getDefaultValue(selector,'.ui.accordion');             
        $(selector).accordion({
            selector: {
                trigger: '.title .dropdown'
            }
        });        
    };
}

var categoryView = new CategoryView();

arikaim.page.onReady(function() {
    categoryView.init();   
    categoryView.initRows();
});