<?php
//The groups that is defined 
$config['group']['admin']   = 'Administrators';     //Has all the rights
$config['group']['ap']      = 'Access Providers';   //Has selected right
$config['group']['user']    = 'Permanent Users';              //Has very limited rights

$config['language']['default']    = '4_4';     //This is the id 4 of Languages and id 4 of countries (GB_en)

$config['commands']['msgcat'] = '/usr/bin/msgcat';

//Define the connection types and if they are active or not
$config['conn_type'][0]     = array('name' => __('Direct (Fixed IP)'),  'id' => 'direct',   'active' => true);
$config['conn_type'][1]     = array('name' => __('OpenVPN'),            'id' => 'openvpn',  'active' => false);
$config['conn_type'][2]     = array('name' => __('PPTP'),               'id' => 'pptp',     'active' => false);
$config['conn_type'][3]     = array('name' => __('Dynamic Client'),     'id' => 'dynamic',  'active' => false);

//Define the location of ccd (client config directory)
//FIXME This value does not get read by the OpenvpnClients Model - investigate
$config['openvpn']['ccd_dir_location']  = '/etc/openvpn/ccd/';
$config['openvpn']['ip_half']           = '10.8.';

//Define pptp specific settings
$config['pptp']['start_ip']                        = '10.20.30.2';
$config['pptp']['server_ip']                       = '10.20.30.1';
$config['pptp']['chap_secrets']                    = '/etc/ppp/chap-secrets';

//Define dynamic specific settings
$config['dynamic']['start_ip']                     = '10.120.0.1'; //Make this a Class B subnet (64000) which will never include a value also specified for a FIXED client

//Dictionary files to include for profiles...
//FR2
//$config['freeradius']['path_to_dictionary_files']   = '/usr/local/share/freeradius/';
//$config['freeradius']['main_dictionary_file']       = '/usr/local/etc/raddb/dictionary';
//$config['freeradius']['radclient']                  = '/usr/local/bin/radclient';

//FR3
$config['freeradius']['path_to_dictionary_files']   = '/usr/share/freeradius/';
$config['freeradius']['main_dictionary_file']       = '/etc/freeradius/dictionary';
$config['freeradius']['radclient']                  = '/usr/bin/radclient';


//Define the configured dynamic attributes
$config['dynamic_attributes'][0]     = array('name' => 'Called-Station-Id',  'id' => 'Called-Station-Id',   'active' => true);
$config['dynamic_attributes'][1]     = array('name' => 'Mikrotik-Realm',     'id' => 'Mikrotik-Realm',      'active' => true);
$config['dynamic_attributes'][2]     = array('name' => 'NAS-Identifier',     'id' => 'NAS-Identifier',      'active' => true);

//Define nas types
$config['nas_types'][0]     = array('name' => 'Other',                  'id' => 'other',                    'active' => true);
$config['nas_types'][1]     = array('name' => 'CoovaChilli',            'id' => 'CoovaChilli',              'active' => true);
$config['nas_types'][2]     = array('name' => 'CoovaChilli-Heartbeat',  'id' => 'CoovaChilli-Heartbeat',    'active' => true);
$config['nas_types'][3]     = array('name' => 'Mikrotik',               'id' => 'Mikrotik',                 'active' => true);
$config['nas_types'][4]     = array('name' => 'Mikrotik-Heartbeat',     'id' => 'Mikrotik-Heartbeat',       'active' => true);
$config['nas_types'][5]     = array('name' => 'Telkom',                 'id' => 'Telkom',                   'active' => true);

