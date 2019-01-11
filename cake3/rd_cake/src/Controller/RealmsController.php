<?php

namespace App\Controller;
use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;

use Cake\Utility\Inflector;


class RealmsController extends AppController{
  
    protected $base  = "Access Providers/Controllers/Realms/";
    
    protected $owner_tree = array();
    protected $main_model = 'Realms';
  
    public function initialize(){  
        parent::initialize();
        $this->loadModel('Realms');     
        $this->loadModel('Users');
        
        $this->loadModel('NaRealms');
        $this->loadModel('DynamicClientRealms');
           
        $this->loadComponent('Aa');
        $this->loadComponent('GridButtons');
        $this->loadComponent('CommonQuery', [ //Very important to specify the Model
            'model' => 'Realms'
        ]);
        
        $this->loadComponent('Notes', [
            'model'     => 'RealmNotes',
            'condition' => 'realm_id'
        ]);
        $this->loadComponent('TimeCalculations');  
        
        $this->loadComponent('RealmAcl');        
    }
    
    public function indexApCreate(){
    //This method will display the Access Provider we are looking at's list of realms which it has create rights for
    //This will be used to display in the Wizard of available Realms in the Create screens of Vouchers; Permanent Users; and Devices
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        $this->_doApListFor($user,'create');    
    }
    
    public function indexApUpdate(){
    //This method will display the Access Provider we are looking at's list of realms which it has update rights for
    //This will be used to display in the Wizard of available Realms in the Create screens of Vouchers; Permanent Users; and Devices
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        $this->_doApListFor($user,'update');    
    }
    
    
     public function indexAp(){
    //This method will display the Access Provider we are looking at's list of available realms.
    //This will be:
    // ALL the upstream AP's who has realms that is flagged as 'available_to_siblings' (And That's it :-))
    //The Access Provider creating a sub-provider can create a private realm that is not listed in any of the sub-provider's lists (but that is under the realms application)
    //We will also go through each of these ones to determine if the AP has CRUD access to the realm and reflect it in the feedback...

        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }

        $user_id = null;
        if($user['group_name'] == Configure::read('group.admin')){  //Admin
            $user_id = $user['id'];
        }

        if($user['group_name'] == Configure::read('group.ap')){  //Or AP
            $user_id = $user['id'];
        }
        $items      = array();

