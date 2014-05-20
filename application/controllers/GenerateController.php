<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $form = new Application_Form_Generate();
        $this->view->form = $form;
    }

}







