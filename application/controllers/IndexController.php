<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $form = new Application_Form_Generate();
        $this->view->form = $form;

        //$albums = new Application_Model_DbTable_Albums();
        //$this->view->albums = $albums->fetchAll();
    }

}







