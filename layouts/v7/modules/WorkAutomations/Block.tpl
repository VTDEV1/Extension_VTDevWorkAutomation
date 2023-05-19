<div class="WA-blockContainer {if $NEW_BLOCK}WA-block-new{/if} blockContainer">
    <div class="block-header">
        <button type="button" class="btn btn-default addFieldBtn">Add Field</button>
    </div>
    <br>
    <div class="fieldBlockContainer">
        <table class="table table-borderless">
            {assign var=RELATED_MODULE_MODEL value=Vtiger_Module_Model::getInstance($RELATED_MODULE)}
            {assign var=RELATED_MODULE_FIELDS value=$RELATED_MODULE_MODEL->getFields()}
            {if $BLOCK_DATA}
                {foreach from=$BLOCK_DATA key=SELECTED_FIELDNAME item=VALUE}
                    {assign var=SELECTED_FIELD_MODEL value=Vtiger_Field_Model::getInstance($SELECTED_FIELDNAME, $RELATED_MODULE_MODEL)}
                    {assign var=SELECTED_FIELD_MODEL value=$SELECTED_FIELD_MODEL->set('fieldvalue',$VALUE)}
                    {if $SELECTED_FIELDNAME != $RELATION_FIELD}
                        {include file="Field.tpl"|@vtemplate_path: $MODULE  RELATED_MODULE_FIELDS=$RELATED_MODULE_FIELDS SELECTED_FIELDNAME=$SELECTED_FIELDNAME SELECTED_FIELD_MODEL=$SELECTED_FIELD_MODEL RELATION_FIELD=$RELATION_FIELD RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
                    {/if}
                {/foreach}
            {else}
                {foreach from=$RELATED_MODULE_FIELDS item=M_FIELD_MODEL}
                    {if $M_FIELD_MODEL->isMandatory() && $M_FIELD_MODEL->get('name') != $RELATION_FIELD}
                        {assign var=SELECTED_FIELDNAME value=$M_FIELD_MODEL->get('name')}
                        {assign var=SELECTED_FIELD_MODEL value=Vtiger_Field_Model::getInstance($SELECTED_FIELDNAME, $RELATED_MODULE_MODEL)}
                        {assign var=SELECTED_FIELD_MODEL value=$SELECTED_FIELD_MODEL->set('fieldvalue',$SELECTED_FIELD_MODEL->get('defaultvalue'))}
                        {include file="Field.tpl"|@vtemplate_path: $MODULE  RELATED_MODULE_FIELDS=$RELATED_MODULE_FIELDS SELECTED_FIELDNAME=$SELECTED_FIELDNAME SELECTED_FIELD_MODEL=$SELECTED_FIELD_MODEL RELATION_FIELD=$RELATION_FIELD RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
                    {/if}
                {/foreach}
            {/if}
        </table>
    </div>
    <div class="block-footer">
        <button type="button" class="btn btn-default pull-right deleteBlock"><i style="color: red" class="alignLeft fa fa-trash"></i> Delete Row</button>
    </div>
</div>