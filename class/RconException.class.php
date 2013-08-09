<?php

/**
 * Copyright (c) 2013 Daniele Pantaleone
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author      Daniele Pantaleone
 * @copyright   Daniele Pantaleone, 3 August, 2013
 * @version     1.0
 **/

class RconException extends Exception {
    
    /**
     * Construct an RconException object.
     * 
     * @author Daniele Pantaleone
     * @param  string $message The Exception message
     * @param  int $code The user defined Exception code
     * @param  Exception $previous A previous Exception (if nested)
     */
    public function __construct($message, $code=0, Exception $previous=NULL) {
        parent::__construct($message, $code, $previous);
    }

    
    /**
     * String object representation.
     * 
     * @author Daniele Pantaleone
     * @see    Exception::__toString()
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}