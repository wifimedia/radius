Ext.define('Rd.store.sMeshViewEntries', {
    extend  : 'Ext.data.Store',
    model   : 'Rd.model.mMeshViewEntry',
    //To force server side sorting:
    remoteSort: false,
    proxy: {
            type    : 'ajax',
            format  : 'json',
            batchActions: true, 
            url     : '/cake2/rd_cake/mesh_reports/view_entries.json',
            reader  : {
                type            : 'json',
                rootProperty    : 'items',
                messageProperty : 'message',
                totalProperty   : 'totalCount' //Required for dynamic paging
            }
    },
    autoLoad: false,
    groupField: 'name'
});
