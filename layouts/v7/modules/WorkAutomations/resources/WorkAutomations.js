/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger.Class("WorkAutomations_Js",{
},{
    registerAppendNewButtonToModuleHeader: function () {
        var instance = this;
        var parentModuleName = app.getModuleName();
        var params = {};
        params.module = 'WorkAutomations';
        params.action = 'ActionAjax';
        params.mode = 'getTemplates';
        params.parent_module = parentModuleName;
        app.request.post({ data: params }).then(
            function (err, data) {
                app.helper.hideProgress();
                if (err === null && data.length > 0) {
                    var buttonShowTemplates = "<button type='button' class='btn btn-primary' id='addRecordByCustomTemplate'>Work Automations</button>";
                    var selectTemplate = "<select style='min-width: 250px' class='select2 pull-left hide' id='selectCustomTemplate'></select>";
                    $('#starToggle').before(buttonShowTemplates);
                    $('#addRecordByCustomTemplate').before(selectTemplate);
                    vtUtils.showSelect2ElementView($('#selectCustomTemplate'));
                    var options = '<option value="" selected>Select Template</option>';
                    var customTemplate = $('#selectCustomTemplate');
                    for(var key in data) {
                        if(data[key].length === 0) {
                            $('#addRecordByCustomTemplate').removeClass('hide');
                            $('#addRecordByCustomTemplate').addClass('hide');
                        } else {
                            $('#addRecordByCustomTemplate').removeClass('hide');
                        }
                        if(data.hasOwnProperty(key) &&  data[key].template_id && data[key].template_name) {
                            options += '<option value="' + data[key].template_id + '">' + data[key].template_name + '</option>';
                        }
                    }
                    customTemplate.html(options).trigger("change");
                    instance.registerButtonAddRecordByCustomTemplate();
                }
            }
        );
    },

    registerButtonAddRecordByCustomTemplate: function() {
        var instance = this;
        $('#addRecordByCustomTemplate').off('click');
        $('#addRecordByCustomTemplate').on('click', function(e) {
            var selectedTemplate = $('#selectCustomTemplate');
            var buttonShowTemplates = $(this);
            var buttonCreateRecordByTemplate = "<button type='button' class='btn btn-success' id='createRecordByCustomTemplate'>Create</button>";
            if (selectedTemplate.hasClass('hide')) {
                buttonShowTemplates.html('Cancel');
                buttonShowTemplates.removeClass('btn-primary').addClass('btn-danger');
                selectedTemplate.removeClass('hide');
                $('#addRecordByCustomTemplate').before(buttonCreateRecordByTemplate);
                instance.registerCreateRecordEvent();
            } else {
                buttonShowTemplates.html('Work Automations');
                buttonShowTemplates.removeClass('btn-danger').addClass('btn-primary');
                selectedTemplate.val('').trigger('change');
                selectedTemplate.addClass('hide');
                $('#createRecordByCustomTemplate').remove();
            }
        });
    },

    registerCreateRecordEvent: function() {
        $('#createRecordByCustomTemplate').off('click');
        $('#createRecordByCustomTemplate').on('click', function() {
            var selectedTemplateId = $('#selectCustomTemplate').val();
            var parentModuleName = app.getModuleName();
            var recordId = app.getRecordId();
            if(selectedTemplateId){
                var params = {};
                params.module = 'WorkAutomations';
                params.action = 'ActionAjax';
                params.mode = 'createRelatedRecords';
                params.parent_module = parentModuleName;
                params.template_id = selectedTemplateId;
                params.record = recordId;
                app.request.post({ data: params }).then(
                    function (err, data) {
                        app.helper.hideProgress();
                        if (err === null && data) {
                            app.helper.showSuccessNotification({message: data.result});
                            window.location.reload();
                        }
                    }
                );
            }
        });
    },

    registerEvents : function(){
        this.registerAppendNewButtonToModuleHeader();
    }

});
//On Page Load
jQuery(document).ready(function() {
    var instance = new WorkAutomations_Js();
    instance.registerEvents();
});
