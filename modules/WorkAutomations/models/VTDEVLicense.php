<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoter ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vtdevsolutions.com
 * Portions created by vtdevsolutions.com. are Copyright(C) vtdevsolutions.com.
 * All Rights Reserved.
 * ****************************************************************************** */
class WorkAutomations_VTDEVLicense_Model {
    public static function validate() {
        if(!class_exists('VTDEVStore_VTDEVModule_Model')) $result =  array('valid'=>false,"notInstalled"=> true,"message" => "VTEDEV Extension was not installed");
        else{
            $modelInstance = new VTDEVStore_VTDEVModule_Model();
            $license = $modelInstance->checkLicense();
            if(!$license['result']){
                if($license['detail'] == "NOT_INIT"){
                    $message =  vtranslate('PLEASE_REGISTER_TO_VTDPLEASE_REGISTER_TO_VTDEV_STOREEV_STORE','VTDEVStore');
                    $result =  array('valid'=>false,"notInstalled"=> true,"message" => $message);
                }
                elseif($license['detail'] == "EXPIRED"){
                    $message =  vtranslate('TRIAL_EXPIRED','VTDEVStore');
                    $result =  array('valid'=>false,"notInstalled"=> false,"message" => $message);
                }

            }
            else $result =  array('valid'=>true,"notInstalled"=> false,"message" => "");
        }
        return $result;
    }
}