        if(isset($this->request->query['ap_id'])){
            $ap_id      = $this->request->query['ap_id'];
            //Get all the parents up to the root
            $q_r = $this->Users->find('path',['for' => $ap_id]); 
            foreach($q_r as $i){        
                $user_id    = $i->id;
                if($user_id != $ap_id){ //Any parents the user itself will have full rights and we do that in the last step

                    $r  = $this->{$this->main_model}
                            ->find()
                            ->where(['Realms.user_id' => $user_id, 'Realms.available_to_siblings' => true])
                            ->all();
                    foreach($r  as $j){
                        $id     = $j->id;
                        $name   = $j->name;
                        
                        $create = false;
                        $read   = false;
                        $update = false;
                        $delete = false;
                        
                        $temp_debug = Configure::read('debug');
                        Configure::write('debug', 0); // turn off debugging
                        try{
                            $create = $this->Acl->check(
                                array('model' => 'User', 'foreign_key' => $ap_id), 
                                array('model' => 'Realms','foreign_key' => $id), 'create'); //Only if they have create right
                        }catch(\Exception $e){               
                             $create = false;  
                        }
                        
                        try{
                            $read = $this->Acl->check(
                                array('model' => 'User', 'foreign_key' => $ap_id), 
                                array('model' => 'Realms','foreign_key' => $id), 'read'); //Only if they have create right
                        }catch(\Exception $e){               
                             $read = false;  
                        }
                        
                        try{
                            $update = $this->Acl->check(
                                array('model' => 'User', 'foreign_key' => $ap_id), 
                                array('model' => 'Realms','foreign_key' => $id), 'update'); //Only if they have create right
                        }catch(\Exception $e){               
                             $update = false;  
                        }
                       
                        try{
                            $delete = $this->Acl->check(
                                array('model' => 'User', 'foreign_key' => $ap_id), 
                                array('model' => 'Realms','foreign_key' => $id), 'delete'); //Only if they have create right
                        }catch(\Exception $e){               
                             $delete = false;  
                        }
                                               
                        Configure::write('debug', $temp_debug); // return previous setting 
                                              
                        array_push($items,array(
                            'id'        => $id, 
                            'name'      => $name, 
                            'create'    => $create, 
                            'read'      => $read, 
                            'update'    => $update, 
                            'delete'    => $delete
                        ));
                    }
                    
                }   
            }
        
            //All the realms owned by anyone this access provider created (and also itself) 
            //will automatically be under full controll of this access provider
            $tree_array     = [['Realms.user_id' => $ap_id]]; //Itself
            $this->children = $this->Users->find('children', ['for' => $ap_id]);
            
            if($this->children){   //Only if the AP has any children...
                foreach($this->children as $i){
                    $id = $i['id'];
                    array_push($tree_array,array('Realms.user_id' => $id));
                }       
            }  
            $r_sub  = $this->Realms->find()->where(['OR' => $tree_array])->all();
            foreach($r_sub  as $j){
                $id     = $j->id;
                $name   = $j->name;
                array_push($items,array('id' => $id, 'name' => $name, 'create' => true, 'read' => true, 'update' => true, 'delete' => true));
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
        $this->CommonQuery->build_common_query($query,$user,['Users','RealmNotes' => ['Notes']]);
        
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
                        foreach($i->realm_notes as $un){
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
        $query = $this->{$this->main_model}->find();

        $this->CommonQuery->build_common_query($query,$user,['Users','RealmNotes' => ['Notes']]);
 
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
         
            //------------------------
            //We only list realms which the Access Provider has read rights to at least    
            $id         = $i->{"id"};
            $owner_id   = $i->{"user_id"};
            if($owner_id !== $user_id){     
                if(!$this->RealmAcl->can_manage_realm($user_id,$owner_id, $id)){
                    continue;
                }
            }
            //-----------------------
            if(!array_key_exists($owner_id,$this->owner_tree)){
                $owner_tree     = $this->Users->find_parents($owner_id);
            }else{
                $owner_tree = $this->owner_tree[$owner_id];
            }
            
            $action_flags   = $this->Aa->get_action_flags($owner_id,$user);   
            
            $notes_flag     = false;
            foreach($i->realm_notes as $un){
                if(!$this->Aa->test_for_private_parent($un->note,$user)){
                    $notes_flag = true;
                    break;
                }
            }
           
            
            $row        = array();
            $fields     = $this->{$this->main_model}->schema()->columns();
            foreach($fields as $field){
                $row["$field"]= $i->{"$field"};
                
                if($field == 'created'){
                   // print_r($i->{"$field"}->i18nFormat('yyyy-MM-dd HH:mm:ss','Africa/Johannesburg'));
                   // print_r($i->{"$field"}->listTimezones());
                    $row['created_in_words'] = $this->TimeCalculations->time_elapsed_string($i->{"$field"});
                }
                if($field == 'modified'){
                    $row['modified_in_words'] = $this->TimeCalculations->time_elapsed_string($i->{"$field"});
                }   
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
    
    public function add(){
    
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $this->_addOrEdit($user,'add');
        
    }
    
    public function edit(){
    
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }
        $this->_addOrEdit($user,'edit');
        
    }
     
    private function _addOrEdit($user,$type= 'add') {

        //__ Authentication + Authorization __
        
        $user_id    = $user['id'];

        //Get the creator's id
        if(isset($this->request->data['user_id'])){
            if($this->request->data['user_id'] == '0'){ //This is the holder of the token - override '0'
                $this->request->data['user_id'] = $user_id;
            }
        }

        $check_items = array(
			'available_to_siblings',
			'suffix_permanent_users',
			'suffix_vouchers',
            'suffix_devices'
		);
		
        foreach($check_items as $i){
            if(isset($this->request->data[$i])){
                $this->request->data[$i] = 1;
            }else{
                $this->request->data[$i] = 0;
            }
        }
       
        if($type == 'add'){ 
            $entity = $this->{$this->main_model}->newEntity($this->request->data());
        }
       
        if($type == 'edit'){
            $entity = $this->{$this->main_model}->get($this->request->data['id']);
            $this->{$this->main_model}->patchEntity($entity, $this->request->data());
        }
              
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
                'message'   => array('message' => __('Could not create item')),
                '_serialize' => array('errors','success','message')
            ));
        }
	}
	