//Define Voucher format types
$config['voucher_formats'][0]     = array('name' => 'Generic A4',               'id' => 'a4',               'active' => true);
$config['voucher_formats'][1]     = array('name' => 'Generic A4 Page/Voucher',  'id' => 'a4_page',          'active' => true);
$config['voucher_formats'][2]     = array('name' => 'Avery 5160',               'id' => '5160',             'active' => true);
$config['voucher_formats'][3]     = array('name' => 'Avery 5161',               'id' => '5161',             'active' => true);
$config['voucher_formats'][4]     = array('name' => 'Avery 5162',               'id' => '5162',             'active' => true);
$config['voucher_formats'][5]     = array('name' => 'Avery 5163',               'id' => '5163',             'active' => true);
$config['voucher_formats'][6]     = array('name' => 'Avery 5164',               'id' => '5164',             'active' => false); //gives trouble
$config['voucher_formats'][7]     = array('name' => 'Avery 8600',               'id' => '8600',             'active' => true); 
$config['voucher_formats'][8]     = array('name' => 'Avery L7160',              'id' => 'L7160',            'active' => true); 
$config['voucher_formats'][9]     = array('name' => 'Avery L7161',              'id' => 'L7161',            'active' => true); 
$config['voucher_formats'][10]    = array('name' => 'Avery L7163',              'id' => 'L7163',            'active' => true); 


//FIXME To incorporate
$config['paths']['wallpaper_location']  = "/rd/resources/images/wallpapers/";

$config['paths']['dynamic_photos']      = "/cake2/rd_cake/webroot/img/dynamic_photos/";   
$config['paths']['dynamic_detail_icon'] = "/cake2/rd_cake/webroot/img/dynamic_details/";

//Define default settings for the users:
$config['user_settings']['wallpaper']       = "9.jpg";
$config['user_settings']['map']['type']     = "ROADMAP";
$config['user_settings']['map']['zoom']     = 18;
$config['user_settings']['map']['lng']      = -71.0955740216735;
$config['user_settings']['map']['lat']      = 42.3379770178396;

//Define default settings for users's Dynamic Clients map
$config['user_settings']['dynamic_client_map']['type']     = "ROADMAP";
$config['user_settings']['dynamic_client_map']['zoom']     = 18;
$config['user_settings']['dynamic_client_map']['lng']      = -71.0955740216735;
$config['user_settings']['dynamic_client_map']['lat']      = 42.3379770178396;


//Set to true to allow  the user to remove their device out of the realm it has been assigned to
$config['UserCanRemoveDevice']              = true;

//SMTP configs are defined in the Config/email.php file. Here we specify which one to use application wide
$config['EmailServer']						= 'default';


//== 30/3/16 -> Some server wide configurations ==
$config['server_settings']['user_stats_cut_off_days']   = 90; //3 months (make zero to have no cut off)
$config['server_settings']['radacct_cut_off_days']      = 90; //3 months (make zero to have no cut off)
 
//== End server wide configurations ==

