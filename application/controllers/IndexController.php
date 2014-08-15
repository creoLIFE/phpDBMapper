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


        if( $params->getParam('do') === 'connect'){
            $this->view->homePage = false;
            try {

                $db->getConnection();

                $tables = $db->listTables();
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

            $this->view->homePage = false;
            $tables = $db->listTables();
            $form = new Application_Form_Generate(
                    $post->getParam('dbhost'),
                    $post->getParam('dbname'),
                    $post->getParam('dbuser'),
                    $post->getParam('dbpassword'),
                    $post->getParam('sprefix') !== '' ? $post->getParam('sprefix') : 'Main',
                    $post->getParam('sfolder') !== '' ? $post->getParam('sfolder') : 'Library',
                    $tables,
                    $post->getParam('dbtablename')
            );

            $tableInfo = $db->describeTable( $tables[$post->getParam('dbtablename')] );




            $sprefix = $post->getParam('sprefix') !== '' ? '/' . $post->getParam('sprefix') : '/Library';
            if( !is_dir($dirOutput . $sprefix) ) {
                mkdir($dirOutput . $sprefix);
            }
            $dirOutput = $dirOutput . $sprefix;


            $elApp = array(
                "name"       => "Generator: phpDBMapper",
                "ver"        => "v.0.1b",
                "github"     => "https://github.com/creoLIFE/phpDBMapper"
            );

            $elToChange = array(
                'pDbConfig'             => strtolower( $post->getParam('sprefix',null,'Main') ),
                'pMapperPrefix'         => $post->getParam('sprefix',null,'Main'),
                'pTableName'            => $tables[$post->getParam('dbtablename')],
                'pTableIndexCell'       => self::getTablePrimaryName( $tableInfo ),
                'pTableIndexCellFixed'  => self::getNameFixed( self::getTablePrimaryName( $tableInfo ) ),
                'pClassName'            => self::getNameFixed( $tables[$post->getParam('dbtablename')] ),
                'pDate'                 => date("Y-m-d H:i:s"),
                'pYear'                 => date("Y")
            );
            

            //DAO      
            $dao = file_get_contents( $dirInput . '/dao.template' );
            foreach( $elToChange as $key=>$val ){
                $dao = str_replace( '%'. $key .'%', $val, $dao );
            }
            $dao = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $dao );
            if( !is_dir($dirOutput . '/Dao') ) {
                mkdir($dirOutput . '/Dao');
            }
            file_put_contents( $dirOutput . '/Dao/' . $elToChange['pClassName'] . '.php', $dao);







            //DAO Interface     
            $daoInterface = file_get_contents( $dirInput . '/dao_interface.template' );
            foreach( $elToChange as $key=>$val ){
                $daoInterface = str_replace( '%'. $key .'%', $val, $daoInterface );
            }
            $daoInterface = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $daoInterface );
            if( !is_dir($dirOutput . '/Dao/Interface') ) {
                mkdir($dirOutput . '/Dao/Interface');
            }
            file_put_contents( $dirOutput . '/Dao/Interface/' . $elToChange['pClassName'] . '.php', $daoInterface);





            //User temp repository
            $userRepository = file_get_contents( $dirInput . '/repository_temp.template' );
            foreach( $elToChange as $key=>$val ){
                $userRepository = str_replace( '%'. $key .'%', $val, $userRepository );
            }
            $userRepository = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $userRepository );
            if( !is_dir($dirOutput . '/Repository') ) {
                mkdir($dirOutput . '/Repository');
            }
            file_put_contents( $dirOutput . '/Repository/' . $elToChange['pClassName'] . '_REMOVE_THIS_TEXT.php', $userRepository);






            //Repository
            $repositoryBase = file_get_contents( $dirInput . '/repository_base.template' );
            foreach( $elToChange as $key=>$val ){
                $repositoryBase = str_replace( '%'. $key .'%', $val, $repositoryBase );
            }

            preg_match( '/<phpDBMapper:methods>(.*)<\/phpDBMapper:methods>/s', $repositoryBase, $m );

            $paramsOut = '';
            $methodsOut = '';
            foreach( self::getTableRowsInfo($tableInfo) as $c ){
                $methodsTmp = $m[1];

                foreach( $c as $key=>$val ){
                    $methodsTmp = str_replace( '%'. $key .'%', $val, $methodsTmp );
                    switch( $val ){
                        case 'int':
                            $methodsTmp = str_replace( '%pValidator%', 'Int', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', '', $methodsTmp );
                        break;
                        case 'text':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                        case 'varchar':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                        case 'datetime':
                            $methodsTmp = str_replace( '%pValidator%', 'Date', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('format' => 'Y-m-d H:m:s.u')", $methodsTmp );
                        break;
                        case 'longtext':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                    }
                }
                
                $methodsOut .= $methodsTmp;
            }
      
            $repositoryBase = preg_replace( '/<phpDBMapper:methods[^>]*?>.*?<\/phpDBMapper:methods>/is', $methodsOut, $repositoryBase );

            $repositoryBase = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $repositoryBase );
            if( !is_dir($dirOutput . '/Repository/Base') ) {
                mkdir($dirOutput . '/Repository/Base');
            }
            file_put_contents( $dirOutput . '/Repository/Base/' . $elToChange['pClassName'] . '.php', $repositoryBase);








            //Entity
            $entity = file_get_contents( $dirInput . '/entity.template' );
            foreach( $elToChange as $key=>$val ){
                $entity = str_replace( '%'. $key .'%', $val, $entity );
            }

            preg_match( '/<phpDBMapper:params>(.*)<\/phpDBMapper:params>/s', $entity, $p );
            preg_match( '/<phpDBMapper:methods>(.*)<\/phpDBMapper:methods>/s', $entity, $m );


            $paramsOut = '';
            $methodsOut = '';
            foreach( self::getTableRowsInfo($tableInfo) as $c ){
                $paramsTmp = $p[1];
                $methodsTmp = $m[1];

                foreach( $c as $key=>$val ){
                    $paramsTmp = str_replace( '%'. $key .'%', $val, $paramsTmp );
                    $methodsTmp = str_replace( '%'. $key .'%', $val, $methodsTmp );
                    switch( $val ){
                        case 'int':
                            $methodsTmp = str_replace( '%pValidator%', 'Int', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', '', $methodsTmp );
                        break;
                        case 'text':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                        case 'varchar':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                        case 'datetime':
                            $methodsTmp = str_replace( '%pValidator%', 'Date', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('format' => 'Y-m-d H:m:s.u')", $methodsTmp );
                        break;
                        case 'longtext':
                            $methodsTmp = str_replace( '%pValidator%', 'Regex', $methodsTmp );
                            $methodsTmp = str_replace( '%pValidatorPattern%', "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $methodsTmp );
                        break;
                    }
                }
                
                $paramsOut .= $paramsTmp;
                $methodsOut .= $methodsTmp;
            }
      
            $entity = preg_replace( '/<phpDBMapper:params[^>]*?>.*?<\/phpDBMapper:params>/is', $paramsOut, $entity );
            $entity = preg_replace( '/<phpDBMapper:methods[^>]*?>.*?<\/phpDBMapper:methods>/is', $methodsOut, $entity );

            $entity = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $entity );
            if( !is_dir($dirOutput . '/Entity') ) {
                mkdir($dirOutput . '/Entity');
            }
            file_put_contents( $dirOutput . '/Entity/' . $elToChange['pClassName'] . '.php', $entity);








            //Entity search
            $entitysearch = file_get_contents( $dirInput . '/entity_search.template' );
            foreach( $elToChange as $key=>$val ){
                $entitysearch = str_replace( '%'. $key .'%', $val, $entitysearch );
            }

            preg_match( '/<phpDBMapper:params>(.*)<\/phpDBMapper:params>/s', $entitysearch, $p );
            preg_match( '/<phpDBMapper:methods>(.*)<\/phpDBMapper:methods>/s', $entitysearch, $m );


            $paramsOut = '';
            $methodsOut = '';
            foreach( self::getTableRowsInfo($tableInfo) as $c ){
                $paramsTmp = $p[1];
                $methodsTmp = $m[1];

                foreach( $c as $key=>$val ){
                    $paramsTmp = str_replace( '%'. $key .'%', $val, $paramsTmp );
                    $methodsTmp = str_replace( '%'. $key .'%', $val, $methodsTmp );
                }
                
                $paramsOut .= $paramsTmp;
                $methodsOut .= $methodsTmp;
            }
      
            $entitysearch = preg_replace( '/<phpDBMapper:params[^>]*?>.*?<\/phpDBMapper:params>/is', $paramsOut, $entitysearch );
            $entitysearch = preg_replace( '/<phpDBMapper:methods[^>]*?>.*?<\/phpDBMapper:methods>/is', $methodsOut, $entitysearch );

            $entitysearch = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $entitysearch );

            if( !is_dir($dirOutput . '/Entity/Search/') ) {
                mkdir($dirOutput . '/Entity/Search/');
            }
            file_put_contents( $dirOutput . '/Entity/Search/' . $elToChange['pClassName'] . '.php', $entitysearch);








            //Mapper
            $mapper = file_get_contents( $dirInput . '/mapper.template' );
            foreach( $elToChange as $key=>$val ){
                $mapper = str_replace( '%'. $key .'%', $val, $mapper );
            }

            preg_match( '/<phpDBMapper:map>(.*)<\/phpDBMapper:map>/s', $mapper, $m );


            $mapOut = '';
            foreach( self::getTableMapper($tableInfo) as $c ){
                $mapTmp = $m[1];

                foreach( $c as $key=>$val ){
                    $mapTmp = str_replace( '%'. $key .'%', $val, $mapTmp );
                    $mapTmp = str_replace(array("\n", "\r"), '', $mapTmp);
                }

                $mapOut .= $mapTmp . "\n";
            }

            $mapper = preg_replace( '/<phpDBMapper:map[^>]*?>.*?<\/phpDBMapper:map>/is', $mapOut, $mapper );

            $mapper = str_replace( '<?php', "<?php\n\n/*" . implode($elApp,', ') . "*/\n", $mapper );

            if( !is_dir($dirOutput . '/Mapper/') ) {
                mkdir($dirOutput . '/Mapper/');
            }
            file_put_contents( $dirOutput . '/Mapper/' . $elToChange['pClassName'] . '.php', $mapper);

            $this->view->status = "Stored in to: " . $dirOutput . "</br>";
        }

        //Run
        $this->view->form = $form;

    }


    public function getTablePrimaryName( $tableInfo ){
        foreach( $tableInfo as $key=>$val ){
            if( $val['PRIMARY'] == 1 ){
                return $val['COLUMN_NAME'];
            }
        }
    }

    public function getTableMapper( $tableInfo ){
        foreach( $tableInfo as $key=>$val ){
            $out[] = array(
                'pMapperKey'       => $val['COLUMN_NAME'],
                'pMapperValue'     => $val['COLUMN_NAME']
            );
        }
        return $out;
    }

    public function getTableRowsInfo( $tableInfo ){
        foreach( $tableInfo as $key=>$val ){
            $out[] = array(
                'pColumnName'       => $val['COLUMN_NAME'],
                'pColumnNameFixed'  => self::getNameFixed( $val['COLUMN_NAME'] ),
                'pColumnType'       => $val['DATA_TYPE']
            );
        }
        return $out;
    }

    public function getNameFixed( $toFix ){
        $el = explode( '_', $toFix);
        foreach($el as $e){
            $out[] = ucfirst($e);
        }
        return  implode( '', $out);
    }

}







