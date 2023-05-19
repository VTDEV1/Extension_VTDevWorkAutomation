<?php
/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com are Copyright(C) vtigerdev.com
 * All Rights Reserved.
 * ****************************************************************************** */

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class WorkAutomations extends CRMEntity {
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
        if($event_type == 'module.postinstall') {
            self::addWidgetTo();
            self::iniData();
            self::resetValid();
        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
            self::removeWidgetTo();
            self::removeEventHandle();
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
            self::addWidgetTo();
        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
            self::removeWidgetTo();
            self::removeEventHandle();
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
            self::removeWidgetTo();
            self::removeEventHandle();
            self::addWidgetTo();
            self::iniData();
            self::resetValid();
        }
    }

    static function iniData() {
        global $adb;

    }

    /**
     * Add header script to other module.
     * @return unknown_type
     */
    static function addWidgetTo() {
        global $adb;
        $widgetType = 'HEADERSCRIPT';
        $widgetName = 'WorkAutomationsJs';
        $link = 'layouts/v7/modules/WorkAutomations/resources/WorkAutomations.js';
        include_once 'vtlib/Vtiger/Module.php';

        $moduleNames = array('WorkAutomations');
        foreach($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if($module) {
                $module->addLink($widgetType, $widgetName, $link);
            }
        }
        $adb->pquery("UPDATE vtiger_settings_field_seq SET id=(SELECT MAX(fieldid) FROM vtiger_settings_field)",array());
        $max_id=$adb->getUniqueID('vtiger_settings_field');
        $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`) VALUES (?, ?, ?, ?, ?, ?)",array($max_id,'4','Work_Automations', 'Settings area for Work_Automations', 'index.php?module=WorkAutomations&parent=Settings&view=Settings',$max_id));
    }

    static function removeWidgetTo() {
        global $adb;
        $widgetType = 'HEADERSCRIPT';
        $widgetName = 'WorkAutomationsJs';
        $link = 'layouts/v7/modules/WorkAutomations/resources/WorkAutomations.js';
        include_once 'vtlib/Vtiger/Module.php';

        $moduleNames = array('WorkAutomations');
        foreach($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if($module) {
                $module->deleteLink($widgetType, $widgetName, $link);
            }
        }
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?",array('Work_Automations'));
    }

    static function resetValid() {
        global $adb;
        $adb->pquery("DELETE FROM `vtdev_modules` WHERE module=?;",array('WorkAutomations'));
        $adb->pquery("INSERT INTO `vtdev_modules` (`module`, `valid`) VALUES (?, ?);",array('WorkAutomations','0'));
    }

    // Remove handle
    static function removeEventHandle(){
        global $adb;
        $em = new VTEventsManager($adb);
        $em->unregisterHandler('WorkAutomationsHandler');
    }
}