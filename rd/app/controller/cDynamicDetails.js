Ext.define('Rd.controller.cDynamicDetails', {
    extend: 'Ext.app.Controller',
    actionIndex: function(pnl){

        var me = this;
        
        
        if (me.populated) {
            return; 
        }     
        pnl.add({
            xtype   : 'tabpanel',
            border  : false,
            itemId  : 'tabDynamicDetails',
            plain   : true,
            cls     : 'subSubTab', //Make darker -> Maybe grey
            items   : [
                { 'title' : i18n('sHome'), 'xtype':'gridDynamicDetails','glyph': Rd.config.icnHome}
            ]
        });
        me.populated = true;  
    },
    views:  [
        'dynamicDetails.gridDynamicDetails',                'dynamicDetails.winDynamicDetailAddWizard', 'dynamicDetails.pnlDynamicDetail',
        'components.winCsvColumnSelect',    'components.winNote',       'components.winNoteAdd','dynamicDetails.pnlDynamicDetailDetail',
        'dynamicDetails.pnlDynamicDetailLogo',  'dynamicDetails.pnlDynamicDetailPhoto', 'dynamicDetails.winPhotoAdd',
        'dynamicDetails.winPhotoEdit',      'dynamicDetails.gridDynamicDetailPages',    'dynamicDetails.winPageAdd',
        'dynamicDetails.winPageEdit',       'dynamicDetails.gridDynamicDetailPairs',    'dynamicDetails.winPairAdd',
        'dynamicDetails.winPairEdit',       'dynamicDetails.pnlDynamicDetailSettings',  'dynamicDetails.pnlDynamicDetailClickToConnect',
		'dynamicDetails.cmbThemes',			'components.cmbPermanentUser',				'components.cmbRealm',
		'components.cmbProfile',			'dynamicDetails.pnlDynamicDetailSocialLogin'  ,
		'dynamicDetails.cmbDynamicDetailLanguages'     
    ],
    stores: ['sDynamicDetails','sAccessProvidersTree', 'sPermanentUsers','sProfiles','sRealms'],
    models: [
		'mDynamicDetail','mAccessProviderTree','mDynamicPhoto', 
		'mDynamicPage', 'mDynamicPair', 'mPermanentUser',
		'mProfile',		'mRealm'
	],
    selectedRecord: null,
    config: {
        urlAdd:             '/cake3/rd_cake/dynamic-details/add.json',
        urlEdit:            '/cake3/rd_cake/dynamic-details/edit.json',
        urlEditSettings:    '/cake3/rd_cake/dynamic-details/edit-settings.json',
        urlEditClickToConnect:    '/cake3/rd_cake/dynamic-details/edit-click-to-connect.json',
        urlDelete:          '/cake3/rd_cake/dynamic-details/delete.json',
        urlApChildCheck:    '/cake3/rd_cake/access-providers/child-check.json',
        urlExportCsv:       '/cake3/rd_cake/dynamic-details/export-csv',
        urlNoteAdd:         '/cake3/rd_cake/dynamic-details/note-add.json',
        urlViewDynamicDetail: '/cake3/rd_cake/dynamic-details/view.json',
        urlLogoBase:        '/cake3/rd_cake/img/dynamic_details/',
        urlUploadLogo:      '/cake3/rd_cake/dynamic-details/upload_logo/',
        urlUploadPhoto:     '/cake3/rd_cake/dynamic-details/upload-photo/',
        urlEditPhoto:       '/cake3/rd_cake/dynamic-details/edit-photo/',
        urlAddPage:         '/cake3/rd_cake/dynamic-details/add-page.json',
        urlEditPage:        '/cake3/rd_cake/dynamic-details/edit_page.json',
        urlAddPair:         '/cake3/rd_cake/dynamic-details/add_pair.json',
        urlEditPair:        '/cake3/rd_cake/dynamic-details/edit_pair.json',
        urlPreviewMobile:   '/cake3/rd_cake/dynamic-details/preview-chilli-mobile',
        urlPreviewDesktop:  '/cake3/rd_cake/dynamic-details/preview-chilli-desktop',
		urlViewSocial:		'/cake3/rd_cake/dynamic-details/view-social-login.json',
		urlEditSocial:		'/cake3/rd_cake/dynamic-details/edit-social-login.json'
    },
    refs: [
         {  ref:    'grid',           selector:   'gridDynamicDetails'}
    ],
    init: function() {
        var me = this;
        if (me.inited) {
            return;
        }
        me.inited = true;
        me.control({
            '#tabDynamicDetails'    : {
                destroy   :      me.appClose
            },
            'gridDynamicDetails #reload': {
                click:      me.reload
            },
            'gridDynamicDetails #add': {
                click:      me.add
            },
            'gridDynamicDetails #delete': {
                click:      me.del
            },
            'gridDynamicDetails #edit': {
                click:      me.edit
            },
            'gridDynamicDetails #note'   : {
                click:      me.note
            },
            'gridDynamicDetails #csv'  : {
                click:      me.csvExport
            },
            'gridDynamicDetails #mobile'  : {
                click:      me.previewMobile
            },
            'gridDynamicDetails #desktop'  : {
                click:      me.previewDesktop
            },
            'gridDynamicDetails'   : {
                itemclick:  me.gridClick
            },
            'winDynamicDetailAddWizard #btnTreeNext' : {
                click:  me.btnTreeNext
            },
            'winDynamicDetailAddWizard #btnDynamicDetailDetailPrev' : {
                click:  me.btnDynamicDetailDetailPrev
            },
            'winDynamicDetailAddWizard #chkTc' : {
                change:  me.chkTcChange
            },
            'winDynamicDetailAddWizard #save' : {
                click:  me.addSubmit
            },
            'pnlDynamicDetail pnlDynamicDetailDetail #save' : {
                click:  function(b){
                    var me = this;
                    me.editSubmit(b,me.getUrlEdit());
                }
            },
            '#winCsvColumnSelectDynamicDetails #save': {
                click:  me.csvExportSubmit
            },
            'gridNote[noteForGrid=dynamicDetails] #reload' : {
                click:  me.noteReload
            },
            'gridNote[noteForGrid=dynamicDetails] #add' : {
                click:  me.noteAdd
            },
            'gridNote[noteForGrid=dynamicDetails] #delete' : {
                click:  me.noteDelete
            },
            'gridNote[noteForGrid=dynamicDetails]' : {
                itemclick: me.gridNoteClick
            },
            'winNoteAdd[noteForGrid=dynamicDetails] #btnTreeNext' : {
                click:  me.btnNoteTreeNext
            },
            'winNoteAdd[noteForGrid=dynamicDetails] #btnNoteAddPrev'  : {   
                click: me.btnNoteAddPrev
            },
            'winNoteAdd[noteForGrid=dynamicDetails] #btnNoteAddNext'  : {   
                click: me.btnNoteAddNext
            },
            'pnlDynamicDetail #tabDetail': {
                beforerender:   me.tabDetailActivate,
                activate:       me.tabDetailActivate
            },
			'pnlDynamicDetail #tabSettings #chkUserLogin' : {
                change:  me.chkUserLoginChange
            },
			'pnlDynamicDetail #tabSettings #chkAutoSuffix' : {
                change:  me.chkAutoSuffixChange
            },
            'pnlDynamicDetail #tabSettings #chkTc' : {
                change:  me.chkTcChange
            },
            'pnlDynamicDetail #tabSettings #chkRedirect' : {
                change:  me.chkRedirectChange
            },
			'pnlDynamicDetail #tabSettings #chkUsage' : {
                change:  me.chkUsageChange
            },
            'pnlDynamicDetail #tabClickToConect #chkClickToConnect' : {
                change:  me.chkClickToConnectChange
            },
            'pnlDynamicDetail #tabLogo': {
                activate:       me.tabLogoActivate
            },
            'pnlDynamicDetail #tabLogo #save': {
                click:       me.logoSave
            },
            'pnlDynamicDetail #tabLogo #cancel': {
                click:       me.logoCancel
            },
            'pnlDynamicDetail #tabPhoto': {
                activate:       me.tabPhotoActivate
            },
            'pnlDynamicDetail #tabPhoto #reload': {
                click:       me.photoReload
            },
            'pnlDynamicDetail #tabPhoto #add': {
                click:       me.photoAdd
            },
            'pnlDynamicDetail #tabPhoto #delete': {
                click:      me.photoDel
            },
            'pnlDynamicDetail #tabPhoto #edit': {
                click:      me.photoEdit
            },
            'winPhotoAdd #save': {
                click:      me.photoAddSave
            },
            'winPhotoAdd #cancel': {
                click:      me.photoAddCancel
            },
            'winPhotoEdit #save': {
                click:      me.photoEditSave
            },
            'winPhotoEdit #cancel': {
                click:      me.photoEditCancel
            },
            'pnlDynamicDetail #tabPages': {
                activate:       me.tabPagesActivate
            },
            'pnlDynamicDetail gridDynamicDetailPages #reload': {
                click:       me.pageReload
            },
            'pnlDynamicDetail gridDynamicDetailPages #add': {
                click:       me.pageAdd
            },
            'pnlDynamicDetail gridDynamicDetailPages #delete': {
                click:      me.pageDel
            },
            'pnlDynamicDetail gridDynamicDetailPages #edit': {
                click:      me.pageEdit
            },
            'winPageAdd #save': {
                click:      me.pageAddSave
            },
            'winPageEdit #save': {
                click:      me.pageEditSave
            },
            'pnlDynamicDetail #tabPairs': {
                activate:       me.tabPairsActivate
            },
            'pnlDynamicDetail gridDynamicDetailPairs #reload': {
                click:       me.pairReload
            },
            'pnlDynamicDetail gridDynamicDetailPairs #add': {
                click:       me.pairAdd
            },
            'pnlDynamicDetail gridDynamicDetailPairs #delete': {
                click:      me.pairDel
            },
            'pnlDynamicDetail gridDynamicDetailPairs #edit': {
                click:      me.pairEdit
            },
            'winPairAdd #save': {
                click:      me.pairAddSave
            },
            'winPairEdit #save': {
                click:      me.pairEditSave
            },
            'pnlDynamicDetail #tabSettings': {
                activate:       me.tabSettingsActivate
            },
            'pnlDynamicDetail #tabClickToConect': {
                activate:       me.tabDetailActivate
            },
			'pnlDynamicDetail #tabSocialLogin': {
                activate:       me.tabSocialLoginActivate
            },
            'pnlDynamicDetail pnlDynamicDetailSettings #save' : {
                click:  function(b){
                    var me = this;
                    me.editSubmit(b,me.getUrlEditSettings());
                }
            },
            'pnlDynamicDetail pnlDynamicDetailClickToConnect #save' : {
                click:  function(b){
                    var me = this;
                    me.editSubmit(b,me.getUrlEditClickToConnect());
                }
            },
			'pnlDynamicDetail pnlDynamicDetailSocialLogin #save' : {
                click:  function(b){
                    var me = this;
                    me.editSubmit(b,me.getUrlEditSocial());
                }
            }
        });
    },
    appClose:   function(){
        var me          = this;
        me.populated    = false;
    },
    reload: function(){
        var me =this;
        me.getGrid().getSelectionModel().deselectAll(true);
        me.getStore('sDynamicDetails').load();
    },
    gridClick:  function(grid, record, item, index, event){
        var me                  = this;
        me.selectedRecord = record;
        //Dynamically update the top toolbar
        tb = me.getGrid().down('toolbar[dock=top]');

        var edit = record.get('update');
        if(edit == true){
            if(tb.down('#edit') != null){
                tb.down('#edit').setDisabled(false);
            }
        }else{
            if(tb.down('#edit') != null){
                tb.down('#edit').setDisabled(true);
            }
        }

        var del = record.get('delete');
        if(del == true){
            if(tb.down('#delete') != null){
                tb.down('#delete').setDisabled(false);
            }
        }else{
            if(tb.down('#delete') != null){
                tb.down('#delete').setDisabled(true);
            }
        }
    },
    add: function(button){
        var me = this;
        //We need to do a check to determine if this user (be it admin or acess provider has the ability to add to children)
        //admin/root will always have, an AP must be checked if it is the parent to some sub-providers. If not we will simply show the add window
        //if it does have, we will show the add wizard.

        Ext.Ajax.request({
            url: me.getUrlApChildCheck(),
            method: 'GET',
            success: function(response){
                var jsonData    = Ext.JSON.decode(response.responseText);
                if(jsonData.success){
                    if(jsonData.items.tree == true){
                    
                        if(!Ext.WindowManager.get('winDynamicDetailAddWizardId')){
                            var w = Ext.widget('winDynamicDetailAddWizard',
                            {
                                id          :'winDynamicDetailAddWizardId'
                            });
                            w.show();         
                        }
                    }else{
                        if(!Ext.WindowManager.get('winDynamicDetailAddWizardId')){
                            var w   = Ext.widget('winDynamicDetailAddWizard',
                            {
                                id          : 'winDynamicDetailAddWizardId',
                                startScreen : 'scrnData',
                                user_id     : '0',
                                owner       : i18n('sLogged_in_user'),
                                no_tree     : true
                            });
                            w.show();       
                        }
                    }
                }   
            },
            scope: me
        });
    },
    btnTreeNext: function(button){
        var me = this;
        var tree = button.up('treepanel');
        //Get selection:
        var sr = tree.getSelectionModel().getLastSelected();
        if(sr){    
            var win = button.up('winDynamicDetailAddWizard');
            win.down('#owner').setValue(sr.get('username'));
            win.down('#user_id').setValue(sr.getId());
            win.getLayout().setActiveItem('scrnData');
        }else{
            Ext.ux.Toaster.msg(
                        i18n('sSelect_an_owner'),
                        i18n('sFirst_select_an_Access_Provider_who_will_be_the_owner'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }
    },
    btnDynamicDetailDetailPrev: function(button){
        var me = this;
        var win = button.up('winDynamicDetailAddWizard');
        win.getLayout().setActiveItem('scrnApTree');
    },
    addSubmit: function(button){
        var me      = this;
        var win     = button.up('window');
        var form    = win.down('form');
        form.submit({
            clientValidation: true,
            url: me.getUrlAdd(),
            success: function(form, action) {
                win.close();
                me.getStore('sDynamicDetails').load();
                Ext.ux.Toaster.msg(
                    i18n('sNew_item_created'),
                    i18n('sItem_created_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
            },
            //Focus on the first tab as this is the most likely cause of error 
            failure: function(form,action){
                var tp = win.down('tabpanel');
                tp.setActiveTab(0);
                Ext.ux.formFail(form,action)
            }
        });
    },
	chkUserLoginChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
        var chkSuff = form.down('#chkAutoSuffix');
		var txtSuff = form.down('#txtSuffix');
        var value   = chk.getValue();
        if(value){
            chkSuff.setDisabled(false); 
			//txtSuff.setDisabled(false);               
        }else{
			chkSuff.setDisabled(true); 
			txtSuff.setDisabled(true);
        }
    },
	chkAutoSuffixChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
		var txtSuff = form.down('#txtSuffix');
        var value   = chk.getValue();
        if(value){
			txtSuff.setDisabled(false);               
        }else{
			txtSuff.setDisabled(true);
        }
    },
    chkTcChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
        var url     = form.down('#txtTcUrl');
        var value   = chk.getValue();
        if(value){
            url.setDisabled(false);                
        }else{
            url.setDisabled(true);
        }
    },
    chkRedirectChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
        var url     = form.down('#txtRedirectUrl');
        var value   = chk.getValue();
        if(value){
            url.setDisabled(false);                
        }else{
            url.setDisabled(true);
        }
    },
	chkUsageChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
        var nr      = form.down('#nrUsageRefresh');
        var value   = chk.getValue();
        if(value){
            nr.setDisabled(false);                
        }else{
            nr.setDisabled(true);
        }
    },
    chkClickToConnectChange: function(chk){
        var me      = this;
        var form    = chk.up('form');
        var un      = form.down('#txtConnectUsername');
        var sx      = form.down('#txtConnectSuffix');
        var cd      = form.down('#nrConnectDelay');
        var co      = form.down('#chkConnectOnly');
        var value   = chk.getValue();
        if(value){
            un.setDisabled(false);
            sx.setDisabled(false);
            cd.setDisabled(false);
            co.setDisabled(false);                
        }else{
            un.setDisabled(true);
            sx.setDisabled(true);
            cd.setDisabled(true);
            co.setDisabled(true);
        }
    },
    del:   function(button){
        var me      = this;     
        //Find out if there was something selected
        if(me.getGrid().getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_delete'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            Ext.MessageBox.confirm(i18n('sConfirm'), i18n('sAre_you_sure_you_want_to_do_that_qm'), function(val){
                if(val== 'yes'){

                    var selected    = me.getGrid().getSelectionModel().getSelection();
                    var list        = [];
                    Ext.Array.forEach(selected,function(item){
                        var id = item.getId();
                        Ext.Array.push(list,{'id' : id});
                    });
                    Ext.Ajax.request({
                        url: me.getUrlDelete(),
                        method: 'POST',          
                        jsonData: list,
                        success: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sItem_deleted'),
                                i18n('sItem_deleted_fine'),
                                Ext.ux.Constants.clsInfo,
                                Ext.ux.Constants.msgInfo
                            );
                            me.reload(); //Reload from server
                        },                                    
                        failure: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sProblems_deleting_item'),
                                batch.proxy.getReader().rawData.message.message,
                                Ext.ux.Constants.clsWarn,
                                Ext.ux.Constants.msgWarn
                            );
                            me.reload(); //Reload from server
                        }
                    });

                }
            });
        }
    },
    edit: function(button){
        var me = this;  
        //Find out if there was something selected
        if(me.getGrid().getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            //Check if the node is not already open; else open the node:
            var tp      = me.getGrid().up('tabpanel');
            var sr      = me.getGrid().getSelectionModel().getLastSelected();
            var id      = sr.getId();
            var user_id = sr.get('user_id');
            var tab_id  = 'dynamicDetailTab_'+id;
            var nt      = tp.down('#'+tab_id);
            if(nt){
                tp.setActiveTab(tab_id); //Set focus on  Tab
                return;
            }

            var tab_name = me.selectedRecord.get('name');
            //Tab not there - add one
            tp.add({ 
                title :     tab_name,
                itemId:     tab_id,
                closable:   true,
                glyph       : Rd.config.icnEdit, 
                layout:     'fit', 
                items:      {'xtype' : 'pnlDynamicDetail',dynamic_detail_id: id, user_id: user_id}
            });
            tp.setActiveTab(tab_id); //Set focus on Add Tab
        }
    },
    editSubmit: function(button,url){
        var me      = this;
        var form    = button.up('form');
        form.submit({
            clientValidation: true,
            url: url,
            success: function(form, action) {
                me.getStore('sDynamicDetails').load();
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
            },
            failure: Ext.ux.formFail
        });
    },
    csvExport: function(button,format) {
        var me          = this;
        var columns     = me.getGrid().columns;
        var col_list    = [];
        Ext.Array.each(columns, function(item,index){
            if(item.dataIndex != ''){
                var chk = {boxLabel: item.text, name: item.dataIndex, checked: true};
                col_list[index] = chk;
            }
        }); 

        if(!Ext.WindowManager.get('winCsvColumnSelectDynamicDetails')){
            var w = Ext.widget('winCsvColumnSelect',{id:'winCsvColumnSelectDynamicDetails',columns: col_list});
            w.show();        
        }
    },
    csvExportSubmit: function(button){

        var me      = this;
        var win     = button.up('window');
        var form    = win.down('form');

        var chkList = form.query('checkbox');
        var c_found = false;
        var columns = [];
        var c_count = 0;
        Ext.Array.each(chkList,function(item){
            if(item.getValue()){ //Only selected items
                c_found = true;
                columns[c_count] = {'name': item.getName()};
                c_count = c_count +1; //For next one
            }
        },me);

        if(!c_found){
            Ext.ux.Toaster.msg(
                        i18n('sSelect_one_or_more'),
                        i18n('sSelect_one_or_more_columns_please'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{     
            //next we need to find the filter values:
            var filters     = [];
            var f_count     = 0;
            var f_found     = false;
            var filter_json ='';
            
            var filter_collection = me.getGrid().getStore().getFilters();     
            if(filter_collection.count() > 0){
                var i = 0;
                while (f_count < filter_collection.count()) { 

                   // console.log(filter_collection.getAt(f_count).serialize( ));
                    f_found         = true;
                    var ser_item    = filter_collection.getAt(f_count).serialize( );
                    ser_item.field  = ser_item.property;
                    filters[f_count]= ser_item;
                    f_count         = f_count + 1;
                    
                }     
            }
             
            var col_json        = "columns="+encodeURIComponent(Ext.JSON.encode(columns));
            var extra_params    = Ext.Object.toQueryString(Ext.Ajax.getExtraParams());
            var append_url      = "?"+extra_params+'&'+col_json;
            if(f_found){
                filter_json = "filter="+encodeURIComponent(Ext.JSON.encode(filters));
                append_url  = append_url+'&'+filter_json;
            }
            window.open(me.getUrlExportCsv()+append_url);
            win.close();
        }
    },

    note: function(button,format) {
        var me      = this;    
        //Find out if there was something selected
        var sel_count = me.getGrid().getSelectionModel().getCount();
        if(sel_count == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(sel_count > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{

                //Determine the selected record:
                var sr = me.getGrid().getSelectionModel().getLastSelected();
                
                if(!Ext.WindowManager.get('winNoteDynamicDetails'+sr.getId())){
                    var w = Ext.widget('winNote',
                        {
                            id          : 'winNoteDynamicDetails'+sr.getId(),
                            noteForId   : sr.getId(),
                            noteForGrid : 'dynamicDetails',
                            noteForName : sr.get('name')
                        });
                    w.show();       
                }
            }    
        }
    },
    noteReload: function(button){
        var me      = this;
        var grid    = button.up('gridNote');
        grid.getStore().load();
    },
    noteAdd: function(button){
        var me      = this;
        var grid    = button.up('gridNote');

        //See how the wizard should be displayed:
        Ext.Ajax.request({
            url: me.getUrlApChildCheck(),
            method: 'GET',
            success: function(response){
                var jsonData    = Ext.JSON.decode(response.responseText);
                if(jsonData.success){                      
                    if(jsonData.items.tree == true){
                        if(!Ext.WindowManager.get('winNoteDynamicDetailsAdd'+grid.noteForId)){
                            var w   = Ext.widget('winNoteAdd',
                            {
                                id          : 'winNoteDynamicDetailsAdd'+grid.noteForId,
                                noteForId   : grid.noteForId,
                                noteForGrid : grid.noteForGrid,
                                refreshGrid : grid
                            });
                            w.show();       
                        }
                    }else{
                        if(!Ext.WindowManager.get('winNoteDynamicDetailsAdd'+grid.noteForId)){
                            var w   = Ext.widget('winNoteAdd',
                            {
                                id          : 'winNoteDynamicDetailsAdd'+grid.noteForId,
                                noteForId   : grid.noteForId,
                                noteForGrid : grid.noteForGrid,
                                refreshGrid : grid,
                                startScreen : 'scrnNote',
                                user_id     : '0',
                                owner       : i18n('sLogged_in_user'),
                                no_tree     : true
                            });
                            w.show();      
                        }
                    }
                }   
            },
            scope: me
        });
    },
    gridNoteClick: function(item,record){
        var me = this;
        //Dynamically update the top toolbar
        grid    = item.up('gridNote');
        tb      = grid.down('toolbar[dock=top]');
        var del = record.get('delete');
        if(del == true){
            if(tb.down('#delete') != null){
                tb.down('#delete').setDisabled(false);
            }
        }else{
            if(tb.down('#delete') != null){
                tb.down('#delete').setDisabled(true);
            }
        }
    },
    btnNoteTreeNext: function(button){
        var me = this;
        var tree = button.up('treepanel');
        //Get selection:
        var sr = tree.getSelectionModel().getLastSelected();
        if(sr){    
            var win = button.up('winNoteAdd');
            win.down('#owner').setValue(sr.get('username'));
            win.down('#user_id').setValue(sr.getId());
            win.getLayout().setActiveItem('scrnNote');
        }else{
            Ext.ux.Toaster.msg(
                        i18n('sSelect_an_owner'),
                        i18n('sFirst_select_an_Access_Provider_who_will_be_the_owner'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }
    },
    btnNoteAddPrev: function(button){
        var me = this;
        var win = button.up('winNoteAdd');
        win.getLayout().setActiveItem('scrnApTree');
    },
    btnNoteAddNext: function(button){
        var me      = this;
        var win     = button.up('winNoteAdd');
        win.refreshGrid.getStore().load();
        var form    = win.down('form');
        form.submit({
            clientValidation: true,
            url: me.getUrlNoteAdd(),
            params: {for_id : win.noteForId},
            success: function(form, action) {
                win.close();
                win.refreshGrid.getStore().load();
                me.reload();
                Ext.ux.Toaster.msg(
                    i18n('sNew_item_created'),
                    i18n('sItem_created_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
            },
            failure: Ext.ux.formFail
        });
    },
    noteDelete: function(button){
        var me      = this;
        var grid    = button.up('gridNote');
        //Find out if there was something selected
        if(grid.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            Ext.MessageBox.confirm(i18n('sConfirm'), i18n('sAre_you_sure_you_want_to_do_that_qm'), function(val){
                if(val== 'yes'){
                    grid.getStore().remove(grid.getSelectionModel().getSelection());
                    grid.getStore().sync({
                        success: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sItem_deleted'),
                                i18n('sItem_deleted_fine'),
                                Ext.ux.Constants.clsInfo,
                                Ext.ux.Constants.msgInfo
                            );
                            grid.getStore().load();   //Update the count
                            me.reload();   
                        },
                        failure: function(batch,options,c,d){
                            Ext.ux.Toaster.msg(
                                i18n('sProblems_deleting_item'),
                                batch.proxy.getReader().rawData.message.message,
                                Ext.ux.Constants.clsWarn,
                                Ext.ux.Constants.msgWarn
                            );
                            grid.getStore().load(); //Reload from server since the sync was not good
                        }
                    });
                }
            });
        }
    },
    tabDetailActivate : function(tab){
        var me      = this;
        var form    = tab.down('form');
        var dynamic_detail_id= tab.up('pnlDynamicDetail').dynamic_detail_id;
        form.load({url:me.getUrlViewDynamicDetail(), method:'GET',params:{dynamic_detail_id:dynamic_detail_id}});
    },
    tabSettingsActivate : function(tab){
        var me      = this;
        var form    = tab.down('form');
        var dynamic_detail_id= tab.up('pnlDynamicDetail').dynamic_detail_id;
        form.load({
            url         : me.getUrlViewDynamicDetail(), 
            method      : 'GET',
            params      : {dynamic_detail_id:dynamic_detail_id},
            success     : function(a,b,c){
                 if(b.result.data.realm_id != null){
                    var realm = form.down("#realm");
                    var mr    = Ext.create('Rd.model.mRealm', {name: b.result.data.realm, id: b.result.data.realm_id});
                    realm.getStore().loadData([mr],false);
                    realm.setValue(b.result.data.realm_id);
                }
                if(b.result.data.profile_id != null){
                    var profile = form.down("#profile");
                    var mp     = Ext.create('Rd.model.mProfile', {name: b.result.data.profile, id: b.result.data.profile_id});
                    profile.getStore().loadData([mp],false);
                    profile.setValue(b.result.data.profile_id);
                }
            }
        }); 
    },
    tabLogoActivate: function(tab){
        var me      = this;
        var pnl_n   = tab.up('pnlNas');
        var dynamic_detail_id= tab.up('pnlDynamicDetail').dynamic_detail_id;
        var p_img   = tab.down('#pnlImg');
        Ext.Ajax.request({
            url: me.getUrlViewDynamicDetail(),
            method: 'GET',
            params: {dynamic_detail_id : dynamic_detail_id },
            success: function(response){
                var jsonData    = Ext.JSON.decode(response.responseText);
                if(jsonData.success){
                    var img_url = me.getUrlLogoBase()+jsonData.data.icon_file_name;
                    p_img.update({image:img_url});
                }   
            },
            scope: me
        });
    },
    logoSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var pnl_r   = form.up('pnlDynamicDetail');
        var p_form  = form.up('panel');
        var p_img   = p_form.down('#pnlImg');
        form.submit({
            clientValidation: true,
            waitMsg: 'Uploading your photo...',
            url: me.getUrlUploadLogo(),
            params: {'id' : pnl_r.dynamic_detail_id },
            success: function(form, action) {              
                if(action.result.success){ 
                    var new_img = action.result.icon_file_name;    
                    var img_url = me.getUrlLogoBase()+new_img;
                    p_img.update({image:img_url});
                } 
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
            },
            failure: Ext.ux.formFail
        });
    },
    logoCancel: function(button){
        var me      = this;
        var form    = button.up('form');
        form.getForm().reset();
    },
    tabPhotoActivate:  function(t){
        var me = this;
        t.down('dataview').getStore().load();
    },
    photoReload:  function(b){
        var me = this;
        b.up('#tabPhoto').down('dataview').getStore().load();
    },
    photoAdd: function(b){
        var me = this;
        var d_id = b.up('pnlDynamicDetail').dynamic_detail_id;
        var d_v  = b.up('#tabPhoto').down('dataview');

        if(!Ext.WindowManager.get('winPhotoAddId')){
            var w   = Ext.widget('winPhotoAdd',
            {
                id                  : 'winPhotoAddId',
                dynamic_detail_id   : d_id,
                data_view           : d_v
            });
            w.show();      
        }
    },
    photoAddSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            waitMsg: 'Uploading your photo...',
            url: me.getUrlUploadPhoto(),
            params: {'dynamic_detail_id' : window.dynamic_detail_id },
            success: function(form, action) {              
                //FIXME reload store....
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.data_view.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    photoAddCancel: function(button){
        var me      = this;
        var form    = button.up('form');
        form.getForm().reset();
    },
    photoDel:   function(button){
        var me      = this;
        var d_view  = button.up('#tabPhoto').down('dataview');     
        //Find out if there was something selected
        if(d_view.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_delete'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            Ext.MessageBox.confirm(i18n('sConfirm'), i18n('sAre_you_sure_you_want_to_do_that_qm'), function(val){
                if(val== 'yes'){
                    d_view.getStore().remove(d_view.getSelectionModel().getSelection());
                    d_view.getStore().sync({
                        success: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sItem_deleted'),
                                i18n('sItem_deleted_fine'),
                                Ext.ux.Constants.clsInfo,
                                Ext.ux.Constants.msgInfo
                            );
                            d_view.getStore().load();   //Update the count   
                        },
                        failure: function(batch,options,c,d){
                            Ext.ux.Toaster.msg(
                                i18n('sProblems_deleting_item'),
                                batch.proxy.getReader().rawData.message.message,
                                Ext.ux.Constants.clsWarn,
                                Ext.ux.Constants.msgWarn
                            );
                            d_view.getStore().load(); //Reload from server since the sync was not good
                        }
                    });

                }
            });
        }
    },
    photoEdit:   function(button){
        var me      = this;
        var d_view  = button.up('#tabPhoto').down('dataview');     
        //Find out if there was something selected
        if(d_view.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_edit'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(d_view.getSelectionModel().getCount() > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{
                if(!Ext.WindowManager.get('winPhotoEditId')){
                    var w   = Ext.widget('winPhotoEdit',
                    {
                        id                  : 'winPhotoEditId',
                        data_view           : d_view
                    });
                    w.down('form').loadRecord(d_view.getSelectionModel().getLastSelected());
                    w.show();     
                }
            }    
        }
    },
    photoEditSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            waitMsg: 'Updating your photo...',
            url: me.getUrlEditPhoto(),
            success: function(form, action) {              
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.data_view.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    photoEditCancel: function(button){
        var me      = this;
        var form    = button.up('form');
        form.getForm().reset();
    },
    tabPagesActivate: function(g){
        var me      = this;
        g.getStore().load();
    },
    pageReload:  function(b){
        var me = this;
        b.up('pnlDynamicDetail').down('#tabPages').getStore().load();
    },
    pageAdd: function(b){
        var me      = this;
        var d_id    = b.up('pnlDynamicDetail').dynamic_detail_id;
        var grid    = b.up('pnlDynamicDetail').down('#tabPages');

        if(!Ext.WindowManager.get('winPageAddId')){
            var w   = Ext.widget('winPageAdd',
            {
                id                  : 'winPageAddId',
                dynamic_detail_id   : d_id,
                grid                : grid
            });
            w.show();      
        }
    },
    pageAddSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            url: me.getUrlAddPage(),
            params: {'dynamic_detail_id' : window.dynamic_detail_id },
            success: function(form, action) {              
                //FIXME reload store....
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.grid.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    pageEdit:   function(b){
        var me      = this;
        var grid    = b.up('pnlDynamicDetail').down('#tabPages');     
        //Find out if there was something selected
        if(grid.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_edit'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(grid.getSelectionModel().getCount() > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{
                if(!Ext.WindowManager.get('winPageEditId')){
                    var w   = Ext.widget('winPageEdit',
                    {
                        id                  : 'winPageEditId',
                        grid                : grid
                    });
                    w.down('form').loadRecord(grid.getSelectionModel().getLastSelected());
                    w.show();      
                }
            }    
        }
    },
    pageEditSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            url: me.getUrlEditPage(),
            success: function(form, action) {              
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.grid.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    pageDel:   function(b){
        var me      = this;
        var grid    = b.up('pnlDynamicDetail').down('#tabPages');     
        //Find out if there was something selected
        if(grid.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_delete'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            Ext.MessageBox.confirm(i18n('sConfirm'), i18n('sAre_you_sure_you_want_to_do_that_qm'), function(val){
                if(val== 'yes'){
                    grid.getStore().remove(grid.getSelectionModel().getSelection());
                    grid.getStore().sync({
                        success: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sItem_deleted'),
                                i18n('sItem_deleted_fine'),
                                Ext.ux.Constants.clsInfo,
                                Ext.ux.Constants.msgInfo
                            );
                            grid.getStore().load();   //Update the count   
                        },
                        failure: function(batch,options,c,d){
                            Ext.ux.Toaster.msg(
                                i18n('sProblems_deleting_item'),
                                batch.proxy.getReader().rawData.message.message,
                                Ext.ux.Constants.clsWarn,
                                Ext.ux.Constants.msgWarn
                            );
                            grid.getStore().load(); //Reload from server since the sync was not good
                        }
                    });

                }
            });
        }
    },
    tabPairsActivate: function(g){
        var me      = this;
        g.getStore().load();
    },
    pairReload:  function(b){
        var me = this;
        b.up('pnlDynamicDetail').down('#tabPairs').getStore().load();
    },
    pairAdd: function(b){
        var me      = this;
        var d_id    = b.up('pnlDynamicDetail').dynamic_detail_id;
        var grid    = b.up('pnlDynamicDetail').down('#tabPairs');

        if(!Ext.WindowManager.get('winPairAddId')){
            var w   = Ext.widget('winPairAdd',
            {
                id                  : 'winPairAddId',
                dynamic_detail_id   : d_id,
                grid                : grid
            });
            w.show();       
        }
    },
    pairAddSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            url: me.getUrlAddPair(),
            params: {'dynamic_detail_id' : window.dynamic_detail_id },
            success: function(form, action) {              
                //FIXME reload store....
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.grid.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    pairEdit:   function(b){
        var me      = this;
        var grid    = b.up('pnlDynamicDetail').down('#tabPairs');     
        //Find out if there was something selected
        if(grid.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_edit'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(grid.getSelectionModel().getCount() > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{
                if(!Ext.WindowManager.get('winPairEditId')){
                    var w   = Ext.widget('winPairEdit',
                    {
                        id                  : 'winPairEditId',
                        grid                : grid
                    });
                    w.down('form').loadRecord(grid.getSelectionModel().getLastSelected());
                    w.show();     
                }
            }    
        }
    },
    pairEditSave: function(button){
        var me      = this;
        var form    = button.up('form');
        var window  = form.up('window');

        form.submit({
            clientValidation: true,
            url: me.getUrlEditPair(),
            success: function(form, action) {              
                Ext.ux.Toaster.msg(
                    i18n('sItem_updated'),
                    i18n('sItem_updated_fine'),
                    Ext.ux.Constants.clsInfo,
                    Ext.ux.Constants.msgInfo
                );
                window.grid.getStore().load();
                window.close();
            },
            failure: Ext.ux.formFail
        });
    },
    pairDel:   function(b){
        var me      = this;
        var grid    = b.up('pnlDynamicDetail').down('#tabPairs');     
        //Find out if there was something selected
        if(grid.getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item_to_delete'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            Ext.MessageBox.confirm(i18n('sConfirm'), i18n('sAre_you_sure_you_want_to_do_that_qm'), function(val){
                if(val== 'yes'){
                    grid.getStore().remove(grid.getSelectionModel().getSelection());
                    grid.getStore().sync({
                        success: function(batch,options){
                            Ext.ux.Toaster.msg(
                                i18n('sItem_deleted'),
                                i18n('sItem_deleted_fine'),
                                Ext.ux.Constants.clsInfo,
                                Ext.ux.Constants.msgInfo
                            );
                            grid.getStore().load();   //Update the count   
                        },
                        failure: function(batch,options,c,d){
                            Ext.ux.Toaster.msg(
                                i18n('sProblems_deleting_item'),
                                batch.proxy.getReader().rawData.message.message,
                                Ext.ux.Constants.clsWarn,
                                Ext.ux.Constants.msgWarn
                            );
                            grid.getStore().load(); //Reload from server since the sync was not good
                        }
                    });

                }
            });
        }
    },
    previewMobile: function(b){
        var me          = this;
        //Find out if there was something selected
        if(me.getGrid().getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(me.getGrid().getSelectionModel().getCount() > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{
                var record = me.getGrid().getSelectionModel().getLastSelected();
                window.open(me.getUrlPreviewMobile()+"?dynamic_id="+record.getId())
            }         
        }
    },
    previewDesktop: function(b){
         var me          = this;
        //Find out if there was something selected
        if(me.getGrid().getSelectionModel().getCount() == 0){
             Ext.ux.Toaster.msg(
                        i18n('sSelect_an_item'),
                        i18n('sFirst_select_an_item'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
            );
        }else{
            if(me.getGrid().getSelectionModel().getCount() > 1){
                Ext.ux.Toaster.msg(
                        i18n('sLimit_the_selection'),
                        i18n('sSelection_limited_to_one'),
                        Ext.ux.Constants.clsWarn,
                        Ext.ux.Constants.msgWarn
                );
            }else{
                var record = me.getGrid().getSelectionModel().getLastSelected();
                window.open(me.getUrlPreviewDesktop()+"?dynamic_id="+record.getId())
            }         
        }
    },
	tabSocialLoginActivate : function(tab){
        var me      = this;
        var form    = tab.down('form');
        var dynamic_detail_id= tab.up('pnlDynamicDetail').dynamic_detail_id;
        
        if(me.modelInited == undefined ){
		    Ext.define('SocialForm', {
			    extend: 'Ext.data.Model',
			    fields: [
				    {name: 'id',       							type: 'int'},
				    {name: 'social_temp_permanent_user_id',     type: 'int'},
				    {name: 'social_temp_permanent_user_name',   type: 'string'},
				    {name: 'social_enable',    					type: 'boolean', defaultValue: false},
				    {name: 'fb_enable',    					    type: 'boolean', defaultValue: false},
				    {name: 'fb_record_info',    				type: 'boolean', defaultValue: false},
				    {name: 'fb_id',    							type: 'string'},
				    {name: 'fb_secret',    						type: 'string'},
				    {name: 'fb_profile',    					type: 'int'},
				    {name: 'fb_profile_name',    				type: 'string'},
				    {name: 'fb_realm',    						type: 'int'},
				    {name: 'fb_realm_name',    					type: 'string'},
				    {name: 'fb_voucher_or_user',    			type: 'string'},
				    {name: 'gp_enable',    					    type: 'boolean', defaultValue: false},
				    {name: 'gp_record_info',    				type: 'boolean', defaultValue: false},
				    {name: 'gp_id',    							type: 'string'},
				    {name: 'gp_secret',    						type: 'string'},
				    {name: 'gp_profile',    					type: 'int'},
				    {name: 'gp_profile_name',    				type: 'string'},
				    {name: 'gp_realm',    						type: 'int'},
				    {name: 'gp_realm_name',    					type: 'string'},
				    {name: 'gp_voucher_or_user',    			type: 'string'},
				    {name: 'tw_enable',    					    type: 'boolean', defaultValue: false},
				    {name: 'tw_record_info',    				type: 'boolean', defaultValue: false},
				    {name: 'tw_id',    							type: 'string'},
				    {name: 'tw_secret',    						type: 'string'},
				    {name: 'tw_profile',    					type: 'int'},
				    {name: 'tw_profile_name',    				type: 'string'},
				    {name: 'tw_realm',    						type: 'int'},
				    {name: 'tw_realm_name',    					type: 'string'},
				    {name: 'tw_voucher_or_user',    			type: 'string'}
			    ]
		    });
		    
		    me.modelInited = true;
	    }

		//Fetch the info for this tab manually and load the combo's and form
		Ext.Ajax.request({
            url		: me.getUrlViewSocial(),
            method	: 'GET',
			params	:{dynamic_detail_id:dynamic_detail_id},
            success	: function(response){
                var jsonData    = Ext.JSON.decode(response.responseText);
                if(jsonData.success){
                   
					var instance = Ext.create('SocialForm', jsonData.data);
					form.loadRecord(instance);
					//temp username 
					var tu_id   = instance.get('social_temp_permanent_user_id');
					var tu_n    = instance.get('social_temp_permanent_user_name');

					var tu_rec   = Ext.create('Rd.model.mPermanentUser', {username: tu_n, id: tu_id});
					form.down('#socialTempUser').getStore().loadData([tu_rec],false);
					form.down('#socialTempUser').setValue(tu_id);

					//fb profile
					var fb_p_id     = instance.get('fb_profile');
					var fb_p_name   = instance.get('fb_profile_name');
					var fb_p_rec    = Ext.create('Rd.model.mProfile', {name: fb_p_name, id: fb_p_id});
					form.down('#fbProfile').getStore().loadData([fb_p_rec],false);
					form.down('#fbProfile').setValue(fb_p_id);
					
					//fb realm
					var fb_r_id     = instance.get('fb_realm');
					var fb_r_name   = instance.get('fb_realm_name');
					var fb_r_rec    = Ext.create('Rd.model.mRealm', {name: fb_r_name, id: fb_r_id});
					form.down('#fbRealm').getStore().loadData([fb_r_rec],false);
					form.down('#fbRealm').setValue(fb_r_id);

					//gp profile
					var gp_p_id     = instance.get('gp_profile');
					var gp_p_name   = instance.get('gp_profile_name');
					var gp_p_rec    = Ext.create('Rd.model.mProfile', {name: gp_p_name, id: gp_p_id});
					form.down('#gpProfile').getStore().loadData([gp_p_rec],false);
					form.down('#gpProfile').setValue(gp_p_id);
					
					//gp realm
					var gp_r_id     = instance.get('gp_realm');
					var gp_r_name   = instance.get('gp_realm_name');
					var gp_r_rec    = Ext.create('Rd.model.mRealm', {name: gp_r_name, id: gp_r_id});
					form.down('#gpRealm').getStore().loadData([fb_r_rec],false);
					form.down('#gpRealm').setValue(gp_r_id);

					//tw profile
					var tw_p_id     = instance.get('tw_profile');
					var tw_p_name   = instance.get('tw_profile_name');
					var tw_p_rec    = Ext.create('Rd.model.mProfile', {name: tw_p_name, id: tw_p_id});
					form.down('#twProfile').getStore().loadData([tw_p_rec],false);
					form.down('#twProfile').setValue(tw_p_id);
					
					//tw realm
					var tw_r_id     = instance.get('tw_realm');
					var tw_r_name   = instance.get('tw_realm_name');
					var tw_r_rec    = Ext.create('Rd.model.mRealm', {name: tw_r_name, id: tw_r_id});
					form.down('#twRealm').getStore().loadData([tw_r_rec],false);
					form.down('#twRealm').setValue(tw_r_id);

                }   
            },
            scope: me
        });
    }
		
});
