<?php
/**
 * Db information
 * @package Application_Model
 * @author Mirek Ratman
 * @version 1.0
 * @since 2014-08-29
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class Application_Model_Db
{

    /**
    * @var [mixed]
    */
    private $db = '';

    /**
    * @var [mixed]
    */
    private $tableList = '';

    /**
    * @var [string]
    */
    private $tableName = '';

    /**
    * @var [string]
    */
    private $tableInfo;



    /**
    * Class constructor
    * @param [mixed] $db - DB instance
    * @param [string] $tableName - name of the table in DB
    */
    public function __construct( $db ){
        $this->db = $db;
        $this->tableList = $this->db->listTables();
    }
    
    /**
    * method will get table info
    * @param [string] $tableName
    */
    public function setTableName( $tableName ){
        $this->tableName = $tableName;
        $this->tableInfo = $this->db->describeTable( $this->tableList[ $this->tableName ] );
    }

    /**
    * method will list all tables in DB
    */
    public function getTablesList(){
        return $this->tableList;
    }

    /**
    * method will get table info
    * @param [string] $tableName
    */
    public function getTableInfo( $tableName ){
        return $this->tableInfo;
    }

    /**
    * method will get table mapper
    */
    public function getTablePrimaryName(){
        foreach( $this->tableInfo as $key=>$val ){
            if( $val['PRIMARY'] == 1 ){
                return $val['COLUMN_NAME'];
            }
        }
    }

    /**
    * method will get table mapper
    */
    public function getTableMapper(){
        foreach( $this->tableInfo as $key=>$val ){
            $out[] = array(
                'pMapperKey'       => $val['COLUMN_NAME'],
                'pMapperValue'     => $val['COLUMN_NAME']
            );
        }
        return $out;
    }

    /**
    * method will get table rows info
    * @param [string] $tableName
    */
    public function getTableRowsInfo(){
        foreach( $this->tableInfo as $key=>$val ){
            $out[] = array(
                'pColumnName'       => $val['COLUMN_NAME'],
                'pColumnNameFixed'  => Application_Model_Helper::getNameFixed( $val['COLUMN_NAME'] ),
                'pColumnType'       => $val['DATA_TYPE'],
                'pColumnIdentity'   => $val['IDENTITY']
            );
        }
        return $out;
    }


}
