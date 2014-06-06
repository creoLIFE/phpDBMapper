<?php
/**
 * DAO model
 * @package Application_Model
 * @author Mirek Ratman
 * @version 1.0
 * @since 2014-06-06
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class Application_Model_Dao
{

    /**
    * Method will create DAO class
    * @method create
    * @param [array] $data = data to update
    * @return [boolean]
    */
    public function create(){

        //DAO      
        $dao = file_get_contents( $dirInput . '/dao.template' );
        foreach( $elToChange as $key=>$val ){
            $dao = str_replace( '%'. $key .'%', $val, $dao );
        }
        if( !is_dir($dirOutput . '/Dao') ) {
            mkdir($dirOutput . '/Dao');
        }
        file_put_contents( $dirOutput . '/Dao/' . $elToChange['pClassName'] . '.php', $dao);

    }

}
