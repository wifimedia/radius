Ext.define('Rd.view.iPPools.gridIpPools' ,{
    extend      :'Ext.grid.Panel',
    alias       : 'widget.gridIpPools',
    multiSelect : true,
    store       : 'sIpPools',
    stateful    : true,
    stateId     : 'StateGridIpPools',
    stateEvents :['groupclick','columnhide'],
    border      : true,
    requires: [
        'Rd.view.components.ajaxToolbar'
    ],
    viewConfig: {
        loadMask    :true
    },
    urlMenu: '/cake2/rd_cake/ip_pools/menu_for_grid.json',
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
            { xtype: 'rownumberer',stateId: 'StateGridIpPools1', width: Rd.config.buttonMargin },
            { 

                text        :'Name', 
                dataIndex   : 'pool_name',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : false,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools2'
            },
            { 

                text        : 'IP Address', 
                dataIndex   : 'framedipaddress',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : false,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools3'
            },
            { 

                text        : 'Last NAS IP', 
                dataIndex   : 'nasipaddress',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools4'
            },
            { 
                text        : 'Called ID (NAS MAC)', 
                dataIndex   : 'calledstationid',   
                tdCls       : 'gridTree', 
                flex        : 1,
				hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools5'
            },
			{ 
                text        : 'Calling ID (Client MAC)', 
                dataIndex   : 'callingstationid',   
                tdCls       : 'gridTree', 
                flex        : 1,
				hidden      : false,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools6'
            },
            { 

                text        : 'Expiry time', 
                dataIndex   : 'expiry_time',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : false,
                xtype       : 'datecolumn',   
                format      :'Y-m-d H:i:s',
                filter      : {type: 'date',dateFormat: 'Y-m-d'},stateId: 'StateGridIpPools7'
            },
            { 

                text        : 'Username', 
                dataIndex   : 'username',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : false,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools8'
            },
			{ 

                text        : 'Pool key', 
                dataIndex   : 'pool_key',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools9'
            },
			{ 

                text        : 'NAS ID', 
                dataIndex   : 'nasidentifier',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools10'
            },
			{ 

                text        : 'Extra name', 
                dataIndex   : 'extra_name',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools11'
            },
			{ 

                text        : 'Extra value', 
                dataIndex   : 'extra_value',          
                tdCls       : 'gridTree', 
                flex        : 1,
                hidden      : true,
                filter      : {type: 'string'},
                stateId     : 'StateGridIpPools12'
            },
			{ 
                text        : 'Active',  
                xtype       : 'templatecolumn', 
                tpl         : new Ext.XTemplate(
                                "<tpl if='active == true'><div class=\"hasRight\">"+i18n("sYes")+"</div></tpl>",
                                "<tpl if='active == false'><div class=\"noRight\">"+i18n("sNo")+"</div></tpl>"
                            ),
                dataIndex   : 'active',
                filter      : { type: 'boolean'},stateId: 'StateGridIpPools13'
            },
            { 
                text        : 'Created',
                dataIndex   : 'created', 
                tdCls       : 'gridTree',
                hidden      : true, 
                flex        : 1,
                xtype       : 'datecolumn',   
                format      :'Y-m-d H:i:s',
                filter      : {type: 'date',dateFormat: 'Y-m-d'},stateId: 'StateGridIpPools14'
            },
            { 
                text        : 'Modified',
                dataIndex   : 'modified', 
                tdCls       : 'gridTree',
                hidden      : true, 
                flex        : 1,
                xtype       : 'datecolumn',   
                format      :'Y-m-d H:i:s',
                filter      : {type: 'date',dateFormat: 'Y-m-d'},stateId: 'StateGridIpPools15'
            }
        ]; 
        me.callParent(arguments);
    }
});
