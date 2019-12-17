arikaim.page.onReady(function() {   
    category.initCategoryDropDown();
    arikaim.ui.form.addRules("#category_form",{
        inline: false,
        fields: {
            title: {
            identifier: "title",      
                rules: [{
                    type: "minLength[2]"       
                }]
            }
        }
    });   
});