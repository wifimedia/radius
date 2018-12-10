Ext.define('Rd.view.vouchers.pnlVoucherGraphs', {
    extend  : 'Ext.tab.Panel',
    alias   : 'widget.pnlVoucherGraphs',
    layout  : 'fit',
    margin  : '0 0 0 0',
    plain   : true,
    border  : true,
    tabPosition: 'bottom',
    requires: [
        'Wfl.view.components.pnlUsageGraph'
    ],
    v_name: undefined,
    initComponent: function(){
        var me = this;      
        me.items   =   [
            {
                title   : i18n('sDaily'),
                itemId  : "daily",
                xtype   : 'pnlUsageGraph',
                span    : 'daily',
                layout  : 'fit',
                username: me.v_name,
                type    : 'voucher'
            },
            {
                title   : i18n('sWeekly'),
                itemId  : "weekly",
                xtype   : 'pnlUsageGraph',
                span    : 'weekly',
                layout  : 'fit',
                username: me.v_name,
                type    : 'voucher'
            },
            {
                title   : i18n('sMonthly'),
                itemId  : "monthly",
                layout  : 'fit',
                xtype   : 'pnlUsageGraph',
                span    : 'monthly',
                username: me.v_name,
                type    : 'voucher'
            }
        ];
        me.callParent(arguments);
    }
});
