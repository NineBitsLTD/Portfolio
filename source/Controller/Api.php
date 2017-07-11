<?php

namespace Controller;

class Api extends \Core\Controller {
    public function getIndex() {
        if(\Registry::$Session->IsLogged()){
            if( isset($_GET['command']) && $_GET['command']='faq_item' && 
                isset($_GET['id']) && (int)isset($_GET['id'])>0){
                $modelFaq = new \Model\Faq();
                $item = $modelFaq->Where("`id`=".(int)$_GET['id'])->Get()->GetResult()->Row;
                header('Content-type: application/json');
                echo json_encode($item);
            } else {
                header('Content-type: application/json');
                echo json_encode(['user_id'=> \Registry::$Session->User->Data['id'],'message'=>'Please set command']);
            }
        } else {            
            header('Content-type: application/json');
            echo json_encode(['message'=>'Please authorize and set command']);
        }
    }
    public function postFaq_item(){
        if(\Registry::$Session->IsLogged()){
            if(isset($_GET['id']) && (int)isset($_GET['id'])>0){
                $modelFaq = new \Model\Faq();
                $item = $modelFaq->Where("`id`=".(int)$_GET['id'])->Get()->GetResult()->Row;
                //header('Content-type: application/json');
                echo json_encode($item);
            } else {
                header('Content-type: application/json');
                echo json_encode(['user_id'=> \Registry::$Session->User->Data['id'],'message'=>'Please set item id>0']);
            }
        } else {            
            header('Content-type: application/json');
            echo json_encode(['message'=>'Please authorize and set command']);
        }
    }
}

