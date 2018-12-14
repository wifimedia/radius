Ext.define('Rd.store.sMeshViewNodes', {
    extend  : 'Ext.data.Store',
    model   : 'Rd.model.mMeshViewNode',
    //To force server side sorting:
    remoteSort: false,
    proxy: {
            type    : 'ajax',
            format  : 'json',
            batchActions: true, 
            url     : '/cake2/rd_cake/mesh_reports/view_nodes.json',
            reader  : {
                type            : 'json',
                rootProperty    : 'items',
                messageProperty : 'message',
                totalProperty   : 'totalCount' //Required for dynamic paging
            }
    },
    autoLoad: false
});
