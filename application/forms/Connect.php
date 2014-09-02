<?php

class Application_Form_Connect extends Zend_Form
{
    private $pDbHost;
    private $pDbName;
    private $pDbUser;
    private $pDbPassword;

    public function __construct($pDbHost = '', $pDbName = '', $pDbUser = '', $pDbPassword = ''){
        $this->pDbHost = $pDbHost;
        $this->pDbName = $pDbName;
        $this->pDbUser = $pDbUser;
        $this->pDbPassword = $pDbPassword;
        parent::__construct();
    }

    public function init()
    {
        $this->setName('phpDBMapper');
        $this->setAttrib('role','form');
        $this->setAction('?do=connect');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $dbHost = new Zend_Form_Element_Text('dbhost');
        $dbHost->setLabel('Database host/IP')
               ->setValue( $this->pDbHost )
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->setAttrib('class', 'form-control')
               ->addValidator('NotEmpty');

        $dbName = new Zend_Form_Element_Text('dbname');
        $dbName->setLabel('Database name')
               ->setValue( $this->pDbName )
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->setAttrib('class', 'form-control')
               ->addValidator('NotEmpty');

        $dbUser = new Zend_Form_Element_Text('dbuser');
        $dbUser->setLabel('Database User name')
              ->setValue( $this->pDbUser )
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setAttrib('class', 'form-control')
              ->addValidator('NotEmpty');

        $dbPassword = new Zend_Form_Element_Password('dbpassword');
        $dbPassword->setLabel('Database User password')
              ->setValue( $this->pDbPassword )
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setAttrib('class', 'form-control')
              ->addValidator('NotEmpty');


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Connect')
              ->setAttrib('id', 'submitbutton')
              ->setAttrib('class', 'btn btn-primary');

        $this->addElements(array($id, $dbHost, $dbName, $dbUser, $dbPassword, $submit));
    }
}