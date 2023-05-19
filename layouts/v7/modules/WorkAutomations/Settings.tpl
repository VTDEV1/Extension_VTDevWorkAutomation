{*<!--
/* ********************************************************************************
* The content of this file is subject to the Work_Automations ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is vtigerdev.com
* Portions created by vtigerdev.com. are Copyright(C) vtigerdev.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<style>
    .list-view-header{
        background-color: #f3f3f3;
        text-align: center;
    }
    .list-view-entry{
        text-align: center;
        cursor: default;
    }
</style>
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('MODULE_LBL', $MODULE)}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="listViewContentDiv" id="listViewContents">
        <div class="listViewEntriesDiv contents-bottomscroll">
            <div class="bottomscroll-div">
                {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
                <table class="table table-bordered listViewEntriesTable" >
                    <thead>
                    <tr class="listViewHeaders">
                        {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                            <th class="list-view-header">
                                {vtranslate($LISTVIEW_HEADER, $MODULE)}
                            </th>
                        {/foreach}
                        <th class="list-view-header" style="width: 230px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach item=LISTVIEW_ENTRY from=$TEMPLATES name=listview}
                        <tr>
                            {foreach item=LISTVIEW_HEADER key=COLUMNNAME from=$LISTVIEW_HEADERS}
                                <td class="list-view-entry">
                                    {vtranslate($LISTVIEW_ENTRY[$COLUMNNAME], $MODULE)}
                                </td>
                                {if $LISTVIEW_HEADER@last}
                                    <td>
                                        <div class="actions text-center">
                                            <span class="actionImages">
                                                <a href='index.php?module={$MODULE}&view=Edit&template_id={$LISTVIEW_ENTRY['template_id']}'>
                                                    <i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil glyphicon glyphicon-pencil"></i>&nbsp;Edit
                                                </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a data-id="{$LISTVIEW_ENTRY['template_id']}'" class="delete-template" href="javascript:void(0)">
                                                    <i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash glyphicon glyphicon-trash"></i>&nbsp;Delete
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <!--added this div for Temporarily -->
                {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                    <table class="emptyRecordsDiv">
                        <tbody>
                        <tr>
                            <td>
                                {assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
                                {vtranslate('LBL_NO')} {vtranslate('LBL_TEMPLATE', $MODULE)} {vtranslate('LBL_FOUND')}.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                {/if}
            </div>
        </div>
    </div>
    <div class="modal-overlay-footer clearfix">
        <div class="row clearfix">
            <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
                <a href="index.php?module={$MODULE}&view=Edit" type="button" class="btn btn-primary" id="btnAddTemplate">
                    <i class="icon-plus glyphicon glyphicon-plus"></i>&nbsp;Add&nbsp;Template
                </a>
            </div>
        </div>
    </div>
</div>