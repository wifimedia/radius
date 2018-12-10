Ext.define('Rd.view.meshes.cmbStaticEntries', {
    extend          : 'Ext.form.ComboBox',
    alias           : 'widget.cmbStaticEntries',
    fieldLabel      : i18n('sStatic_entry_points'),
    labelSeparator  : '',
    queryMode       : 'local',
    valueField      : 'id',
    displayField    : 'name',
    allowBlank      : true,
    editable        : false,
    mode            : 'local',
    itemId          : 'static_entries',
    name            : 'static_entries[]',
    labelClsExtra   : 'lblRd',
    meshId          : '' ,
    nodeId          : '',
	multiSelect     : true,
    initComponent: function(){
        var me      = this;
        var s       = Ext.create('Ext.data.Store', {
            fields: ['id', 'name'],
            proxy: {
                type    : 'ajax',
                format  : 'json',
                batchActions: true,
                extraParams: { 'mesh_id' : me.meshId, 'node_id' : me.nodeId }, 
                url     : '/cake2/rd_cake/meshes/static_entry_options.json',
                reader: {
                    type: 'json',
                    root: 'items',
                    messageProperty: 'message'
                }
            },
            autoLoad: true
        });
        me.store = s;
        me.callParent(arguments);
    }
});
