/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("WorkAutomations_Edit_Js", {}, {

    getRelatedModulesFromParentModule: function (parentModule) {
        var allRelatedModules = $('#allRelatedModules').val();
        allRelatedModules = JSON.parse(allRelatedModules);
        var relatedModules = allRelatedModules[parentModule];
        return relatedModules;
    },

    loadRelatedModules: function (parentModule) {
        window.onbeforeunload = null;
        var relatedModulesMapping = this.getRelatedModulesFromParentModule(parentModule);
        $('#related_module_name').removeAttr('disabled').html('');
        var option = '<option value="">Select an option</option>';
        $('#related_module_name').append(option);
        for (var key in relatedModulesMapping) {
            var option1 = '<option value="' + key + '">' + app.vtranslate(relatedModulesMapping[key], key) + '</option>';
            $('#related_module_name').append(option1);
        }
        $('#related_module_name').val('').change().trigger('liszt:updated');
    },

    registerFieldChange: function () {
        var thisInstance = this;
        $('select[name="WA-field-name"]').on('change', function (e) {
            var selectedElement = $(e.currentTarget);
            var thisRow = $(this).closest('tr.WA-field');
            var related_module_name = $('#related_module_name').val();
            var field_name = selectedElement.val();
            if (selectedElement.val()) {
                if(related_module_name){
                    var actionParams = {
                        "data" : {
                            "module":"WorkAutomations",
                            "view": "EditAjax",
                            "related_module_name": related_module_name,
                            "field_name": field_name,
                            "mode" : 'generateFieldInput'
                        }
                    };
                    app.request.post(actionParams).then(function(err,data){
                        if(err == null && data) {
                            var newFieldInput= jQuery(data);
                            thisRow.find('.WA-field-value').html('').append(newFieldInput);
                            thisInstance.registerStyleEventForFields(thisRow);
                            thisInstance.registerDeleteFieldEvent();

                        }
                    });
                }else{
                    app.helper.showAlertNotification({message:'You must select Parent and Related Module'});
                }
            }else{
                thisRow.find('.WA-field-value').html('').append('<input type="text" value="" class="inputElement"/>');
                thisInstance.registerStyleEventForFields(thisRow);
                thisInstance.registerDeleteFieldEvent();
            }
        });
    },

    registerAddFieldEvent: function () {
        var thisInstance = this;
        $('.addFieldBtn').off('click');
        $('.addFieldBtn').on('click',function (e) {
            var thisBlock = $(e.currentTarget).closest('.WA-blockContainer');
            var parent_module_name = $('#parent_module_name').val();
            var related_module_name = $('#related_module_name').val();
            var template_id = $('#template_id').val();
            if(parent_module_name && related_module_name){
                var actionParams = {
                    "data" : {
                        "module":"WorkAutomations",
                        "view": "EditAjax",
                        "parent_module_name": parent_module_name,
                        "related_module_name": related_module_name,
                        "template_id": template_id,
                        "mode" : 'generateNewField'
                    }
                };
                app.request.post(actionParams).then(function(err,data){
                    if(err == null && data) {
                        var newField= jQuery(data);
                        thisBlock.find('.fieldBlockContainer table tbody').append(newField);
                        thisInstance.registerStyleEventForFields(newField);
                        thisInstance.registerFieldChange();
                        thisInstance.registerDeleteFieldEvent();
                    }
                });
            }else{
                app.helper.showAlertNotification({message:'You must select Parent and Related Module'});
            }
        });
    },

    registerDeleteFieldEvent: function () {
        $('.deleteField').off('click');
        $('.deleteField').on('click',  function (e) {
            $(e.currentTarget).closest('tr.WA-field').remove();
        });
    },
    registerAddBlockEvent: function () {
        var thisInstance =this;
        $('#addNewBlockBtn').off('click');
        $('#addNewBlockBtn').on('click', function (e) {
            var parent_module_name = $('#parent_module_name').val();
            var related_module_name = $('#related_module_name').val();
            var template_id = $('#template_id').val();
            if(parent_module_name && related_module_name){
                var actionParams = {
                    "data" : {
                        "module":"WorkAutomations",
                        "view": "EditAjax",
                        "parent_module_name": parent_module_name,
                        "related_module_name": related_module_name,
                        "template_id": template_id,
                        "mode" : 'generateNewBlock'
                    }
                };
                app.request.post(actionParams).then(function(err,data){
                    if(err == null && data) {
                        var newBlock= jQuery(data);
                        $('#WA-blocks').append(newBlock);
                        thisInstance.registerStyleEventForFields(newBlock);
                        thisInstance.registerDeleteBlockEvent();
                        thisInstance.registerDeleteFieldEvent();
                        thisInstance.registerAddFieldEvent();
                        thisInstance.registerAutoFillAddressFromOrgan();
                    }
                });
            }else{
                app.helper.showAlertNotification({message:'You must select Parent and Related Module'});
            }
        });
    },
    registerStyleEventForFields:function(container){
        var thisInstance = this;
        vtUtils.applyFieldElementsView(container);
        var indexInstance = Vtiger_Index_Js.getInstance();
        indexInstance.referenceModulePopupRegisterEvent(container);
        indexInstance.registerAutoCompleteFields(container);
        thisInstance.registerClearReferenceSelectionEvent(container);
        thisInstance.registerValidateFieldOnChange(container);
    },
    registerClearReferenceSelectionEvent:function(container){
        container.find('.clearReferenceSelection').on('click', function(e){
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldName = fieldNameElement.attr('name');
            fieldNameElement.val('');
            parentTdElement.find('[name="'+fieldName+'_display"]').removeAttr("disabled").removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        });

        container.find('.sourceField').on(Vtiger_Edit_Js.postReferenceSelectionEvent,function(e,result){
            var fieldName = jQuery(this).attr("name");
            var element = container.find('[name="'+fieldName+'_display"]');
            element.attr("disabled","disabled");
        });
    },
    registerValidateFieldOnChange:function (newRow) {
        newRow.find('input,select').on('change',function() {
            var data_rule_currency = $(this).data('rule-currency');
            var data_rule_required = $(this).data('rule-required');
            var field_type =  $(this).closest('td').data('field-type');
            if (typeof data_rule_currency !== "undefined" && data_rule_currency) {
                if(jQuery(this).val() != ''){
                    var check_num = parseFloat(jQuery(this).val());
                    if(isNaN(check_num) || check_num < 0){
                        jQuery(this).addClass('input-error');
                        var errorInfo = app.vtranslate('JS_PLEASE_ENTER_VALID_VALUE');
                        vtUtils.showValidationMessage(jQuery(this), errorInfo);
                        return false;
                    }
                    else{
                        vtUtils.hideValidationMessage(jQuery(this));
                        jQuery(this).removeClass('input-error');
                    }
                }
                else{
                    vtUtils.hideValidationMessage(jQuery(this));
                    jQuery(this).removeClass('input-error');
                }
            }
            else if(typeof data_rule_required !== "undefined" && data_rule_required) {
                if(jQuery(this).val() == ''){
                    jQuery(this).addClass('input-error');
                    var errorInfo = app.vtranslate('JS_REQUIRED_FIELD');
                    vtUtils.showValidationMessage(jQuery(this), errorInfo);
                    return false;
                }
                else{
                    vtUtils.hideValidationMessage(jQuery(this));
                    jQuery(this).removeClass('input-error');
                }
            }
            else if(typeof field_type !== "undefined" && (field_type == 'percentage' || field_type == 'double' )) {
                if(jQuery(this).val() != '' || jQuery(this).val() != undefined){
                    var check_num = parseFloat(jQuery(this).val());
                    if(isNaN(check_num)){
                        jQuery(this).addClass('input-error');
                        var errorInfo = app.vtranslate('JS_PLEASE_ENTER_VALID_VALUE');
                        vtUtils.showValidationMessage(jQuery(this), errorInfo);
                        return false;
                    }
                    else{
                        vtUtils.hideValidationMessage(jQuery(this));
                        jQuery(this).removeClass('input-error');
                    }
                }
                else{
                    vtUtils.hideValidationMessage(jQuery(this));
                    jQuery(this).removeClass('input-error');
                }
            }
            else if(typeof field_type !== "undefined" && field_type == 'integer') {
                if(jQuery(this).val() != '' || jQuery(this).val() != undefined){
                    var check_num = parseInt(jQuery(this).val());
                    if(isNaN(check_num)){
                        jQuery(this).addClass('input-error');
                        var errorInfo = app.vtranslate('JS_PLEASE_ENTER_INTEGER_VALUE');
                        vtUtils.showValidationMessage(jQuery(this), errorInfo);
                        return false;
                    }
                    else{
                        vtUtils.hideValidationMessage(jQuery(this));
                        jQuery(this).removeClass('input-error');
                    }
                }
            }
        });
    },
    registerDeleteBlockEvent: function () {
        $('.deleteBlock').off('click');
        $('.deleteBlock').on('click', function (e) {
            $(e.currentTarget).closest('.blockContainer').remove();
        });
    },

    registerParentModuleChangeEvent: function () {
        var instance = this;
        $('#parent_module_name').on('change', function (e) {
            var parentModule = $(this).val();
            if (parentModule)
                instance.loadRelatedModules(parentModule);
            else {
                $('#related_module_name').val('').attr("disabled", "disabled");
            }
        });
    },

    registerRelatedModuleChangeEvent: function () {
        $('#related_module_name').on('change', function (e) {
            window.onbeforeunload = null;
            var template_name = $('#template_name').val();
            var parent_module_name = $('#parent_module_name').val();
            var related_module_name = $('#related_module_name').val();
            var template_id = $('#template_id').val();
            window.location.href = 'index.php?module=WorkAutomations&view=Edit&template_id='+template_id+'&parent_module_name='+parent_module_name+'&related_module_name='+related_module_name+'&template_name='+template_name;
        });
    },

    registerButtonSaveTemplateClickEvent: function () {
        $('#saveTemplateButton').on('click', function () {
            window.onbeforeunload = null;
            var templateName = $('#template_name').val();
            var parentModuleName = $('#parent_module_name').val();
            var relatedModuleName = $('#related_module_name').val();
            var template_id = $('#template_id').val();
            var blocks = $('.WA-blockContainer');
            var temp = [];
            var blocks_arr = [];
            blocks.each(function (k,block) {
                var fields_arr = [];
                var fields = $(block).find('[name="WA-field-name"]');
                fields.each(function (k1,field) {
                    var field_arr = [];
                    var field_name = $(field).val();
                    if(field_name){
                        var field_value = $(field).closest('.WA-field').find('[name="'+field_name+'"]').val();
                        var field_obj = $(field).closest('.WA-field').find('.inputElement[name="'+field_name+'"]');
                        var type = field_obj.attr('type');
                        if(type == 'checkbox') {
                            if(field_obj.attr('checked') == 'checked'){
                                field_value = 1;
                            }else{
                                field_value = 0;
                            }
                        }
                        var obj = {};
                        obj[field_name] = field_value;
                        field_arr.push(obj);
                    }
                    fields_arr.push(field_arr);
                });
                blocks_arr.push(fields_arr);
            });
            temp = blocks_arr;
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'WorkAutomations';
            params.mode = 'SaveTemplate';
            params.templateName = templateName;
            params.parentModuleName = parentModuleName;
            params.relatedModuleName = relatedModuleName;
            params.template_id = template_id;
            params.template = temp;
            app.helper.showProgress();
            app.request.post({ data: params }).then(
                function (err, data) {
                    app.helper.hideProgress();
                    var template_id = data.template_id;
                    if(template_id){
                        window.location.href= 'index.php?module=WorkAutomations&view=Edit&template_id='+template_id;
                    }else{
                        app.helper.showAlertNotification({message:data.result});
                    }
                }
            );
        });
    },

    registerChangeLink: function () {
        $('.container-fluid').find('.module-title').closest('a').attr('href', 'index.php?module=WorkAutomations&parent=Settings&view=Settings');
    },

    registerAutoFillAddressFromOrgan: function () {
        var thisInstance = this;
        $("#EditView").find('.sourceField').on(Vtiger_Edit_Js.postReferenceSelectionEvent,function(e,result){
            var fieldName = $(e.currentTarget).attr('name');
            var formContainer = $(e.currentTarget).closest('form');
            if (fieldName === 'account_id') {
                var accountId = $(e.currentTarget).val();
                var params = {};
                params.module = 'WorkAutomations';
                params.action = 'ActionAjax';
                params.mode = 'GetOrganizationInfo';
                params.accountid = accountId;
                app.helper.showProgress();
                app.request.post({ data: params }).then(
                    function (err, data) {
                        app.helper.hideProgress();
                        if (err === null && data) {
                            formContainer.find('[name="bill_street"]').val(data.bill_street);
                            formContainer.find('[name="ship_street"]').val(data.ship_street);
                        }
                    }
                );
            }
        });
    },

    registerEvents: function () {
        this._super();
        this.registerAddBlockEvent();
        this.registerDeleteBlockEvent();
        this.registerAddFieldEvent();
        this.registerDeleteFieldEvent();
        this.registerParentModuleChangeEvent();
        this.registerRelatedModuleChangeEvent();
        this.registerButtonSaveTemplateClickEvent();
        this.registerFieldChange();
        this.registerChangeLink();
        this.registerAutoFillAddressFromOrgan();
    }
});
