<?php
/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class WorkAutomations_Module_Model extends Vtiger_Module_Model {

    function getSettingLinks() {
        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Settings',
            'linkurl' => 'index.php?module=WorkAutomations&parent=Settings&view=Settings',
            'linkicon' => ''
        );

        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Uninstall',
            'linkurl' => 'index.php?module=WorkAutomations&parent=Settings&view=Uninstall',
            'linkicon' => ''
        );
        return $settingsLinks;
    }
    public function getRelatedField($parentModuleName,$relatedModuleName) {
        global $adb;
        $relationFieldName = '';
        $resultRelationFieldId = $adb->pquery('SELECT relationfieldid FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?;', array(getTabid($parentModuleName), getTabid($relatedModuleName)));
        $relationFieldId = $adb->query_result($resultRelationFieldId, 0, 'relationfieldid');
        if($relationFieldId){
            $relationFieldName = Vtiger_Field_Model::getInstance($relationFieldId)->getName();
        }
        return $relationFieldName;
    }
    public function getRestrictedModules() {
        $restricted_modules = array('Events','Webmails','Emails','PBXManager','Documents','ModComments');
        return $restricted_modules;
    }
    static public function GetAllTemplates() {
        global $adb;
        $arrTemplates = array();
        $result = $adb->pquery('SELECT * FROM vtdev_work_automations_template;', array());
        if ($adb->num_rows($result) > 0) {
            while($row = $adb->fetchByAssoc($result)) {
                array_push($arrTemplates, $row);
            }
        }
        return $arrTemplates;
    }
    public function getRelatedModules($parentModuleName) {
        global $adb;
        $relatedModules = array();
        $restricted_modules = self::getRestrictedModules();
        $result = $adb->pquery(
            "SELECT vtiger_tab.name, vtiger_relatedlists.tabid FROM vtiger_tab
					INNER JOIN vtiger_relatedlists on vtiger_tab.tabid=vtiger_relatedlists.related_tabid
					WHERE vtiger_tab.isentitytype=1
					AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
					AND vtiger_relatedlists.tabid = ?
					AND vtiger_tab.presence = 0 AND vtiger_relatedlists.label!='Activity History'
					UNION
					SELECT module, vtiger_tab.tabid FROM vtiger_fieldmodulerel
					INNER JOIN vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.relmodule
					INNER JOIN vtiger_tab AS vtiger_tabrel ON vtiger_tabrel.name = vtiger_fieldmodulerel.module AND vtiger_tabrel.presence = 0
                    INNER JOIN vtiger_field ON vtiger_field.fieldid = vtiger_fieldmodulerel.fieldid
					WHERE vtiger_tab.isentitytype = 1
					AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
					AND vtiger_tab.tabid = ?
					AND vtiger_tab.presence = 0
                    AND vtiger_field.fieldname NOT LIKE ?",
            array($restricted_modules,getTabid($parentModuleName),$restricted_modules,getTabid($parentModuleName), 'cf_%')
        );
        if ($adb->num_rows($result) > 0) {
            while($row = $adb->fetchByAssoc($result)) {
                $relatedModules[$row['name']] = vtranslate($row['name'],$row['name']);
            }
        }
        return $relatedModules;
    }
}