$config['webFont']      = 'FontAwesome';
$config['icnLock']      = 'xf023@'.$config['webFont'];
$config['icnYes']       = 'xf00c@'.$config['webFont'];
$config['icnMenu']      = 'xf0c9@'.$config['webFont'];
$config['icnInfo']      = 'xf129@'.$config['webFont'];
$config['icnPower']     = 'xf011@'.$config['webFont'];
$config['icnSpanner']   = 'xf0ad@'.$config['webFont'];
$config['icnHome']      = 'xf015@'.$config['webFont'];
$config['icnDynamic']   = 'xf0d0@'.$config['webFont'];
$config['icnVoucher']   = 'xf145@'.$config['webFont'];
$config['icnReload']    = 'xf021@'.$config['webFont'];//
$config['icnAdd']       = 'xf067@'.$config['webFont'];//
$config['icnEdit']      = 'xf040@'.$config['webFont'];//
$config['icnDelete']    = 'xf1f8@'.$config['webFont'];//
$config['icnPdf']       = 'xf1c1@'.$config['webFont'];
$config['icnCsv']       = 'xf1c3@'.$config['webFont'];
$config['icnRadius']    = 'xf10c@'.$config['webFont'];
$config['icnLight']     = 'xf204@'.$config['webFont'];
$config['icnNote']      = 'xf08d@'.$config['webFont'];//
$config['icnKey']       = 'xf084@'.$config['webFont'];
$config['icnRealm']     = 'xf17d@'.$config['webFont'];
$config['icnNas']       = 'xf1cb@'.$config['webFont'];
$config['icnTag']       = 'xf02b@'.$config['webFont'];
$config['icnProfile']   = 'xf1b3@'.$config['webFont'];
$config['icnComponent'] = 'xf12e@'.$config['webFont'];
$config['icnActivity']  = 'xf0e7@'.$config['webFont'];
$config['icnLog']       = 'xf044@'.$config['webFont'];
$config['icnTranslate'] = 'xf0ac@'.$config['webFont'];
$config['icnConfigure'] = 'xf0ad@'.$config['webFont'];
$config['icnUser']      = 'xf007@'.$config['webFont'];
$config['icnDevice']    = 'xf10a@'.$config['webFont'];
$config['icnMesh']      = 'xf20e@'.$config['webFont'];
$config['icnBug']       = 'xf188@'.$config['webFont'];
$config['icnMobile']    = 'xf10b@'.$config['webFont'];
$config['icnDesktop']   = 'xf108@'.$config['webFont'];
$config['icnView']      = 'xf002@'.$config['webFont'];
$config['icnMeta']      = 'xf0cb@'.$config['webFont'];
$config['icnMap']       = 'xf041@'.$config['webFont'];
$config['icnConnect']   = 'xf0c1@'.$config['webFont'];
$config['icnGraph']     = 'xf080@'.$config['webFont'];
$config['icnKick']      = 'xf1e6@'.$config['webFont'];
$config['icnClose']     = 'xf00d@'.$config['webFont'];
$config['icnFinance']   = 'xf09d@'.$config['webFont'];
$config['icnOnlineShop']= 'xf07a@'.$config['webFont'];
$config['icnEmail']     = 'xf0e0@'.$config['webFont'];
$config['icnAttach']    = 'xf0c6@'.$config['webFont'];
$config['icnCut']       = 'xf0c4@'.$config['webFont'];
$config['icnTopUp']     = 'xf0f4@'.$config['webFont'];
$config['icnSubtract']  = 'xf068@'.$config['webFont'];
$config['icnWatch']     = 'xf017@'.$config['webFont'];
$config['icnStar']      = 'xf005@'.$config['webFont'];
$config['icnGrid']      = 'xf00a@'.$config['webFont'];
$config['icnFacebook']	= 'xf09a@'.$config['webFont'];
$config['icnGoogle']	= 'xf1a0@'.$config['webFont'];
$config['icnTwitter']	= 'xf099@'.$config['webFont'];
$config['icnWifi']		= 'xf012@'.$config['webFont'];
$config['icnIP']		= 'xf1c0@'.$config['webFont'];
$config['icnThumbUp']   = 'xf087@'.$config['webFont'];
$config['icnThumbDown']	= 'xf088@'.$config['webFont'];
$config['icnCPU']		= 'xf085@'.$config['webFont'];
$config['icnCamera']    = 'xf030@'.$config['webFont'];
$config['icnRedirect']  = 'xf074@'.$config['webFont'];
$config['icnDynamicNas']= 'xf239@'.$config['webFont'];
$config['icnCloud']     = 'xf0c2@'.$config['webFont'];
$config['icnVPN']       = 'xf10e@'.$config['webFont'];

$config['icnAdmin']     = 'xf19d@'.$config['webFont'];
$config['icnRadius']    = 'xf140@'.$config['webFont'];
$config['icnBan']       = 'xf05e@'.$config['webFont'];
$config['icnData']      = 'xf1c0@'.$config['webFont'];
$config['icnGears']     = 'xf085@'.$config['webFont'];
$config['icnWizard']    = 'xf0d0@'.$config['webFont'];

//=== Dynamic RADIUS Clients 23/1/17 ===
/*--- This has to be the same value as the ons specified in--
sudo vi /etc/freeradius/sites-enabled/dynamic-clients
       FreeRADIUS-Client-Secret = "testing123"
       
TIP: Also remember to change the values in:
ApProfiles.php
MESHdesk.php 
*/
$config['DynamicClients']['shared_secret'] = 'mediai123';

//=== EXPERIMENTAL STUFF =====
//Show experimental menus
$config['experimental']['active']   = false;

$config['dnsfilter']['dns1'] = '8.8.8.8';
$config['dnsfilter']['dns2'] = '4.4.4.4';

?>
