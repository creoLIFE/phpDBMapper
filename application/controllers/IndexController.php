<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        //Initialize
        $dirInput = $this->getInvokeArg('bootstrap')->getOption('dir')['input'];
        $dirOutput = $this->getInvokeArg('bootstrap')->getOption('dir')['output'];
        $form = new Application_Form_Generate();
        $params = $this->_request->getParams();
        $post = $this->_request->getPost();
        
        //Run
        $this->view->form = $form;


        if( isset($params['do']) && $params['do'] === 'generate'){

            $elToChange = array(
                'pMapperPrefix'     => $params['sprefix'],
                'pTableName'        => 't_clients',
                'pTableIndexCell'   => 'id',
                'pClassName'        => 'Clients',
                'pDate'             => date("Y-m-d H:i:s"),
                'pYear'             => date("Y")
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

            $paramsOut = '';
            $methodsOut = '';
            foreach( $columnToChange as $c ){
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

            $paramsOut = '';
            $methodsOut = '';
            foreach( $columnToChange as $c ){
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

    }

}







