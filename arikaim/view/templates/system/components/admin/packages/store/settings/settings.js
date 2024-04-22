/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.button('.remove-poroduct-order',function(element) {
        return arikaimStore.removeOrder(function(result) {         
            arikaim.page.loadContent({
                id: 'store_product_content',           
                component: 'system:admin.packages.store.settings.product',
                params: {}
            },function(result) {
                arikaimStore.initRegisterOrderForm();
            });
            arikaim.page.loadContent({
                id: 'store_packages_content',           
                component: 'system:admin.packages.store.settings.packages',
                params: {}
            });
        });
    });
}); 