Ext.define('Rd.view.dynamicDetails.pnlDynamicDetailClickToConnect', {
    extend  : 'Ext.panel.Panel',
    alias   : 'widget.pnlDynamicDetailClickToConnect',
    border  : false,
    dynamic_detail_id: null,
    layout  : 'hbox',
    bodyStyle: {backgroundColor : Rd.config.panelGrey },
    initComponent: function(){
        var me = this;
        me.items =  { 
                xtype       : 'form',
                height      : '100%', 
                width       :  450,
                layout      : 'anchor',
                defaults    : {
                    anchor: '100%'
                },
                autoScroll  :true,
                frame       : true,
                fieldDefaults: {
                    msgTarget       : 'under',
                    labelClsExtra   : 'lblRd',
                    labelAlign      : 'left',
                    labelSeparator  : '',
                    margin          : Rd.config.fieldMargin,
                    labelWidth      : 200,
                    maxWidth        : Rd.config.maxWidth  
                },
                items       : [
                    {
                        xtype       : 'textfield',
                        name        : "id",
                        hidden      : true
                    },
                    {
                        xtype       : 'checkbox',      
                        fieldLabel  : 'Enable',
                        itemId      : 'chkClickToConnect',
                        name        : 'connect_check',
                        inputValue  : 'connect_check',
                        checked     : false,
                        labelClsExtra: 'lblRdReq'
                    },                 
                    {
                        xtype       : 'textfield',
                        itemId      : 'txtConnectUsername',
                        fieldLabel  : 'Connect as',
                        name        : "connect_username",
                        allowBlank  : false,
                        blankText   : i18n("sSupply_a_value"),
                        disabled    : true
                    },
                    {
                        xtype       : 'textfield',
                        itemId      : 'txtConnectSuffix',
                        fieldLabel  : 'Add suffix of',
                        name        : "connect_suffix",
                        allowBlank  : false,
                        blankText   : i18n("sSupply_a_value"),
                        disabled    : true
                    },
                    {
                        xtype       : 'numberfield',
                        name        : 'connect_delay',
                        fieldLabel  : 'Delay before connecting (seconds)',
                        itemId      : 'nrConnectDelay',
                        value       : 0,
                        maxValue    : 600,
                        minValue    : 0,
                        disabled    : true
                    },
                    {
                        xtype       : 'checkbox',      
                        fieldLabel  : 'Only Click-to-connect',
                        itemId      : 'chkConnectOnly',
                        name        : 'connect_only',
                        inputValue  : 'connect_only',
                        checked     : false,
                        disabled    : true
                    }
                ],
                buttons: [
                    {
                        itemId  : 'save',
                        formBind: true,
                        text    : i18n('sSave'),
                        scale   : 'large',
                        glyph   : Rd.config.icnYes,
                        margin  : Rd.config.buttonMargin
                    }
                ]
            };
        me.callParent(arguments);
    }
});
