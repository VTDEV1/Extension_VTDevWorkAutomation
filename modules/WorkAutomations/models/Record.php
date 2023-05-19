<?php
class WorkAutomations_Record_Model extends Vtiger_Record_Model {

    public function getData($record) {
        global $adb;
        $arrTemplates = array();
        $result = $adb->pquery('SELECT * FROM vtdev_work_automations_template WHERE template_id = ? ', array($record));
        if ($adb->num_rows($result) > 0) {
            $template_name = $adb->query_result($result, 0, 'template_name');
            $template_parent_module = $adb->query_result($result, 0, 'template_parent_module');
            $template_related_module = $adb->query_result($result, 0, 'template_related_module');
            $template_block_field_info = $adb->query_result($result, 0, 'template_block_field_info');
            $arrTemplates['name'] = $template_name;
            $arrTemplates['parent_module'] = $template_parent_module;
            $arrTemplates['related_module'] = $template_related_module;
            $arrBlocks = ZEND_JSON::decode(html_entity_decode($template_block_field_info, ENT_QUOTES));
            $arrTemplates['blocks'] = $arrBlocks;
        }
        return $arrTemplates;
    }
}