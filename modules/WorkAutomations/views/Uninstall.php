<?php
/* ********************************************************************************
 * The content of this file is subject to the Work_Automations ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtigerdev.com
 * Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include_once 'vtlib/Vtiger/Module.php';
class WorkAutomations_Uninstall_View extends Settings_Vtiger_Index_View {

    function process (Vtiger_Request $request) {
        global $adb;
        echo '<div class="container-fluid">
                <div class="widget_header row-fluid">
                    <h3>Work_Automations</h3>
                </div>
                <hr>';
        // Uninstall module
        $module = Vtiger_Module::getInstance('WorkAutomations');
        if ($module) $module->delete();

        // Remove related data
        $message = $this->removeData();
        echo $message;

        // remove directory
        $res_template = $this->delete_folder('layouts/v7/modules/WorkAutomations');
        echo "&nbsp;&nbsp;- Delete Work_Automations template folder";
        if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        $res_module = $this->delete_folder('modules/WorkAutomations');
        echo "&nbsp;&nbsp;- Delete Work_Automations module folder";
        if($res_module) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';
        // Remove module from other settings
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?",array('Work_Automations'));
        echo "Module was Uninstalled.";
        echo '</div>';
    }

    function delete_folder($tmp_path){
        if(!defined('DS')){
            define('DS','/');
        }
        // check and set folder access
        if(!is_writeable($tmp_path) && is_dir($tmp_path)) {
            chmod($tmp_path,0777);
        }
        $handle = opendir($tmp_path);
        while($tmp=readdir($handle)) {
            if($tmp!='..' && $tmp!='.' && $tmp!=''){
                // check and set file access before delete file
                if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
                    unlink($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)){
                    chmod($tmp_path.DS.$tmp,0666);
                    unlink($tmp_path.DS.$tmp);
                }

                // check and set folder access before delete folder
                if(is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)) {
                    $this->delete_folder($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)){
                    chmod($tmp_path.DS.$tmp,0777);
                    $this->delete_folder($tmp_path.DS.$tmp);
                }
            }
        }
        closedir($handle);
        rmdir($tmp_path);
        if(!is_dir($tmp_path)) {
            return true;
        } else {
            return false;
        }
    }

    /* ********************************************************************************

	 * ****************************************************************************** */
    function removeData(){
        global $adb;
        $message='';

        // drop tables
        $sql = "DROP TABLE IF EXISTS vtdev_work_automations_settings, vtdev_work_automations_template;";
        $result = $adb->pquery($sql,array());
        $message.= "&nbsp;&nbsp;- Delete Work_Automations tables";
        if($result) $message.= " - DONE"; else $message.= " - <b>ERROR</b>";
        $message.= '<br>';

        return $message;
    }
}