Ext.define('Rd.view.meshes.tagMeshEntryPoints', {
    extend          : 'Ext.form.field.Tag',
    alias           : 'widget.tagMeshEntryPoints',
    fieldLabel      : i18n('sConnects_with'),
    queryMode       : 'local',
    emptyText       : 'Select Entry Points',
    displayField    : 'name',
    valueField      : 'id',
    name            : 'entry_points[]',
    initComponent: function(){
        var me      = this;
        var s       = Ext.create('Ext.data.Store', {
            fields: [
                {name: 'id',    type: 'int'},
                {name: 'name',  type: 'string'}
            ],
            proxy: {
                    type    : 'ajax',
                    format  : 'json',
                    batchActions: true, 
                    url     : '/cake2/rd_cake/meshes/mesh_entry_points.json',
                    reader: {
                        type            : 'json',
                        rootProperty            : 'items',
                        messageProperty : 'message'
                    }
            },
            autoLoad: true
        });
        me.store = s;
        me.callParent(arguments);
    }
});
