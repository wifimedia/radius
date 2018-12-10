Ext.define('Rd.model.mNodeDetail', {
    extend: 'Ext.data.Model',
    fields: [
         {name: 'id',               type: 'int'     },
         {name: 'mesh_id',          type: 'int'     },
         {name: 'name',             type: 'string'  },
         {name: 'description',      type: 'string'  },
         {name: 'mac',              type: 'string'  },
         {name: 'hardware',         type: 'string'  },
         {name: 'power',            type: 'int'     },
         {name: 'ip',               type: 'string'  },
	'last_contact',
	'state',
	'hw_human',
	'mem_free',
	'mem_total',
	'uptime',
	'system_time',
	'load_1',
	'load_2',
	'load_3',
	'release',
	'cpu',
	'last_cmd',
	'last_cmd_status',
    'last_contact_human'		
  	]
});
