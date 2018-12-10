<?php

//===== MESHdesk ======

//== Client WPA2 Personal passphrase (key) NOTE: We moved this setting to be under common_node_settings instead
//$config['MESHdesk']['client_key']	= 'radiusdesk'; 

//_______________________________________________
//== Pre-set values for the Captive Portals
$config['Meshes']['captive_portal']['radius_1']         = '198.27.111.78'; // This will be the public IP Address of the FreeRADIUS / RADIUSdesk
//$config['ApProfiles']['captive_portal']['radius_2']         = '198.27.111.78'; //Optional second fallback RADIUS
$config['Meshes']['captive_portal']['radius_secret']    = 'testing123'; //Change this to the common site wide secret used by Dynamic RADIUS Clients
//Use DNS name in ual_url to look more professional / or IP Address 
$config['Meshes']['captive_portal']['uam_url']          = 'http://198.27.111.78/cake3/rd_cake/dynamic-details/chilli-browser-detect/'; 
$config['Meshes']['captive_portal']['uam_secret']       = 'greatsecret'; //Usually you will not change this value

//$config['ApProfiles']['captive_portal']['walled_garden'] = "www.radiusdesk.com,www.google.com"; //Optional
$config['Meshes']['captive_portal']['swap_octet']       = true;
$config['Meshes']['captive_portal']['mac_auth']         = true;
$config['Meshes']['captive_portal']['coova_optional']   = "ssid=radiusdesk";
//__________________________________________________


//== Encryption types ==
//Define the encryption types and if they are active or not
$config['encryption'][0]     = array('name' => __('None'),              'id' => 'none',     'active' => true);
$config['encryption'][1]     = array('name' => __('WEP'),               'id' => 'wep',      'active' => true);
$config['encryption'][2]     = array('name' => __('WPA Personal'),      'id' => 'psk',      'active' => true);
$config['encryption'][3]     = array('name' => __('WPA2 Personal'),     'id' => 'psk2',     'active' => true);
$config['encryption'][4]     = array('name' => __('WPA Enterprise'),    'id' => 'wpa',      'active' => true);
$config['encryption'][5]     = array('name' => __('WPA2 Enterprise'),   'id' => 'wpa2',     'active' => true);

//== Default mesh settings ==
//Define default settings for the mesh which can be overwritten
$config['mesh_settings']['aggregated_ogms']  		= true;  //Aggregation*
$config['mesh_settings']['ap_isolation']  			= false; //AP Isolation*
$config['mesh_settings']['bonding']   				= false; //Bonding*
$config['mesh_settings']['fragmentation']   		= true;  //Fragmentation*
$config['mesh_settings']['gw_sel_class'] 			= 20; //Client Gateway switching*
$config['mesh_settings']['orig_interval']  			= 1000; //OGM Interval*
$config['mesh_settings']['bridge_loop_avoidance']  	= false; //Bridge loop avoidence*
$config['mesh_settings']['distributed_arp_table'] 	= true; //Distributed ARP table

//April 2016 ->Support for 802.11s or Ad-hoc
$config['mesh_settings']['connectivity'] 	        = 'mesh_point'; //One of IBSS or mesh_point
$config['mesh_settings']['encryption']              = false; // true or false  does now work on nodes with 4M flash (eg TP-Link841)
$config['mesh_settings']['encryption_key']          = ''; //Need to be at least 8 characters long

//== Node start ip for defined mesh ==
$config['mesh_node']['start_ip']    = '10.5.5.1';

//== Default node settings ==
$config['common_node_settings']['password']  		= 'admin'; //Root password on nodes
$config['common_node_settings']['password_hash']  	= '$1$TJn8xhHP$BLhc3QEW54de0V8yCYD/T.'; //Output of 'openssl passwd -1 admin'
$config['common_node_settings']['power']     		= 100; //% of tx power to use on devices
$config['common_node_settings']['all_power'] 		= true; //Apply this power to all devices?
$config['common_node_settings']['two_chan']  		= 6; //% Channel to use on 2.4G wifi
$config['common_node_settings']['five_chan'] 		= 44; //% Channel to use on 5G wifi
$config['common_node_settings']['heartbeat_interval']  = 60; //Send a heartbeat pulse through every interval seconds
$config['common_node_settings']['heartbeat_dead_after'] = 300; //Mark a device as dead if we have not had heartbeats in this time

//New features
$config['common_node_settings']['eth_br_chk']		= false;	//set to true to bridge the ethernet ports of non-gw units	
$config['common_node_settings']['eth_br_with']		= 0; 		//Zero has a special meaning which is the LAN
$config['common_node_settings']['eth_br_for_all']	= false; 		//Apply this bridge to all non-gateway nodes

//Timezone addition
$config['common_node_settings']['timezone']	        = 24; 		//Take the number from $config['MESHdesk']['timezones']
$config['common_node_settings']['country']	        = 'ZA'; 	//Take the value from $config['MESHdesk']['countries']
$config['common_node_settings']['tz_name']	        = 'Africa/Johannesburg'; 	//Take the name from $config['MESHdesk']['countries']
$config['common_node_settings']['tz_value']	        = 'SAST-2'; 	//Take the name from $config['MESHdesk']['countries']

//Gateway specific tweaks
$config['common_node_settings']['gw_dhcp_timeout']  = 120;	//
$config['common_node_settings']['gw_use_previous']  = true;	//	
$config['common_node_settings']['gw_auto_reboot']   = true;	//
$config['common_node_settings']['gw_auto_reboot_time']   = 600;	//

//Default key when none is set
$config['common_node_settings']['client_key']	    = 'radiusdesk'; 		


//== Device types for MESHdesk ==

$config['hardware'][0]      = array(
		'name' 		=> __('Dragino MS14'),
		'vendor'    => __('Dragino'),
	    'model'     => __('MS14'),	   	
		'id'    	=> 'dragino',
		'radios'	=> 1,
		'active'    => true, 
		'max_power' => 18,
		'eth_br'	=> 'eth0 eth1',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '18',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
	
);

$config['hardware'][1]      = array(
        'name'          => __('Alix 3D2 (Dual Radio)'),
        'vendor'        => __('Alix'),
	    'model'         => __('3D2'),
        'id'            => 'alix3d2',
        'radios'        => 2,
        'active'    	=> true,
        'eth_br'        => 'eth0',

        //First radio 
        'max_power' => '23',
        'two'           => true,
        'five'          => false,
        'hwmode'        => '11g',

        //Second radio - This is extra for two radio devices
        'max_power1'=> '23',
        'two1'          => false,
        'five1'         => true,
        'hwmode1'       => '11a',

         //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '23',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        ),

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '23',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);


$config['hardware'][2]      = array(
    'name'          => __('AP 505'),
    'vendor'        => __('AP'),
	'model'         => __('505'),
    'id'            => 'pw_cpe505n' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '30',
    'eth_br'        => 'eth0', 
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '30',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][3]      = array(
    'name'          => __('AP 602'),
    'vendor'        => __('AP'),
	'model'         => __('602'),
    'id'            => 'ap_602' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '23',
    'eth_br'        => 'eth0 eth1', 
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '23',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][4]      = array(
    'name'          => __('EnGenius EAP300'),
     'vendor'       => __('EnGenius'),
	'model'         => __('EAP300'),
    'id'            => 'eap300' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '29',
    'eth_br'        => 'eth0',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '29',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);


$config['hardware'][5]      = array(
		'name' 		=> __('Generic 1 Radio'),
		'vendor'    => __('Generic'),
		'model'     => __('Single Radio'), 	
		'id'    	=> 'genoneradio',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '18',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '18',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
	
);

$config['hardware'][6]      = array(
        'name'          => __('Generic 2 Radio'),
        'vendor'        => __('Generic'),
		'model'         => __('Dual Radio'), 
        'id'            => 'gentworadio',
        'radios'        => 2,
        'active'    	=> true,
        'eth_br'        => 'eth0',

        //First radio 
        'max_power' => '18',
        'two'           => true,
        'five'          => false,
        'hwmode'        => '11g',

        //Second radio - This is extra for two radio devices
        'max_power1'=> '18',
        'two1'          => false,
        'five1'         => true,
        'hwmode1'       => '11a',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '18',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        ),

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '18',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][7]      = array(
  	'name'          => __('RB433 (Dual Radio)'),
  	'vendor'        => __('Mikrotik'),
	'model'         => __('RB433'),         
  	'id'          	=> 'rb433',
  	'radios'      	=> 2,
  	'active'    	=> true,
  	'eth_br'      	=> 'eth0 eth1',
  	//First radio
  	'max_power' 	=> '27',
  	'two'         	=> true,
  	'five'        	=> false,
  	'hwmode'      	=> '11g',
  	//Second radio - This is extra for two radio devices
  	'max_power1'	=> '27',
  	'two1'        	=> false,
  	'five1'        	=> true,
  	'hwmode1'     	=> '11a',
    
    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '27',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][8]      = array(
		'name' 		=> __('OpenMesh OM2P'),
		'vendor'    => __('OpenMesh'),
	    'model'     => __('OM2P'),  	
		'id'    	=> 'om2p' , 
		'radios'	=> 1,  
		'active'    => true, 
		'max_power' => '20',
		'eth_br'	=> 'eth0 eth1',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '20',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
	
);

$config['hardware'][9]      = array(
		'name' 		=> __('TP-Link WR841N'),
		'vendor'    => __('TP-Link'),
	    'model'     => __('WR841N'),	  	
		'id'    	=> 'tl841n' , 
		'radios'	=> 1,  
		'active'    => true, 
		'max_power' => '21',
		'eth_br'	=> 'eth0 eth1',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '21',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )	
);

$config['hardware'][10]      = array(
		'name' 		=> __('TP-Link WDR3500 (Dual Radio)'),
		'vendor'    => __('TP-Link'),
	    'model'     => __('WDR3500'),
		'id'    	=> 'tl_wdr3500',
		'radios'	=> 2, 
		'active'    => true,
		'eth_br'	=> 'eth0 eth1',

		//First radio 
		'max_power' => '20',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

		//Second radio - This is extra for two radio devices
		'max_power1'=> '23',
		'two1'		=> false,
		'five1'		=> true,
		'hwmode1'	=> '11a',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '20',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        ),

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '23',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )

);

