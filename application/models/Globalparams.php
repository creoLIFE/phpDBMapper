<?php
/**
 * Global params model
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

class Application_Model_Globalparams
{

    /**
    * @var [string]
    */
    public $appName = '';

    /**
    * @var [string]
    */
    public $appAuthor = '';

    /**
    * @var [string]
    */
    public $appUrl = '';

    /**
    * @var [string]
    */
    public $appGithubUrl = '';

    /**
    * @var [string]
    */
    public $appVer = '';

    /**
    * @var [string]
    */
    public $appLicense = '';

    /**
    * @var [string]
    */
    public $appCopyright = '';

    /**
    * @var [string]
    */
    public $appGenerated = '';



    /**
    * @var [string]
    */
    public $pDbConfig = '';

    /**
    * @var [string]
    */
    public $pMapperPrefix = '';

    /**
    * @var [string]
    */
    public $pTableName = '';

    /**
    * @var [string]
    */
    public $pTableIndexCell = '';

    /**
    * @var [string]
    */
    public $pTableIndexCellFixed = '';

    /**
    * @var [string]
    */
    public $pTableRelation = '';

    /**
    * @var [string]
    */
    public $pClassName = '';

    /**
    * @var [string]
    */
    public $pDateTime = '';

    /**
    * @var [string]
    */
    public $pDate = '';

    /**
    * @var [string]
    */
    public $pYear = '';



    /**
    * @var [mixed]
    */
    private $json = '';


    /**
    * Class constructor
    */
    public function __construct(){
        $this->json = json_decode(file_get_contents('../composer.json'));

        $this->appName = $this->appName . $this->json->name;
        $this->appAuthor = $this->json->authors[0]->name;
        $this->appUrl = $this->json->homepage;
        $this->appGithubUrl = $this->json->github;
        $this->appVer = $this->json->version;
        $this->appLicense = $this->json->licence;
        $this->appCopyright = $this->json->copyright;
        $this->appGenerated = date("Y-m-d H:i:s");

        $this->pDateTime = date("Y-m-d H:i:s");
        $this->pDate = date("Y-m-d");
        $this->pYear = date("Y");
    }
    
    public function __toArray() {
        $array = get_object_vars($this);
        unset($array['json']);

        return $array;
    }



    /**
    * @param [string] $str = data to set
    */
    public function setpDbConfig( $str ){
        $this->pDbConfig = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpMapperPrefix( $str ){
        $this->pMapperPrefix = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpTableName( $str ){
        $this->pTableName = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpTableIndexCell( $str ){
        $this->pTableIndexCell = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpTableIndexCellFixed( $str ){
        $this->pTableIndexCellFixed = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpClassName( $str ){
        $this->pClassName = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpTableRelation( $str ){
        $this->pTableRelation = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpDateTime( $str ){
        $this->pDateTime = $str;
    }


    /**
    * @param [string] $str = data to set
    */
    public function setpDate( $str ){
        $this->pDate = $str;
    }

    /**
    * @param [string] $str = data to set
    */
    public function setpYear( $str ){
        $this->pYear = $str;
    }

}
