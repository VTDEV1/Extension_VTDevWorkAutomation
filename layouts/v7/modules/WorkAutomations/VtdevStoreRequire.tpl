{*/* * *******************************************************************************
* The content of this file is subject to the Google Address Lookup ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is vtdevsolutions.com
* Portions created by vtdevsolutions.com. are Copyright(C)vtdevsolutions.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="installationContents" style="border:1px solid #ccc;padding:2%;">
        <form name="activateLicenseForm" action="index.php" method="post" id="installation_step2" class="form-horizontal">
            <input type="hidden" class="step" value="2" />

            <div class="row">
                <label>
                    <strong>{vtranslate('Thank you for choosing the WorkAutomations extension',$QUALIFIED_MODULE)}</strong>
                </label>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div>
                    <span>
                        {if $VTDEVLICENSE['notInstalled']}
                            {vtranslate('LBL_YOU_ARE_REQUIRED_VALIDATE',$QUALIFIED_MODULE)}
                        {else}
                            {vtranslate('Please active VTEDEV store to use all our extensions',$QUALIFIED_MODULE)}
                        {/if}
                    </span>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px; margin-top: 5px">
                <span class="col-lg-1">
                    <strong>{vtranslate('LBL_VTIGER_URL',$QUALIFIED_MODULE)}</strong>
                </span>
                <span class="col-lg-4">
                    {$SITE_URL}
                </span>
            </div>
            {if !$VTDEVLICENSE['valid']}
                <div class="alert alert-danger" id="error_message">
                    {$VTDEVLICENSE['message']}
                </div>
            {/if}


            <div class="row">
                <div><span>{vtranslate('if you encounter any problems while installing extensions,',$QUALIFIED_MODULE)} {vtranslate('please Contact Us!',$QUALIFIED_MODULE)}</span></div>
            </div>
            <div class="row">
                <ul style="padding-left: 10px;">
                    <li style="list-style-type: none;"><i class="fa fa-envelope fa-2x"></i>&nbsp <a style="color: #0088cc; text-decoration:none;" href="mailto:Support@vtdevsolutions.com">Support@vtdevsolutions.com</a></li>
                    <li style="list-style-type: none;"><i class="fa fa-phone fa-2x"></i>&nbsp <span>+1 (209) 437-4542</span></li>
                    <li style="list-style-type: none;"><i class="fa fa-skype fa-2x"></i>&nbsp <a style="color: #0088cc; text-decoration:none;" href="skype:profile_name?vtdev_support">{vtranslate('Chat with us on Skype',$QUALIFIED_MODULE)}</a></li>
                    <li style="list-style-type: none;"><i class="fa fa-whatsapp fa-2x"></i>&nbsp <a style="color: #0088cc; text-decoration:none;" href="whatsapp://send?text=Hello World!&phone=+12094374542">{vtranslate('Chat with us on WhatsApp',$QUALIFIED_MODULE)}</a></li>
                </ul>
            </div>

            <div class="row">
                <center>
                    {if $VTDEVLICENSE['notInstalled']}
                        <a href="{$VTDEV_STORE_LINK}" download>Download VTDEVStore extension now</a>
                    {else}
                        <a href="index.php?module=VTDEVStore&parent=Settings&view=Settings">Active VTDEVStore extension</a>
                    {/if}
                </center>
            </div>
    </div>
    <div class="clearfix"></div>
    </form>
    </div>
{/strip}