Ext.define('Rd.store.sI18nJsPhrases', {
    extend: 'Ext.data.Store',
    model: 'Rd.model.mI18nJsPhrase',
    proxy: {
        'type'  :'rest',
        'url'   : '/cake2/rd_cake/phrase_values/list_phrases_for', 
        format: 'json',
        api: {
            update  : '/cake2/rd_cake/phrase_values/update_phrase',
            destroy : '/cake2/rd_cake/phrase_values/delete_keys'
        },
        reader: {
            type : 'json',
            root: 'items'
        }
    },
    listeners: {
        update: function(store, records, success, options) {
            store.sync({
                success: function(batch,options){
                    Ext.ux.Toaster.msg(
                        i18n('sUpdated_database'),
                        i18n('sDatabase_has_been_updated'),
                        Ext.ux.Constants.clsInfo,
                        Ext.ux.Constants.msgInfo
                    );   
                },
                failure: function(batch,options){
                    Ext.ux.Toaster.msg(
                        i18n('sProblems_updating_the_database'),
                        i18n('sDatabase_could_not_be_updated'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                    );
                }
            }); 
        },
        scope: this
    },
    autoLoad: true    
});
