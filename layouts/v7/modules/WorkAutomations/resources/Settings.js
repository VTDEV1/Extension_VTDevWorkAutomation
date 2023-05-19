/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_Index_Js("WorkAutomations_Settings_Js",{},{
    deleteTemplate:function(){
        $('.delete-template').on('click',function(){
            var id = $(this).data('id');
            var params = {
                'module' : 'WorkAutomations',
                'action' : 'ActionAjax',
                'mode'   : 'DeleteTemplate',
                'template_id' : id
            };
            var message = app.vtranslate('JS_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
            app.helper.showConfirmationBox({'message' : message}).then(function() {
                app.helper.showProgress();
                app.request.post({"data":params}).then(function(err,data){
                    if(err === null){
                        app.helper.hideProgress();
                        window.location.reload();
                    }
                });
            });
        });
    },
    registerEvents : function() {
        this._super();
        this.deleteTemplate();
    }
});

jQuery(document).ready(function () {
    var instance = new WorkAutomations_Settings_Js();
    instance.registerEvents();
});

