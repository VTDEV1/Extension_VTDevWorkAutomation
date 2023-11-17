
<?php
/* ********************************************************************************
 * The content of this file is subject to the Custom Header/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */
class WorkAutomations_ActionAjax_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
        return true;
    }

    function __construct() {
        parent::__construct();
        $this->vtdevLicense();
        $this->exposeMethod('SaveTemplate');
        $this->exposeMethod('DeleteTemplate');
        $this->exposeMethod('GetTemplates');
        $this->exposeMethod('CreateRelatedRecords');
        $this->exposeMethod('GetOrganizationInfo');
    }
    function vtdevLicense() {
        $vTELicense = WorkAutomations_VTDEVLicense_Model::validate();
        if(!$vTELicense['valid']){
            header("Location: index.php?module=WorkAutomations&parent=Settings&view=Settings&mode=showVtdevStoreRequireScreen");
        }
    }
    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    function GetTemplates(Vtiger_Request $request){
        global $adb;
        $parentModule = $request->get('parent_module');
        $arrTemplates = array();
        $results = $adb->pquery('SELECT * FROM vtdev_work_automations_template WHERE template_parent_module = ? ', array($parentModule));
        if ($adb->num_rows($results) > 0) {
            while ($row = $adb->fetchByAssoc($results)){
                $arrayData = array();
                $arrayData['template_id'] = $row['template_id'];
                $arrayData['template_name'] = $row['template_name'];
                array_push($arrTemplates, $arrayData);
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrTemplates);
        $response->emit();
    }
    function CreateRelatedRecords(Vtiger_Request $request) {
        global $adb;
        $parentModuleName = $request->get('parent_module');
        $templateId = $request->get('template_id');
        $parentRecordId = $request->get('record');
        if($templateId) {
            $resultTemplate = $adb->pquery('SELECT * FROM vtdev_work_automations_template WHERE template_id = ? ', array($templateId));
            if ($adb->num_rows($resultTemplate) > 0) {
                $arrBlocks = $adb->query_result($resultTemplate, 0, 'template_block_field_info');
                $arrBlocks = ZEND_JSON::decode(html_entity_decode($arrBlocks, ENT_QUOTES));
                $relatedModuleName = $adb->query_result($resultTemplate, 0, 'template_related_module');
                $resultRelationFieldId = $adb->pquery('SELECT relationfieldid FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?;', array(getTabid($parentModuleName), getTabid($relatedModuleName)));
                $relationFieldId = $adb->query_result($resultRelationFieldId, 0, 'relationfieldid');
                if($relationFieldId) {
                    $relationFieldName = Vtiger_Field_Model::getInstance($relationFieldId)->getName();
                }
                foreach ($arrBlocks as $arrFields) {
                    $newRecord = Vtiger_Record_Model::getCleanInstance($relatedModuleName);
                    foreach ($arrFields as $fieldName => $fieldValue) {
                        switch (strtolower($fieldValue)){
                            case 'today':
                                $fieldValue = date('Y-m-d');
                                break;
                            case 'tomorrow':
                                $fieldValue = date('Y-m-d',strtotime("+1 days"));
                                break;
                            case 'yesterday':
                                $fieldValue = date('Y-m-d',strtotime("-1 days"));
                                break;
                            case 'nextmonth':
                                $fieldValue = date('Y-m-d',strtotime("+1 months"));
                                break;
                            case 'lastmonth':
                                $fieldValue = date('Y-m-d',strtotime("-1 months"));
                                break;
                            default:
                                break;
                        }
                        $newRecord->set($fieldName, $fieldValue);
                    }
                    $newRecord->save();
                    $newRecordId = $newRecord->getId();
                    if($relationFieldId) {
                        $newRecord = Vtiger_Record_Model::getInstanceById($newRecordId, $relatedModuleName);
                        $newRecord->set('mode', 'edit');
                        $newRecord->set($relationFieldName, $parentRecordId);
                        $newRecord->save();
                    } else {
                        $relationModel = Vtiger_Relation_Model::getInstance(Vtiger_Module_Model::getInstance($parentModuleName), Vtiger_Module_Model::getInstance($relatedModuleName));
                        $relationModel->addRelation($parentRecordId, $newRecordId);
                    }
                }
            }

            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult(array('result'=>'All record are created!'));
            $response->emit();
        }
    }
    function SaveTemplate(Vtiger_Request $request) {
        global $adb;
        $templateName = $request->get('templateName');
        $parentModuleName = $request->get('parentModuleName');
        $relatedModuleName = $request->get('relatedModuleName');
        $template_id = $request->get('template_id');
        $template = $request->get('template');
        if($parentModuleName && $relatedModuleName && $template){
            $rebuild_blocks = array();
            // $relModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
            foreach ($template as $block){
                $rebuild_fields = [];
                foreach ($block as $fields){
                    foreach ($fields as $field){
                        foreach ($field as $name=>$val){
                            /* $fieldModel = Vtiger_Field_Model::getInstance($name,$relModuleModel);
                            $fieldDataType = $fieldModel->getFieldDataType();
                            if($fieldDataType == 'date'){
                                $val = Vtiger_Date_UIType::getDBInsertedValue($val);
                            } */
                            $rebuild_fields[$name]=$val;
                        }
                    }
                }
                $rebuild_blocks[]=$rebuild_fields;
            }
            $template = ZEND_JSON::encode($rebuild_blocks);
            if($template_id) {
                $adb->pquery("UPDATE vtdev_work_automations_template SET template_name = ?, template_parent_module = ?, template_related_module = ?, template_block_field_info = ? WHERE template_id = ?;",array($templateName, $parentModuleName, $relatedModuleName, $template, $template_id));
            } else {
                $adb->pquery("INSERT INTO vtdev_work_automations_template (template_name, template_parent_module, template_related_module, template_block_field_info) VALUE (?, ?, ?, ?)",array($templateName, $parentModuleName, $relatedModuleName, $template));
                $template_id = $adb->getLastInsertID();
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        if($template_id){
            $response->setResult(array('result'=>'success', 'template_id'=>$template_id));
        }else{
            $response->setResult(array('result'=>'Can not save Template'));
        }
        $response->emit();
    }

    function DeleteTemplate(Vtiger_Request $request) {
        global $adb;
        $templateId = $request->get('template_id');
        if($templateId)
            $adb->pquery("DELETE FROM vtdev_work_automations_template WHERE template_id = ?;",array($templateId));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('result'=>'success'));
        $response->emit();
    }

    public function GetOrganizationInfo(Vtiger_Request $request) {
        $accountId = $request->get('accountid');
        $accountRecordModel = Vtiger_Record_Model::getInstanceById($accountId);
        $billStreet = $accountRecordModel->get('bill_street');
        $shipStreet = $accountRecordModel->get('ship_street');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('bill_street' => $billStreet, 'ship_street' => $shipStreet));
        $response->emit();
    }
}