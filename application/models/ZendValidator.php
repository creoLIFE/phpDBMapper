<?php
/**
 * Validator class
 * @package Application_Helpers
 * @author Mirek Ratman
 * @version 1.0
 * @since 2014-08-29
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class Application_Model_ZendValidator
{

    /**
    * @var [string]
    */
    private $content;

    /**
    * method will fix some string to App standard format
    * @param [string] $valueType - type of value to validate
    * @param [string] $content - content to replace
    * @param [string] $validatorNameKey - hash for zend validator type used in content
    * @param [string] $validatorPatternKey - hash for zend validator pattern used in content
    * @return [string]
    */
    public function __construct( $valueType, $content, $validatorNameKey = '%pValidator%', $validatorPatternKey = '%pValidatorPattern%' ){
        $this->content = $content;

        switch( strtolower($valueType) ){
            case 'tinyint':
            case 'int':
                $this->content = str_replace( $validatorNameKey, 'Int', $this->content );
                $this->content = str_replace( $validatorPatternKey, "array('pattern' => '/[0-9]+/')", $this->content );
            break;
            case 'tinytext':
            case 'text':
                $this->content = str_replace( $validatorNameKey, 'Regex', $this->content );
                $this->content = str_replace( $validatorPatternKey, "array('pattern' => '/[a-zA-Z0-9\s\#\.\,\!\?\-\_]+/')", $this->content );
            break;
            case 'varchar':
                $this->content = str_replace( $validatorNameKey, 'Regex', $this->content );
                $this->content = str_replace( $validatorPatternKey, "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_\:\/]+/')", $this->content );
            break;
            case 'datetime':
                $this->content = str_replace( $validatorNameKey, 'Date', $this->content );
                $this->content = str_replace( $validatorPatternKey, "array('format' => 'Y-m-d H:m:s.u')", $this->content );
            break;
            case 'longtext':
                $this->content = str_replace( $validatorNameKey, 'Regex', $this->content );
                $this->content = str_replace( $validatorPatternKey, "array('pattern' => '/[a-zA-Z0-9\s\.\,\!\?\-\_]+/')", $this->content );
            break;
        }
    }

    /**
    * Method will get parsed content
    * @return [string]
    */
    public function getContent(){
        return $this->content;
    }


}
