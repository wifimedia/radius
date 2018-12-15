Ext.define('Rd.view.activityMonitor.gridRadpostauths' ,{
    extend:'Ext.grid.Panel',
    alias : 'widget.gridRadpostauths',
    multiSelect: true,
    store : 'sRadpostauths',
    stateful: true,
    stateId: 'StateGridRadpostauths',
    stateEvents:['groupclick','columnhide'],
    border: false,
    requires: [
        'Rd.view.components.ajaxToolbar'
    ],
    viewConfig: {
        loadMask:true
    },
    urlMenu: '/cake2/rd_cake/radpostauths/menu_for_grid.json',
    bbar: [
        {   xtype: 'component', itemId: 'count',   tpl: i18n('sResult_count_{count}'),   style: 'margin-right:5px', cls: 'lblYfi' }
    ],
    initComponent: function(){
        var me      = this;
        var filters = {
            ftype   : 'filters',
            encode  : true, 
            local   : false
        };
        me.tbar     = Ext.create('Rd.view.components.ajaxToolbar',{'url': me.urlMenu});
        me.features = [filters];

        me.columns  = [
            {xtype: 'rownumberer',stateId: 'StateGridRadpostauths1'},
            { text: i18n('sUsername'),      dataIndex: 'username',      tdCls: 'gridTree', flex: 1,filter: {type: 'string'},stateId: 'StateGridRadpostauths2'},
            { text: i18n('sPassword'),      dataIndex: 'pass',          tdCls: 'gridTree', flex: 1,filter: {type: 'string'},stateId: 'StateGridRadpostauths3'},
            { text: i18n('sRealm'),         dataIndex: 'realm',         tdCls: 'gridTree', flex: 1,filter: {type: 'string'},stateId: 'StateGridRadpostauths4'},
            { 
                text            : i18n('sReply'),         
                dataIndex       : 'reply',         
                tdCls           : 'gridTree', 
                flex            : 1,
                filter          : {type: 'string'},
                xtype           : 'templatecolumn',
                tpl             : new Ext.XTemplate(
                                "<tpl if='reply == \"Access-Reject\"'><div class=\"noRight\">{reply}</div>",
                                '<tpl else>',
                                '{reply}',
                                '</tpl>'
                            ),stateId: 'StateGridRadpostauths5'
            },
            { text: i18n('sNasname'),       dataIndex: 'nasname',       tdCls: 'gridTree', flex: 1,filter: {type: 'string'},stateId: 'StateGridRadpostauths6'},
            { text: i18n('sDate'),          dataIndex: 'authdate',      tdCls: 'gridTree', flex: 1,filter: {type: 'date',dateFormat: 'Y-m-d'},stateId: 'StateGridRadpostauths7'}
        ];

        //Create a mask and assign is as a property to the window
        me.mask = new Ext.LoadMask(me, {msg: i18n('sConnecting')+" ...."}); 
        me.callParent(arguments);
    }
});
