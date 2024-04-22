/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ArikaimStore() {
    var self = this;

    this.removeOrder = function(onSuccess, onError) {
        return arikaim.put('/core/api/store/product/remove',{},onSuccess,onError);      
    } 

    this.registerOrder = function(orderId, apiDriver, onSuccess, onError) {
        var data = {          
            order_id: orderId,
            api_driver: apiDriver            
        };

        return arikaim.post('/core/api/store/product',data,onSuccess,onError);      
    };   

    this.initRegisterOrderForm = function() {
        arikaim.ui.form.addRules('#register_order_form',{});

        arikaim.ui.form.onSubmit("#register_order_form",function() {  
            var orderId = $('#order_id').val();

            return arikaimStore.registerOrder(orderId,'envato',function(result) {
                arikaim.page.loadContent({
                    id: 'store_product_content',           
                    component: 'system:admin.packages.store.settings.product',
                    params: { product: result.product }
                },function(result) {
                   self.initRegisterOrderForm(); 
                });
                arikaim.page.loadContent({
                    id: 'store_packages_content',           
                    component: 'system:admin.packages.store.settings.packages',
                    params: { packages: result.packages }
                });
            },function(error) {               
            });
        });
    };
}

var arikaimStore = new ArikaimStore();
