<style>
    .WA-settingViewPageDiv{
        padding-left: 58px;
        padding-right: 22px;
    }
    .WA-blockContainer{
        border: 1px solid rgba(2, 2, 2, .2); border-radius: 5px; display: grid; margin-top: 5px;
    }
    td.fieldLabel,td.fieldValue{
        width: 30%;
    }
    .WA-blockContainer-clone{
        border: 1px solid rgba(2, 2, 2, .2); border-radius: 5px; display: grid; margin-top: 5px;
    }
    .block-header{
        padding-left: 7px;
        padding-top: 7px;
    }
    .block-footer{
        padding-right: 7px;
        padding-bottom: 7px;
    }
</style>
<input type=hidden id="template_id" value='{$RECORD}' />
<input type=hidden id="allRelatedModules" value='{ZEND_JSON::encode($RELATED_MODULES)}' />
<div class="WA-settingViewPageDiv">
    <div class="form-group" id="EditViewWorkAutomations">
        <form name="FormEditTemplate" action="index.php" method="post" id="EditView" class="form-horizontal">
            <hr style="margin-top: 0px !important;">
            <div class="settingViewBody">
                <div class="settingViewContents" style="text-align: center; ">
                    <div class="form-group">
                        <label for="template_name" class="col-sm-3 control-label">
                            Template Name
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-5 controls">
                            <input class="form-control col-sm-6 pull-left" id="template_name" name="template_name" required="true" data-placeholder="Template Name" style="text-align: left" value="{$TEMPLATE_DATA['name']}">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="parent_module_name" class="col-sm-3 control-label">
                            Parent Module
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-5 controls">
                            <select class="select2 col-sm-6 pull-left" id="parent_module_name" name="parent_module_name" required="true" data-placeholder="Select Module..." style="text-align: left">
                                <option value="">Select an option</option>
                                {foreach from=$PARENT_MODULES key=TABID item=MODULE_MODEL}
                                    {assign var=MODULE_NAME value=$MODULE_MODEL->getName()}
                                    <option value="{$MODULE_NAME}" {if $TEMPLATE_DATA['parent_module'] == $MODULE_NAME} selected {/if}>
                                        {if $MODULE_NAME eq 'Calendar'}
                                            {vtranslate('LBL_TASK', $MODULE_NAME)}
                                        {else}
                                            {vtranslate($MODULE_NAME, $MODULE_NAME)}
                                        {/if}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">
                            Related Module
                        </label>
                        <div class="col-sm-5 controls">
                            <select class="select2 col-sm-6 pull-left" id="related_module_name" name="related_module_name" data-placeholder="Select Module..." style="text-align: left">
                                <option value="">Select an option</option>
                                {if $RELATED_MODULES[$TEMPLATE_DATA['parent_module']]}
                                    {foreach from=$RELATED_MODULES[$TEMPLATE_DATA['parent_module']] key=REL_MODULE_NAME item=REL_MODULE_LABEL}
                                        <option value="{$REL_MODULE_NAME}" {if $TEMPLATE_DATA['related_module'] == $REL_MODULE_NAME} selected {/if}>
                                            {if $REL_MODULE_NAME eq 'Calendar'}
                                                {vtranslate('LBL_TASK', $REL_MODULE_NAME)}
                                            {else}
                                                {vtranslate($REL_MODULE_NAME, $REL_MODULE_NAME)}
                                            {/if}
                                        </option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group" id="WA-blocks" style="margin-bottom: 10px;">
                {if $TEMPLATE_DATA['blocks']}
                    {foreach from=$TEMPLATE_DATA['blocks'] item=BLOCK}
                        {include file="Block.tpl"|@vtemplate_path: $MODULE BLOCK_DATA=$BLOCK RELATED_MODULE=$TEMPLATE_DATA['related_module'] RELATION_FIELD=$RELATION_FIELD RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
                    {/foreach}
                {elseif $TEMPLATE_DATA['related_module']}
                    {include file="Block.tpl"|@vtemplate_path: $MODULE  RELATED_MODULE=$TEMPLATE_DATA['related_module'] RELATION_FIELD=$RELATION_FIELD RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
                {/if}
            </div>
            <div class="clearfix" style="margin-bottom: 60px;">
                <button type="button" class="btn btn-primary addButton pull-left" id="addNewBlockBtn"><i class="alignLeft fa fa-plus"></i> Add New Row</button>
            </div>
        </form>
    </div>
    <div class="clearfix">
        <div class="row">
            <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                <button type='button' class='btn btn-success saveButton' id="saveTemplateButton">{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                <a class='cancelLink' href="index.php?module=WorkAutomations&parent=Settings&view=Settings" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            </div>
        </div>
    </div>
</div>	