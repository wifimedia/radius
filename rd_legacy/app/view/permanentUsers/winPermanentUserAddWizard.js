Ext.define('Rd.view.permanentUsers.winPermanentUserAddWizard', {
    extend:     'Ext.window.Window',
    alias :     'widget.winPermanentUserAddWizard',
    closable:   true,
    draggable:  true,
    resizable:  false,
    title:      i18n('sNew_permanent_user'),
    width:      400,
    height:     500,
    plain:      true,
    border:     false,
    layout:     'card',
    iconCls:    'add',
    glyph: Rd.config.icnAdd,
    autoShow:   false,
    defaults: {
            border: false
    },
    no_tree	: false, //If the user has no children we don't bother giving them a branchless tree
    user_id	: '',
    owner	: '',
    startScreen: 'scrnApTree', //Default start screen
    selLanguage: null,
    requires: [
        'Ext.layout.container.Card',
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Rd.view.components.vCmbLanguages'
    ],
    initComponent: function() {
        var me = this;
        var scrnApTree      = me.mkScrnApTree();
        var scrnData        = me.mkScrnData();
        me.items = [
            scrnApTree,
            scrnData
        ];
        console.log(me.selLanguage); 
        me.callParent(arguments);
        me.getLayout().setActiveItem(me.startScreen);  
    },

    //____ AccessProviders tree SCREEN ____
    mkScrnApTree: function(){
        var pnlTree = Ext.create('Rd.view.components.pnlAccessProvidersTree',{
            itemId: 'scrnApTree'
        });
        return pnlTree;
    },

    //_______ Data for permanent user  _______
    mkScrnData: function(){


        var me      = this;

        //Set default values for from and to:
        var dtFrom  = new Date();
        var dtTo    = new Date();
        dtTo.setYear(dtTo.getFullYear() + 1);

        var buttons = [
                {
                    itemId: 'btnDataPrev',
                    text: i18n('sPrev'),
                    scale: 'large',
                    iconCls: 'b-prev',
                    glyph: Rd.config.icnBack,
                    margin: '0 20 40 0'
                },
                {
                    itemId: 'btnDataNext',
                    text: i18n('sNext'),
                    scale: 'large',
                    iconCls: 'b-next',
                    glyph: Rd.config.icnNext,
                    formBind: true,
                    margin: '0 20 40 0'
                }
            ];

        if(me.no_tree == true){
            var buttons = [
                {
                    itemId: 'btnDataNext',
                    text: i18n('sNext'),
                    scale: 'large',
                    iconCls: 'b-next',
                    glyph: Rd.config.icnNext,
                    formBind: true,
                    margin: '0 20 40 0'
                }
            ];
        }

        var frmData = Ext.create('Ext.form.Panel',{
            border:     false,
            layout:     'fit',
            itemId:     'scrnData',
            autoScroll: true,
            fieldDefaults: { 
                msgTarget: 'under',
                labelClsExtra: 'lblRd',
                labelAlign: 'left',
                labelSeparator: '',
                labelWidth: 150,
                margin: 15
            },
            defaultType: 'textfield',
            tbar: [
                { xtype: 'tbtext', text: i18n('sSupply_the_following'), cls: 'lblWizard' }
            ],
            items:[
                {
                    xtype   : 'tabpanel',
                    layout  : 'fit',
                    xtype   : 'tabpanel',
                    margins : '0 0 0 0',
                    plain   : true,
                    tabPosition: 'bottom',
                    border  : false,
                    items   : [
                        { 
                            'title'     : i18n('sBasic_info'), 
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items       : [
                                {
                                    itemId  : 'user_id',
                                    xtype   : 'textfield',
                                    name    : "user_id",
                                    hidden  : true
                                },
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : 'Create multiple users',
                                    name        : 'multiple',
                                    inputValue  : 'multiple',
                                    checked     : false,
                                    boxLabelCls : 'lblRdCheck',
                                    itemId      : 'multiple'
                                },
                                {
                                    itemId      : 'owner',
                                    xtype       : 'displayfield',
                                    fieldLabel  : i18n('sOwner'),
                                    value       : me.owner,
                                    labelClsExtra: 'lblRdReq'
                                },
                                {
                                    xtype       : 'textfield',
                                    fieldLabel  : i18n('sUsername'),
                                    name        : "username",
                                    allowBlank  : false,
                                    blankText   : i18n('sSupply_a_value'),
                                    labelClsExtra: 'lblRdReq'
                                },
                                {
                                    xtype       : 'textfield',
                                    fieldLabel  : i18n('sPassword'),
                                    name        : "password",
                                    allowBlank  : false,
                                    blankText   : i18n('sSupply_a_value'),
                                    labelClsExtra: 'lblRdReq'
                                },
                                {
                                    xtype       : 'cmbRealm',
                                    allowBlank  : false,
                                    labelClsExtra: 'lblRdReq',
									itemId      : 'realm',
									extraParam  : me.user_id
                                },
                                {
                                    xtype       : 'cmbProfile',
                                    allowBlank  : false,
                                    labelClsExtra: 'lblRdReq',
                                    itemId      : 'profile',
									extraParam  : me.user_id
                                },
                                {
                                    xtype       : 'cmbCap',
                                    allowBlank  : false,
                                    labelClsExtra: 'lblRdReq',
                                    itemId      : 'cap',
                                    hidden      : true,
                                    value       : 'hard',
                                    fieldLabel  : i18n('sCap_type_for_data'),
                                    itemId      : 'cmbDataCap',
                                    name        : 'cap_data'
                                },
                                {
                                    xtype       : 'cmbCap',
                                    allowBlank  : false,
                                    labelClsExtra: 'lblRdReq',
                                    itemId      : 'cap',
                                    hidden      : true,
                                    value       : 'hard',
                                    fieldLabel  : i18n('sCap_type_for_time'),
                                    itemId      : 'cmbTimeCap',
                                    name        : 'cap_time'
                                }
                            ]
                        },
                        { 
                            'title' : i18n('sPersonal_info'),
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: i18n('sName'),
                                    name : "name",
                                    allowBlank:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: i18n('sSurname'),
                                    name : "surname",
                                    allowBlank:true
                                },
                                { 
                                    xtype       : 'cmbLanguages', 
                                    width       : 350, 
                                    fieldLabel  : i18n('sLanguage'),  
                                    name        : 'language',
                                    value       : me.selLanguage,
                                    allowBlank  : false,
                                    labelClsExtra: 'lblRd' 
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: i18n('sPhone'),
                                    name : "phone",
                                    allowBlank:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: i18n('s_email'),
                                    name : "email",
                                    allowBlank:true
                                },
                                {
                                    xtype     : 'textareafield',
                                    grow      : true,
                                    name      : 'address',
                                    fieldLabel: i18n('sAddress'),
                                    anchor    : '100%'
                                }
                            ]
                        },
                        { 
                            'title' : i18n('sActivate_and_Expire'),
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items       : [
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : i18n('sActivate'),
                                    name        : 'active',
                                    inputValue  : 'active',
                                    checked     : true,
                                    boxLabelCls : 'lblRdCheck'
                                },
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : i18n('sAlways_active'),
                                    name        : 'always_active',
                                    inputValue  : 'always_active',
                                    itemId      : 'always_active',
                                    checked     : true,
                                    boxLabelCls : 'lblRdCheck'
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: i18n('sFrom'),
                                    name: 'from_date',
                                    itemId      : 'from_date',
                                    minValue: new Date(),  // limited to the current date or after
                                    hidden      : true,
                                    disabled    : true,
                                    value       : dtFrom
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: i18n('sTo'),
                                    name: 'to_date',
                                    itemId      : 'to_date',
                                    minValue: new Date(),  // limited to the current date or after
                                    hidden      : true,
                                    disabled    : true,
                                    value       : dtTo
                                }
                            ]
                        },
						{ 
                            'title' : 'SSIDs',
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items       : [
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : 'Connect only from selected SSIDs',
                                    name        : 'ssid_only',
                                    inputValue  : 'ssid_only',
									itemId  	: 'ssid_only',
                                    checked     : false,
                                    boxLabelCls : 'lblRdCheck'
                                },
                                {
                                    xtype       : 'cmbSsid',
                                    labelClsExtra: 'lblRdReq',
									itemId		: 'ssid_list',
									hidden		: true,
									disabled	: true,
									extraParam  : me.user_id
                                }
                            ]
                        },
						{ 
                            'title' : 'Optional fields',
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items       : [
								{
                                    xtype		: 'textfield',
                                    fieldLabel	: 'Static IP',
                                    name 		: "static_ip",
                                    allowBlank	:true
                                },
								{
						            xtype       : 'textfield',
						            name        : 'extra_name',
						            fieldLabel  : 'Extra field name',
						            allowBlank  : true,
						            labelClsExtra: 'lblRd'
						        },
						        {
						            xtype       : 'textfield',
						            name        : 'extra_value',
						            fieldLabel  : 'Extra field value',
						            allowBlank  : true,
						            labelClsExtra: 'lblRd'
						        },
								{
                                    xtype       : 'checkbox',      
                                    boxLabel    : 'Auto-add device after authentication',
                                    name        : 'auto_add',
                                    inputValue  : 'auto_add',
                                    checked     : false,
                                    boxLabelCls : 'lblRdCheck',
                                    itemId      : 'auto_add'
                                }  
                            ]   
                        },
                        { 
                            'title' : i18n('sTracking'),
                            'layout'    : 'anchor',
                            defaults    : {
                                anchor: '100%'
                            },
                            items       : [
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : i18n('sRADIUS_authentication'),
                                    name        : 'track_auth',
                                    inputValue  : 'track_auth',
                                    checked     : false, //Default not to track it
                                    boxLabelCls : 'lblRdCheck'
                                },
                                {
                                    xtype       : 'checkbox',      
                                    boxLabel    : i18n('sRADIUS_accounting'),
                                    name        : 'track_acct',
                                    inputValue  : 'track_acct',
                                    checked     : true,
                                    boxLabelCls : 'lblRdCheck'
                                }
                            ]   
                        }
                    ]
                }
                
            ],
            buttons: buttons
        });
        return frmData;
    }   
});
