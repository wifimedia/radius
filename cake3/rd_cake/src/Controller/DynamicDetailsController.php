<?php

namespace App\Controller;
use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;

use Cake\Utility\Inflector;


class DynamicDetailsController extends AppController{
  
    protected $base  = "Access Providers/Controllers/DynamicDetails/";
    
    protected $owner_tree = array();
    
    protected $main_model = 'DynamicDetails';
    
    protected $theme_default    = 'Default';
	protected $theme_selected   = 'Default';	
	protected $default_language = 'en_GB';
    
  
    public function initialize(){  
        parent::initialize();  
          
        $this->loadModel('DynamicDetails');
        $this->loadModel('DynamicPairs'); 
        $this->loadModel('Users');
        $this->loadModel('DynamicPhotos');
        $this->loadModel('DynamicPages');
        $this->loadModel('DynamicDetailSocialLogins');
        
        $this->loadComponent('Aa');
        $this->loadComponent('GridButtons');
        $this->loadComponent('CommonQuery', [ //Very important to specify the Model
            'model' => $this->main_model
        ]);
        
        $this->loadComponent('Notes', [
            'model'     => 'DynamicDetailNotes',
            'condition' => 'dynamic_detail_id'
        ]);    
    }
    
    
    public function infoFor(){

        $items      = array();
		$sl_items	= array();
		$social_enable = false;

        if(isset($this->request->query['dynamic_id'])){ //preview link will call this page ?dynamic_id=<id>
        
            $dynamic_id = $this->request->query['dynamic_id'];

            $q_r = $this->{$this->main_model}
                ->find()
                ->contain([
                    'DynamicPages',
                    'DynamicPhotos' => function ($q) {
                       return $q
                            ->where(['DynamicPhotos.active' => true]);
                    },
                    'DynamicDetailSocialLogins'
                ])
                ->where([$this->main_model.'.id' =>$dynamic_id])
                ->first();                

            if($q_r){
                //Modify the photo path:
                $c = 0;
                foreach($q_r->dynamic_photos as $i){
                
                    $full_file_name = Configure::read('paths.absolute_photo_path').$i->file_name;
                    $info           = getimagesize($full_file_name);
                    $width          = $info[0];
                    $height         = $info[1];
                    
                    $layout = 'landscape';
                    if($width == $height){
                        $layout = 'block';
                    }
                    if($width < $height){
                        $layout = 'portrait';
                    }
                    
                    $q_r->dynamic_photos[$c]['file_name']   = Configure::read('paths.dynamic_photos').$i->file_name;
                    $q_r->dynamic_photos[$c]['width']       = $width;
                    $q_r->dynamic_photos[$c]['height']      = $height;
                    $q_r->dynamic_photos[$c]['layout']      = $layout;
                    $c++;
                }
                
                $items['photos']    = $q_r->dynamic_photos;
                $items['pages']     = $q_r->dynamic_pages;
				$sl_items           = $q_r->dynamic_detail_social_logins;
				$icon_file_name     = Configure::read('paths.dynamic_detail_icon').$q_r->icon_file_name;
				if($q_r->social_enable == true){
				    $social_enable = true;
				}
            }

        }else{ //Build a query since it was not called from the preview link
            $conditions = array("OR" =>array());
      
            foreach(array_keys($this->request->query) as $key){
                array_push($conditions["OR"],
                    array("DynamicPairs.name" => $key, "DynamicPairs.value" =>  $this->request->query[$key])
                ); //OR query all the keys
            }
           	
			$q_r = $this->DynamicPairs
                ->find()
                ->contain([
                    'DynamicDetails' => 
                        [
                            'DynamicPhotos' => function ($q) {
                               return $q
                                    ->where(['DynamicPhotos.active' => true]);
                            },
                            'DynamicPages',
                            'DynamicDetailSocialLogins'
                        ]
                ])
                ->where([$conditions])
                ->order(['DynamicPairs.priority' => 'DESC'])
                ->first();                

            if($q_r){
                
                //Modify the photo path:
                $c = 0;
                foreach($q_r->dynamic_detail->dynamic_photos as $i){
                    $full_file_name = Configure::read('paths.absolute_photo_path').$i->file_name;
                    $info           = getimagesize($full_file_name);
                    $width          = $info[0];
                    $height         = $info[1];
                    
                    $layout = 'landscape';
                    if($width == $height){
                        $layout = 'block';
                    }
                    if($width < $height){
                        $layout = 'portrait';
                    }
                    
                    $q_r->dynamic_detail->dynamic_photos[$c]['file_name']   = Configure::read('paths.dynamic_photos').$i->file_name;
                    $q_r->dynamic_detail->dynamic_photos[$c]['width']       = $width;
                    $q_r->dynamic_detail->dynamic_photos[$c]['height']      = $height;
                    $q_r->dynamic_detail->dynamic_photos[$c]['layout']      = $layout;
                    $c++;

                }
                
                
                $items['photos']    = $q_r->dynamic_detail->dynamic_photos;
                $items['pages']     = $q_r->dynamic_detail->dynamic_pages;
				$sl_items           = $q_r->dynamic_detail->dynamic_detail_social_logins;
				$icon_file_name     = Configure::read('paths.dynamic_detail_icon').$q_r->dynamic_detail->icon_file_name;
				if($q_r->dynamic_detail->social_enable == true){
				    $social_enable = true;
				}
            }
            
        }
        
        $detail_fields  = array(
			'id',		'name',			'phone',		'fax',			'cell',		'email',
			'url',		'street_no',	'street',		'town_suburb',	'city',		'country',
			'lat',		'lon',	       
		);
		
		$settings_fields    = array(
			't_c_check',	        't_c_url',	        'redirect_check',   'redirect_url',
			'slideshow_check',      'seconds_per_slide','connect_check',    'connect_username', 'connect_suffix',
			'connect_delay',        'connect_only',     'user_login_check', 'voucher_login_check',
			'auto_suffix_check',    'auto_suffix',      'usage_show_check', 'usage_refresh_interval', 
			'register_users',       'lost_password',
			'slideshow_enforce_watching',
			'slideshow_enforce_seconds',
			'available_languages'
		);
        
		//print_r($q_r);

        //Get the detail for the page
        if($q_r){
        
                $direct_flag = true;
                if($q_r->dynamic_detail != null){
                    $direct_flag = false;
                }
        
                foreach($detail_fields as $field){
                    if($direct_flag){
                        $items['detail']["$field"]= $q_r->{"$field"};  
                    }else{
                        $items['detail']["$field"]= $q_r->dynamic_detail->{"$field"}; 
                    }
                }
                $items['detail']['icon_file_name'] = $icon_file_name;
                
                foreach($settings_fields as $field){
                    if($direct_flag){
                        $items['settings']["$field"]= $q_r->{"$field"};  
                    }else{
                        $items['settings']["$field"]= $q_r->dynamic_detail->{"$field"}; 
                    } 
                }
                
                //IF the available languages are selected we need to get more data on them
                if($items['settings']["available_languages"] !== ''){
                    $final_array = [];
                    $selected_array = explode(",",$items['settings']["available_languages"]);
                    Configure::load('DynamicLogin','default'); 
                    $i18n = Configure::read('DynamicLogin.i18n');
                    foreach($i18n as $i){
                        if($i['active']){
                            if(in_array($i['id'],$selected_array)){
                                array_push($final_array,['value' => $i['name'],'id'=>$i['id']]);   
                            }
                        }
                    }
                    $items['settings']["available_languages"] = $final_array;
                }
                
			if($social_enable){
				$items['settings']['social_login']['active'] = true;			
				//Find the temp username and password
				if($direct_flag){
                    $temp_user_id   = $q_r->social_temp_permanent_user_id;  
                }else{
                    $temp_user_id   = $q_r->dynamic_detail->social_temp_permanent_user_id; 
                }
				
				$user_detail  = $this->_find_username_and_password($temp_user_id);
				$items['settings']['social_login']['temp_username'] = $user_detail['username'];
				$items['settings']['social_login']['temp_password'] = $user_detail['password'];
				//Find if there are any defined
				$items['settings']['social_login']['items'] = array();
				foreach($sl_items as $i){
					$n = $i->name;
					array_push($items['settings']['social_login']['items'], array('name' => $n));
				}

			}else{
				$items['settings']['social_login']['active'] = false;
			}

        }

        $success = true;
        if(count($items) == 0){ //Not found
            $success = false;
        }

        $this->set(array(
            'data' => $items,
            'success' => $success,
            '_serialize' => array('data','success')
        ));
    }
    
