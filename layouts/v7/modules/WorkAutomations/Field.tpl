{if $SELECTED_FIELDNAME neq 'time_start'}
    <tr class="WA-field {if $NEW_FIELD}WA-field-new{/if}">
        <td class="fieldLabel">
                <select {if $SELECTED_FIELD_MODEL && $SELECTED_FIELD_MODEL->isMandatory()}disabled{/if} class="select2" name="WA-field-name" style="min-width: 250px">
                    <option value="">None</option>
                    {foreach from=$RELATED_MODULE_FIELDS item=FIELD_MODEL}
                    {if $FIELD_MODEL->isEditable() && $FIELD_MODEL->get('name') != $RELATION_FIELD}
                        <option {if $SELECTED_FIELDNAME eq $FIELD_MODEL->get('name')}selected{/if} value="{$FIELD_MODEL->get('name')}">{vtranslate($FIELD_MODEL->get('label'), $RELATED_MODULE)}</option>
                    {/if}
                    {/foreach}
                </select>
                {if $SELECTED_FIELD_MODEL && $SELECTED_FIELD_MODEL->isMandatory()}<span class="redColor">*</span>{/if}
        </td>
        <td class="WA-field-value fieldValue ">
            {include file="FieldInput.tpl"|@vtemplate_path: $MODULE  SELECTED_FIELD_MODEL=$SELECTED_FIELD_MODEL RELATED_MODULE=$RELATED_MODULE SELECTED_FIELDNAME=$SELECTED_FIELDNAME RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
        </td>
        <td>
            <span class="cursorPointer col-lg-1"><i class="alignMiddle deleteField fa fa-trash"></i></span>
        </td>
    </tr>
{/if}