	public function view(){
        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }

        $items = array();       
        //Fields
		$fields = $this->{$this->main_model}->schema()->columns();
             
        if(isset($this->request->query['realm_id'])){
            $q_r = $this->{$this->main_model}->find()->where([$this->main_model.'.id' => $this->request->query['realm_id']])->first();
            if($q_r){    
                $owner_tree         = $this->Users->find_parents($q_r->user_id);
                $items['owner']     = $owner_tree;      
                foreach($fields as $field){
	                $items["$field"]= $q_r->{"$field"};
		        }  
            }
        }
        
        $this->set(array(
            'data'      => $items,
            'success'   => true,
            '_serialize'=> array('success', 'data')
        ));
    }
	
	
    public function menuForGrid(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
         
        $menu = $this->GridButtons->returnButtons($user,true,'realms'); //No "Action" title basic refresh/add/delete/edit
        $this->set(array(
            'items'         => $menu,
            'success'       => true,
            '_serialize'    => array('items','success')
        ));
    }
 
    public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        //__ Authentication + Authorization __
        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }

        $user_id    = $user['id'];
        $fail_flag = false;

	    if(isset($this->request->data['id'])){   //Single item delete
            $message = "Single item ".$this->request->data['id'];

            //NOTE: we first check of the user_id is the logged in user OR a sibling of them:         
            $entity     = $this->{$this->main_model}->get($this->request->data['id']);   
            $owner_id   = $entity->user_id;
            
            if($owner_id != $user_id){
                if($this->Users->is_sibling_of($user_id,$owner_id)== true){
                    $this->{$this->main_model}->delete($entity);
                }else{
                    $fail_flag = true;
                }
            }else{
                $this->{$this->main_model}->delete($entity);
            }
   
        }else{                          //Assume multiple item delete
            foreach($this->request->data as $d){
                $entity     = $this->{$this->main_model}->get($d['id']);  
                $owner_id   = $entity->user_id;
                if($owner_id != $user_id){
                    if($this->Users->is_sibling_of($user_id,$owner_id) == true){
                        $this->{$this->main_model}->delete($entity);
                    }else{
                        $fail_flag = true;
                    }
                }else{
                    $this->{$this->main_model}->delete($entity);
                }
            }
        }

        if($fail_flag == true){
            $this->set(array(
                'success'   => false,
                'message'   => array('message' => __('Could not delete some items')),
                '_serialize' => array('success','message')
            ));
        }else{
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }
	}
	
	public function uploadLogo($id = null){

        //This is a deviation from the standard JSON serialize view since extjs requires a html type reply when files
        //are posted to the server.    
        $this->viewBuilder()->layout('ext_file_upload');

        $path_parts     = pathinfo($_FILES['photo']['name']);
        $unique         = time();
        $dest           = WWW_ROOT."/img/realms/".$unique.'.'.$path_parts['extension'];
        $dest_www       = "/cake3/rd_cake/webroot/img/realms/".$unique.'.'.$path_parts['extension'];
       
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
            $file_to_delete = WWW_ROOT."/img/realms/".$old_file;
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
    
    public function listRealmsForNasOwner(){

        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }

        $user_id = null;
        if($user['group_name'] == Configure::read('group.admin')){  //Admin
            $user_id = $user['id'];
        }

        if($user['group_name'] == Configure::read('group.ap')){  //Or AP
            $user_id = $user['id'];
        }

        if(isset($this->request->query['owner_id'])){

            //Check if it was 0 -> which means it is the current user
            if($this->request->query['owner_id'] == 0){
                $owner_id = $user_id;
            }else{
                $owner_id = $this->request->query['owner_id'];
            }
        }

        if(isset($this->request->query['available_to_siblings'])){
            $a_to_s      = $this->request->query['available_to_siblings'];  
        }

        //By default nas_id not included
        $nas_id = false;
        if(isset($this->request->query['nas_id'])){
            $nas_id      = $this->request->query['nas_id'];  
        }

        //========== CLEAR FIRST CHECK =======
        //By default clear_flag is not included
        $clear_flag = false;
        if(isset($this->request->query['clear_flag'])){
            if($this->request->query['clear_flag'] == 'true'){
                $clear_flag = true;
            }
        }

        if($clear_flag){    //If we first need to remove previous associations! 
            $this->NaRealms->deleteAll(['NaRealms.na_id' => $nas_id]);
        }
        //========== END CLEAR FIRST CHECK =======

        $items = array();

        //if $a_to_s is false we need to find the chain upwards to root and seek the public realms
        if($a_to_s == 'false'){
                    
            $q_r = $this->Users->find('path',['for' => $owner_id]);
            
            foreach($q_r as $i){
                $user_id = $i->id;
                
                if($owner_id != $user_id){
                
                    $q = $this->Realms
                        ->find()
                        ->contain(['NaRealms'])
                        ->where(['Realms.user_id' => $owner_id,'Realms.available_to_siblings' => true])
                        ->all();
                       
                    foreach($q as $j){ 
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($nas_id){
                            foreach($j->na_realms as $nr){
                                if($nr->na_id == $nas_id){
                                    $selected = true;
                                }
                            }
                        }
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));  
                    }
                
                }
                
                //When it got down to the owner; also get the private realms
                if($user_id == $owner_id){
                    $q = $this->Realms
                        ->find()
                        ->contain(['NaRealms'])
                        ->where(['Realms.user_id' => $owner_id])
                        ->all();
                       
                    foreach($q as $j){ 
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($nas_id){
                            foreach($j->na_realms as $nr){
                                if($nr->na_id == $nas_id){
                                    $selected = true;
                                }
                            }
                        }
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));  
                    }
                }
            }
        }

        //If $a_to_s is true, we neet to find the chain downwards to list ALL the realms of belonging to children of the owner
        if($a_to_s == 'true'){

            //First find all the realms beloning to the owner:
            $q = $this->Realms
                    ->find()
                    ->contain(['NaRealms'])
                    ->where(['Realms.user_id' => $owner_id])
                    ->all();
            foreach($q as $j){
                $selected = false;
                //Check if the nas is not already assigned to this realm
                if($nas_id){
                    foreach($j->na_realms as $nr){
                        if($nr->na_id == $nas_id){
                            $selected = true;
                        }
                    }
                }
                array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));                
            }
            
            //Now get all the realms of the siblings of the owner
            $children = $this->Users->find('children', ['for' => $owner_id]);

            if($children){   //Only if the AP has any children...
                foreach($children as $i){
                    $user_id = $i->id;
                    $q = $this->Realms
                        ->find()
                        ->contain(['NaRealms'])
                        ->where(['Realms.user_id' => $user_id])
                        ->all();
                  
                    foreach($q as $j){
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($nas_id){
                            foreach($j->na_realms as $nr){
                                if($nr->na_id == $nas_id){
                                    $selected = true;
                                }
                            }
                        }   
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));                
                    }
                }       
            }               
        }
       
        $this->set(array(
            'items'     => $items,
            'success'   => true,
            '_serialize' => array('items','success')
        ));
    }
    
    public function updateNaRealm(){

        if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }

        if(isset($this->request->query['nas_id'])){
            $nas_id     = $this->request->query['nas_id'];
	        if(isset($this->request->data['id'])){   //Single item select
                $realm_id   = $this->request->data['id'];
                if($this->request->data['selected']){
                
                    $entity = $this->NaRealms->newEntity();
                    $entity->na_id = $nas_id;
                    $entity->realm_id =  $realm_id;
                    $this->NaRealms->save($entity);
                }else{      
                    $this->NaRealms->deleteAll(['NaRealms.na_id' => $nas_id,'NaRealms.realm_id' => $realm_id]);            
                }
            }else{                          //Assume multiple item select
                foreach($this->request->data as $d){
                    if(isset($d['id'])){   //Single item select
                        $realm_id   = $d['id'];
                        if($d['selected']){
                            $entity = $this->NaRealms->newEntity();
                            $entity->na_id = $nas_id;
                            $entity->realm_id =  $realm_id;
                            $this->NaRealms->save($entity);
                        }else{
                        
                            $this->NaRealms->deleteAll(['NaRealms.na_id' => $nas_id,'NaRealms.realm_id' => $realm_id]);        
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
    
    
    public function listRealmsForDynamicClientOwner(){

        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }

        $user_id = null;
        if($user['group_name'] == Configure::read('group.admin')){  //Admin
            $user_id = $user['id'];
        }

        if($user['group_name'] == Configure::read('group.ap')){  //Or AP
            $user_id = $user['id'];
        }

        if(isset($this->request->query['owner_id'])){

            //Check if it was 0 -> which means it is the current user
            if($this->request->query['owner_id'] == 0){
                $owner_id = $user_id;
            }else{
                $owner_id = $this->request->query['owner_id'];
            }
        }

        if(isset($this->request->query['available_to_siblings'])){
            $a_to_s      = $this->request->query['available_to_siblings'];  
        }

        //By default nas_id not included
        $dynamic_client_id = false;
        if(isset($this->request->query['dynamic_client_id'])){
            $dynamic_client_id      = $this->request->query['dynamic_client_id'];  
        }

        //========== CLEAR FIRST CHECK =======
        //By default clear_flag is not included
        $clear_flag = false;
        if(isset($this->request->query['clear_flag'])){
            if($this->request->query['clear_flag'] == 'true'){
                $clear_flag = true;
            }
        }

        if($clear_flag){    //If we first need to remove previous associations! 
            $this->DynamicClientRealms->deleteAll(['DynamicClientRealms.dynamic_client_id' => $dynamic_client_id]);
        }
        //========== END CLEAR FIRST CHECK =======

        $items = array();

        //if $a_to_s is false we need to find the chain upwards to root and seek the public realms
        if($a_to_s == 'false'){
        
        
            $q_r = $this->Users->find('path',['for' => $owner_id]);
            
            foreach($q_r as $i){
                $user_id = $i->id;
                
                if($owner_id != $user_id){
                
                    $q = $this->Realms
                        ->find()
                        ->contain(['DynamicClientRealms'])
                        ->where(['Realms.user_id' => $owner_id,'Realms.available_to_siblings' => true])
                        ->all();
                       
                    foreach($q as $j){ 
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($dynamic_client_id){
                            foreach($j->dynamic_client_realms as $nr){
                                if($nr->dynamic_client_id == $dynamic_client_id){
                                    $selected = true;
                                }
                            }
                        }
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));  
                    }
                
                }
                
                //When it got down to the owner; also get the private realms
                if($user_id == $owner_id){
                    $q = $this->Realms
                        ->find()
                        ->contain(['DynamicClientRealms'])
                        ->where(['Realms.user_id' => $owner_id])
                        ->all();
                       
                    foreach($q as $j){ 
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($dynamic_client_id){
                            foreach($j->dynamic_client_realms as $nr){
                                if($nr->dynamic_client_id == $dynamic_client_id){
                                    $selected = true;
                                }
                            }
                        }
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));  
                    }
                }
            }    
        }

        //If $a_to_s is true, we neet to find the chain downwards to list ALL the realms of belonging to children of the owner
        if($a_to_s == 'true'){
          
             //First find all the realms beloning to the owner:
            $q = $this->Realms
                    ->find()
                    ->contain(['DynamicClientRealms'])
                    ->where(['Realms.user_id' => $owner_id])
                    ->all();
            foreach($q as $j){
                $selected = false;
                //Check if the nas is not already assigned to this realm
                if($dynamic_client_id){
                    foreach($j->dynamic_client_realms as $nr){
                        if($nr->dynamic_client_id == $dynamic_client_id){
                            $selected = true;
                        }
                    }
                }
                array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));                
            }
            
            //Now get all the realms of the siblings of the owner
            $children = $this->Users->find('children', ['for' => $owner_id]);

            if($children){   //Only if the AP has any children...
                foreach($children as $i){
                    $user_id = $i->id;
                    $q = $this->Realms
                        ->find()
                        ->contain(['DynamicClientRealms'])
                        ->where(['Realms.user_id' => $user_id])
                        ->all();
                  
                    foreach($q as $j){
                        $selected = false;
                        //Check if the nas is not already assigned to this realm
                        if($dynamic_client_id){
                            foreach($j->dynamic_client_realms as $nr){
                                if($nr->dynamic_client_id == $dynamic_client_id){
                                    $selected = true;
                                }
                            }
                        }   
                        array_push($items,array('id' => $j->id, 'name' => $j->name,'selected' => $selected));                
                    }
                }       
            }               
        }
       
        $this->set(array(
            'items'     => $items,
            'success'   => true,
            '_serialize' => array('items','success')
        ));
    }
    
    public function updateDynamicClientRealm(){
    
    
        if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        $user = $this->_ap_right_check();
        if(!$user){
            return;
        }

        if(isset($this->request->query['dynamic_client_id'])){
            $dynamic_client_id     = $this->request->query['dynamic_client_id'];
	        if(isset($this->request->data['id'])){   //Single item select
                $realm_id   = $this->request->data['id'];
                if($this->request->data['selected']){
                    $entity = $this->DynamicClientRealms->newEntity();
                    $entity->dynamic_client_id = $dynamic_client_id;
                    $entity->realm_id =  $realm_id;
                    $this->DynamicClientRealms->save($entity);
                }else{
                    $this->DynamicClientRealms->deleteAll(
                        ['DynamicClientRealms.dynamic_client_id' => $dynamic_client_id,'DynamicClientRealms.realm_id' => $realm_id]
                    );        
                }
            }else{                          //Assume multiple item select
                foreach($this->request->data as $d){
                    if(isset($d['id'])){   //Single item select
                        $realm_id   = $d['id'];
                        if($d['selected']){
                            $entity = $this->DynamicClientRealms->newEntity();
                            $entity->dynamic_client_id = $dynamic_client_id;
                            $entity->realm_id =  $realm_id;
                            $this->DynamicClientRealms->save($entity);
                        }else{
                            $this->DynamicClientRealms->deleteAll(
                                ['DynamicClientRealms.dynamic_client_id' => $dynamic_client_id,'DynamicClientRealms.realm_id' => $realm_id]
                            );        
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
     
    public function editAp(){

        //The ap_id who's realm rights HAS to be a sibling of the user who initiated the request
        //___AA Check Starts ___
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        $user_id = null;
        if($user['group_name'] == Configure::read('group.admin')){  //Admin
            $user_id = $user['id'];
        }elseif($user['group_name'] == Configure::read('group.ap')){  //Or AP
            $user_id = $user['id'];
        }else{
           $this->Aa->fail_no_rights($this);
           return;
        }
        //__ AA Check Ends ___

        if(isset($this->request->query['ap_id'])){

            //Make sure the $ap_id is a child of $user_id - perhaps we should sub-class the Behaviaour...
            //TODO Complete this check
            
            $temp_debug = Configure::read('debug');
            Configure::write('debug', 0); // turn off debugging
            
            $success = false;

            $ap_id  = $this->request->query['ap_id'];
            $id     = $this->request->data['id'];
            
            try{
                       
                if($this->request->data['create'] == true){
                    $this->Acl->allow(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'create');
                }else{
                    $this->Acl->deny(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'create');
                } 

                if($this->request->data['read'] == true){
                    $this->Acl->allow(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'read');
                }else{
                    $this->Acl->deny(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'read');
                }

                if($this->request->data['update'] == true){
                    $this->Acl->allow(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'update');
                }else{
                    $this->Acl->deny(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'update');
                } 
                
                if($this->request->data['delete'] == true){
                    $this->Acl->allow(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'delete');
                }else{
                    $this->Acl->deny(
                    array('model' => 'User', 'foreign_key' => $ap_id), 
                    array('model' => 'Realms','foreign_key' => $id), 'delete');
                } 
                $success = true;
                    
            }catch(\Exception $e){               
                $success = false;  
            }        
            Configure::write('debug', $temp_debug); // turn off debugging 
        }

        $this->set(array(
            'items' => array(),
            'success' => true,
            '_serialize' => array('items','success')
        ));
    }
    //____ END :: Access Providers application ______

    
    
    
    private function _doApListFor($user,$right = 'create'){
    
        $user_id    = null;
        $admin_flag = false;
    
        if($user['group_name'] == Configure::read('group.admin')){  //Admin
            $user_id    = $user['id'];
            $admin_flag = true;
        }

        if($user['group_name'] == Configure::read('group.ap')){  //Or AP
            $user_id = $user['id'];
        }
        $items      = array();
        
        $ap_id = false;
        if(isset($this->request->query['ap_id'])){
            $ap_id      = $this->request->query['ap_id'];
        }

        if(($admin_flag)&&($ap_id == false)){
            $r = $this->{$this->main_model}->find()->all();
            foreach($r as $j){
                $id     = $j->id;
                $name   = $j->name;
                array_push($items,array('id' => $id, 'name' => $name));
            }

        }else{
            //Access Providers needs more work...
            if($ap_id == false){
                $ap_id      = $user_id;
            }
            if($ap_id == 0){
                $ap_id = $user_id;
            }
            $q_r = $this->Users->find('path',['for' => $ap_id]);
                   
            foreach($q_r as $i){    
                $user_id    = $i->id; 
                if($user_id != $ap_id){ //Only parents not the user self
                    $r  = $this->{$this->main_model}
                            ->find()
                            ->where(['Realms.user_id' => $user_id, 'Realms.available_to_siblings' => true])
                            ->all();
                    foreach($r  as $j){
                        $id     = $j->id;
                        $name   = $j->name; 
                        $create = false;
                        
                        $temp_debug = Configure::read('debug');
                        Configure::write('debug', 0); // turn off debugging
                        
                        
                        try{
                            $create = $this->Acl->check(
                                array('model' => 'User', 'foreign_key' => $ap_id), 
                                array('model' => 'Realms','foreign_key' => $id), $right); //Only if they have create right
                        }catch(\Exception $e){               
                             $create = false;  
                        }
                        
                        if($create == true){
                            array_push($items,array('id' => $id, 'name' => $name));
                        }
                        
                        Configure::write('debug', $temp_debug); // turn off debugging
                    }
                }
            }

            //All the realms owned by anyone this access provider created (and also itself) 
            //will automatically be under full controll of this access provider           
            $this->children = $this->Users->find('children', ['for' => $ap_id]);
            
            $tree_array     = array(['Realms.user_id' => $ap_id]); //Start with itself
            
            if($this->children){   //Only if the AP has any children...
                foreach($this->children as $i){
                    $id = $i->id;                   
                    array_push($tree_array,['Realms.user_id' => $id]);
                }       
            } 
            $r_sub  = $this->{$this->main_model}
                    ->find()
                    ->where(['OR' => $tree_array])
                    ->all();
                    
            foreach($r_sub  as $j){
                $id     = $j->id;
                $name   = $j->name;
                array_push($items,array('id' => $id, 'name' => $name));
            }   
        }

        $this->set(array(
            'items' => $items,
            'success' => true,
            '_serialize' => array('items','success')
        ));
    }
    
}
