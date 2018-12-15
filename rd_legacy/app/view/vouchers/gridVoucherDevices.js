Ext.define('Rd.view.vouchers.gridVoucherDevices' ,{
    extend		:'Ext.grid.Panel',
    alias 		: 'widget.gridVoucherDevices',
    multiSelect	: true,
    stateful	: true,
    stateId		: 'StateGridVoucherDevice',
    stateEvents	:['groupclick','columnhide'],
    border		: false,
    viewConfig: {
        loadMask:true
    },
    bbar: [
        {   xtype: 'component', itemId: 'count',   tpl: i18n('sResult_count_{count}'),   style: 'margin-right:5px', cls: 'lblYfi' }
    ],
    tbar: [
        { xtype: 'buttongroup', title: i18n('sAction'),items : [ 
            {   xtype: 'button',   glyph   : Rd.config.icnReload,   scale: 'large',   itemId: 'reload',    tooltip:    i18n('sReload')},
			{   xtype: 'button',   glyph   : Rd.config.icnAdd,      scale: 'large',   itemId: 'add',       tooltip:    i18n('sAdd')},
            {   xtype: 'button',   glyph   : Rd.config.icnDelete,   scale: 'large',   itemId: 'delete',    tooltip:    i18n('sDelete')}	
        ]}     
    ],
    username	: 'nobody', //dummy value
    initComponent: function(){
        var me      = this;

        me.columns = [
            {xtype: 'rownumberer',stateId: 'StateGridVoucherDevice1'},  
            { text: i18n('sMAC_address'),    dataIndex: 'mac', tdCls: 'gridTree', flex: 1, stateId: 'StateGridVoucherDevice2'}       
        ];

        //Create a store specific to this Access Provider
        me.store = Ext.create(Ext.data.Store,{
            model: 'Rd.model.mVoucherDevice',
            proxy: {
                type        : 'ajax',
                format      : 'json',
                batchActions: true,
                extraParams : { 'username' : me.username },
                reader      : {
                    type        : 'json',
                    root        : 'items',
                    messageProperty: 'message'
                },
                api         : {
                    create      : '/cake2/rd_cake/vouchers/voucher_device_add.json',
                    read        : '/cake2/rd_cake/vouchers/voucher_device_index.json',
                    update      : '/cake2/rd_cake/vouchers/voucher_device_edit.json',
                    destroy     : '/cake2/rd_cake/vouchers/voucher_device_delete.json'
                }
            },
            autoLoad: false    
        });
 
        //Create a mask and assign is as a property to the window
        me.mask = new Ext.LoadMask(me, {msg: i18n('sConnecting')+" ...."});
        me.callParent(arguments);
    }
});
