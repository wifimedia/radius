Ext.define('Rd.view.devices.pnlDeviceGraphs', {
    extend  : 'Ext.tab.Panel',
    alias   : 'widget.pnlDeviceGraphs',
    layout  : 'fit',
    margin  : '0 0 0 0',
    plain   : true,
    border  : true,
    tabPosition: 'bottom',
    requires: [
        'Wfl.view.components.pnlUsageGraph'
    ],
    d_name: undefined,
    initComponent: function(){
        var me = this;      
        me.items   =   [
            {
                title   : i18n('sDaily'),
                itemId  : "daily",
                xtype   : 'pnlUsageGraph',
                span    : 'daily',
                layout  : 'fit',
                username: me.d_name,
                type    : 'device'
            },
            {
                title   : i18n('sWeekly'),
                itemId  : "weekly",
                xtype   : 'pnlUsageGraph',
                span    : 'weekly',
                layout  : 'fit',
                username: me.d_name,
                type    : 'device'
            },
            {
                title   : i18n('sMonthly'),
                itemId  : "monthly",
                layout  : 'fit',
                xtype   : 'pnlUsageGraph',
                span    : 'monthly',
                username: me.d_name,
                type    : 'device'
            }
        ];
        me.callParent(arguments);
    }
});
