{if $SELECTED_FIELD_MODEL}
    {assign var=FIELD_DATA_TYPE value=$SELECTED_FIELD_MODEL->getFieldDataType()}
    {if $FIELD_DATA_TYPE eq 'date' or $FIELD_DATA_TYPE eq 'datetime'}
        <select class='inputElement select2 select2-offscreen' id={$SELECTED_FIELD_MODEL->get('name')} name={$SELECTED_FIELD_MODEL->get('name')} data-fieldname={$SELECTED_FIELD_MODEL->get('name')}>
            <option value="">Select an Option</option>
            <option value="today" {if $SELECTED_FIELD_MODEL->get('fieldvalue') eq 'today'}selected{/if}>Today</option>
            <option value="tomorrow" {if $SELECTED_FIELD_MODEL->get('fieldvalue') eq 'tomorrow'}selected{/if}>Tomorrow</option>
            <option value="yesterday" {if $SELECTED_FIELD_MODEL->get('fieldvalue') eq 'yesterday'}selected{/if}>Yesterday</option>
            <option value="nextmonth" {if $SELECTED_FIELD_MODEL->get('fieldvalue') eq 'nextmonth'}selected{/if}>Next Month</option>
            <option value="lastmonth" {if $SELECTED_FIELD_MODEL->get('fieldvalue') eq 'lastmonth'}selected{/if}>Last Month</option>
        </select>
    {else}
        {include file=vtemplate_path($SELECTED_FIELD_MODEL->getUITypeModel()->getTemplateName(),$RELATED_MODULE) FIELD_MODEL=$SELECTED_FIELD_MODEL FIELD_NAME=$SELECTED_FIELDNAME RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
    {/if}
{else}
    <input type="text" value="" class="inputElement"/>
{/if}