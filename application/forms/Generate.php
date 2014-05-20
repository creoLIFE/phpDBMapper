<?php

class Application_Form_Generate extends Zend_Form
{
    public function init()
    {
        $this->setName('phpDBMapper');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $dbName = new Zend_Form_Element_Text('dbname');
        $dbName->setLabel('Database name')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->addValidator('NotEmpty');

        $dbUser = new Zend_Form_Element_Text('dbuser');
        $dbUser->setLabel('Database User name')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbPassword = new Zend_Form_Element_Password('dbpassword');
        $dbPassword->setLabel('Database User password')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $dbTable = new Zend_Form_Element_Text('dbtablename');
        $dbTable->setLabel('Database Table name')
              ->setRequired(true)
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

        $this->addElements(array($id, $dbName, $dbUser, $dbPassword, $dbTable, $sPrefix, $sFolder, $fNote, $fNote1, $submit));
    }
}