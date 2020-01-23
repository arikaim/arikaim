arikaim.page.onReady(function() {
    $('#user_menu_dropdown').dropdown({
        onChange: function(value) {    
            console.log(value);
                   
            arikaim.page.loadContent({
                id: 'tool_content',
                component: value
            });
        }
    });
});