    public function idMe(){
       // $this->layout = 'rd';
        $u_a = $this->request->header('User-Agent');
        $this->set('u_a',$u_a);
        $this->set(array(
            'data' => array("User-Agent" => $u_a),
            'success' => true,
            '_serialize' => array('data','success')
        ));
    }
    
    public function chilliBrowserDetect(){  
		$redir_to = $this->_doBrowserDetectFor('coova');
		$this->response->header('Location', $redir_to);
        return $this->response;	
    }
    
    public function mikrotikBrowserDetect(){  
		$redir_to = $this->_doBrowserDetectFor('coova');
		$this->response->header('Location', $redir_to);
        return $this->response;	
    }
    
    public function ruckusBrowserDetect(){  
		$redir_to = $this->_doBrowserDetectFor('ruckus');
		$this->response->header('Location', $redir_to);
        return $this->response;	
    }
    
    //----- Give better preview pages -----
    public function previewChilliDesktop(){
        $redir_to = $this->_doPreviewChilli('desktop');
		$this->response->header('Location', $redir_to);
        return $this->response;	
    }
    
    public function previewChilliMobile(){
        $redir_to = $this->_doPreviewChilli('mobile');
		$this->response->header('Location', $redir_to);
        return $this->response;	
    }
    
    public function i18n(){
        $items = array();
        Configure::load('DynamicLogin','default');
        $i18n = Configure::read('DynamicLogin.i18n');
        foreach($i18n as $i){
            if($i['active']){
                array_push($items, $i);
            }
        }

        $this->set(array(
            'items' => $items,
            'success' => true,
            '_serialize' => array('items','success')
        ));
    }
       