$config['hardware'][11]      = array(
		'name' 		=> __('TP-Link WDR3600 (Dual Radio)'),
		'vendor'    => __('TP-Link'),
	    'model'     => __('WDR3600'),	
		'id'    	=> 'tl_wdr3600',
		'radios'	=> 2, 
		'active'    => true,
		'eth_br'	=> 'eth0 eth1',

		//First radio 
		'max_power' => '20',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

		//Second radio - This is extra for two radio devices
		'max_power1'=> '23',
		'two1'		=> false,
		'five1'		=> true,
		'hwmode1'	=> '11a',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '20',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        ),

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '23',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )

);


$config['hardware'][12]      = array(
    'name'          => __('TP-Link WA850RE'),
    'vendor'        => __('TP-Link'),
	'model'         => __('WA850RE'), 
    'id'            => 'tl_wa850re',
    'radios'        => 1, 
    'active'        => true,
    'max_power'     => '21',
    'eth_br'        => 'eth0',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '21',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][13]      = array(
    'name'          => __('TP-Link WA901ND'), 
    'vendor'        => __('TP-Link'),
	'model'         => __('WA901ND'), 
    'id'            => 'tl_wa901n' ,
    'radios'        => 1, 
    'active'        => true,
    'max_power'     => '21',
    'eth_br'        => 'eth0',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '21',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )

);

$config['hardware'][14]      = array(
		'name' 		    => __('TP-Link Archer C7 (AC)'),
		'vendor'        => __('TP-Link'),
	    'model'         => __('Archer C7'),	
		'id'    	    => 'tl_ac1750_c7',
		'radios'	    => 2, 
		'active'        => true,
		'device_type'   => 'ac', //Options are 'standard' (if left out) or 'ac' for AC devices, more options to follow
		'eth_br'	    => 'eth1',

		//First radio 
		'max_power'     => '30',
		'two'		    => false,
		'five'		    => true,
		'hwmode'	    => '11a',

		//Second radio - This is extra for two radio devices
		'max_power1'    => '25',
		'two1'		    => true,
		'five1'		    => false,
		'hwmode1'	    => '11n',

        'radio0_htmode'         => 'VHT80',
        'radio0_txpower'        => '30',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
   
        ),

        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '23',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][15]      = array(
    'name'          => __('TP-Link CPE210'),
    'vendor'        => __('TP-Link'),
	'model'         => __('CPE210'),
    'id'            => 'tl_cpe210' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '29',
    'eth_br'        => 'eth0 eth1',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '29',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][16]      = array(
    'name'          => __('TP-Link WR1043ND'),
    'vendor'        => __('TP-Link'),
	'model'         => __('WR1043ND'),
    'id'            => 'tl_wr1043' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '22',
    'eth_br'        => 'eth1',  //Important This guy is swapped around!
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '22',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][17]      = array(
		'name' 		=> __('PicoStation M2'),
	    'vendor'    => __('Ubiquiti'),
	    'model'     => __('PicoStation M2'),	
		'id'    	=> 'pico2',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '28',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '28',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )

);

$config['hardware'][18]      = array(
		'name' 		=> __('PicoStation M5'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('PicoStation M5'),		
		'id'    	=> 'pico5', 
		'radios'	=> 1,
		'active'    => true, 
		'max_power' => '28',
		'eth_br'	=> 'eth0',
		'two'		=> false,
		'five'		=> true,
		'hwmode'	=> '11a',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '28',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )

);

$config['hardware'][19]      = array(
		'name' 		=> __('NanoStation M2'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('NanoStation M2'),	
		'id'    	=> 'nano2',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '28',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '28',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][20]      = array(
		'name' 		=> __('NanoStation M5'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('NanoStation M5'),	
		'id'    	=> 'nano5',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '28',
		'eth_br'	=> 'eth0',
		'two'		=> false,
		'five'		=> true,
		'hwmode'	=> '11a',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '28',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )	
);

$config['hardware'][21]      = array(
		'name' 		=> __('UniFi AP'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('UniFi AP'),	
		'id'    	=> 'unifiap',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '23',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '23',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )	
);

$config['hardware'][22]      = array(
		'name' 		=> __('UniFi AP-LR'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('UniFi AP-LR'),		
		'id'    	=> 'unifilrap',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '27',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '27',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )	
);

$config['hardware'][23]      = array(
		'name' 		=> __('UniFi AP PRO (Dual Radio)'),
		'vendor'    => __('Ubiquiti'),
	    'model'     => __('UniFi AP PRO'),	
		'id'    	=> 'unifiappro',
		'radios'	=> 2, 
		'active'    => true,
		'eth_br'	=> 'eth0',

		//First radio 
		'max_power' => '17',
		'two'		=> false,
		'five'		=> true,
		'hwmode'	=> '11a',

		//Second radio - This is extra for two radio devices
		'max_power1'=> '30',
		'two1'		=> true,
		'five1'		=> false,
		'hwmode1'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '17',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        ),

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '30',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio1_disable_b'      => true,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )

);

$config['hardware'][24]      = array(
	'name'    		=> __('AirGateway'),
	'vendor'        => __('Ubiquiti'),
	'model'         => __('AirGateway'),       
    'id'          	=> 'airgw',
    'radios'      	=> 1,
    'active'    	=> true,
    'max_power' 	=> 18,
    'eth_br'      	=> 'eth0 eth1',
    'two'         	=> true,
    'five'        	=> false,
    'hwmode'      	=> '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '18',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
     
);

$config['hardware'][25]      = array(
	'name'     		=> __('AirRouter'), 
	'vendor'        => __('Ubiquiti'),
	'model'         => __('AirRouter'),         
    'id'          	=> 'airrouter' ,  
   	'radios'      	=> 1,
    'active'    	=> true,
 	'max_power' 	=> '19',
   	'eth_br'      	=> 'eth0',
   	'two'         	=> true,
   	'five'        	=> false,
  	'hwmode'      	=> '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '19',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )     
);

$config['hardware'][26]      = array(
	'name'          => __('AirRouterHP'),
	'vendor'        => __('Ubiquiti'),
	'model'         => __('AirRouterHP'),            
	'id'          	=> 'airrouterhp' ,  
	'radios'      	=> 1,
	'active'    	=> true,
	'max_power'   	=> '26',
	'eth_br'      	=> 'eth0',
	'two'         	=> true,
	'five'        	=> false,
	'hwmode'      	=> '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '26',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    ) 
);

$config['hardware'][27]      = array(
  	'name'          => __('BulletM2'),
  	'vendor'        => __('Ubiquiti'),
	'model'         => __('BulletM2'),           
  	'id'          	=> 'bulm2',
  	'radios'      	=> 1,
	'active'    	=> true,
  	'max_power' 	=> 28,
  	'eth_br'      	=> 'eth0',
  	'two'         	=> true,
  	'five'        	=> false,
  	'hwmode'      	=> '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '28',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);


$config['hardware'][28]      = array(
		'name' 		    => __('Wally DR344 (AC)'),
		'vendor'        => __('Wally'),
	    'model'         => __('DR344'),	
		'id'    	    => 'wally_dr344_ac',
		'radios'	    => 2, 
		'active'        => true,
		'device_type'   => 'ac', //Options are 'standard' (if left out) or 'ac' for AC devices, more options to follow
		'eth_br'	    => 'eth0',

		//First radio 
		'max_power'     => '30',
		'two'		    => false,
		'five'		    => true,
		'hwmode'	    => '11a',

		//Second radio - This is extra for two radio devices
		'max_power1'    => '30',
		'two1'		    => true,
		'five1'		    => false,
		'hwmode1'	    => '11n',

        'radio0_htmode'         => 'VHT80',
        'radio0_txpower'        => '30',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
   
        ),

        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '30',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][29]      = array(
		'name' 		    => __('Xiaomi MiWiFi Mini (AC)'),
		'vendor'        => __('Xiaom'),
	    'model'         => __('MiWiFi Mini'),	
		'id'    	    => 'miwifi_mini',
		'radios'	    => 2, 
		'active'        => true,
		'device_type'   => 'ac', //Options are 'standard' (if left out) or 'ac' for AC devices, more options to follow
		'eth_br'	    => 'eth0.1',

		//First radio 
		'max_power'     => '30',
		'two'		    => false,
		'five'		    => true,
		'hwmode'	    => '11a',

		//Second radio - This is extra for two radio devices
		'max_power1'    => '25',
		'two1'		    => true,
		'five1'		    => false,
		'hwmode1'	    => '11n',

        'radio0_htmode'         => 'VHT80',
        'radio0_txpower'        => '30',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
   
        ),

        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '30',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);


$config['hardware'][30]      = array(
		'name' 		    => __('Yuncore  XD3200 (AC)'),
		'vendor'        => __('Yuncore'),
	    'model'         => __('XD3200'),
		'id'    	    => 'yc_xd3200',
		'radios'	    => 2, 
		'active'        => true,
		'device_type'   => 'ac', //Options are 'standard' (if left out) or 'ac' for AC devices, more options to follow
		'eth_br'	    => 'eth0',

		//First radio 
		'max_power'     => '30',
		'two'		    => false,
		'five'		    => true,
		'hwmode'	    => '11a',

		//Second radio - This is extra for two radio devices
		'max_power1'    => '30',
		'two1'		    => true,
		'five1'		    => false,
		'hwmode1'	    => '11n',

        'radio0_htmode'         => 'VHT80',
        'radio0_txpower'        => '30',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_ht_capab'       => array(
   
        ),

        'radio1_htmode'         => 'HT20',
        'radio1_txpower'        => '30',
        'radio1_diversity'      => true,
        'radio1_distance'       => '300',
        'radio1_noscan'         => false,
        'radio1_ldpc'           => true,
        'radio1_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio1_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][31]      = array(
		'name' 		=> __('Yuncore AP90'),
		'vendor'    => __('Yuncore'),
	    'model'     => __('AP90'),	
		'id'    	=> 'yc_ap90q',
		'radios'	=> 1, 
		'active'    => true, 
		'max_power' => '28',
		'eth_br'	=> 'eth0',
		'two'		=> true,
		'five'		=> false,
		'hwmode'	=> '11g',

        //Default Advanced WiFi settings (Enchancement made 8/9/15)
        'radio0_htmode'         => 'HT20',
        'radio0_txpower'        => '28',
        'radio0_diversity'      => true,
        'radio0_distance'       => '300',
        'radio0_noscan'         => false,
        'radio0_ldpc'           => true,
        'radio0_beacon_int'     => 100,
        'radio0_disable_b'      => true,
        'radio0_ht_capab'       => array(
            'SHORT-GI-40',
            'RX-STBC1',
            'TX-STBC',
            'DSSS_CCK-40'
        )
);

