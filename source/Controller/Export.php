<?php
namespace Controller;

class Export extends \Core\Controller {
    
    public function getIndex(){
        $view = new \View\Base();
        $view->Content = new \View\Export();
        $view->Content->Components = [
            [
                'title'=>'Project to CSV',
                'link'=> \Registry::$Data->BaseLink.'export/csv?table=project',
            ],
            [
                'title'=>'FAQ to CSV',
                'link'=> \Registry::$Data->BaseLink.'export/csv?table=faq',
            ],
            [
                'title'=>'Users to CSV',
                'link'=> \Registry::$Data->BaseLink.'export/csv?table=user',
            ]
        ];
        $view->printContent();
    }
    
    public function postCsv() {
        if(\Registry::$Session->IsLogged() && isset($_GET['table']) && $_GET['table']!=''){
            $config = new \Config\DataBase();
            $conn = new \mysqli($config->Host, $config->User, $config->Password, $config->DBName);
            $sql = "SELECT * FROM `{$_GET['table']}`";
            $result = $conn->query($sql);
            if(isset($result)){
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="'.$_GET['table'].'.csv";');

                $fp = fopen('php://output', 'w');  // Открываем поток для записи

                while($row = $result->fetch_assoc()) {  // Перебираем строки
                    fputcsv($fp, $row, ";");  // Записываем строки в поток
                }        
                fclose($fp);
                $conn->close();  // Закрываем коннект к БД
                exit();
            }
            \Registry::$Data->Msg = "Table: {$_GET['table']}, not found.";
        }
        $this->getIndex();
    }
}
?>

