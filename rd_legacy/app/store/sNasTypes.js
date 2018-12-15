Ext.define('Rd.store.sNasTypes', {
    extend: 'Ext.data.Store',
    model: 'Rd.model.mNasType',
    proxy: {
            type    : 'ajax',
            format  : 'json',
            batchActions: true, 
            url     : '/cake2/rd_cake/nas/nas_types.json',
            reader: {
                type: 'json',
                root: 'items',
                messageProperty: 'message'
            }
    },
    autoLoad: true
});