$config['hardware'][32]      = array(
    'name'          => __('ZBT WE1526'),
    'vendor'        => __('ZBT-Link'),
	'model'         => __('WE1526'), 
    'id'            => 'zbt_we1526' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '20',
    'eth_br'        => 'eth0 eth1',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '22',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);

$config['hardware'][33]      = array(
    'name'          => __('ZBT WE2026'),
    'vendor'        => __('ZBT-Link'),
	'model'         => __('WE2026'),
    'id'            => 'zbt_we2026' ,
    'radios'        => 1,
    'active'        => true,
    'max_power'     => '20',
    'eth_br'        => 'eth0.1',
    'two'           => true,
    'five'          => false,
    'hwmode'        => '11g',

    //Default Advanced WiFi settings (Enchancement made 8/9/15)
    'radio0_htmode'         => 'HT20',
    'radio0_txpower'        => '20',
    'radio0_diversity'      => true,
    'radio0_distance'       => '300',
    'radio0_noscan'         => false,
    'radio0_ldpc'           => true,
    'radio0_beacon_int'     => 100,
    'radio0_disable_b'      => true,
    'radio0_ht_capab'       => array(
        'SHORT-GI-40',
        'RX-STBC1',
        'TX-STBC',
        'DSSS_CCK-40'
    )
);


//== MESHdesk SSID/BSSID
$config['MEHSdesk']['bssid'] = "02:CA:FE:CA:00:00"; //This will be the first one; subsequent ones will be incremented 

//== MESHdesk Defaul MAP settings ==
$config['mesh_specifics']['map']['type']     = "ROADMAP";
$config['mesh_specifics']['map']['zoom']     = 18;
$config['mesh_specifics']['map']['lng']      = -71.0955740216735;
$config['mesh_specifics']['map']['lat']      = 42.3379770178396;


//== OpenWrt timezones====

