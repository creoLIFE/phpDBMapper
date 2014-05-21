<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $form = new Application_Form_Generate();
        $this->view->form = $form;

        $params = $this->_request->getParams();
        $post = $this->_request->getPost();


        if( isset($params['do']) && $params['do'] === 'generate'){
            echo "aa";
        }

    }

}







