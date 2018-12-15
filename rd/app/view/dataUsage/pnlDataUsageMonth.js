Ext.define('Rd.view.dataUsage.pnlDataUsageMonth', {
    extend  : 'Ext.panel.Panel',
    alias   : 'widget.pnlDataUsageMonth',
    //ui      : 'light',
    title   : "This Month",
    headerPosition: 'right',
    height  : 550,
    margin  : 0,
    padding : 0,
    layout: {
        type    : 'vbox',
        align   : 'stretch'
    },
    initComponent: function() {
        var me      = this; 
        var m       = 5;
        var p       = 5;
        Ext.create('Ext.data.Store', {
            storeId : 'monthStore',
            fields  :[ 
                {name: 'id',            type: 'int'},
                {name: 'username',      type: 'string'},
                {name: 'mac',           type: 'string'},
                {name: 'data_in',       type: 'int'},
                {name: 'data_out',      type: 'int'},
                {name: 'data_total',    type: 'int'}
            ]
        });
        
        me.items = [
            {
                xtype   : 'panel',
                flex    : 1,
                border  : false,
                layout: {
                    type    : 'hbox',
                    align   : 'stretch'
                },
                items : [
                    {
                        xtype   : 'panel',
                        margin  : m,
                        padding : p,
                        flex    : 1,
                        bodyCls : 'pnlInfo',
                        layout  : 'fit',
                        border  : true,
                        ui      : 'light',
                        itemId  : 'monthlyTotal',
                        tpl     : new Ext.XTemplate(
                            '<div class="divInfo">',   
                            '<tpl if="type==\'realm\'"><h2 style="color:#303030;font-weight:lighter;"><i class="fa fa-dribbble"></i> {item_name}</h2></tpl>',
                            '<tpl if="type==\'user\'"><h2 style="color:#033278;font-weight:lighter;"><i class="fa fa-user"></i> {item_name}</h2></tpl>',
                            '<h1 style="font-size:250%;font-weight:lighter;">{data_total}</h1>',       
                            '<p style="color: #000000; font-size:110%;">',
                                '<span class="grpUp"><i class="fa fa-arrow-circle-down"></i></span> In: {data_in}',
                                '&nbsp;&nbsp;&nbsp;&nbsp;',
                                '<span class="grpDown"><i class="fa fa-arrow-circle-up"></i></span> Out: {data_out}',
                            '</p>',
                            '</div>'
                        ),
                        data    : {
                        },
                        bbar    : ['->',{ 
                            xtype   : 'button',    
                            scale   : 'large',  
                            text    : 'See More..',
                            glyph   : Rd.config.icnView,
                            itemId  : 'btnSeeMore'
                        }]
                    },
                    {
                        xtype   : 'pnlDataUsageUserDetail',
                        margin  : m,
                        padding : p,
                        hidden  : true,
                        flex    : 1  
                    },
                    {
                        flex            : 1,
                        margin          : m,
                        padding         : p,
                        border          : false,
                        itemId          : 'plrMonthly',
                        xtype           : 'polar',
                        innerPadding    : 10,
                        interactions    : ['rotate', 'itemhighlight'],
                        store: Ext.data.StoreManager.lookup('monthStore'),
                        series: {
                           type         : 'pie',
                          
                           highlight    : true,
                           angleField   : 'data_total',
                           label        : {
                               field    : 'name',
                               display  : 'rotate'
                           },
                           donut        : 20,    
                           tooltip : {
                                trackMouse: true,
                                renderer: function (tooltip, record, item) {
                                    tooltip.setHtml(
                                        "<h2>"+record.get('username')+"</h2><h3>"+Ext.ux.bytesToHuman(record.get('data_total'))+"</h3>"
                                        
                                    
                                    );
                                }
                            }    
                        }
                    },
                    {
                        xtype   : 'grid',
                        margin  : m,
                        padding : p,
                        ui      : 'light',
                        title   : 'Top 10 Users This Month',
                        itemId  : 'gridTopTenMonthly',
                        border  : true,       
                        store   : Ext.data.StoreManager.lookup('monthStore'),
                        emptyText: 'No Users For This Month',
                        columns: [
                            { xtype: 'rownumberer'},
                            { text: 'Username',  dataIndex: 'username', flex: 1},
                            { text: 'MAC Address',  dataIndex: 'mac', flex: 1, hidden: true},
                            { text: 'Data In',   dataIndex: 'data_in',  hidden: true, renderer: function(value){
                                    return Ext.ux.bytesToHuman(value)              
                                } 
                            },
                            { text: 'Data Out',  dataIndex: 'data_out', hidden: true,renderer: function(value){
                                    return Ext.ux.bytesToHuman(value)              
                                } 
                            },
                            { text: 'Data Total',dataIndex: 'data_total',tdCls: 'gridMain',renderer: function(value){
                                    return Ext.ux.bytesToHuman(value)              
                                } 
                            }
                        ],
                        flex: 1
                    }
                ]
            },
            {
                xtype   : 'panel',
                flex    : 1,
                border  : false,
                layout: {
                    type    : 'hbox',
                    align   : 'stretch'
                },
                items   : [
                    {
                        xtype   : 'pnlDataUsageGraph',
                        flex    : 1,
                        margin  : 0,
                        padding : 0,
                        layout  : 'fit',
                        border  : false   
                    }
                ]
            }
        ];
        me.callParent(arguments);
    }
});
