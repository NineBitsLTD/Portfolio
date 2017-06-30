<?php
namespace Core;

abstract class View {
    /**
     * 
     * @return string
     */
    public function getContent(){
        ob_start();
        $this->printContent();
        return ob_get_clean();
    }
    public function printContent(){
        
    }
}

