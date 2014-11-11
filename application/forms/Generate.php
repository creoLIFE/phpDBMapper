<?php

class Application_Form_Generate extends Zend_Form
{
    private $pDbHost;
    private $pDbName;
    private $pDbUser;
    private $pDbPassword;
    private $pPrefix;
    private $pFolder;
    private $pDbTables;
    private $pDbTableSelected;

    public function __construct($pDbHost = '', $pDbName = '', $pDbUser = '', $pDbPassword = '', $pPrefix = 'Main', $pFolder = 'Library', $pDbTables = array(), $pDbTableSelected = '' ){
        $this->pDbHost = $pDbHost;
        $this->pDbName = $pDbName;
        $this->pDbUser = $pDbUser;
        $this->pDbPassword = $pDbPassword;
        $this->pPrefix = $pPrefix;
        $this->pFolder = $pFolder;
        $this->pDbTables = $pDbTables;
        $this->pDbTableSelected = $pDbTableSelected;
        parent::__construct();
    }

    public function init()
    {
        $this->setName('phpDBMapper');
        $this->setAction('?do=generate');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int')
          ->removeDecorator('Label');

        $dbHost = new Zend_Form_Element_Hidden('dbhost');
        $dbHost->setRequired(true)
              ->removeDecorator('Label')
              ->setValue( $this->pDbHost )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbName = new Zend_Form_Element_Hidden('dbname');
        $dbName->setRequired(true)
              ->removeDecorator('Label')
              ->setValue( $this->pDbName )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbUser = new Zend_Form_Element_Hidden('dbuser');
        $dbUser->setRequired(true)
              ->removeDecorator('Label')
              ->setValue( $this->pDbUser )
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbPassword = new Zend_Form_Element_Hidden('dbpassword');
        $dbPassword->setRequired(true)
              ->removeDecorator('Label')
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
              ->setAttrib('class', 'form-control')
              ->addValidator('NotEmpty');

        $sPrefix = new Zend_Form_Element_Text('sprefix');
        $sPrefix->setLabel('Mapper prefix')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setValue($this->pPrefix)
              ->setAttrib('class', 'form-control')
              ->addValidator('NotEmpty');

        $sFolder = new Zend_Form_Element_Text('sfolder');
        $sFolder->setLabel('Mapper library folder')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setValue($this->pFolder)
              ->setAttrib('class', 'form-control')
              ->addValidator('NotEmpty');

        $fNote = new Zend_Form_Element_Note('note');
        $fNote->setValue( 'Files will be stored to (example): /output/' . $sFolder->getValue() . '/' . $sPrefix->getValue() )
//              ->removeDecorator('Label')
              ->addDecorator('HtmlTag', array('tag'=>'dt', 'class'=>'alert alert-warning'));

        $fNote1 = new Zend_Form_Element_Note('note1');
        $fNote1->setValue('Class namespace will look like (example): ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Repository_SomeClass, ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Entity_SomeClass, ' . $sFolder->getValue() . '_' . $sPrefix->getValue() . '_Dao_SomeClass')
              ->removeDecorator('Label')
              ->addDecorator('HtmlTag', array('tag'=>'dt', 'class'=>'alert alert-warning'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Generate')
              ->setAttrib('id', 'submitbutton')
              ->setAttrib('class', 'btn btn-primary');

        $back = new Zend_Form_Element_Note('back');
        $back->setValue( 'Back' )
              ->removeDecorator('Label')
              ->addDecorator('HtmlTag', array('tag'=>'a', 'class'=>'btnBack btn btn-default', 'href'=>'/'));


        $this->addElements(array($dbTable, $sPrefix, $sFolder, $fNote, $fNote1, $submit, $back, $id, $dbHost, $dbName, $dbUser, $dbPassword));
    }
}