Ext.define('Rd.view.finPayPalTransactions.winEmailVoucherDetail', {
    extend      : 'Ext.window.Window',
    alias       : 'widget.winEmailVoucherDetail',
    closable    : true,
    draggable   : true,
    resizable   : true,
    title       : 'e-mail voucher detail',
    width       : 400,
    height      : 300,
    plain       : true,
    border      : false,
    layout      : 'fit',
    glyph       : Rd.config.icnEmail,
    autoShow    : false,
    paypalId    : undefined,
    defaults: {
            border: false
    },
    requires: [
        'Ext.form.field.Text',
    ],
     initComponent: function() {
        var me = this;  
        var frmData = Ext.create('Ext.form.Panel',{
            border:     false,
            layout:     'anchor',
            defaults: {
                anchor: '100%'
            },
            itemId:     'scrnData',
            autoScroll: true,
            fieldDefaults: {
                msgTarget       : 'under',
                labelClsExtra   : 'lblRd',
                labelAlign      : 'left',
                labelSeparator  : '',
                labelClsExtra   : 'lblRd',
                labelWidth      : Rd.config.labelWidth,
                maxWidth        : Rd.config.maxWidth, 
                margin          : Rd.config.fieldMargin
            },
            defaultType: 'textfield',
            buttons : [
                {
                    itemId  : 'send',
                    text    : i18n('sOK'),
                    scale   : 'large',
                    glyph   : Rd.config.icnYes,
                    formBind: true,
                    margin  : Rd.config.buttonMargin
                }
            ],
            items: [
                    {
                        itemId      : 'fin_paypal_transaction_id',
                        xtype       : 'textfield',
                        name        : "id",
                        hidden      : true,
                        value       : me.paypalId
                    },
                    {
                        xtype       : 'displayfield',
                        fieldLabel  : 'Voucher',
                        name        : 'voucher',
                        value       : me.voucher_name
                    },
                    {
                        xtype       : 'textfield',
                        fieldLabel  : 'e-mail',
                        name        : "email",
                        allowBlank  : false,
                        blankText   : i18n('sSupply_a_value'),
                        labelClsExtra: 'lblRdReq',
                        vtype       : 'email',
                        value       : me.email
                    },
                    {
                        xtype       : 'textareafield',
                        grow        : true,
                        name        : 'message',
                        fieldLabel  : 'Extra message'
                    }
            ]
        });

        me.items = frmData;
        me.callParent(arguments);
    }
});
