Ext.define('Rd.model.mAccessPointExit', {
    extend: 'Ext.data.Model',
    fields: [
         {name: 'id',               type: 'int'     },
         {name: 'ap_profile_id',    type: 'int'     },
         {name: 'type',             type: 'string'  },
         'connects_with'
        ]
});
