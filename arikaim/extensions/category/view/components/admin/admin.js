/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function CategoryControlPanel() {
    var self = this;

    this.delete = function(uuid,onSuccess,onError) {
        return arikaim.delete('/api/category/admin/delete/' + uuid,onSuccess,onError);          
    };

    /**
     * 
     * @param string uuid 
     * @param int status  0 - disabled, 1 - active
     */
    this.setStatus = function(uuid, status ,onSuccess, onError) {           
        var data = { 
            uuid: uuid, 
            status: status 
        };

        return arikaim.put('/api/category/admin/status',data,onSuccess,onError);      
    };

    this.loadList = function(element, parentId, uuid, language, branch, onSuccess) { 
        return arikaim.page.loadContent({
            id : element,
            component : 'category::admin.view.items',
            params: { 
                parent_id: parentId,
                language: language,
                uuid: uuid,
                branch: branch 
            }
        },onSuccess);
    };

    this.loadAddCategory = function(parentId, language) {
        arikaim.ui.setActiveTab('#add_category','.category-tab-item');

        arikaim.page.loadContent({
            id: 'category_content',
            component: 'category::admin.add',
            params: { 
                parent_id: parentId,
                language: language 
            }
        });          
    };

    this.loadCategoryRelations = function(uuid, language) {
        arikaim.ui.setActiveTab('#relations','.category-tab-item');
             
        arikaim.page.loadContent({
            id: 'category_content',
            component: 'category::admin.relations',
            params: { 
                uuid: uuid,
                language: language 
            }
        });  
    };

    this.loadEditCategory = function(uuid, language) {
        arikaim.ui.setActiveTab('#edit_category','.category-tab-item')      
        arikaim.page.loadContent({
            id: 'category_content',
            component: 'category::admin.edit',
            params: { 
                uuid: uuid,
                language: language 
            }
        });  
    };

    this.initCategoryDropDown = function() {
        $('#category_dropdown').dropdown({
            allowCategorySelection: true,
            
            onChange: function(value, text, choice) { 
                var title = $(choice).attr('title');
                $(this).children('.text').html(title);
            }
        });
    };

    this.init = function() {    
        $('#choose_language').dropdown({
            onChange: function(value) {
                arikaim.page.loadContent({
                    id: 'tab_content',
                    component: 'category::admin.menu',
                    params: { language: value }
                });
            }
        }); 
        arikaim.ui.tab();
    };
}

var category = new CategoryControlPanel();

arikaim.page.onReady(function() {
    category.init();
});