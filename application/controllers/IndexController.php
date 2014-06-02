<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //Initialize
        $dirInput = $this->getInvokeArg('bootstrap')->getOption('dir')['input'];
        $dirOutput = $this->getInvokeArg('bootstrap')->getOption('dir')['output'];
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
            try {

                $db->getConnection();

                $tables = $db->listTables();

                $form = new Application_Form_Generate($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'), $tables);
            } catch (Zend_Db_Adapter_Exception $e) {
                print_r($e);
                $form = new Application_Form_Connect($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'));
            } catch (Zend_Exception $e) {
                print_r($e);
                $form = new Application_Form_Connect($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'));
            }
        }

        if( $params->getParam('do') === 'generate'){
            $tables = $db->listTables();
            $form = new Application_Form_Generate($post->getParam('dbhost'),$post->getParam('dbname'),$post->getParam('dbuser'),$post->getParam('dbpassword'), $tables, $post->getParam('dbtablename'));

            $tableInfo = $db->describeTable( $tables[$post->getParam('dbtablename')] );

echo "<pre>";
print_r($tableInfo);

            $elToChange = array(
                'pMapperPrefix'         => $post->getParam('sprefix',null,'Main'),
                'pTableName'            => $post->getParam('dbtablename'),
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
            if( !is_dir($dirOutput . '/Dao') ) {
                mkdir($dirOutput . '/Dao');
            }
            file_put_contents( $dirOutput . '/Dao/' . $elToChange['pClassName'] . '.php', $dao);


            //DAO Interface     
            $daoInterface = file_get_contents( $dirInput . '/dao_interface.template' );
            foreach( $elToChange as $key=>$val ){
                $daoInterface = str_replace( '%'. $key .'%', $val, $daoInterface );
            }
            if( !is_dir($dirOutput . '/Dao/Interface') ) {
                mkdir($dirOutput . '/Dao/Interface');
            }
            file_put_contents( $dirOutput . '/Dao/Interface/' . $elToChange['pClassName'] . '.php', $daoInterface);


            //Repository
            $repository = file_get_contents( $dirInput . '/repository.template' );
            foreach( $elToChange as $key=>$val ){
                $repository = str_replace( '%'. $key .'%', $val, $repository );
            }
            if( !is_dir($dirOutput . '/Repository') ) {
                mkdir($dirOutput . '/Repository');
            }
            file_put_contents( $dirOutput . '/Repository/' . $elToChange['pClassName'] . '.php', $repository);


            //Entity
            $entity = file_get_contents( $dirInput . '/entity.template' );
            foreach( $elToChange as $key=>$val ){
                $entity = str_replace( '%'. $key .'%', $val, $entity );
            }

            preg_match( '/<phpDBMapper:params>(.*)<\/phpDBMapper:params>/s', $entity, $p );
            preg_match( '/<phpDBMapper:methods>(.*)<\/phpDBMapper:methods>/s', $entity, $m );

            
            /*
            $columnToChange = array(
                0 => array(
                    'pColumnName'       => 'id',
                    'pColumnNameFixed'  => 'Id',
                    'pColumnType'       => 'integer',
                ),
                1 => array(
                    'pColumnName'       => 'name',
                    'pColumnNameFixed'  => 'Name',
                    'pColumnType'       => 'string',
                )
            );
            */

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
      
            $entity = preg_replace( '/<phpDBMapper:params[^>]*?>.*?<\/phpDBMapper:params>/is', $paramsOut, $entity );
            $entity = preg_replace( '/<phpDBMapper:methods[^>]*?>.*?<\/phpDBMapper:methods>/is', $methodsOut, $entity );

            if( !is_dir($dirOutput . '/Entity') ) {
                mkdir($dirOutput . '/Entity');
            }
            file_put_contents( $dirOutput . '/Entity/' . $elToChange['pClassName'] . '.php', $entity);


            //Entity search
            $entity = file_get_contents( $dirInput . '/entity_search.template' );
            foreach( $elToChange as $key=>$val ){
                $entity = str_replace( '%'. $key .'%', $val, $entity );
            }

            preg_match( '/<phpDBMapper:params>(.*)<\/phpDBMapper:params>/s', $entity, $p );
            preg_match( '/<phpDBMapper:methods>(.*)<\/phpDBMapper:methods>/s', $entity, $m );

            /*
            $columnToChange = array(
                0 => array(
                    'pColumnName'       => 'id',
                    'pColumnNameFixed'  => 'Id',
                    'pColumnType'       => 'integer',
                ),
                1 => array(
                    'pColumnName'       => 'name',
                    'pColumnNameFixed'  => 'Name',
                    'pColumnType'       => 'string',
                )
            );
            */

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
      
            $entity = preg_replace( '/<phpDBMapper:params[^>]*?>.*?<\/phpDBMapper:params>/is', $paramsOut, $entity );
            $entity = preg_replace( '/<phpDBMapper:methods[^>]*?>.*?<\/phpDBMapper:methods>/is', $methodsOut, $entity );

            if( !is_dir($dirOutput . '/Entity/Search/') ) {
                mkdir($dirOutput . '/Entity/Search/');
            }
            file_put_contents( $dirOutput . '/Entity/Search/' . $elToChange['pClassName'] . '.php', $entity);


            //Mapper
            $entity = file_get_contents( $dirInput . '/mapper.template' );
            foreach( $elToChange as $key=>$val ){
                $entity = str_replace( '%'. $key .'%', $val, $entity );
            }

            preg_match( '/<phpDBMapper:map>(.*)<\/phpDBMapper:map>/s', $entity, $m );

            $columnToMap = array(
                0 => array(
                    'pMapperKey'        => 'id',
                    'pMapperValue'      => 'id',
                ),
                1 => array(
                    'pMapperKey'        => 'p_name',
                    'pMapperValue'      => 'p_name',
                )
            );

            $mapOut = '';
            foreach( $columnToMap as $c ){
                $mapTmp = $m[1];

                foreach( $c as $key=>$val ){
                    $mapTmp = str_replace( '%'. $key .'%', $val, $mapTmp );
                    $mapTmp = str_replace(array("\n", "\r"), '', $mapTmp);
                }

                $mapOut .= $mapTmp . "\n";
            }

            $entity = preg_replace( '/<phpDBMapper:map[^>]*?>.*?<\/phpDBMapper:map>/is', $mapOut, $entity );

            if( !is_dir($dirOutput . '/Mapper/') ) {
                mkdir($dirOutput . '/Mapper/');
            }
            file_put_contents( $dirOutput . '/Mapper/' . $elToChange['pClassName'] . '.php', $entity);

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







