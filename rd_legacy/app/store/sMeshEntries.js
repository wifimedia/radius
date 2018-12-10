Ext.define('Rd.store.sMeshEntries', {
    extend: 'Ext.data.Store',
    model: 'Rd.model.mMeshEntry',
    //To force server side sorting:
    remoteSort: true,
    proxy: {
            type    : 'ajax',
            format  : 'json',
            batchActions: true, 
            url     : '/cake2/rd_cake/meshes/mesh_entries_index.json',
            reader: {
                type            : 'json',
                root            : 'items',
                messageProperty : 'message',
                totalProperty   : 'totalCount' //Required for dynamic paging
            },
            api: {
                destroy  : '/cake2/rd_cake/meshes/mesh_entry_delete.json'
            },
            simpleSortMode: true //This will only sort on one column (sort) and a direction(dir) value ASC or DESC
    },
    autoLoad: false
});
