<?php

class Application_Form_Generate extends Zend_Form
{
    private $pDbHost;
    private $pDbName;
    private $pDbUser;
    private $pDbPassword;
    private $pDbTables;
    private $pDbTableSelected;

    public function __construct($pDbHost = '', $pDbName = '', $pDbUser = '', $pDbPassword = '', $pDbTables = array(), $pDbTableSelected = '' ){
        $this->pDbHost = $pDbHost;
        $this->pDbName = $pDbName;
        $this->pDbUser = $pDbUser;
        $this->pDbPassword = $pDbPassword;
        $this->pDbTables = $pDbTables;
        $this->pDbTableSelected = $pDbTableSelected;
        parent::__construct();
    }

    public function init()
    {
        $this->setName('phpDBMapper');
        $this->setAction('?do=generate');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $dbHost = new Zend_Form_Element_Hidden('dbhost');
        $dbHost->setRequired(true)
              ->setValue( $this->pDbHost )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbName = new Zend_Form_Element_Hidden('dbname');
        $dbName->setRequired(true)
              ->setValue( $this->pDbName )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbUser = new Zend_Form_Element_Hidden('dbuser');
        $dbUser->setRequired(true)
              ->setValue( $this->pDbUser )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbPassword = new Zend_Form_Element_Hidden('dbpassword');
        $dbPassword->setRequired(true)
              ->setValue( $this->pDbPassword )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbTable = new Zend_Form_Element_Select('dbtablename');
        $dbTable->setLabel('Database Table name')
              ->setRequired(true)
              ->setValue( $this->pDbTableSelected )
              ->setMultiOptions( $this->pDbTables )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $sPrefix = new Zend_Form_Element_Text('sprefix');
        $sPrefix->setLabel('Mapper prefix')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setValue('Main')
              ->addValidator('NotEmpty');

        $sFolder = new Zend_Form_Element_Text('sfolder');
        $sFolder->setLabel('Mapper library folder')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setValue('Library')
              ->addValidator('NotEmpty');

        $fNote = new Zend_Form_Element_Note('note');
        $fNote->setLabel('Files will be stored to (example): /output/' . $sFolder->getValue() . '/' . $sPrefix->getValue())
              ->addDecorator('Label', array('class' => 'note'));

        $fNote1 = new Zend_Form_Element_Note('note1');
        $fNote1->setLabel('Class namespace will look like (example): ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Repository_SomeClass, ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Entity_SomeClass, ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Dao_SomeClass')
              ->addDecorator('Label', array('class' => 'note'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Generate')
              ->setAttrib('id', 'submitbutton');

        $this->addElements(array($id, $dbHost, $dbName, $dbUser, $dbPassword, $dbTable, $sPrefix, $sFolder, $fNote, $fNote1, $submit));
    }
}