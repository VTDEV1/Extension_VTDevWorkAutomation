<?php
/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */
class WorkAutomations_Settings_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }
    public function process(Vtiger_Request $request) {
        $this->renderSettingsUI($request);
    }
    function renderSettingsUI(Vtiger_Request $request) {
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $this->initializeListViewContents($request, $viewer);
        echo $viewer->view('Settings.tpl',$module,true);
    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();

        $listViewHeaders = array(
            'template_id' => 'ID',
            'template_name' => 'Template Name',
            'template_parent_module' => 'Parent Module',
            'template_related_module' => 'Related Module'
        );
        $viewer->assign('LISTVIEW_HEADERS', $listViewHeaders);
        $templates = WorkAutomations_Module_Model::GetAllTemplates();
        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', count($templates));
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.WorkAutomations.resources.Settings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}