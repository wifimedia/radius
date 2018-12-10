Ext.define('Rd.view.meshes.vcMeshExitPoint', {
    extend  : 'Ext.app.ViewController',
    alias   : 'controller.vcMeshExitPoint',
    config : {
        urlCheckExperimental : '/cake2/rd_cake/meshes/mesh_experimental_check.json'
    },
    init: function() {
        var me = this;
    },    
	onChkDnsOverrideChange: function(chk){
		var me 		= this;
		var form    = chk.up('form');
		var d1      = form.down('#txtDns1');
		var d2      = form.down('#txtDns2');
		var desk    = form.down('#chkDnsDesk');
		if(chk.getValue()){
		    d1.enable();
		    d2.enable();
		    desk.setValue(false);
		    desk.disable(); 
		}else{
		    d1.disable();
		    d2.disable();
		    desk.enable(); 
		}
	},
	onChkDnsDeskChange: function(chk){
	    var me 		= this;
		var form    = chk.up('form');
		var override= form.down('#chkDnsOverride');
		var any     = form.down('#chkAnyDns');
		if(chk.getValue()){
		    any.setValue(false);
		    any.disable();
		    override.setValue(false);
		    override.disable();  
		}else{
		    any.enable();
		    override.enable();  
		}
	},
	onDnsDeskBeforeRender : function(chk){
	    var me = this; 
	    Ext.Ajax.request({
            url: me.getUrlCheckExperimental(),
            method: 'GET',
            success: function(response){
                var jsonData    = Ext.JSON.decode(response.responseText);
                if(jsonData.success){                      
                    if(jsonData.active){
                        chk.show();
                        chk.enable();  
                    }else{
                        chk.hide();
                        chk.disable(); 
                    }
                }   
            },
            scope: me
        });
	},
	onRgrpProtocolChange : function(grp){
	    var me          = this; 
	    var win         = grp.up('window');
	    var txtIpaddr   = win.down('#txtIpaddr');
        var txtNetmask  = win.down('#txtNetmask');
        var txtGateway  = win.down('#txtGateway');
        var txtDns1     = win.down('#txtDns1');
        var txtDns2     = win.down('#txtDns2');
        
        if(grp.getValue().proto == 'static'){         
            txtIpaddr.setVisible(true);
		    txtIpaddr.setDisabled(false);
            txtNetmask.setVisible(true);
            txtNetmask.setDisabled(false);  
            txtGateway.setVisible(true);
            txtGateway.setDisabled(false);     
            txtDns1.setVisible(true);
            txtDns1.setDisabled(false);
            txtDns2.setVisible(true);  
            txtDns2.setDisabled(false);
        }else{
            txtIpaddr.setVisible(false);
		    txtIpaddr.setDisabled(true);
            txtNetmask.setVisible(false);
            txtNetmask.setDisabled(true);  
            txtGateway.setVisible(false);
            txtGateway.setDisabled(true);     
            txtDns1.setVisible(false);
            txtDns1.setDisabled(true);
            txtDns2.setVisible(false);  
            txtDns2.setDisabled(true);
        }
	}
});