    public function exportCsv(){

        $this->autoRender   = false;

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $query = $this->{$this->main_model}->find(); 
        $this->CommonQuery->build_common_query($query,$user,['Users','DynamicDetailNotes' => ['Notes']]);
        
        $q_r    = $query->all();

        //Create file
        $this->ensureTmp();     
        $tmpFilename    = TMP . $this->tmpDir . DS .  strtolower( Inflector::pluralize($this->modelClass) ) . '-' . date('Ymd-Hms') . '.csv';
        $fp             = fopen($tmpFilename, 'w');

        //Headings
        $heading_line   = array();
        if(isset($this->request->query['columns'])){
            $columns = json_decode($this->request->query['columns']);
            foreach($columns as $c){
                array_push($heading_line,$c->name);
            }
        }
        fputcsv($fp, $heading_line,';','"');
        foreach($q_r as $i){

            $columns    = array();
            $csv_line   = array();
            if(isset($this->request->query['columns'])){
                $columns = json_decode($this->request->query['columns']);
                foreach($columns as $c){
                    $column_name = $c->name;
                    if($column_name == 'notes'){
                        $notes   = '';
                        foreach($i->dynamic_detail_notes as $un){
                            if(!$this->Aa->test_for_private_parent($un->note,$user)){
                                $notes = $notes.'['.$un->note->note.']';    
                            }
                        }
                        array_push($csv_line,$notes);
                    }elseif($column_name =='owner'){
                        $owner_id       = $i->user_id;
                        $owner_tree     = $this->Users->find_parents($owner_id);
                        array_push($csv_line,$owner_tree); 
                    }else{
                        array_push($csv_line,$i->{$column_name});  
                    }
                }
                fputcsv($fp, $csv_line,';','"');
            }
        }

        //Return results
        fclose($fp);
        $data = file_get_contents( $tmpFilename );
        $this->cleanupTmp( $tmpFilename );
        $this->RequestHandler->respondAs('csv');
        $this->response->download( strtolower( Inflector::pluralize( $this->modelClass ) ) . '.csv' );
        $this->response->body($data);
    } 
       
    //____ BASIC CRUD Manager ________
    public function index(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        
        //Fields
		$fields  	= array(
			'id',		'name',			'user_id',		'available_to_siblings',
			'phone',    'fax',			'cell',		    'email',
			'url',		'street_no',	'street',		'town_suburb',	'city',		'country',
			'lat',		'lon',			't_c_check',	't_c_url',		'theme',	'register_users',
			'lost_password'
		);
		
        $query = $this->{$this->main_model}->find();

        $this->CommonQuery->build_common_query($query,$user,['Users','DynamicDetailNotes' => ['Notes']]);
 
        //===== PAGING (MUST BE LAST) ======
        $limit  = 50;   //Defaults
        $page   = 1;
        $offset = 0;
        if(isset($this->request->query['limit'])){
            $limit  = $this->request->query['limit'];
            $page   = $this->request->query['page'];
            $offset = $this->request->query['start'];
        }
        
        $query->page($page);
        $query->limit($limit);
        $query->offset($offset);

        $total  = $query->count();       
        $q_r    = $query->all();

        $items      = array();

        foreach($q_r as $i){    
            $owner_id   = $i->user_id;
            if(!array_key_exists($owner_id,$this->owner_tree)){
                $owner_tree     = $this->Users->find_parents($owner_id);
            }else{
                $owner_tree = $this->owner_tree[$owner_id];
            }
            
            $action_flags   = $this->Aa->get_action_flags($owner_id,$user);
            
            $notes_flag     = false;
            foreach($i->dynamic_detail_notes as $un){
                if(!$this->Aa->test_for_private_parent($un->note,$user)){
                    $notes_flag = true;
                    break;
                }
            }
            
            $row = array();
            foreach($fields as $field){
                $row["$field"]= $i->{"$field"};
            }
            
            $row['owner']		= $owner_tree;
			$row['notes']		= $notes_flag;
			$row['update']		= $action_flags['update'];
			$row['delete']		= $action_flags['delete'];
            array_push($items,$row);
        }
       
        //___ FINAL PART ___
        $this->set(array(
            'items' => $items,
            'success' => true,
            'totalCount' => $total,
            '_serialize' => array('items','success','totalCount')
        ));
    }
     
    public function indexForFilter(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $query  = $this->{$this->main_model}->find();
        $this->CommonQuery->build_common_query($query,$user,[]);        
        $q_r    = $query->all();
        $items      = array();
        foreach($q_r as $i){
            array_push($items,array(
                'id'                    => $i->id, 
                'text'                  => $i->name
            ));
        }   

        $this->set(array(
            'items' => $items,
            'success' => true,
            '_serialize' => array('items','success')
        ));
    }
      
