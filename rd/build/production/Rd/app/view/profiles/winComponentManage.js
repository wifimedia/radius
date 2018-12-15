Ext.define('Rd.view.profiles.winComponentManage', {
    extend: 'Ext.window.Window',
    alias : 'widget.winComponentManage',
    title : i18n('sEdit_profile'),
    layout: 'fit',
    autoShow: false,
    width:    450,
    height:   400,
    iconCls: 'edit',
    glyph: Rd.config.icnEdit,
    initComponent: function() {
        var me = this;
        this.items = [
            {
                xtype: 'form',
                border:     false,
                layout:     'anchor',
                autoScroll: true,
                defaults: {
                    anchor: '100%'
                },
                fieldDefaults: {
                    msgTarget: 'under',
                    labelClsExtra: 'lblRd',
                    labelAlign: 'left',
                    labelSeparator: '',
                    margin: 15
                },
                defaultType: 'textfield',
                tbar: [
                    { xtype: 'tbtext', text: i18n('sSelect_an_action'), cls: 'lblWizard' }
                ],
                items: [
                    {
                        xtype       : 'radiogroup',
                        fieldLabel  : i18n('sAction'),
                        columns: 1,
                        vertical: true,
                        items: [
                            { boxLabel: i18n('sAdd_component'),                     name: 'rb',     inputValue: 'add', checked: true },
                            { boxLabel: i18n('sRemove_component'),                  name: 'rb',     inputValue: 'remove'},
                            { boxLabel: i18n('sMake_available_to_sub_providers'),   name: 'rb',     inputValue: 'sub'},
                            { boxLabel: i18n('sMake_private'),                      name: 'rb',     inputValue: 'no_sub'}
                        ]
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: i18n('sProfile_component'),
                        store: 'sProfileComponents',
                        queryMode: 'local',
                        editable: false,
                        allowBlank: false,
                        disabled: false,
                        name: 'component_id',
                        displayField: 'name',
                        valueField: 'id'
                    },
                    {
                        xtype: 'numberfield',
                        anchor: '100%',
                        name: 'priority',
                        fieldLabel: i18n('sPriority'),
                        value: 100,
                        maxValue: 500,
                        minValue: 1,
                        itemId: 'priority'
                    }
                ],
                buttons: [
                    {
                        itemId: 'save',
                        text: i18n('sOK'),
                        scale: 'large',
                        iconCls: 'b-next',
                        glyph: Rd.config.icnNext,
                       // formBind: true,
                        margin: '0 20 40 0'
                    }
                ]
            }
        ];
        this.callParent(arguments);
    }
});
