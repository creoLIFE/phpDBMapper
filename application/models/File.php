<?php
/**
 * File model
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

class Application_Model_File extends Main_Io
{

    /**
    * @var [string] $fileTemplate
    */
    protected $template = '';

    /**
    * @var [string] $outputFile
    */
    protected $outputFile = '';

    /**
    * @var [mixed] $globalParams
    */
    protected $globalVars;

    /**
    * @var [mixed] $methods
    */
    protected $methods;

    /**
    * @var [mixed] $params
    */
    protected $params;

    /**
    * @var [mixed]
    */
    protected $tableRowsInfo;

    /**
    * @var [mixed]
    */
    protected $tableMapper;


    /**
    * Class constructor
    * @param [string] $inputFile - template file 
    * @param [string] $outputFile - output file 
    * @param [Application_Model_Globalparams] $globalVars - instance of Application_Model_Globalparams with data
    * @param [mixed] $db - DB instance
    */
    public function __construct( $file, $outputFile, $globalVars, $db = null ){
        $this->template = file_get_contents( $file );
        $this->outputFile = $outputFile;
        $this->globalVars = $globalVars;

        if( isset($db) ){
            $this->tableRowsInfo = $db->getTableRowsInfo();
            $this->tableMapper = $db->getTableMapper();
        }
    }

    /**
    * Method will update file header
    */
    public function updateVaribles(){
        foreach( $this->globalVars as $key=>$val ){
            $this->template = str_replace( '%'. $key .'%', $val, $this->template );
        }
    }

    /**
    * Method will parse template and update parameters
    * @param [string] $loopName - name on the loop in template to parse and update
    * @return [boolean]
    */
    public function updateLoop( $loopName = 'phpDBMapper:loop', $columnIdHash = '%columnIdentity%' ){
        $out = '';
        $columnIdentity = '';

        //'/<phpDBMapper:params>(.*)<\/phpDBMapper:params>/s'
        preg_match( '/<'.$loopName.'>(.*)<\/'.$loopName.'>/s', $this->template, $loop );

        foreach( $this->tableRowsInfo as $c ){
            if( $c['pColumnIdentity'] == '1' ){
                $columnIdentity = $c['pColumnName'];
            }

            $tmp = $loop[1];

            foreach( $c as $key=>$val ){
                $tmp = str_replace( '%'. $key .'%', $val, $tmp );
                $validator = new Application_Model_Validator($val, $tmp);
                $tmp = $validator->getContent();
            }
            $out .= $tmp;
        }

        $this->template = str_replace( $columnIdHash, $columnIdentity, $this->template );
        $this->template = preg_replace( '/<'.$loopName.'[^>]*?>.*?<\/'.$loopName.'>/is', $out, $this->template );
        return $this->template;
    }

    /**
    * Method will parse template and update mapper definition
    * @param [string] $loopName - name on the loop in template to parse and update
    * @return [boolean]
    */
    public function updateMap(){
        $out = '';

        preg_match( '/<phpDBMapper:map>(.*)<\/phpDBMapper:map>/s', $this->template, $m );

        foreach( $this->tableMapper as $c ){
            $tmp = $m[1];

            foreach( $c as $key=>$val ){
                $tmp = str_replace( '%'. $key .'%', $val, $tmp );
                $tmp = str_replace(array("\n", "\r"), '', $tmp);
            }

            $out .= $tmp . "\n";
        }

        $this->template = preg_replace( '/<phpDBMapper:map[^>]*?>.*?<\/phpDBMapper:map>/is', $out, $this->template );
        return $this->template;
    }

    /**
    * Method will write file
    * @return [boolean]
    */
    public function save(){
        return parent::writeFile( $this->outputFile, $this->template);
    }

}
