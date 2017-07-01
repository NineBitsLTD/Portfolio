<?php

namespace User\Model;

class User extends \DataBase\SourceProvider{
    function __construct() {
        parent::__construct('user');
    }
    
 /*   public function GetByPassword($name, $password){        
        $records = $this->Reg->DB->Query(
            "SELECT * FROM `".$this->GetTableName()."` WHERE (`email`='".$this->Escape($name).
            "' AND `email` IS NOT NULL AND `email` <> '') OR (`username`='".$this->Escape($name).
            "' AND `username` IS NOT NULL AND `username` <> '')");
        if($records->Count>0){
            if(\Sys\Helper::$Security->ValidatePassword($password, $records->Row['password_hash'], $this->Reg)){
                return $records;
            }
        }
        return new \Sys\DataBase\ActiveRecord($this->Reg, $this->Reg->DB->Provider);
    }
    public function GetByRolesStatuses($bin_status_pos=null, $roles_id=null, $where = ''){
        $wh = "WHERE";
        if($bin_status_pos!=null){
            $bin_status_pos = (int)$bin_status_pos;
            $wh .= " (LENGTH(BIN(`status`))>={$bin_status_pos} AND SUBSTRING(REVERSE(BIN(`status`)),{$bin_status_pos},1)='1')";
        } else  $wh .= " (`status` IS NOT NULL)";
        if($roles_id!=null){
            $roles_id = (int)$roles_id;
            $wh .= "AND (`roles_id`={$roles_id})";
        } else  $wh .= "AND (`roles_id` IS NOT NULL)";
        if($where!='') $wh .= "AND ({$where})";
        $sql = "SELECT * FROM `{$this->GetTableName()}` {$where}";
        $records = $this->Reg->DB->Query($sql);
        return $records;
    }    
    public function UserExists($email, $phone="", $login=null){
        if(isset($email) && $email!=""){
            $records = $this->Reg->DB->Query("SELECT * FROM `".$this->GetTableName()."` WHERE (`email` = '".$this->Escape($email).
            "' AND `email` IS NOT NULL AND `email` <> '')");
            if($records->Count>0) return true;
        } else {
            return 2;
        }
        return false;
    }    
    public function Add($data, $debug=false){
        $id = 0;
        $data['password_reset_token'] = \Sys\Helper::$Security->TokenCreate(16); 
        if(array_key_exists('group_id', $data)) unset($data['group_id']);
        if(array_key_exists('expirate_at', $data)) unset($data['expirate_at']);
        if(array_key_exists('updated_at', $data)) unset($data['updated_at']);
        if(array_key_exists('created_at', $data)) unset($data['created_at']);
        if(!array_key_exists('lng_default', $data)) $data['lng_default'] = $this->Reg->Lng->GetCode();
        if(!array_key_exists('user_id', $data)) $data['user_id'] = 1;
        if(!array_key_exists('sales_id', $data)) $data['sales_id'] = 0;
        $data['role_id'] = 7;
        $data['status'] = 0;
        $data['path'] = '0,1';
        $data['auth_key'] = \Sys\Helper::$Security->TokenCreate();
        $fields = $this->GetFields();
        $data['user_id'] = (int)$data['user_id'];
        $parent = $this->GetById($data['user_id']);
        if($parent->Count>0){
            $data['path']=$parent->Row['path'];
            if(isset($data['path'])) $data['path'] .=','.$data['user_id'];
            else $data['path'] =$data['user_id'];
        }
        $data['sales_id'] = (int)$data['sales_id'];             
        $sep="";
        $head="";
        $values="";
        foreach($fields as $field){
            if($field['name']!='id' && array_key_exists($field['name'], $data)){
                $head .= $sep . "`{$field['name']}`";
                if(is_null($data[$field['name']])) $values .= $sep . "NULL ";
                else if(!is_array($data[$field['name']])) {
                    if($field['name']=='password_hash') $values .= $sep ."'{$data[$field['name']]}' ";
                    else $values .= $sep ."'" . $this->Escape((string)$data[$field['name']]) . "' ";
                } else if($this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                    $values .= $sep ."'" . implode(',',  array_keys($data[$field['name']])) . "' ";
                } else $values .= $sep ."'' ";
                $sep = ",";
            }
        }                
        $sql="INSERT INTO `".$this->GetTableName()."` ({$head}) VALUES({$values})";
        if($debug) {
            print_r($sql); exit();                    
        }
        $this->Reg->DB->Query($sql);
        $id = $this->Reg->DB->GetLastId();
        foreach($fields as $field) 
        if($field['name']!='id' && array_key_exists($field['name'], $data) && 
            $this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
            $this->TranslateSave($field['name'], $id, $data[$field['name']]);
        }
        return [
            'id'=>$id,
            'user_id'=>$data['user_id'],
            'password_reset_token'=>$data['password_reset_token'],
        ];
    }
    public function SetPwd($id, $old_pwd, $pwd, $confirm_pwd){
        if(isset($this->Reg->Session) && !array_key_exists('msg', $this->Reg->Session->Data)) $this->Reg->Session->Data['msg']=[];
        $user = $this->GetById($id);
        if($user->Count<1 || !\Sys\Helper::$Security->ValidatePassword($old_pwd, $user->Row['password_hash'], $this->Reg)){
            if(isset($this->Reg->Session)){
                if(!array_key_exists('error', $this->Reg->Session->Data['msg'])) $this->Reg->Session->Data['msg']['error']="";
                $this->Reg->Session->Data['msg']['error'] .= "Не верный старый пароль. ";
            }
            return;
        }
        if($pwd!=$confirm_pwd){
            if(isset($this->Reg->Session)){
                if(!array_key_exists('error', $this->Reg->Session->Data['msg'])) $this->Reg->Session->Data['msg']['error']="";
                $this->Reg->Session->Data['msg']['error'] .= "Новый пароль и подтверждение пароля не совпадают. ";
            }
            return;
        }
        $hash = \Sys\Helper::$Security->PasswordHash($pwd);
        $sql="UPDATE `{$this->GetTableName()}` SET `password_hash` = '{$hash}' WHERE `id` = {$id}";
        $this->Reg->DB->Query($sql);
        if(isset($this->Reg->Session)){
            if(!array_key_exists('success', $this->Reg->Session->Data['msg'])) $this->Reg->Session->Data['msg']['success']="";
            $this->Reg->Session->Data['msg']['success'] .= "Пароль успешно изменен. ";
        }
    }
    public function Update($data){
        if(array_key_exists('id', $data)){
            $sql="UPDATE `{$this->GetTableName()}` SET `updated_at` = CURRENT_TIMESTAMP".
                (array_key_exists('auth_key', $data)?", `auth_key`='".$data['auth_key']."'":'').
                (array_key_exists('ip', $data)?", `ip`='".$data['ip']."'":'').
                (array_key_exists('password', $data)?", `password_hash`='".\Sys\Helper::$Security->PasswordHash($data['password'])."'":'').
                (array_key_exists('password_reset_token', $data)?", `password_reset_token`='".$data['password_reset_token']."'":', `password_reset_token`=NULL').
                " WHERE `id` = {$data['id']}";
            $this->Reg->DB->Query($sql);
        }
    }
    public function Forgot($data){
        if(array_key_exists('email', $data)){
            $sql="UPDATE `{$this->GetTableName()}` SET `updated_at` = CURRENT_TIMESTAMP".
                (array_key_exists('password_reset_token', $data)?", `password_reset_token`='".$data['password_reset_token']."'":'`password_reset_token` = NULL').
                " WHERE `email` = '".$this->Escape($data['email'])."'";
            $this->Reg->DB->Query($sql);
            $records = $this->Reg->DB->Query("SELECT * FROM `{$this->GetTableName()}` WHERE `email` = '".$this->Escape($data['email'])."'");
            return $records->Row;
        }
        return [];
    }
    public function Save($data, $where = "", $debug = false, $limit=0) {
        $phones=null;
        $social=null;
        if(isset($data) && \Sys\Helper::$Array->KeyIsSet('item', $data) && is_array($data['item'])){
            if(array_key_exists('phones', $data['item']) && is_array($data['item']['phones'])){        
                $phones = $data['item']['phones'];
                unset($data['item']['phones']);
            }
            if(array_key_exists('social', $data['item']) && is_array($data['item']['social'])){        
                $social = $data['item']['social'];
                unset($data['item']['social']);
            }
        }
        
        if($debug) print_r($data);
        $id = "";
        if(isset($this->Reg->Session) && !array_key_exists('msg', $this->Reg->Session->Data)) $this->Reg->Session->Data['msg']=[];
        if(isset($data) && \Sys\Helper::$Array->KeyIsSet('item', $data) && is_array($data['item'])){
            $is_insert = true;
            if(array_key_exists('id', $data['item']) && (int)$data['item']['id']>0 && $this->GetById($data['item']['id'], $where)->Count>0) $is_insert = false;
            $fields = $this->GetFields();            
            $sql="";
            if($debug) print_r([$is_insert]);
            if($is_insert){
                $sep="";
                $head="";
                $values="";
                foreach($fields as $field){
                    if($field['name']!='id' && array_key_exists($field['name'], $data['item'])){
                        $head .= $sep . "`{$field['name']}`";
                        if(is_null($data['item'][$field['name']])) $values .= $sep . "NULL ";
                        else if(!is_array($data['item'][$field['name']])) $values .= $sep ."'" . $this->Escape((string)$data['item'][$field['name']]) . "' ";
                        else if($this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                            $values .= $sep ."'" . implode(',',  array_keys($data['item'][$field['name']])) . "' ";
                        } else $values .= $sep ."'' ";
                        $sep = ",";
                    }
                }                
                $sql="INSERT INTO `".$this->GetTableName()."` ({$head}) VALUES({$values})";
                if($debug) {
                    print_r($sql); exit();                    
                }
                $this->Reg->DB->Query($sql);
                $id = $this->Reg->DB->GetLastId();
                foreach($fields as $field) 
                if($field['name']!='id' && array_key_exists($field['name'], $data['item']) && 
                    $this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                    $this->TranslateSave($field['name'], $id, $data['item'][$field['name']]);
                }
            }else{
                $sep="";
                $values="";
                $id = (int)$data['item']['id'];
                foreach($fields as $field){
                    if($field['name']!='id' && array_key_exists($field['name'], $data['item'])){
                        if(is_null($data['item'][$field['name']])) $values .= $sep . "NULL ";
                        else if(!is_array($data['item'][$field['name']])) $values .= $sep ."`{$field['name']}` = '" . $this->Escape((string)$data['item'][$field['name']]) . "' ";
                        else if($this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                            $this->TranslateSave($field['name'], $id, $data['item'][$field['name']]);
                            $values .= $sep ."`{$field['name']}` = '" . implode(',',array_keys($data['item'][$field['name']])) . "' ";
                        } else $values .= $sep ."`{$field['name']}` = '' ";
                        $sep = ",";
                    } else if($field['name']=='updated_at' && $field['type']=='timestamp' && array_key_exists($field['name'], $data['item'])){
                        $values .= $sep ."`updated_at` = CURRENT_TIMESTAMP ";
                        $sep = ",";
                    }
                }
                $sql = "UPDATE `".$this->GetTableName()."` SET {$values} WHERE `id` = " . $id;
                if($limit>0) $sql .= "LIMIT 0,".(int)$limit;
                if($debug) {
                    print_r($sql); exit();                    
                }
                $this->Reg->DB->Query($sql);
            }
        }
        if(isset($this->Reg->Session)){
            if((int)$id>0) {
                if(!array_key_exists('success', $this->Reg->Session->Data['msg'])) $this->Reg->Session->Data['msg']['success']="";
                $this->Reg->Session->Data['msg']['success'] = $this->Reg->Translate('MsgModelDataSaveSucces');
            } else {
                if(!array_key_exists('error', $this->Reg->Session->Data['msg'])) $this->Reg->Session->Data['msg']['error']="";
                $this->Reg->Session->Data['msg']['error'] .= $this->Reg->Translate('MsgModelDataSaveError');
            }
        }
        
        
        if($id>0){
            if(isset($social)){
                $sql = "DELETE FROM `user_social` WHERE `user_id` = {$id}";
                $this->Reg->DB->Query($sql);
            }
            if(isset($phones)){
                $sql = "DELETE FROM `user_phone` WHERE `user_id` = {$id}";
                $this->Reg->DB->Query($sql);
            }
            if(is_array($phones) && count($phones)>0){
                $sql = "INSERT INTO `user_phone` (`user_id`, `value`) VALUES";
                $sep = " ";
                foreach ($phones as $key => $value) {
                    $value = preg_replace('~\D~','',$value);
                    if($value!=''){
                        $sql .= $sep."({$id}, '".$value."')";
                        $sep = ", ";
                    }
                }
                $this->Reg->DB->Query($sql);
            }
            if(is_array($social) && count($social)>0){
                $sql = "INSERT INTO `user_social` (`user_id`, `social_id`, `value`) VALUES";
                $sep = " ";
                foreach ($social as $key => $value) if(is_array($value) && array_key_exists('value', $value) && trim($value['value'])!='' && array_key_exists('social_id', $value)){
                    $value['value'] = $this->Escape($value['value']);
                    $value['social_id'] = (int)$value['social_id'];
                    $sql .= $sep."({$id}, {$value['social_id']}, '{$value['value']}')";
                    $sep = ", ";
                }
                $this->Reg->DB->Query($sql);
            }
        }
    }
    public function GetNewContacts($sponsor_id, $sponsor_path, $count=0, $debug=false){
        $count = (int)$count;
        $sql = "UPDATE `".$this->GetTableName()."` SET `user_id`={$sponsor_id}, `path`='{$sponsor_path},{$sponsor_id}', `change_sponsor_at`='".date("Y-m-d H:i:s")."' WHERE `id`!=1 AND `role_id`!=1 AND (`user_id` IS NULL OR `user_id`=0) LIMIT {$count}";
        if($debug) {
            print_r([
                'id'=>$sponsor_id,
                'path'=>$sponsor_path,
                'count'=>$count,
                'sql'=>$sql,
            ]);
            exit();                    
        }
        if($count>0){
            $this->Reg->DB->Query($sql);
            return $this->Reg->DB->CountAffected();
        }
        return 0;
    }
*/
}