$config['MESHdesk']['timezones'] = array(
array('id'=>1,   'name' => 'Africa/Abidjan',       'value' => 'GMT0'),
array('id'=>2,   'name' => 'Africa/Accra', 	        'value' => 'GMT0'),
array('id'=>3,   'name' => 'Africa/Addis Ababa', 	'value' => 'EAT-3'),
array('id'=>4,   'name' => 'Africa/Algiers', 	    'value' => 'CET-1'),
array('id'=>5,   'name' => 'Africa/Asmara', 	    'value' => 'EAT-3'),
array('id'=>6,   'name' => 'Africa/Bamako', 	    'value' => 'GMT0'),
array('id'=>7,   'name' => 'Africa/Bangui', 	    'value' => 'WAT-1'),
array('id'=>8,   'name' => 'Africa/Banjul', 	    'value' => 'GMT0'),
array('id'=>9,   'name' => 'Africa/Bissau', 	    'value' => 'GMT0'),
array('id'=>10,  'name' => 'Africa/Blantyre', 	    'value' => 'CAT-2'),
array('id'=>12,  'name' => 'Africa/Bujumbura', 	    'value' => 'CAT-2'),
array('id'=>13,  'name' => 'Africa/Casablanca', 	'value' => 'WET0'),
array('id'=>14,  'name' => 'Africa/Ceuta', 	        'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>15,  'name' => 'Africa/Conakry', 	    'value' => 'GMT0'),
array('id'=>16,  'name' => 'Africa/Dakar', 	        'value' => 'GMT0'),
array('id'=>17,  'name' => 'Africa/Dar es Salaam',  'value' => 'EAT-3'),
array('id'=>18,  'name' => 'Africa/Djibouti', 	    'value' => 'EAT-3'),
array('id'=>19,  'name' => 'Africa/Douala', 	    'value' => 'WAT-1'),
array('id'=>20,  'name' => 'Africa/El Aaiun', 	    'value' => 'WET0'),
array('id'=>21,  'name' => 'Africa/Freetown', 	    'value' => 'GMT0'),
array('id'=>22,  'name' => 'Africa/Gaborone', 	    'value' => 'CAT-2'),
array('id'=>23,  'name' => 'Africa/Harare', 	    'value' => 'CAT-2'),
array('id'=>24,  'name' => 'Africa/Johannesburg', 	'value' => 'SAST-2'),
array('id'=>25,  'name' => 'Africa/Kampala', 	    'value' => 'EAT-3'),
array('id'=>26,  'name' => 'Africa/Khartoum', 	    'value' => 'EAT-3'),
array('id'=>27,  'name' => 'Africa/Kigali', 	    'value' => 'CAT-2'),
array('id'=>28,  'name' => 'Africa/Kinshasa', 	    'value' => 'WAT-1'),
array('id'=>29,  'name' => 'Africa/Lagos', 	        'value' => 'WAT-1'),
array('id'=>30,  'name' => 'Africa/Libreville', 	'value' => 'WAT-1'),
array('id'=>31,  'name' => 'Africa/Lome', 	        'value' => 'GMT0'),
array('id'=>32,  'name' => 'Africa/Luanda', 	    'value' => 'WAT-1'),
array('id'=>33,  'name' => 'Africa/Lubumbashi', 	'value' => 'CAT-2'),
array('id'=>34,  'name' => 'Africa/Lusaka', 	    'value' => 'CAT-2'),
array('id'=>35,  'name' => 'Africa/Malabo', 	    'value' => 'WAT-1'),
array('id'=>36,  'name' => 'Africa/Maputo', 	    'value' => 'CAT-2'),
array('id'=>37,  'name' => 'Africa/Maseru', 	    'value' => 'SAST-2'),
array('id'=>38,  'name' => 'Africa/Mbabane', 	    'value' => 'SAST-2'),
array('id'=>39,  'name' => 'Africa/Mogadishu', 	    'value' => 'EAT-3'),
array('id'=>40,  'name' => 'Africa/Monrovia', 	    'value' => 'GMT0'),
array('id'=>41,  'name' => 'Africa/Nairobi', 	    'value' => 'EAT-3'),
array('id'=>42,  'name' => 'Africa/Ndjamena', 	    'value' => 'WAT-1'),
array('id'=>43,  'name' => 'Africa/Niamey', 	    'value' => 'WAT-1'),
array('id'=>44,  'name' => 'Africa/Nouakchott', 	'value' => 'GMT0'),
array('id'=>45,  'name' => 'Africa/Ouagadougou', 	'value' => 'GMT0'),
array('id'=>46,  'name' => 'Africa/Porto-Novo', 	'value' => 'WAT-1'),
array('id'=>47,  'name' => 'Africa/Sao Tome', 	    'value' => 'GMT0'),
array('id'=>48,  'name' => 'Africa/Tripoli', 	    'value' => 'EET-2'),
array('id'=>49,  'name' => 'Africa/Tunis', 	        'value' => 'CET-1'),
array('id'=>50,  'name' => 'Africa/Windhoek', 	    'value' => 'WAT-1WAST,M9.1.0,M4.1.0'),
array('id'=>51,  'name' => 'America/Adak', 	                    'value' => 'HAST10HADT,M3.2.0,M11.1.0'),
array('id'=>52,  'name' => 'America/Anchorage', 	            'value' => 'AKST9AKDT,M3.2.0,M11.1.0'),
array('id'=>53,  'name' => 'America/Anguilla', 	                'value' => 'AST4'),
array('id'=>54,  'name' => 'America/Antigua', 	                'value' => 'AST4'),
array('id'=>55,  'name' => 'America/Araguaina', 	            'value' => 'BRT3'),
array('id'=>56,  'name' => 'America/Argentina/Buenos Aires', 	'value' => 'ART3'),
array('id'=>57,  'name' => 'America/Argentina/Catamarca', 	    'value' => 'ART3'),
array('id'=>58,  'name' => 'America/Argentina/Cordoba', 	    'value' => 'ART3'),
array('id'=>59,  'name' => 'America/Argentina/Jujuy', 	        'value' => 'ART3'),
array('id'=>60,  'name' => 'America/Argentina/La Rioja', 	    'value' => 'ART3'),
array('id'=>61,  'name' => 'America/Argentina/Mendoza', 	    'value' => 'ART3'),
array('id'=>62,  'name' => 'America/Argentina/Rio Gallegos', 	'value' => 'ART3'),
array('id'=>63,  'name' => 'America/Argentina/Salta', 	        'value' => 'ART3'),
array('id'=>64,  'name' => 'America/Argentina/San Juan', 	    'value' => 'ART3'),
array('id'=>65,  'name' => 'America/Argentina/Tucuman', 	    'value' => 'ART3'),
array('id'=>66,  'name' => 'America/Argentina/Ushuaia', 	    'value' => 'ART3'),
array('id'=>67,  'name' => 'America/Aruba', 	                'value' => 'AST4'),
array('id'=>68,  'name' => 'America/Asuncion', 	                'value' => 'PYT4PYST,M10.1.0/0,M4.2.0/0'),
array('id'=>69,  'name' => 'America/Atikokan', 	                'value' => 'EST5'),
array('id'=>70,  'name' => 'America/Bahia', 	                'value' => 'BRT3'),
array('id'=>71,  'name' => 'America/Barbados', 	                'value' => 'AST4'),
array('id'=>72,  'name' => 'America/Belem', 	                'value' => 'BRT3'),
array('id'=>73,  'name' => 'America/Belize', 	                'value' => 'CST6'),
array('id'=>74,  'name' => 'America/Blanc-Sablon', 	            'value' => 'AST4'),
array('id'=>75,  'name' => 'America/Boa Vista', 	            'value' => 'AMT4'),
array('id'=>76,  'name' => 'America/Bogota', 	                'value' => 'COT5'),
array('id'=>77,  'name' => 'America/Boise', 	                'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>78,  'name' => 'America/Cambridge Bay', 	        'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>79,  'name' => 'America/Campo Grande', 	            'value' => 'AMT4AMST,M10.3.0/0,M2.3.0/0'),
array('id'=>80,  'name' => 'America/Cancun', 	                'value' => 'CST6CDT,M4.1.0,M10.5.0'),
array('id'=>80,  'name' => 'America/Caracas', 	                'value' => 'VET4:30'),
array('id'=>81,  'name' => 'America/Cayenne', 	                'value' => 'GFT3'),
array('id'=>82,  'name' => 'America/Cayman', 	                'value' => 'EST5'),
array('id'=>83,  'name' => 'America/Chicago', 	                'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>84,  'name' => 'America/Chihuahua', 	            'value' => 'MST7MDT,M4.1.0,M10.5.0'),
array('id'=>85,  'name' => 'America/Costa Rica', 	            'value' => 'CST6'),
array('id'=>86,  'name' => 'America/Cuiaba', 	                'value' => 'AMT4AMST,M10.3.0/0,M2.3.0/0'),
array('id'=>87,  'name' => 'America/Curacao', 	                'value' => 'AST4'),
array('id'=>88,  'name' => 'America/Danmarkshavn', 	        'value' => 'GMT0'),
array('id'=>89,  'name' => 'America/Dawson', 	                'value' => 'PST8PDT,M3.2.0,M11.1.0'),
array('id'=>90,  'name' => 'America/Dawson Creek', 	        'value' => 'MST7'),
array('id'=>91,  'name' => 'America/Denver', 	                'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>92,  'name' => 'America/Detroit',                  'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>93,  'name' => 'America/Dominica',                 'value' => 'AST4'),
array('id'=>94,  'name' => 'America/Edmonton',                 'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>95,  'name' => 'America/Eirunepe',                 'value' => 'AMT4'),
array('id'=>96,  'name' => 'America/El Salvador',              'value' => 'CST6'),
array('id'=>97,  'name' => 'America/Fortaleza',                'value' => 'BRT3'),
array('id'=>98,  'name' => 'America/Glace Bay',                'value' => 'AST4ADT,M3.2.0,M11.1.0'),
array('id'=>99,  'name' => 'America/Goose Bay',                'value' => 'AST4ADT,M3.2.0/0:01,M11.1.0/0:01'),
array('id'=>100, 'name' => 'America/Grand Turk',               'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>101, 'name' => 'America/Grenada',                  'value' => 'AST4'),
array('id'=>102, 'name' => 'America/Guadeloupe',               'value' => 'AST4'),
array('id'=>103, 'name' => 'America/Guatemala',                'value' => 'CST6'),
array('id'=>104, 'name' => 'America/Guayaquil',                'value' => 'ECT5'),
array('id'=>105, 'name' => 'America/Guyana',                   'value' => 'GYT4'),
array('id'=>106, 'name' => 'America/Halifax',                  'value' => 'AST4ADT,M3.2.0,M11.1.0'),
array('id'=>107, 'name' => 'America/Havana',                   'value' => 'CST5CDT,M3.2.0/0,M10.5.0/1'),
array('id'=>108, 'name' => 'America/Hermosillo',               'value' => 'MST7'),
array('id'=>109, 'name' => 'America/Indiana/Indianapolis',     'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>110, 'name' => 'America/Indiana/Knox',             'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>111, 'name' => 'America/Indiana/Marengo',          'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>112, 'name' => 'America/Indiana/Petersburg',       'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>113, 'name' => 'America/Indiana/Tell City',        'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>114, 'name' => 'America/Indiana/Vevay',            'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>115, 'name' => 'America/Indiana/Vincennes',        'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>116, 'name' => 'America/Indiana/Winamac',          'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>117, 'name' => 'America/Inuvik',                   'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>118, 'name' => 'America/Iqaluit',                  'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>119, 'name' => 'America/Jamaica',                  'value' => 'EST5'),
array('id'=>120, 'name' => 'America/Juneau',                   'value' => 'AKST9AKDT,M3.2.0,M11.1.0'),
array('id'=>121, 'name' => 'America/Kentucky/Louisville',      'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>122, 'name' => 'America/Kentucky/Monticello',      'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>123, 'name' => 'America/La Paz',                   'value' => 'BOT4'),
array('id'=>124, 'name' => 'America/Lima',                     'value' => 'PET5'),
array('id'=>125, 'name' => 'America/Los Angeles',              'value' => 'PST8PDT,M3.2.0,M11.1.0'),
array('id'=>126, 'name' => 'America/Maceio',                   'value' => 'BRT3'),
array('id'=>127, 'name' => 'America/Managua',                  'value' => 'CST6'),
array('id'=>128, 'name' => 'America/Manaus',                   'value' => 'AMT4'),
array('id'=>129, 'name' => 'America/Marigot',                  'value' => 'AST4'),
array('id'=>130, 'name' => 'America/Martinique',               'value' => 'AST4'),
array('id'=>131, 'name' => 'America/Matamoros',                'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>132, 'name' => 'America/Mazatlan',                 'value' => 'MST7MDT,M4.1.0,M10.5.0'),
array('id'=>133, 'name' => 'America/Menominee',                'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>134, 'name' => 'America/Merida',                   'value' => 'CST6CDT,M4.1.0,M10.5.0'),
array('id'=>135, 'name' => 'America/Mexico City',              'value' => 'CST6CDT,M4.1.0,M10.5.0'),
array('id'=>136, 'name' => 'America/Miquelon',                 'value' => 'PMST3PMDT,M3.2.0,M11.1.0'),
array('id'=>137, 'name' => 'America/Moncton',                  'value' => 'AST4ADT,M3.2.0,M11.1.0'),
array('id'=>138, 'name' => 'America/Monterrey',                'value' => 'CST6CDT,M4.1.0,M10.5.0'),
array('id'=>139, 'name' => 'America/Montevideo',               'value' => 'UYT3UYST,M10.1.0,M3.2.0'),
array('id'=>140, 'name' => 'America/Montreal',                 'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>141, 'name' => 'America/Montserrat',               'value' => 'AST4'),
array('id'=>142, 'name' => 'America/Nassau',                   'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>143, 'name' => 'America/New York',                 'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>144, 'name' => 'America/Nipigon',                  'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>145, 'name' => 'America/Nome',                     'value' => 'AKST9AKDT,M3.2.0,M11.1.0'),
array('id'=>146, 'name' => 'America/Noronha',                  'value' => 'FNT2'),
array('id'=>147, 'name' => 'America/North Dakota/Center',      'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>148, 'name' => 'America/North Dakota/New Salem ',  'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>149, 'name' => 'America/Ojinaga',                  'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>150, 'name' => 'America/Panama',                   'value' => 'EST5'),
array('id'=>151, 'name' => 'America/Pangnirtung',              'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>152, 'name' => 'America/Paramaribo',               'value' => 'SRT3'),
array('id'=>153, 'name' => 'America/Phoenix',                  'value' => 'MST7'),
array('id'=>154, 'name' => 'America/Port of Spain',            'value' => 'AST4'),
array('id'=>155, 'name' => 'America/Port-au-Prince',           'value' => 'EST5'),
array('id'=>156, 'name' => 'America/Porto Velho',              'value' => 'AMT4'),
array('id'=>157, 'name' => 'America/Puerto Rico',              'value' => 'AST4'),
array('id'=>158, 'name' => 'America/Rainy River',              'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>159, 'name' => 'America/Rankin Inlet',             'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>160, 'name' => 'America/Recife',                   'value' => 'BRT3'),
array('id'=>161, 'name' => 'America/Regina',                   'value' => 'CST6'),
array('id'=>162, 'name' => 'America/Rio Branco',               'value' => 'AMT4'),
array('id'=>163, 'name' => 'America/Santa Isabel',             'value' => 'PST8PDT,M4.1.0,M10.5.0'),
array('id'=>164, 'name' => 'America/Santarem',                 'value' => 'BRT3'),
array('id'=>165, 'name' => 'America/Santo Domingo',            'value' => 'AST4'),
array('id'=>166, 'name' => 'America/Sao Paulo',                'value' => 'BRT3BRST,M10.3.0/0,M2.3.0/0'),
array('id'=>167, 'name' => 'America/Scoresbysund',             'value' => 'EGT1EGST,M3.5.0/0,M10.5.0/1'),
array('id'=>168, 'name' => 'America/Shiprock',                 'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>169, 'name' => 'America/St Barthelemy',            'value' => 'AST4'),
array('id'=>170, 'name' => 'America/St Johns',                 'value' => 'NST3:30NDT,M3.2.0/0:01,M11.1.0/0:01'),
array('id'=>171, 'name' => 'America/St Kitts',                 'value' => 'AST4'),
array('id'=>172, 'name' => 'America/St Lucia',                 'value' => 'AST4'),
array('id'=>173, 'name' => 'America/St Thomas',                'value' => 'AST4'),
array('id'=>174, 'name' => 'America/St Vincent',               'value' => 'AST4'),
array('id'=>175, 'name' => 'America/Swift Current',            'value' => 'CST6'),
array('id'=>176, 'name' => 'America/Tegucigalpa',              'value' => 'CST6'),
array('id'=>177, 'name' => 'America/Thule',                    'value' => 'AST4ADT,M3.2.0,M11.1.0'),
array('id'=>178, 'name' => 'America/Thunder Bay',              'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>179, 'name' => 'America/Tijuana',                  'value' => 'PST8PDT,M3.2.0,M11.1.0'),
array('id'=>180, 'name' => 'America/Toronto',                  'value' => 'EST5EDT,M3.2.0,M11.1.0'),
array('id'=>181, 'name' => 'America/Tortola',                  'value' => 'AST4'),
array('id'=>182, 'name' => 'America/Vancouver',                'value' => 'PST8PDT,M3.2.0,M11.1.0'),
array('id'=>183, 'name' => 'America/Whitehorse',               'value' => 'PST8PDT,M3.2.0,M11.1.0'),
array('id'=>184, 'name' => 'America/Winnipeg',                 'value' => 'CST6CDT,M3.2.0,M11.1.0'),
array('id'=>185, 'name' => 'America/Yakutat',                  'value' => 'AKST9AKDT,M3.2.0,M11.1.0'),
array('id'=>186, 'name' => 'America/Yellowknife',              'value' => 'MST7MDT,M3.2.0,M11.1.0'),
array('id'=>187, 'name' => 'Antarctica/Casey',                 'value' => 'WST-8'),
array('id'=>188, 'name' => 'Antarctica/Davis',                 'value' => 'DAVT-7'),
array('id'=>189, 'name' => 'Antarctica/DumontDUrville',        'value' => 'DDUT-10'),
array('id'=>190, 'name' => 'Antarctica/Macquarie',             'value' => 'MIST-11'),
array('id'=>191, 'name' => 'Antarctica/Mawson',                'value' => 'MAWT-5'),
array('id'=>192, 'name' => 'Antarctica/McMurdo',               'value' => 'NZST-12NZDT,M9.5.0,M4.1.0/3'),
array('id'=>193, 'name' => 'Antarctica/Rothera',               'value' => 'ROTT3'),
array('id'=>194, 'name' => 'Antarctica/South Pole',            'value' => 'NZST-12NZDT,M9.5.0,M4.1.0/3'),
array('id'=>195, 'name' => 'Antarctica/Syowa',                 'value' => 'SYOT-3'),
array('id'=>196, 'name' => 'Antarctica/Vostok',                'value' => 'VOST-6'),
array('id'=>197, 'name' => 'Arctic/Longyearbyen',              'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>198, 'name' => 'Asia/Aden',                         'value' => 'AST-3'),
array('id'=>199, 'name' => 'Asia/Almaty',                       'value' => 'ALMT-6'),
array('id'=>200, 'name' => 'Asia/Anadyr',                       'value' => 'ANAT-11ANAST,M3.5.0,M10.5.0/3'),
array('id'=>201, 'name' => 'Asia/Aqtau',                        'value' => 'AQTT-5'),
array('id'=>202, 'name' => 'Asia/Aqtobe',                       'value' => 'AQTT-5'),
array('id'=>203, 'name' => 'Asia/Ashgabat',                     'value' => 'TMT-5'),
array('id'=>204, 'name' => 'Asia/Baghdad',                      'value' => 'AST-3'),
array('id'=>205, 'name' => 'Asia/Bahrain',                      'value' => 'AST-3'),
array('id'=>206, 'name' => 'Asia/Baku',                         'value' => 'AZT-4AZST,M3.5.0/4,M10.5.0/5'),
array('id'=>207, 'name' => 'Asia/Bangkok',                      'value' => 'ICT-7'),
array('id'=>208, 'name' => 'Asia/Beirut',                       'value' => 'EET-2EEST,M3.5.0/0,M10.5.0/0'),
array('id'=>209, 'name' => 'Asia/Bishkek',                      'value' => 'KGT-6'),
array('id'=>210, 'name' => 'Asia/Brunei',                       'value' => 'BNT-8'),
array('id'=>211, 'name' => 'Asia/Choibalsan',                   'value' => 'CHOT-8'),
array('id'=>212, 'name' => 'Asia/Chongqing',                    'value' => 'CST-8'),
array('id'=>213, 'name' => 'Asia/Colombo',                      'value' => 'IST-5:30'),
array('id'=>214, 'name' => 'Asia/Damascus',                     'value' => 'EET-2EEST,M4.1.5/0,M10.5.5/0'),
array('id'=>215, 'name' => 'Asia/Dhaka',                        'value' => 'BDT-6'),
array('id'=>216, 'name' => 'Asia/Dili',                         'value' => 'TLT-9'),
array('id'=>217, 'name' => 'Asia/Dubai',                        'value' => 'GST-4'),
array('id'=>218, 'name' => 'Asia/Dushanbe',                     'value' => 'TJT-5'),
array('id'=>219, 'name' => 'Asia/Gaza',                         'value' => 'EET-2EEST,M3.5.6/0:01,M9.1.5'),
array('id'=>220, 'name' => 'Asia/Harbin ',                      'value' => 'CST-8'),
array('id'=>221, 'name' => 'Asia/Ho Chi Minh',                  'value' => 'ICT-7'),
array('id'=>222, 'name' => 'Asia/Hong Kong',                    'value' => 'HKT-8'),
array('id'=>223, 'name' => 'Asia/Hovd',                         'value' => 'HOVT-7'),
array('id'=>224, 'name' => 'Asia/Irkutsk',                      'value' => 'IRKT-8IRKST,M3.5.0,M10.5.0/3'),
array('id'=>225, 'name' => 'Asia/Jakarta',                      'value' => 'WIT-7'),
array('id'=>226, 'name' => 'Asia/Jayapura',                     'value' => 'EIT-9'),
array('id'=>227, 'name' => 'Asia/Kabul',                        'value' => 'AFT-4:30'),
array('id'=>228, 'name' => 'Asia/Kamchatka',                    'value' => 'PETT-11PETST,M3.5.0,M10.5.0/3'),
array('id'=>229, 'name' => 'Asia/Karachi',                      'value' => 'PKT-5'),
array('id'=>230, 'name' => 'Asia/Kashgar',                      'value' => 'CST-8'),
array('id'=>231, 'name' => 'Asia/Kathmandu',                    'value' => 'NPT-5:45'),
array('id'=>232, 'name' => 'Asia/Kolkata',                      'value' => 'IST-5:30'),
array('id'=>233, 'name' => 'Asia/Krasnoyarsk',                  'value' => 'KRAT-7KRAST,M3.5.0,M10.5.0/3'),
array('id'=>234, 'name' => 'Asia/Kuala Lumpur',                 'value' => 'MYT-8'),
array('id'=>235, 'name' => 'Asia/Kuching',                      'value' => 'MYT-8'),
array('id'=>236, 'name' => 'Asia/Kuwait',                       'value' => 'AST-3'),
array('id'=>237, 'name' => 'Asia/Macau',                        'value' => 'CST-8'),
array('id'=>238, 'name' => 'Asia/Magadan',                      'value' => 'MAGT-11MAGST,M3.5.0,M10.5.0/3'),
array('id'=>239, 'name' => 'Asia/Makassar',                     'value' => 'CIT-8'),
array('id'=>240, 'name' => 'Asia/Manila',                       'value' => 'PHT-8'),
array('id'=>241, 'name' => 'Asia/Muscat',                       'value' => 'GST-4'),
array('id'=>242, 'name' => 'Asia/Nicosia',                      'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>243, 'name' => 'Asia/Novokuznetsk',                 'value' => 'NOVT-6NOVST,M3.5.0,M10.5.0/3'),
array('id'=>244, 'name' => 'Asia/Novosibirsk',                  'value' => 'NOVT-6NOVST,M3.5.0,M10.5.0/3'),
array('id'=>245, 'name' => 'Asia/Omsk',                         'value' => 'OMST-7'),
array('id'=>246, 'name' => 'Asia/Oral',                         'value' => 'ORAT-5'),
array('id'=>247, 'name' => 'Asia/Phnom Penh',                   'value' => 'ICT-7'),
array('id'=>248, 'name' => 'Asia/Pontianak',                    'value' => 'WIT-7'),
array('id'=>249, 'name' => 'Asia/Pyongyang',                    'value' => 'KST-9'),
array('id'=>250, 'name' => 'Asia/Qatar',                        'value' => 'AST-3'),
array('id'=>251, 'name' => 'Asia/Qyzylorda',                    'value' => 'QYZT-6'),
array('id'=>252, 'name' => 'Asia/Rangoon',                      'value' => 'MMT-6:30'),
array('id'=>253, 'name' => 'Asia/Riyadh',                 	 'value' => 'AST-3'),
array('id'=>254, 'name' => 'Asia/Sakhalin',                  	 'value' => 'SAKT-10SAKST,M3.5.0,M10.5.0/3'),
array('id'=>255, 'name' => 'Asia/Samarkand',                    'value' => 'UZT-5'),
array('id'=>256, 'name' => 'Asia/Seoul',                        'value' => 'KST-9'),
array('id'=>257, 'name' => 'Asia/Shanghai',                  	 'value' => 'CST-8'),
array('id'=>258, 'name' => 'Asia/Singapore',                    'value' => 'SGT-8'),
array('id'=>259, 'name' => 'Asia/Taipei',                   	 'value' => 'CST-8'),
array('id'=>260, 'name' => 'Asia/Tashkent',                     'value' => 'UZT-5'),
array('id'=>261, 'name' => 'Asia/Tbilisi', 				 'value' => 'GET-4'),
array('id'=>262, 'name' => 'Asia/Tehran',  				 'value' => 'IRST-3:30IRDT,80/0,264/0'),
array('id'=>263, 'name' => 'Asia/Thimphu', 				 'value' => 'BTT-6'),
array('id'=>264, 'name' => 'Asia/Tokyo',				 'value' => 'JST-9'),
array('id'=>265, 'name' => 'Asia/Ulaanbaatar ',			 'value' => 'ULAT-8'),
array('id'=>266, 'name' => 'Asia/Urumqi', 				 'value' => 'CST-8'),
array('id'=>267, 'name' => 'Asia/Vientiane', 			 'value' => 'ICT-7'),
array('id'=>268, 'name' => 'Asia/Vladivostok',  			 'value' => 'VLAT-10VLAST,M3.5.0,M10.5.0/3'),
array('id'=>269, 'name' => 'Asia/Yakutsk', 				 'value' => 'YAKT-9YAKST,M3.5.0,M10.5.0/3'),
array('id'=>270, 'name' => 'Asia/Yekaterinburg',			 'value' => 'YEKT-5YEKST,M3.5.0,M10.5.0/3'),
array('id'=>271, 'name' => 'Asia/Yerevan', 				 'value' => 'AMT-4AMST,M3.5.0,M10.5.0/3'),
array('id'=>272, 'name' => 'Atlantic/Azores',			 'value' => 'AZOT1AZOST,M3.5.0/0,M10.5.0/1'),
array('id'=>273, 'name' => 'Atlantic/Bermuda', 			 'value' => 'AST4ADT,M3.2.0,M11.1.0'),
array('id'=>274, 'name' => 'Atlantic/Canary', 			 'value' => 'WET0WEST,M3.5.0/1,M10.5.0'),
array('id'=>275, 'name' => 'Atlantic/Cape Verde',			 'value' => 'CVT1'),
array('id'=>276, 'name' => 'Atlantic/Faroe',			 'value' => 'WET0WEST,M3.5.0/1,M10.5.0'),
array('id'=>277, 'name' => 'Atlantic/Madeira', 			 'value' => 'WET0WEST,M3.5.0/1,M10.5.0'),
array('id'=>278, 'name' => 'Atlantic/Reykjavik', 			 'value' => 'GMT0'),
array('id'=>279, 'name' => 'Atlantic/South Georgia', 		 'value' => 'GST2'),
array('id'=>280, 'name' => 'Atlantic/St Helena', 			 'value' => 'GMT0'),
array('id'=>281, 'name' => 'Atlantic/Stanley', 			 'value' => 'FKT4FKST,M9.1.0,M4.3.0'),
array('id'=>282, 'name' => 'Australia/Adelaide', 			 'value' => 'CST-9:30CST,M10.1.0,M4.1.0/3'),
array('id'=>283, 'name' => 'Australia/Brisbane',			 'value' => 'EST-10'),
array('id'=>284, 'name' => 'Australia/Broken Hill', 		 'value' => 'CST-9:30CST,M10.1.0,M4.1.0/3'),
array('id'=>285, 'name' => 'Australia/Currie', 			 'value' => 'EST-10EST,M10.1.0,M4.1.0/3'),
array('id'=>286, 'name' => 'Australia/Darwin', 			 'value' => 'CST-9:30'),
array('id'=>287, 'name' => 'Australia/Eucla', 			 'value' => 'CWST-8:45'),
array('id'=>288, 'name' => 'Australia/Hobart', 			 'value' => 'EST-10EST,M10.1.0,M4.1.0/3'),
array('id'=>289, 'name' => 'Australia/Lindeman', 			 'value' => 'EST-10'),
array('id'=>290, 'name' => 'Australia/Lord Howe', 			 'value' => 'LHST-10:30LHST-11,M10.1.0,M4.1.0'),
array('id'=>291, 'name' => 'Australia/Melbourne', 			 'value' => 'EST-10EST,M10.1.0,M4.1.0/3'),
array('id'=>292, 'name' => 'Australia/Perth', 			 'value' => 'WST-8'),
array('id'=>293, 'name' => 'Australia/Sydney', 			 'value' => 'EST-10EST,M10.1.0,M4.1.0/3'),
array('id'=>294, 'name' => 'Europe/Amsterdam',			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>295, 'name' => 'Europe/Andorra', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>296, 'name' => 'Europe/Athens', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>297, 'name' => 'Europe/Belgrade', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>298, 'name' => 'Europe/Berlin', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>299, 'name' => 'Europe/Bratislava', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>300, 'name' => 'Europe/Brussels', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>301, 'name' => 'Europe/Bucharest', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>302, 'name' => 'Europe/Budapest', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>303, 'name' => 'Europe/Chisinau', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>304, 'name' => 'Europe/Copenhagen', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>305, 'name' => 'Europe/Dublin', 			 'value' => 'GMT0IST,M3.5.0/1,M10.5.0'),
array('id'=>306, 'name' => 'Europe/Gibraltar', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>307, 'name' => 'Europe/Guernsey', 			 'value' => 'GMT0BST,M3.5.0/1,M10.5.0'),
array('id'=>308, 'name' => 'Europe/Helsinki', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>309, 'name' => 'Europe/Isle of Man', 			 'value' => 'GMT0BST,M3.5.0/1,M10.5.0'),
array('id'=>310, 'name' => 'Europe/Istanbul', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>311, 'name' => 'Europe/Jersey', 			 'value' => 'GMT0BST,M3.5.0/1,M10.5.0'),
array('id'=>312, 'name' => 'Europe/Kaliningrad', 			 'value' => 'EET-2EEST,M3.5.0,M10.5.0/3'),
array('id'=>313, 'name' => 'Europe/Kiev', 				 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>314, 'name' => 'Europe/Lisbon', 			 'value' => 'WET0WEST,M3.5.0/1,M10.5.0'),
array('id'=>315, 'name' => 'Europe/Ljubljana', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>316, 'name' => 'Europe/London', 			 'value' => 'GMT0BST,M3.5.0/1,M10.5.0'),
array('id'=>317, 'name' => 'Europe/Luxembourg', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>318, 'name' => 'Europe/Madrid', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>319, 'name' => 'Europe/Malta', 				 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>320, 'name' => 'Europe/Mariehamn ',			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>321, 'name' => 'Europe/Minsk', 				 'value' => 'EET-2EEST,M3.5.0,M10.5.0/3'),
array('id'=>322, 'name' => 'Europe/Monaco', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>323, 'name' => 'Europe/Moscow', 			 'value' => 'MSK-4'),
array('id'=>324, 'name' => 'Europe/Oslo', 				 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>325, 'name' => 'Europe/Paris', 				 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>326, 'name' => 'Europe/Podgorica', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>327, 'name' => 'Europe/Prague', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>328, 'name' => 'Europe/Riga', 				 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>329, 'name' => 'Europe/Rome',				 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>330, 'name' => 'Europe/Samara', 			 'value' => 'SAMT-3SAMST,M3.5.0,M10.5.0/3'),
array('id'=>331, 'name' => 'Europe/San Marino', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>332, 'name' => 'Europe/Sarajevo', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>333, 'name' => 'Europe/Simferopol', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>334, 'name' => 'Europe/Skopje', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>335, 'name' => 'Europe/Sofia', 				 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>336, 'name' => 'Europe/Stockholm', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>337, 'name' => 'Europe/Tallinn', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>338, 'name' => 'Europe/Tirane', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>339, 'name' => 'Europe/Uzhgorod', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>340, 'name' => 'Europe/Vaduz', 				 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>341, 'name' => 'Europe/Vatican', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>342, 'name' => 'Europe/Vienna', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>343, 'name' => 'Europe/Vilnius', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>344, 'name' => 'Europe/Volgograd', 			 'value' => 'VOLT-3VOLST,M3.5.0,M10.5.0/3'),
array('id'=>345, 'name' => 'Europe/Warsaw', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>346, 'name' => 'Europe/Zagreb', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>347, 'name' => 'Europe/Zaporozhye', 			 'value' => 'EET-2EEST,M3.5.0/3,M10.5.0/4'),
array('id'=>348, 'name' => 'Europe/Zurich', 			 'value' => 'CET-1CEST,M3.5.0,M10.5.0/3'),
array('id'=>349, 'name' => 'Indian/Antananarivo', 			 'value' => 'EAT-3'),
array('id'=>350, 'name' => 'Indian/Chagos', 			 'value' => 'IOT-6'),
array('id'=>351, 'name' => 'Indian/Christmas', 			 'value' => 'CXT-7'),
array('id'=>352, 'name' => 'Indian/Cocos', 				 'value' => 'CCT-6:30'),
array('id'=>353, 'name' => 'Indian/Comoro', 			 'value' => 'EAT-3'),
array('id'=>354, 'name' => 'Indian/Kerguelen', 			 'value' => 'TFT-5'),
array('id'=>355, 'name' => 'Indian/Mahe', 				 'value' => 'SCT-4'),
array('id'=>356, 'name' => 'Indian/Maldives', 			 'value' => 'MVT-5'),
array('id'=>357, 'name' => 'Indian/Mauritius', 			 'value' => 'MUT-4'),
array('id'=>358, 'name' => 'Indian/Mayotte', 			 'value' => 'EAT-3'),
array('id'=>359, 'name' => 'Indian/Reunion', 			 'value' => 'RET-4'),
array('id'=>360, 'name' => 'Pacific/Apia', 				 'value' => 'WST11'),
array('id'=>361, 'name' => 'Pacific/Auckland',			 'value' => 'NZST-12NZDT,M9.5.0,M4.1.0/3'),
array('id'=>362, 'name' => 'Pacific/Chatham', 			 'value' => 'CHAST-12:45CHADT,M9.5.0/2:45,M4.1.0/3:45'),
array('id'=>363, 'name' => 'Pacific/Efate', 			 'value' => 'VUT-11'),
array('id'=>364, 'name' => 'Pacific/Enderbury', 			 'value' => 'PHOT-13'),
array('id'=>365, 'name' => 'Pacific/Fakaofo', 			 'value' => 'TKT10'),
array('id'=>366, 'name' => 'Pacific/Fiji', 				 'value' => 'FJT-12'),
array('id'=>367, 'name' => 'Pacific/Funafuti', 			 'value' => 'TVT-12'),
array('id'=>368, 'name' => 'Pacific/Galapagos', 			 'value' => 'GALT6'),
array('id'=>369, 'name' => 'Pacific/Gambier', 			 'value' => 'GAMT9'),
array('id'=>370, 'name' => 'Pacific/Guadalcanal', 			 'value' => 'SBT-11'),
array('id'=>371, 'name' => 'Pacific/Guam', 				 'value' => 'ChST-10'),
array('id'=>372, 'name' => 'Pacific/Honolulu ',			 'value' => 'HST10'),
array('id'=>373, 'name' => 'Pacific/Johnston', 			 'value' => 'HST10'),
array('id'=>374, 'name' => 'Pacific/Kiritimati', 			 'value' => 'LINT-14'),
array('id'=>375, 'name' => 'Pacific/Kosrae', 			 'value' => 'KOST-11'),
array('id'=>376, 'name' => 'Pacific/Kwajalein', 			 'value' => 'MHT-12'),
array('id'=>377, 'name' => 'Pacific/Majuro', 			 'value' => 'MHT-12'),
array('id'=>378, 'name' => 'Pacific/Marquesas',			 'value' => 'MART9:30'),
array('id'=>379, 'name' => 'Pacific/Midway', 			 'value' => 'SST11'),
array('id'=>380, 'name' => 'Pacific/Nauru', 			 'value' => 'NRT-12'),
array('id'=>381, 'name' => 'Pacific/Niue', 				 'value' => 'NUT11'),
array('id'=>382, 'name' => 'Pacific/Norfolk', 			 'value' => 'NFT-11:30'),
array('id'=>383, 'name' => 'Pacific/Noumea', 			 'value' => 'NCT-11'),
array('id'=>384, 'name' => 'Pacific/Pago Pago', 			 'value' => 'SST11'),
array('id'=>385, 'name' => 'Pacific/Palau', 			 'value' => 'PWT-9'),
array('id'=>386, 'name' => 'Pacific/Pitcairn', 			 'value' => 'PST8'),
array('id'=>387, 'name' => 'Pacific/Ponape', 			 'value' => 'PONT-11'),
array('id'=>388, 'name' => 'Pacific/Port Moresby', 		 'value' => 'PGT-10'),
array('id'=>389, 'name' => 'Pacific/Rarotonga', 			 'value' => 'CKT10'),
array('id'=>390, 'name' => 'Pacific/Saipan', 			 'value' => 'ChST-10'),
array('id'=>391, 'name' => 'Pacific/Tahiti', 			 'value' => 'TAHT10'),
array('id'=>392, 'name' => 'Pacific/Tarawa', 			 'value' => 'GILT-12'),
array('id'=>393, 'name' => 'Pacific/Tongatapu', 			 'value' => 'TOT-13'),
array('id'=>394, 'name' => 'Pacific/Truk', 				 'value' => 'TRUT-10'),
array('id'=>395, 'name' => 'Pacific/Wake', 				 'value' => 'WAKT-12'),
array('id'=>396, 'name' => 'Pacific/Wallis', 			 'value' => 'WFT-12'),


);

$config['MESHdesk']['countries'] = array(
	array( 'id' => 'AF', 'name' => 'Afghanistan'),
	array( 'id' => 'AX', 'name' => 'Aland Islands'),
	array( 'id' => 'AL', 'name' => 'Albania'),
	array( 'id' => 'DZ', 'name' => 'Algeria'),
	array( 'id' => 'AS', 'name' => 'American Samoa'),
	array( 'id' => 'AD', 'name' => 'Andorra'),
	array( 'id' => 'AO', 'name' => 'Angola'),
	array( 'id' => 'AI', 'name' => 'Anguilla'),
	array( 'id' => 'AQ', 'name' => 'Antarctica'),
	array( 'id' => 'AG', 'name' => 'Antigua And Barbuda'),
	array( 'id' => 'AR', 'name' => 'Argentina'),
	array( 'id' => 'AM', 'name' => 'Armenia'),
	array( 'id' => 'AW', 'name' => 'Aruba'),
	array( 'id' => 'AU', 'name' => 'Australia'),
	array( 'id' => 'AT', 'name' => 'Austria'),
	array( 'id' => 'AZ', 'name' => 'Azerbaijan'),
	array( 'id' => 'BS', 'name' => 'Bahamas'),
	array( 'id' => 'BH', 'name' => 'Bahrain'),
	array( 'id' => 'BD', 'name' => 'Bangladesh'),
	array( 'id' => 'BB', 'name' => 'Barbados'),
	array( 'id' => 'BY', 'name' => 'Belarus'),
	array( 'id' => 'BE', 'name' => 'Belgium'),
	array( 'id' => 'BZ', 'name' => 'Belize'),
	array( 'id' => 'BJ', 'name' => 'Benin'),
	array( 'id' => 'BM', 'name' => 'Bermuda'),
	array( 'id' => 'BT', 'name' => 'Bhutan'),
	array( 'id' => 'BO', 'name' => 'Bolivia'),
	array( 'id' => 'BA', 'name' => 'Bosnia And Herzegovina'),
	array( 'id' => 'BW', 'name' => 'Botswana'),
	array( 'id' => 'BV', 'name' => 'Bouvet Island'),
	array( 'id' => 'BR', 'name' => 'Brazil'),
	array( 'id' => 'IO', 'name' => 'British Indian Ocean Territory'),
	array( 'id' => 'BN', 'name' => 'Brunei Darussalam'),
	array( 'id' => 'BG', 'name' => 'Bulgaria'),
	array( 'id' => 'BF', 'name' => 'Burkina Faso'),
	array( 'id' => 'BI', 'name' => 'Burundi'),
	array( 'id' => 'KH', 'name' => 'Cambodia'),
	array( 'id' => 'CM', 'name' => 'Cameroon'),
	array( 'id' => 'CA', 'name' => 'Canada'),
	array( 'id' => 'CV', 'name' => 'Cape Verde'),
	array( 'id' => 'KY', 'name' => 'Cayman Islands'),
	array( 'id' => 'CF', 'name' => 'Central African Republic'),
	array( 'id' => 'TD', 'name' => 'Chad'),
	array( 'id' => 'CL', 'name' => 'Chile'),
	array( 'id' => 'CN', 'name' => 'China'),
	array( 'id' => 'CX', 'name' => 'Christmas Island'),
	array( 'id' => 'CC', 'name' => 'Cocos (Keeling) Islands'),
	array( 'id' => 'CO', 'name' => 'Colombia'),
	array( 'id' => 'KM', 'name' => 'Comoros'),
	array( 'id' => 'CG', 'name' => 'Congo'),
	array( 'id' => 'CD', 'name' => 'Congo, Democratic Republic'),
	array( 'id' => 'CK', 'name' => 'Cook Islands'),
	array( 'id' => 'CR', 'name' => 'Costa Rica'),
	array( 'id' => 'CI', 'name' => 'Cote D\'Ivoire'),
	array( 'id' => 'HR', 'name' => 'Croatia'),
	array( 'id' => 'CU', 'name' => 'Cuba'),
	array( 'id' => 'CY', 'name' => 'Cyprus'),
	array( 'id' => 'CZ', 'name' => 'Czech Republic'),
	array( 'id' => 'DK', 'name' => 'Denmark'),
	array( 'id' => 'DJ', 'name' => 'Djibouti'),
	array( 'id' => 'DM', 'name' => 'Dominica'),
	array( 'id' => 'DO', 'name' => 'Dominican Republic'),
	array( 'id' => 'EC', 'name' => 'Ecuador'),
	array( 'id' => 'EG', 'name' => 'Egypt'),
	array( 'id' => 'SV', 'name' => 'El Salvador'),
	array( 'id' => 'GQ', 'name' => 'Equatorial Guinea'),
	array( 'id' => 'ER', 'name' => 'Eritrea'),
	array( 'id' => 'EE', 'name' => 'Estonia'),
	array( 'id' => 'ET', 'name' => 'Ethiopia'),
	array( 'id' => 'FK', 'name' => 'Falkland Islands (Malvinas)'),
	array( 'id' => 'FO', 'name' => 'Faroe Islands'),
	array( 'id' => 'FJ', 'name' => 'Fiji'),
	array( 'id' => 'FI', 'name' => 'Finland'),
	array( 'id' => 'FR', 'name' => 'France'),
	array( 'id' => 'GF', 'name' => 'French Guiana'),
	array( 'id' => 'PF', 'name' => 'French Polynesia'),
	array( 'id' => 'TF', 'name' => 'French Southern Territories'),
	array( 'id' => 'GA', 'name' => 'Gabon'),
	array( 'id' => 'GM', 'name' => 'Gambia'),
	array( 'id' => 'GE', 'name' => 'Georgia'),
	array( 'id' => 'DE', 'name' => 'Germany'),
	array( 'id' => 'GH', 'name' => 'Ghana'),
	array( 'id' => 'GI', 'name' => 'Gibraltar'),
	array( 'id' => 'GR', 'name' => 'Greece'),
	array( 'id' => 'GL', 'name' => 'Greenland'),
	array( 'id' => 'GD', 'name' => 'Grenada'),
	array( 'id' => 'GP', 'name' => 'Guadeloupe'),
	array( 'id' => 'GU', 'name' => 'Guam'),
	array( 'id' => 'GT', 'name' => 'Guatemala'),
	array( 'id' => 'GG', 'name' => 'Guernsey'),
	array( 'id' => 'GN', 'name' => 'Guinea'),
	array( 'id' => 'GW', 'name' => 'Guinea-Bissau'),
	array( 'id' => 'GY', 'name' => 'Guyana'),
	array( 'id' => 'HT', 'name' => 'Haiti'),
	array( 'id' => 'HM', 'name' => 'Heard Island & Mcdonald Islands'),
	array( 'id' => 'VA', 'name' => 'Holy See (Vatican City State)'),
	array( 'id' => 'HN', 'name' => 'Honduras'),
	array( 'id' => 'HK', 'name' => 'Hong Kong'),
	array( 'id' => 'HU', 'name' => 'Hungary'),
	array( 'id' => 'IS', 'name' => 'Iceland'),
	array( 'id' => 'IN', 'name' => 'India'),
	array( 'id' => 'ID', 'name' => 'Indonesia'),
	array( 'id' => 'IR', 'name' => 'Iran, Islamic Republic Of'),
	array( 'id' => 'IQ', 'name' => 'Iraq'),
	array( 'id' => 'IE', 'name' => 'Ireland'),
	array( 'id' => 'IM', 'name' => 'Isle Of Man'),
	array( 'id' => 'IL', 'name' => 'Israel'),
	array( 'id' => 'IT', 'name' => 'Italy'),
	array( 'id' => 'JM', 'name' => 'Jamaica'),
	array( 'id' => 'JP', 'name' => 'Japan'),
	array( 'id' => 'JE', 'name' => 'Jersey'),
	array( 'id' => 'JO', 'name' => 'Jordan'),
	array( 'id' => 'KZ', 'name' => 'Kazakhstan'),
	array( 'id' => 'KE', 'name' => 'Kenya'),
	array( 'id' => 'KI', 'name' => 'Kiribati'),
	array( 'id' => 'KR', 'name' => 'Korea'),
	array( 'id' => 'KW', 'name' => 'Kuwait'),
	array( 'id' => 'KG', 'name' => 'Kyrgyzstan'),
	array( 'id' => 'LA', 'name' => 'Lao People\'s Democratic Republic'),
	array( 'id' => 'LV', 'name' => 'Latvia'),
	array( 'id' => 'LB', 'name' => 'Lebanon'),
	array( 'id' => 'LS', 'name' => 'Lesotho'),
	array( 'id' => 'LR', 'name' => 'Liberia'),
	array( 'id' => 'LY', 'name' => 'Libyan Arab Jamahiriya'),
	array( 'id' => 'LI', 'name' => 'Liechtenstein'),
	array( 'id' => 'LT', 'name' => 'Lithuania'),
	array( 'id' => 'LU', 'name' => 'Luxembourg'),
	array( 'id' => 'MO', 'name' => 'Macao'),
	array( 'id' => 'MK', 'name' => 'Macedonia'),
	array( 'id' => 'MG', 'name' => 'Madagascar'),
	array( 'id' => 'MW', 'name' => 'Malawi'),
	array( 'id' => 'MY', 'name' => 'Malaysia'),
	array( 'id' => 'MV', 'name' => 'Maldives'),
	array( 'id' => 'ML', 'name' => 'Mali'),
	array( 'id' => 'MT', 'name' => 'Malta'),
	array( 'id' => 'MH', 'name' => 'Marshall Islands'),
	array( 'id' => 'MQ', 'name' => 'Martinique'),
	array( 'id' => 'MR', 'name' => 'Mauritania'),
	array( 'id' => 'MU', 'name' => 'Mauritius'),
	array( 'id' => 'YT', 'name' => 'Mayotte'),
	array( 'id' => 'MX', 'name' => 'Mexico'),
	array( 'id' => 'FM', 'name' => 'Micronesia, Federated States Of'),
	array( 'id' => 'MD', 'name' => 'Moldova'),
	array( 'id' => 'MC', 'name' => 'Monaco'),
	array( 'id' => 'MN', 'name' => 'Mongolia'),
	array( 'id' => 'ME', 'name' => 'Montenegro'),
	array( 'id' => 'MS', 'name' => 'Montserrat'),
	array( 'id' => 'MA', 'name' => 'Morocco'),
	array( 'id' => 'MZ', 'name' => 'Mozambique'),
	array( 'id' => 'MM', 'name' => 'Myanmar'),
	array( 'id' => 'NA', 'name' => 'Namibia'),
	array( 'id' => 'NR', 'name' => 'Nauru'),
	array( 'id' => 'NP', 'name' => 'Nepal'),
	array( 'id' => 'NL', 'name' => 'Netherlands'),
	array( 'id' => 'AN', 'name' => 'Netherlands Antilles'),
	array( 'id' => 'NC', 'name' => 'New Caledonia'),
	array( 'id' => 'NZ', 'name' => 'New Zealand'),
	array( 'id' => 'NI', 'name' => 'Nicaragua'),
	array( 'id' => 'NE', 'name' => 'Niger'),
	array( 'id' => 'NG', 'name' => 'Nigeria'),
	array( 'id' => 'NU', 'name' => 'Niue'),
	array( 'id' => 'NF', 'name' => 'Norfolk Island'),
	array( 'id' => 'MP', 'name' => 'Northern Mariana Islands'),
	array( 'id' => 'NO', 'name' => 'Norway'),
	array( 'id' => 'OM', 'name' => 'Oman'),
	array( 'id' => 'PK', 'name' => 'Pakistan'),
	array( 'id' => 'PW', 'name' => 'Palau'),
	array( 'id' => 'PS', 'name' => 'Palestinian Territory, Occupied'),
	array( 'id' => 'PA', 'name' => 'Panama'),
	array( 'id' => 'PG', 'name' => 'Papua New Guinea'),
	array( 'id' => 'PY', 'name' => 'Paraguay'),
	array( 'id' => 'PE', 'name' => 'Peru'),
	array( 'id' => 'PH', 'name' => 'Philippines'),
	array( 'id' => 'PN', 'name' => 'Pitcairn'),
	array( 'id' => 'PL', 'name' => 'Poland'),
	array( 'id' => 'PT', 'name' => 'Portugal'),
	array( 'id' => 'PR', 'name' => 'Puerto Rico'),
	array( 'id' => 'QA', 'name' => 'Qatar'),
	array( 'id' => 'RE', 'name' => 'Reunion'),
	array( 'id' => 'RO', 'name' => 'Romania'),
	array( 'id' => 'RU', 'name' => 'Russian Federation'),
	array( 'id' => 'RW', 'name' => 'Rwanda'),
	array( 'id' => 'BL', 'name' => 'Saint Barthelemy'),
	array( 'id' => 'SH', 'name' => 'Saint Helena'),
	array( 'id' => 'KN', 'name' => 'Saint Kitts And Nevis'),
	array( 'id' => 'LC', 'name' => 'Saint Lucia'),
	array( 'id' => 'MF', 'name' => 'Saint Martin'),
	array( 'id' => 'PM', 'name' => 'Saint Pierre And Miquelon'),
	array( 'id' => 'VC', 'name' => 'Saint Vincent And Grenadines'),
	array( 'id' => 'WS', 'name' => 'Samoa'),
	array( 'id' => 'SM', 'name' => 'San Marino'),
	array( 'id' => 'ST', 'name' => 'Sao Tome And Principe'),
	array( 'id' => 'SA', 'name' => 'Saudi Arabia'),
	array( 'id' => 'SN', 'name' => 'Senegal'),
	array( 'id' => 'RS', 'name' => 'Serbia'),
	array( 'id' => 'SC', 'name' => 'Seychelles'),
	array( 'id' => 'SL', 'name' => 'Sierra Leone'),
	array( 'id' => 'SG', 'name' => 'Singapore'),
	array( 'id' => 'SK', 'name' => 'Slovakia'),
	array( 'id' => 'SI', 'name' => 'Slovenia'),
	array( 'id' => 'SB', 'name' => 'Solomon Islands'),
	array( 'id' => 'SO', 'name' => 'Somalia'),
	array( 'id' => 'ZA', 'name' => 'South Africa'),
	array( 'id' => 'GS', 'name' => 'South Georgia And Sandwich Isl.'),
	array( 'id' => 'ES', 'name' => 'Spain'),
	array( 'id' => 'LK', 'name' => 'Sri Lanka'),
	array( 'id' => 'SD', 'name' => 'Sudan'),
	array( 'id' => 'SR', 'name' => 'Suriname'),
	array( 'id' => 'SJ', 'name' => 'Svalbard And Jan Mayen'),
	array( 'id' => 'SZ', 'name' => 'Swaziland'),
	array( 'id' => 'SE', 'name' => 'Sweden'),
	array( 'id' => 'CH', 'name' => 'Switzerland'),
	array( 'id' => 'SY', 'name' => 'Syrian Arab Republic'),
	array( 'id' => 'TW', 'name' => 'Taiwan'),
	array( 'id' => 'TJ', 'name' => 'Tajikistan'),
	array( 'id' => 'TZ', 'name' => 'Tanzania'),
	array( 'id' => 'TH', 'name' => 'Thailand'),
	array( 'id' => 'TL', 'name' => 'Timor-Leste'),
	array( 'id' => 'TG', 'name' => 'Togo'),
	array( 'id' => 'TK', 'name' => 'Tokelau'),
	array( 'id' => 'TO', 'name' => 'Tonga'),
	array( 'id' => 'TT', 'name' => 'Trinidad And Tobago'),
	array( 'id' => 'TN', 'name' => 'Tunisia'),
	array( 'id' => 'TR', 'name' => 'Turkey'),
	array( 'id' => 'TM', 'name' => 'Turkmenistan'),
	array( 'id' => 'TC', 'name' => 'Turks And Caicos Islands'),
	array( 'id' => 'TV', 'name' => 'Tuvalu'),
	array( 'id' => 'UG', 'name' => 'Uganda'),
	array( 'id' => 'UA', 'name' => 'Ukraine'),
	array( 'id' => 'AE', 'name' => 'United Arab Emirates'),
	array( 'id' => 'GB', 'name' => 'United Kingdom'),
	array( 'id' => 'US', 'name' => 'United States'),
	array( 'id' => 'UM', 'name' => 'United States Outlying Islands'),
	array( 'id' => 'UY', 'name' => 'Uruguay'),
	array( 'id' => 'UZ', 'name' => 'Uzbekistan'),
	array( 'id' => 'VU', 'name' => 'Vanuatu'),
	array( 'id' => 'VE', 'name' => 'Venezuela'),
	array( 'id' => 'VN', 'name' => 'Viet Nam'),
	array( 'id' => 'VG', 'name' => 'Virgin Islands, British'),
	array( 'id' => 'VI', 'name' => 'Virgin Islands, U.S.'),
	array( 'id' => 'WF', 'name' => 'Wallis And Futuna'),
	array( 'id' => 'EH', 'name' => 'Western Sahara'),
	array( 'id' => 'YE', 'name' => 'Yemen'),
	array( 'id' => 'ZM', 'name' => 'Zambia'),
	array( 'id' => 'ZW', 'name' => 'Zimbabwe'),
);



?>
