<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        $this->view->error = Main_Error::display($error);
    }
}







