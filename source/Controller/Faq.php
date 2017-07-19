<?php
namespace Controller;

class Faq extends \Core\Controller {
    public function getIndex() {
        if(\Registry::$Data->Msg!='') print_r(\Registry::$Data->Msg);
        $view = new \View\Base();
        $view->Content = new \View\Faq();
        $modelFaq = new \Model\Faq();
        if(array_key_exists('email', $_POST) && $_POST['email']!='' && array_key_exists('message', $_POST) && $_POST['message']!=''){
            $post = [
                'firstname' => '',
                'email' => $_POST['email'],
                'message' => $_POST['message'],
            ];
            if(array_key_exists('firstname', $_POST)) $post['firstname'] = $_POST['firstname'];
            $modelFaq->Set($post);
        }
        $view->Content->Offset = 0;
        $view->Content->Limit = 2;
        if(isset($_GET['offset'])) $view->Content->Offset = $_GET['offset'];
        if(isset($_GET['limit'])) $view->Content->Limit = $_GET['limit'];
        $view->Content->Total = $modelFaq->Clear()->Where("`parent_id` IS NULL")->GetTotal();
        $view->Content->List = $modelFaq->Limit($view->Content->Offset,$view->Content->Limit)->Sort(['created_at'=>'desc'])->Get('id')->ClearLimit()->GetResult()->Rows;
        foreach ($view->Content->List as $id=>$value) {
            $view->Content->List[$id]['request'] = $modelFaq->ClearWhere()->Where("`parent_id`='{$value['id']}'")->Get('id')->GetResult()->Rows;
        }
        $view->printContent();
    }
    public function postEdit(){
        $modelFaq = new \Model\Faq();
        $view = new \View\Base();
        $view->Content = new \View\FaqEdit();
        if(isset($_GET['id'])) {
            $view->Content->Item = $modelFaq->GetById($_GET['id'])->GetResult()->Row;
            unset($view->Content->Item['parent_id']);
            $view->Content->ID = $_GET['id'];
        }
        $view->printContent();
    }
    public function postAnswer(){
        $modelFaq = new \Model\Faq();
        $view = new \View\Base();
        $view->Content = new \View\FaqEdit();
        if(isset($_GET['id'])) {
            $item = $modelFaq->Where("`parent_id`={$_GET['id']}")->Get()->GetResult()->Row;
            if(array_key_exists('id', $item)){
                $view->Content->Item = $item;
            } else {
                $view->Content->Item = [
                    'parent_id'=>$_GET['id'],
                    'firstname'=>'',
                    'email'=>'',
                    'message'=>''
                ];
            }
            $view->Content->ParentID = $_GET['id'];
        }
        $view->printContent();
    }
    public function postDelete(){
        if(\Registry::$Session->IsLogged() && isset($_GET['id'])){
            $modelFaq = new \Model\Faq();
            $modelFaq->Delete([$_GET['id']]);
        }
        $this->getIndex();
    }
    public function postSave(){
        if(\Registry::$Session->IsLogged() && isset($_POST['item'])){
            $modelFaq = new \Model\Faq();
            if(array_key_exists('id', $_POST['item'])) $_POST['item']['id']=(int)$_POST['item']['id'];
            if(array_key_exists('parent_id', $_POST['item'])) $_POST['item']['parent_id']=(int)$_POST['item']['parent_id'];
            $modelFaq->Set($_POST['item']);
            
            if(array_key_exists('parent_id', $_POST['item'])){
                $q = $modelFaq->Clear()->GetById($_POST['item']['parent_id'])->GetResult()->Row;
                if(isset($q['email'])){             
                    \Registry::$Data->Msg = \Registry::$Mail->Send(
                        $q['email'], 
                        $q['firstname'], 
                        "Answer for question №{$_POST['item']['parent_id']} from ". \Registry::$Data->BaseLink, 
                        $_POST['item']['message']
                    );
                }
            }
        }
        $this->getIndex();
    }
}

