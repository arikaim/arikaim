/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function BlogPostsView() {
    
    this.init = function() {
        this.loadMessages('blog::admin.posts');
        this.setItemComponentName('blog::admin.posts.view.row');
        this.setItemsSelector('view_items');
        this.setItemSelector('row_');
        this.initRows();
    };

    this.initRows = function() {
        arikaim.ui.loadComponentButton('.post-action');

        $('.status-dropdown').dropdown({
            onChange: function(value) {
                var uuid = $(this).attr('uuid');
                blogApi.setPostStatus(uuid,value);     
            }
        });

        arikaim.ui.button('.delete-post',(element) => {
            var uuid = $(element).attr('uuid');
       
            modal.confirmDelete({ 
                title: 'Confirm',
                description: 'Confirm delete blog post'
            },() => {
                blogApi.deletePost(uuid,(result) => {
                    this.deleteItem(result.uuid);
                });
            });
        });
    };

    this.createEditor = function() {
        return new SimpleMDE({
            autofocus: true,
            autoDownloadFontAwesome: true,
            forceSync: true,
            element: document.getElementById("editor")
        });
    };
}

var blogPostView = new createObject(BlogPostsView,ControlPanelView);

arikaim.component.onLoaded(function() {
    blogPostView.init();
    arikaim.ui.loadComponentButton('.create-blog-post');
});