<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //Initialize
        $this->view->homePage = true;
        $dirInput = realpath( $this->getInvokeArg('bootstrap')->getOption('dir')['input'] );
        $dirOutput = realpath( $this->getInvokeArg('bootstrap')->getOption('dir')['output'] );
        $params = new Main_Helper_Params( $this->_request->getParams() );
        $post = new Main_Helper_Params( $this->_request->getPost() );

        $form = new Application_Form_Connect();

        $db = Zend_Db::factory('Pdo_Mysql',array(
            'host'     => $post->getParam('dbhost'),
            'username' => $post->getParam('dbuser'),
            'password' => $post->getParam('dbpassword'),
            'dbname'   => $post->getParam('dbname')
        ));

        $this->view->status = "Not connected";



        if( $params->getParam('do') === 'connect'){
            $pDb = new Application_Model_Db( $db );

            $this->view->homePage = false;
            try {

                $pDb->getConnection();

                $tables = $pDb->getTablesList();
                $form = new Application_Form_Generate(
                    $post->getParam('dbhost'),
                    $post->getParam('dbname'),
                    $post->getParam('dbuser'),
                    $post->getParam('dbpassword'),
                    $post->getParam('sprefix') !== '' ? $post->getParam('sprefix') : 'Main',
                    $post->getParam('sfolder') !== '' ? $post->getParam('sfolder') : 'Library',
                    $tables
                );
            } catch (Zend_Db_Adapter_Exception $e) {
                print_r($e);
                $form = new Application_Form_Connect($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'));
            } catch (Zend_Exception $e) {
                print_r($e);
                $form = new Application_Form_Connect($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'));
            }

            $this->view->status = "Connected";
        }

        if( $params->getParam('do') === 'generate'){
            $pDb = new Application_Model_Db( $db );

            $this->view->homePage = false;
            $tables = $pDb->getTablesList();
            $form = new Application_Form_Generate(
                    $post->getParam('dbhost'),
                    $post->getParam('dbname'),
                    $post->getParam('dbuser'),
                    $post->getParam('dbpassword'),
                    $post->getParam('sprefix') !== '' ? $post->getParam('sprefix') : 'Main',
                    $post->getParam('sfolder') !== '' ? $post->getParam('sfolder') : 'Library',
                    $tables,
                    $post->getParam('dbtablename'),
                    $post->getParam('dboutputtype')
            );

            $pDb->setTableName( $post->getParam('dbtablename') );

            $sprefix = $post->getParam('sprefix') !== '' ? '/' . $post->getParam('sprefix') : '/Library';
            $dirOutput = $dirOutput . $sprefix;

            //Set default params
            $pParams = new Application_Model_Globalparams();

            $pParams->setpDbConfig( strtolower( $post->getParam('sprefix',null,'Main') ) );
            $pParams->setpMapperPrefix( $post->getParam('sprefix',null,'Main') );
            $pParams->setpTableName( $tables[$post->getParam('dbtablename')] );
            $pParams->setpTableIndexCell( $pDb->getTablePrimaryName() );
            $pParams->setpTableIndexCellFixed( Application_Model_Helper::getNameFixed( $pDb->getTablePrimaryName() ) );
            $pParams->setpClassName( Application_Model_Helper::getNameFixed( $tables[$post->getParam('dbtablename')] ) );
            //$pParams->setpTableRelation( $pDb->getForeginRelationships() );

            if (file_exists( $dirInput . '/' . $post->getParam('dboutputtype') ) && is_dir( $dirInput . '/' . $post->getParam('dboutputtype') )) {
                $dirInput = $dirInput . '/' . $post->getParam('dboutputtype');
            }
            else{
                Throw new \Exception('Wrong input path!');
            }


            //Create DAO
            $dao = new Application_Model_Dao( $dirInput . '/dao.template', $dirOutput . '/Dao/' . $pParams->pClassName . '.php', $pParams );
            $dao->create();

            //Create DAO interface
            $daoInterface = new Application_Model_Dao( $dirInput . '/dao_interface.template', $dirOutput . '/Dao/Interface/' . $pParams->pClassName . '.php', $pParams );
            $daoInterface->create();

            //Create Entity
            $entity = new Application_Model_Entity( $dirInput . '/entity.template', $dirOutput . '/Entity/' . $pParams->pClassName . '.php', $pParams, $pDb );
            $entity->create();

            //Create Entity Search
            $entity = new Application_Model_Entity( $dirInput . '/entity_search.template', $dirOutput . '/Entity/Search/' . $pParams->pClassName . '.php', $pParams, $pDb );
            $entity->create();

            //Create User repository
            $repository = new Application_Model_Repository( $dirInput . '/repository_temp.template', $dirOutput . '/Repository/' . $pParams->pClassName . '_EXAMPLE.php', $pParams );
            $repository->create();

            //Create User repository base
            $repositoryBase = new Application_Model_Repositorybase( $dirInput . '/repository_base.template', $dirOutput . '/Repository/Base/' . $pParams->pClassName . '.php', $pParams, $pDb );
            $repositoryBase->create();

            //Create Mapper
            $maper = new Application_Model_Mapper( $dirInput . '/mapper.template', $dirOutput . '/Mapper/' . $pParams->pClassName . '.php', $pParams, $pDb );
            $maper->create();

            //Create Unit test
            $unit = new Application_Model_Unittest( $dirInput . '/unit_test.template', $dirOutput . '/Unittest/' . $pParams->pClassName . '.php', $pParams, $pDb );
            $unit->create();


            $this->view->status = "Stored in to: " . $dirOutput . "</br>";
        }

        //Run
        $this->view->form = $form;

    }
}