    public function add() {

        if(!$this->_ap_right_check()){
            return;
        }

        $user       = $this->Aa->user_for_token($this);
        $user_id    = $user['id'];

        //Get the owner's id
         if($this->request->data['user_id'] == '0'){ //This is the holder of the token - override '0'
            $this->request->data['user_id'] = $user_id;
        }
        
        $check_items = array('available_to_siblings', 't_c_check', 'register_users', 'lost_password');
        foreach($check_items as $ci){
            if(isset($this->request->data[$ci])){
                $this->request->data[$ci] = 1;
            }else{
                $this->request->data[$ci] = 0;
            }
        }
        
        //The rest of the attributes should be same as the form..
        $entity = $this->{$this->main_model}->newEntity($this->request->data()); 
        if($this->{$this->main_model}->save($entity)){
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }else{
            $message = 'Error';
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }   
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not create item')),
                '_serialize' => array('errors','success','message')
            ));
        }   
	}
      
    public function edit() {

        if(!$this->_ap_right_check()){
            return;
        }

        //We will not modify user_id
        unset($this->request->data['user_id']);

        //Make available to siblings check
        if(isset($this->request->data['available_to_siblings'])){
            $this->request->data['available_to_siblings'] = 1;
        }else{
            $this->request->data['available_to_siblings'] = 0;
        }

		$entity = $this->{$this->main_model}->get($this->request->data['id']);
        $this->{$this->main_model}->patchEntity($entity, $this->request->data());

        if ($this->{$this->main_model}->save($entity)) {
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        } else {
            $message = 'Error';
            
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }
            
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not update item')),
                '_serialize' => array('errors','success','message')
            ));
        }
	}
	
	
    public function editSettings(){

        if(!$this->_ap_right_check()){
            return;
        }
            
        //We will not modify user_id
        unset($this->request->data['user_id']);
        $check_items = array(
            'reg_email',
            'reg_auto_add',
            'reg_mac_check',
            'register_users',
            't_c_check',
            'redirect_check',
            'slideshow_check',
            'user_login_check',
            'voucher_login_check',
            'auto_suffix_check',
            'usage_show_check',
            'lost_password',
            'slideshow_enforce_watching'
	    );
	    
        foreach($check_items as $i){
            if(isset($this->request->data[$i])){
                $this->request->data[$i] = 1;
            }else{
                $this->request->data[$i] = 0;
            }
        }
        
        $count      = 0;
        
        if (array_key_exists('available_languages', $this->request->data)) {
            $comma_separated = implode(",", $this->request->data['available_languages']);
            if($comma_separated == 'Just The Default Language'){
                $comma_separated = '';
            }else{
                if (strpos($comma_separated, $this->request->data['default_language']) == false) {
                    $comma_separated = $comma_separated.','.$this->request->data['default_language'];
                } 
            }
            $this->request->data['available_languages'] = $comma_separated;
        }
        
        
        $entity = $this->{$this->main_model}->get($this->request->data['id']);
        $this->{$this->main_model}->patchEntity($entity, $this->request->data());

        if ($this->{$this->main_model}->save($entity)) {
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        } else {
            $message = 'Error';
            
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }
            
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not update item')),
                '_serialize' => array('errors','success','message')
            ));
        }
    }
    
    public function editClickToConnect(){

        if(!$this->_ap_right_check()){
            return;
        }
        //We will not modify user_id
        unset($this->request->data['user_id']);

        //connect_check compulsory check
        if(isset($this->request->data['connect_check'])){
            $this->request->data['connect_check'] = 1;
        }else{
            $this->request->data['connect_check'] = 0;
        }

        //connect_only compulsory check
        if(isset($this->request->data['connect_only'])){
            $this->request->data['connect_only'] = 1;
        }else{
            $this->request->data['connect_only'] = 0;
        }

        $entity = $this->{$this->main_model}->get($this->request->data['id']);
        $this->{$this->main_model}->patchEntity($entity, $this->request->data());

        if ($this->{$this->main_model}->save($entity)) {
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        } else {
            $message = 'Error';
            
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }
            
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not update item')),
                '_serialize' => array('errors','success','message')
            ));
        }
    }
    
    public function delete() {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        if(!$this->_ap_right_check()){
            return;
        }
           
	    if(isset($this->request->data['id'])){   //Single item delete

            //First find all the photos for this item then delete them           
            $q_r = $this->DynamicPhotos
                ->find()
                ->where(['DynamicPhotos.dynamic_detail_id' => $this->request->data['id']])
                ->all();
            foreach($q_r as $i){
                $file_to_delete = WWW_ROOT."img/dynamic_photos/".$i->file_name;
                $entity = $this->DynamicPhotos->get($i->id);
                if($this->DynamicPhotos->delete($entity)){
                    if(file_exists($file_to_delete)){
                        unlink($file_to_delete);
                    }
                }
            }
                    
            $this->DynamicDetail->id = $this->request->data['id'];
            $this->DynamicDetail->delete($this->DynamicDetail->id,true);
      
        }else{                          //Assume multiple item delete
            foreach($this->request->data as $d){

                //First find all the photos for this item then delete them             
                $q_r = $this->DynamicPhotos
                    ->find()
                    ->where(['DynamicPhotos.dynamic_detail_id' => $d['id']])
                    ->all();
                foreach($q_r as $i){
                    $file_to_delete = WWW_ROOT."img/dynamic_photos/".$i->file_name;
                    $entity = $this->DynamicPhotos->get($i->id);
                    if($this->DynamicPhotos->delete($entity)){
                        if(file_exists($file_to_delete)){
                            unlink($file_to_delete);
                        }
                    }
                }
                $e = $this->{$this->main_model}->get($d['id']);
                $this->{$this->main_model}->delete($e);
            }
        }

        $this->set(array(
            'success' => true,
            '_serialize' => array('success')
        ));
	}

    public function view(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        
        $user_id    = $user['id'];
        $items      = array();
        
        if(isset($this->request->query['dynamic_detail_id'])){

            $q_r = $this->{$this->main_model}->get($this->request->query['dynamic_detail_id']);
            if($q_r){
            
                $fields = $this->{$this->main_model}->schema()->columns();
                $realm  = '';
            
                if($q_r->realm_id != null){
                    $this->loadModel('Realms'); 
                    $q_realm    = $this->Realms->get($q_r->realm_id);
                    if($q_r){
                        $realm = $q_realm->name;
                    }
                }
                $profile = '';
                
                if($q_r->profile_id != null){
                    $this->loadModel('Profiles');
                    $q_profile    = $this->Profiles->get($q_r->profile_id);
                    if($q_profile){
                        $profile = $q_profile->name;
                    }
                }
                
                $owner_tree     = $this->Users->find_parents($q_r->user_id);
                
                foreach($fields as $field){
	                $items["$field"]= $q_r->{"$field"};
		        } 
		        
		        if($items['default_language'] == ''){
		            $items['default_language'] = $this->default_language;
		        }
		        
		        if($items['available_languages'] !== ''){
		            $items['available_languages[]'] = explode(',',$items['available_languages']);
		        }
		         
                $items['owner']     = $owner_tree; 
                $items['realm']     = $realm;
                $items['profile']   = $profile;               
            }
        }
        
        $this->set(array(
            'data'     => $items,
            'success'   => true,
            '_serialize'=> array('success', 'data')
        ));
    }
    
    public function uploadLogo($id = null){   
        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }

        //This is a deviation from the standard JSON serialize view since extjs requires a html type reply when files
        //are posted to the server.    
        $this->viewBuilder()->layout('ext_file_upload');

        $path_parts     = pathinfo($_FILES['photo']['name']);
        $unique         = time();
        $dest           = WWW_ROOT."img/dynamic_details/".$unique.'.'.$path_parts['extension'];
        $dest_www       = "/cake3/rd_cake/webroot/img/dynamic_details/".$unique.'.'.$path_parts['extension'];
       
        $entity         = $this->{$this->main_model}->get($this->request->data['id']);
        $icon_file_name = $unique.'.'.$path_parts['extension'];
        $old_file       = $entity->icon_file_name;
        $entity->icon_file_name = $icon_file_name;
        
        if($this->{$this->main_model}->save($entity)){
            move_uploaded_file ($_FILES['photo']['tmp_name'] , $dest);
            $json_return['id']                  = $this->request->data['id'];
            $json_return['success']             = true;
            $json_return['icon_file_name']      = $icon_file_name;
            
            //Remove old file
            $file_to_delete = WWW_ROOT."img/dynamic_details/".$old_file;
            if(file_exists($file_to_delete)){
                unlink($file_to_delete);
            }
    
        }else{       
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }
                  
            $json_return['errors']      = $a;
            $json_return['message']     = array("message"   => __('Problem uploading photo'));
            $json_return['success']     = false;
        }
        $this->set('json_return',$json_return);
    }
    
    public function indexPhoto(){   
        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericIndex("DynamicPhotos");
    }
    
    public function uploadPhoto($id = null){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        
        $check_items = array('active','include_title','include_description');
        foreach($check_items as $ci){
            if(isset($this->request->data[$ci])){
                $this->request->data[$ci] = 1;
            }else{
                $this->request->data[$ci] = 0;
            }
        }
        

        //This is a deviation from the standard JSON serialize view since extjs requires a html type reply when files
        //are posted to the server.
        $this->viewBuilder()->layout('ext_file_upload');

        $path_parts   = pathinfo($_FILES['photo']['name']);
        $unique       = time();
        $dest         = WWW_ROOT."img/dynamic_photos/".$unique.'.'.$path_parts['extension'];
        $dest_www     = "/cake3/rd_cake/img/dynamic_photos/".$unique.'.'.$path_parts['extension'];
        
        $this->request->data['file_name'] = $unique.'.'.$path_parts['extension'];
        
        $entity = $this->DynamicPhotos->newEntity($this->request->data()); 
        if($this->DynamicPhotos->save($entity)){
            move_uploaded_file ($_FILES['photo']['tmp_name'] , $dest);
            $json_return['id']                  = $entity->id;
            $json_return['success']             = true;       
        }else{
            $message = 'Error';
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            } 
            
            $json_return['errors']      = $a;
            $json_return['message']     = array("message"   => __('Problem uploading photo'));
            $json_return['success']     = false;
           
        }
        
        $this->set('json_return',$json_return);   
    }
    
    public function deletePhoto($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        if(!$this->_ap_right_check()){
            return;
        }

	    if(isset($this->request->data['id'])){   //Single item delete
            //Get the filename to delete
            $entity = $this->DynamicPhotos->get($this->request->data['id']);
            if($entity){
                $file_to_delete = WWW_ROOT."img/dynamic_photos/".$entity->file_name;
                if($this->DynamicPhotos->delete($entity)){
                    if(file_exists($file_to_delete)){
                        unlink($file_to_delete);
                    }
                }
            }     
        }else{                          //Assume multiple item delete
            foreach($this->request->data as $d){
                //Get the filename to delete
                $entity = $this->DynamicPhotos->get($d['id']);
                if($entity){
                    $file_to_delete = WWW_ROOT."img/dynamic_photos/".$entity->file_name;
                    if($this->DynamicPhotos->delete($entity)){
                        if(file_exists($file_to_delete)){
                            unlink($file_to_delete);
                        }
                    }
                }
            }
        }

        $this->set(array(
            'success' => true,
            '_serialize' => array('success')
        ));
	}
	
	public function editPhoto(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $this->viewBuilder()->layout('ext_file_upload'); 
        $entity = $this->DynamicPhotos->get($this->request->data['id']);
        
        $check_items = array('active','include_title','include_description');
        foreach($check_items as $ci){
            if(isset($this->request->data[$ci])){
                $this->request->data[$ci] = 1;
            }else{
                $this->request->data[$ci] = 0;
            }
        }
        
        if($entity){ 
            $new_photo = false;
            if($_FILES['photo']['size'] > 0){  
                $file_name      = $entity->file_name;
                $file_to_delete = WWW_ROOT."img/dynamic_photos/".$file_name;
                unlink($file_to_delete);

                $path_parts     = pathinfo($_FILES['photo']['name']);
                $unique         = time();
                $dest           = WWW_ROOT."img/dynamic_photos/".$unique.'.'.$path_parts['extension'];
                move_uploaded_file ($_FILES['photo']['tmp_name'] , $dest);
                $new_photo = true;
            }
             
            $this->DynamicPhotos->patchEntity($entity, $this->request->data());
            if($new_photo){
                $entity->file_name = $unique.'.'.$path_parts['extension']; 
            }  
            $this->DynamicPhotos->save($entity);
        }  

        $json_return['success'] = true;
        $this->set('json_return',$json_return);
    }
    
    public function shufflePhoto(){
    
        if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        if(!$this->_ap_right_check()){
            return;
        }
        $table = 'DynamicPhotos';
        
        if(isset($this->request->query['dynamic_detail_id'])){
            $dd_id = $this->request->query['dynamic_detail_id'];   
            foreach($this->request->data as $d){       
                $entity     = $this->{$table}->get($d['id']);
                $e_data     = $entity->toArray();
                
                $new_entity = $this->{$table}->newEntity($e_data);
                $new_entity->unsetProperty('id');
                $this->{$table}->save($new_entity);
                
                $this->{$table}->delete($entity);
            }    
        }
          
        $this->set(array(
            'success' => true,
            '_serialize' => array('success')
        ));
    }
    
    
    public function indexPage(){
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericIndex("DynamicPages");  
    }

    public function addPage() {

        if(!$this->_ap_right_check()){
            return;
        }
        
        $this->_genericAdd('DynamicPages');
 
       
	}
	
	public function editPage() {

        if(!$this->_ap_right_check()){
            return;
        }
             
        $this->_genericEdit('DynamicPages');
	}
	
	public function deletePage($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        if(!$this->_ap_right_check()){
            return;
        }

	    $this->_genericDelete('DynamicPages');
	}
	
	
	public function indexPair(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericIndex("DynamicPairs");   
    }
    
    public function addPair(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericAdd("DynamicPairs");   
    }
    
    public function editPair(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericEdit("DynamicPairs");   
    }
    
    public function deletePair(){

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];
        $this->_genericDelete("DynamicPairs");   
    }
    
    private function _genericIndex($table){
    
        $fields = $this->{$table}->schema()->columns();
        $items = array();
        
        if(isset($this->request->query['dynamic_detail_id'])){
            $dd_id = $this->request->query['dynamic_detail_id'];
            $q_r = $this->{$table}
                ->find()
       	        ->where([$table.'.dynamic_detail_id' =>$dd_id])
       	        ->all();
       	    
       	    foreach($q_r as $i){ 
           	    $row = array();
                foreach($fields as $field){
                    $row["$field"]= $i->{"$field"};
                } 
                
                //Special clause for DynamicPhotos
                if($table == 'DynamicPhotos'){
                    $f          = $i->file_name;
                    $location   = Configure::read('paths.real_photo_path').$f;
                    $row['img'] = "/cake3/rd_cake/webroot/files/image.php?width=400&height=100&image=".$location;
                }              
           	    array_push($items,$row);
            }
        }
               
        $this->set(array(
            'items'     => $items,
            'success'   => true,
            '_serialize'=> array('success', 'items')
        ));
    }
    
    private function _genericAdd($table){
 
         //The rest of the attributes should be same as the form..
        $entity = $this->{$table}->newEntity($this->request->data()); 
        if($this->{$table}->save($entity)){
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }else{
            $message = 'Error';
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }   
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not create item')),
                '_serialize' => array('errors','success','message')
            ));
        }   
    }
    
    private function _genericEdit($table){
    
        $entity = $this->{$table}->get($this->request->data['id']);
        $this->{$table}->patchEntity($entity, $this->request->data());

        if ($this->{$table}->save($entity)) {
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        } else {
            $message = 'Error';
            
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }
            
            $this->set(array(
                'errors'    => $a,
                'success'   => false,
                'message'   => array('message' => __('Could not update item')),
                '_serialize' => array('errors','success','message')
            ));
        }
    }
    
    private function _genericDelete($table){	
        if(isset($this->request->data['id'])){ 
            $entity = $this->{$table}->get($this->request->data['id']);
            $this->{$table}->delete($entity);
        }else{                          //Assume multiple item delete
            foreach($this->request->data as $d){
                //Get the filename to delete
                $entity = $this->{$table}->get($d['id']);
                $this->{$table}->delete($entity);
            }
        }

        $this->set(array(
            'success' => true,
            '_serialize' => array('success')
        ));
    }
    
    
    public function viewSocialLogin(){

		 //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $user_id    = $user['id'];

        $items = array();
        if(isset($this->request->query['dynamic_detail_id'])){
        
            $dd_id = $this->request->query['dynamic_detail_id'];
            $q_r = $this->{$this->main_model}
                ->find()
                ->contain(['DynamicDetailSocialLogins' => ['Realms','Profiles'],'PermanentUsers'])
                ->where(['DynamicDetails.id' => $dd_id])
                ->first();

            if($q_r){
                
				$items['social_enable']    					= $q_r->social_enable;
				$items['id']    							= $q_r->id;
				$items['social_temp_permanent_user_id'] 	= $q_r->social_temp_permanent_user_id;
				$items['social_temp_permanent_user_name'] 	= $q_r->permanent_user->username;
				
				foreach($q_r->dynamic_detail_social_logins as $i){
				
				    if($i->name == 'Facebook'){
				        $prefix = 'fb';
				    } 
				    if($i->name == 'Google'){
				        $prefix = 'gp';
				    }
				    if($i->name == 'Twitter'){
				        $prefix = 'tw';
				    }
					$items[$prefix.'_enable'] 		= $i->enable;
					$items[$prefix.'_record_info'] 	= $i->record_info;
					$items[$prefix.'_id'] 			= $i->special_key;
					$items[$prefix.'_secret'] 		= $i->secret;
					$items[$prefix.'_active'] 		= $i->enable;
					$items[$prefix.'_profile'] 	    = intval($i->profile_id);
					$items[$prefix.'_profile_name'] = $i->profile->name;
					$items[$prefix.'_realm'] 		= intval($i->realm_id);
					$items[$prefix.'_realm_name'] 	= $i->realm->name;
					$items[$prefix.'_voucher_or_user']= $i->type;

				}
            }
            
        }
        
        $this->set(array(
            'data'     => $items,
            'success'   => true,
            '_serialize'=> array('success', 'data')
        ));
	}
	
	public function editSocialLogin(){
	
	    //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        
        $check_items = array(
			'social_enable',
			'fb_enable',
			'gp_enable',
			'tw_enable',
			'fb_record_info',
			'gp_record_info',
			'tw_record_info'
		);
		
        foreach($check_items as $i){
            if(isset($this->request->data[$i])){
                $this->request->data[$i] = 1;
            }else{
                $this->request->data[$i] = 0;
            }
        }
        
        $prefixes = ['fb','gp','tw'];

		//We have to have a temp user else we fail it
		if(($this->request->data['social_enable'] == 1)&&($this->request->data['social_temp_permanent_user_id'] == '')){
			 $this->set(array(
                'errors'    => array('social_temp_permanent_user_id' => "Temp user cannot be empty"),
                'success'   => false,
                'message'   => array('message' => 'Could not save data'),
                '_serialize' => array('errors','success','message')
            ));
			return;
		}


        //Sanity checks
        foreach($prefixes as $p){
            if(
			    ($this->request->data['social_enable'] == 1)&&
			    ($this->request->data[$p.'_enable'] == 1)
		    ){

			    $fb_check_for  = array($p.'_voucher_or_user',$p.'_secret',$p.'_id',$p.'_realm',$p.'_profile');
			    foreach($fb_check_for as $i){
				    if($this->request->data["$i"] == ''){
					    $this->set(array(
				            'errors'    => array("$i" => $i." is required"),
				            'success'   => false,
				            'message'   => array('message' => 'Could not save data'),
				            '_serialize' => array('errors','success','message')
				        ));
					    return;
				    }
			    }
		    }
        
        }

		//If it got here without a return we can surely then add the social logins the user defined
		//First we delete the existing ones:
		$id = $this->request->data['id'];
		
		$entity = $this->{$this->main_model}->get($this->request->data['id']);
        $this->{$this->main_model}->patchEntity($entity, $this->request->data());
		
		$this->DynamicDetailSocialLogins->deleteAll(['DynamicDetailSocialLogins.dynamic_detail_id' => $id], true);


		if ($this->{$this->main_model}->save($entity)) {
			if($this->request->data['social_enable'] == 0){
			
				//if not enabled we don't care ....
				$this->set(array(
		            'success' => true,
		            '_serialize' => array('success')
		        ));
				return;
			}
			
			foreach($prefixes as $p){
			
			    if($this->request->data[$p.'_enable'] == 1){
				    //Facebook
				    
				    if($p == 'fb'){
				        $name = 'Facebook';   
				    }
				    if($p == 'gp'){
				        $name = 'Google';   
				    }
				    if($p == 'tw'){
				        $name = 'Twitter';   
				    }
				    
				    $data = array();
				    $data['name'] 				= $name;
				    $data['dynamic_detail_id'] 	= $id;
				    $data['profile_id'] 		= $this->request->data["$p"."_profile"];
				    $data['type'] 				= $this->request->data["$p"."_voucher_or_user"];   
				    $data['special_key'] 		= $this->request->data["$p"."_id"];   
				    $data['secret'] 			= $this->request->data["$p"."_secret"]; 
				    $data['realm_id'] 			= $this->request->data["$p"."_realm"];
				    $data['secret'] 			= $this->request->data["$p"."_secret"];
				    $data['record_info']		= $this->request->data["$p"."_record_info"];
				    $data['enable']				= $this->request->data["$p"."_enable"];	
				    	    
				    $entity                     = $this->DynamicDetailSocialLogins->newEntity($data);  
                    $this->DynamicDetailSocialLogins->save($entity);             
			    }
			}
			
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));

        } else {

            $message = 'Error';
            $this->set(array(
                'errors'    => $this->{$this->modelClass}->validationErrors,
                'success'   => false,
                'message'   => array('message' => 'Could not save data'),
                '_serialize' => array('errors','success','message')
            ));
        }
	}
    
    

    public function availableThemes(){
 
        $items = array();
        Configure::load('DynamicLogin','default'); 
        $data       = Configure::read('DynamicLogin.theme');
        foreach(array_keys($data) as $i){
            array_push($items, array('name' => $i,'id' => $i));   
        }
        
        if(
            (isset($this->request->query['exclude_custom']))&&
            ($this->request->query['exclude_custom'] == 'true')
        ){
           array_shift($items); //Remove the first item which will be "Custom" in the config file
        }
            
        $this->set(array(
            'items' => $items,
            'success' => true,
            '_serialize' => array('items','success')
        ));
    }

	 
    public function menuForGrid(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $menu = $this->GridButtons->returnButtons($user,true,'dynamic_details');
        $this->set(array(
            'items'         => $menu,
            'success'       => true,
            '_serialize'    => array('items','success')
        ));
    }
    
    public function menuForPhotos(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $menu = $this->GridButtons->returnButtons($user,false,'basic_no_disabled');
        $this->set(array(
            'items'         => $menu,
            'success'       => true,
            '_serialize'    => array('items','success')
        ));
    }
    
    public function menuForDynamicPages(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $menu = $this->GridButtons->returnButtons($user,false,'basic_no_disabled');
        $this->set(array(
            'items'         => $menu,
            'success'       => true,
            '_serialize'    => array('items','success')
        ));
    }
    
    public function menuForDynamicPairs(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $menu = $this->GridButtons->returnButtons($user,false,'basic_no_disabled');
        $this->set(array(
            'items'         => $menu,
            'success'       => true,
            '_serialize'    => array('items','success')
        ));
    }
    
    
    public function noteIndex(){
        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $items = $this->Notes->index($user); 
    }
    
    public function noteAdd(){
        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }   
        $this->Notes->add($user);
    }
    
    public function noteDel(){  
        if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $this->Notes->del($user);
    }
    
    private function _find_username_and_password($id){
    
        $this->loadModel('PermanentUsers');

		$q_r = $this->PermanentUsers->get($id);
		$user_data = array('username' => 'notfound','password' => 'notfound');
		if($q_r){
			$un                     = $q_r->username;
			$user_data['username']  = $un;	
			$this->loadModel('Radchecks');
			$q_pw = $this->Radchecks
			    ->find() 
				->where(['Radchecks.username' => $un,'Radchecks.attribute' => 'Cleartext-Password'])
				->first();

			if($q_pw){
				$user_data['password'] = $q_pw->value;
			}
		}
		return $user_data;
	}
	
	private function _doBrowserDetectFor($captive_portal = 'coova' ){ //Can be 'coova' / 'mikrotik' / 'ruckus'
   
        $conditions     = array("OR" =>array());
		$query_string   = $_SERVER['QUERY_STRING'];
				
		if($this->request->is('post')){
		    $q_array = array();
		    foreach(array_keys($this->request->data) as $key){
		        $q_array[$key] = $this->request->data[$key];
		        array_push($conditions["OR"],
                    ["DynamicPairs.name" => $key, "DynamicPairs.value" =>  $this->request->data[$key]]
                ); //OR query all the keys
		     }     
		     $query_string =  http_build_query($q_array);
		}else{
		    foreach(array_keys($this->request->query) as $key){
                    array_push($conditions["OR"],
                        ["DynamicPairs.name" => $key, "DynamicPairs.value" =>  $this->request->query[$key]]
                    ); //OR query all the keys
           	}
        }

       	$q_r = $this->DynamicPairs
       	    ->find()
       	    ->contain('DynamicDetails')
       	    ->where($conditions)
       	    ->order(['DynamicPairs.priority' => 'DESC'])
       	    ->first();
      	
		//See which Theme are selected
		$theme          = 'Default';
		$theme_selected = 'Default';	
		$i18n           = 'en_GB';
		if($q_r){
            $theme_selected =  $q_r->dynamic_detail->theme;
            if($q_r->dynamic_detail->default_language != ''){
                $i18n = $q_r->dynamic_detail->default_language;
            }
		}
		if (strpos($query_string, '&i18n') == false){
		    $query_string = $query_string."&i18n=$i18n";
	    }
		
        if($theme_selected == 'Custom'){ //With custom themes we read the valuse out of the DB
        
            //ruckus is special
            if($captive_portal == 'ruckus'){
                $captive_portal = 'coova'; //We use Coova's URLs for the Custom Ruckus
            }
        
		    $redir_to = $q_r->dynamic_detail->{$captive_portal."_desktop_url"}.'?'.$query_string;
		    if($this->request->is('mobile')){
                $redir_to = $q_r->dynamic_detail->{$captive_portal."_mobile_url"}.'?'.$query_string;
            }    
		}else{  //Else we fetch the 'global' theme's value from the file

     	    Configure::load('DynamicLogin','default');
            $pages       = Configure::read('DynamicLogin.theme.'.$theme_selected); //Read the defaults
		    if(!$pages){
			    $pages       = Configure::read('DynamicLogin.theme.'.$theme); //Read the defaults
		    }

		    $redir_to = $pages[$captive_portal.'_desktop'].'?'.$query_string;
            if($this->request->is('mobile')){
                $redir_to = $pages[$captive_portal.'_mobile'].'?'.$query_string;
            }
        }     
        return $redir_to;     
    }
    
    private function _doPreviewChilli(){
	
	    if(isset($this->request->query['wizard_name'])){
	        $w_name = $this->request->query['wizard_name'];
	        $q_r = $this->{$this->main_model}
                ->find()
                ->where([$this->main_model.'.name' => $w_name])
                ->first();
		    if($q_r){
		        $this->request->query['dynamic_id'] = $q_r->id;
		        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'].'&dynamic_id='.$this->request->query['dynamic_id'].'&uamip=10.1.0.1&uamport=3990';
		    }   
	    }else{
		    $q_r = $this->{$this->modelClass}->get($this->request->query['dynamic_id']);
        }
      	
		//See which Theme are selected
		$theme = 'Default';
		$i18n = 'en_GB';
		if($q_r){
            $theme_selected =  $q_r->theme;
            if($q_r->default_language != ''){
                $i18n = $q_r->default_language;
            }
		}

		if($theme_selected == 'Custom'){ //With custom themes we read the valuse out of the DB
		
		    $redir_to = $q_r['DynamicDetail']['coova_desktop_url'].'?'.$_SERVER['QUERY_STRING']."&i18n=$i18n";
		    
        }else{   
		    Configure::load('DynamicLogin','default'); 
            $pages       = Configure::read('DynamicLogin.theme.'.$theme_selected); //Read the defaults
		    if(!$pages){
			    $pages       = Configure::read('DynamicLogin.theme.'.$theme); //Read the defaults
		    }
		    $redir_to = $pages['coova_desktop'].'?'.$_SERVER['QUERY_STRING']."&i18n=$i18n";
		}
        return $redir_to;
	}	
}
