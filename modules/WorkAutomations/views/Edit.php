<?php
/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class WorkAutomations_Edit_View extends Vtiger_Edit_View {

    public function checkPermission(Vtiger_Request $request) {
        return true;
    }
    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        global $adb;
        $WorkAutomations_Module_Model = new WorkAutomations_Module_Model();
        $WorkAutomations_Record_Model = new WorkAutomations_Record_Model();
        $module = $request->getModule();
        $templateId = $request->get('template_id');
        $template_data = array();
        $parent_module_name = $request->get('parent_module_name');
        $related_module_name = $request->get('related_module_name');
        $template_name = $request->get('template_name');
        if($templateId){
            $template_data = $WorkAutomations_Record_Model->getData($templateId);
            if(!$template_data){
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
            if(($related_module_name && $related_module_name != $template_data['related_module']) || ($parent_module_name && $parent_module_name != $template_data['parent_module'] ) ){
                $template_data['name'] = $template_name;
                $template_data['parent_module'] = $parent_module_name;
                $template_data['related_module'] = $related_module_name;
                $template_data['blocks'] = array();
            }
        }else{
            $template_data['name'] = $template_name;
            $template_data['parent_module'] = $parent_module_name;
            $template_data['related_module'] = $related_module_name;
        }

        $allRelatedModules = array();
        $allParentModules = Vtiger_Module_Model::getEntityModules();
        $restricted_modules = $WorkAutomations_Module_Model->getRestrictedModules();
        foreach ($allParentModules as $k => $parentModule) {
            $parentModuleName = $parentModule->getName();
            if(!in_array($parentModuleName,$restricted_modules)){
                $allRelatedModules[$parentModuleName] = $WorkAutomations_Module_Model->getRelatedModules($parentModuleName);
            }else{
                unset($allParentModules[$k]);
            }
        }
        $relation_field = $WorkAutomations_Module_Model->getRelatedField($template_data['parent_module'],$template_data['related_module']);

        if($related_module_name) {
            $recordModel = Vtiger_Record_Model::getCleanInstance($related_module_name);
            $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('PARENT_MODULES', $allParentModules);
        $viewer->assign('RELATED_MODULES', $allRelatedModules);
        $viewer->assign('RECORD', $templateId);
        $viewer->assign('RELATION_FIELD', $relation_field);
        $viewer->assign('TEMPLATE_DATA', $template_data);
        $viewer->assign('MODULE', $module);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        echo $viewer->view('Edit.tpl',$module,true);
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);

        $jsFileNames = array(
            "modules.WorkAutomations.resources.Edit",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}