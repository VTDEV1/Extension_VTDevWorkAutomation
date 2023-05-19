<?php
class WorkAutomations_EditAjax_View extends Vtiger_IndexAjax_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('generateNewBlock');
        $this->exposeMethod('generateNewField');
        $this->exposeMethod('generateFieldInput');
    }
    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    function generateFieldInput(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $related_module_name = $request->get('related_module_name');
        $field_name = $request->get('field_name');
        $RELATED_MODULE_MODEL = Vtiger_Module_Model::getInstance($related_module_name);
        $field_model = Vtiger_Field_Model::getInstance($field_name, $RELATED_MODULE_MODEL);
        $field_model->set('fieldvalue',$field_model->get('defaultvalue'));
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $related_module_name);
        $viewer->assign('SELECTED_FIELDNAME', $field_name);
        $viewer->assign('SELECTED_FIELD_MODEL', $field_model);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('FieldInput.tpl',$moduleName,true);
    }
    function generateNewField(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $parent_module_name = $request->get('parent_module_name');
        $related_module_name = $request->get('related_module_name');
        $RELATED_MODULE_MODEL = Vtiger_Module_Model::getInstance($related_module_name);
        $RELATED_MODULE_FIELDS = $RELATED_MODULE_MODEL->getFields();
        $viewer = $this->getViewer($request);
        $relation_field = WorkAutomations_Module_Model::getRelatedField($parent_module_name,$related_module_name);
        $viewer->assign('RELATION_FIELD', $relation_field);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $related_module_name);
        $viewer->assign('RELATED_MODULE_FIELDS', $RELATED_MODULE_FIELDS);
        $viewer->assign('SELECTED_FIELDNAME', false);
        $viewer->assign('SELECTED_FIELD_MODEL', false);
        $viewer->assign('NEW_FIELD', true);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('Field.tpl',$moduleName,true);
    }
    function generateNewBlock(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $parent_module_name = $request->get('parent_module_name');
        $related_module_name = $request->get('related_module_name');

        $viewer = $this->getViewer($request);
        $relation_field = WorkAutomations_Module_Model::getRelatedField($parent_module_name,$related_module_name);
        $viewer->assign('RELATION_FIELD', $relation_field);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $related_module_name);
        $viewer->assign('NEW_BLOCK', true);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('Block.tpl',$moduleName,true);
    }
}