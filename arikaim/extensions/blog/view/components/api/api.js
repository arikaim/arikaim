function BlogApi() {
  
    this.addPost = function(data, onSuccess, onError) {
        return arikaim.post('/api/blog/post/add',data,onSuccess,onError);          
    };

    this.deletePost = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/blog/post/delete/' + uuid,onSuccess,onError);          
    };

    this.setPostStatus = function(uuid, status, onSuccess, onError) {
        var data = {
            uuid: uuid,
            status: status
        };
        
        return arikaim.put('/api/blog/post/status',data,onSuccess,onError);          
    };

    this.updatePostSummary = function(data, onSuccess, onError) {
        return arikaim.put('/api/blog/post/update/summary',data,onSuccess,onError);          
    };

    this.updatePost = function(data, onSuccess, onError) {
        return arikaim.put('/api/blog/post/update',data,onSuccess,onError);          
    };

    this.updatePostImage = function(data, onSuccess, onError) {
        return arikaim.put('/api/admin/blog/post/update/image',data,onSuccess,onError);          
    };

    this.updatePostMetaTags = function(data, onSuccess, onError) {
        return arikaim.put('/api/blog/post/update/meta',data,onSuccess,onError);          
    };
    
    this.restorePost = function(uuid, onSuccess, onError) {
        var data = {
            uuid: uuid
        };

        return arikaim.put('/api/blog/post/restore',data,onSuccess,onError);          
    };

    this.emptyTrash = function(onSuccess, onError) {
        return arikaim.delete('/api/blog/trash/empty',onSuccess,onError);          
    };  
}

var blogApi = new BlogApi();
