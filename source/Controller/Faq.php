<?php
namespace Controller;

class Faq extends \Core\Controller {
    public function getIndex() {
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
}

