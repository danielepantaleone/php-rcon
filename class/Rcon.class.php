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

include_once("RconException.class.php");

class Rcon {
    
    private $address;
    private $port;
    private $password;
    private $timeout;
    
    private $socket;
    private $errno;
    private $errstr;
    
    
    /**
     * Construct an Rcon object.
     * 
     * @param string $address The ip/domain where to send RCON commands
     * @param int $port The port on which the server accepts RCON commands
     * @param string $password The server RCON password
     * @param float $timeout An optional UDP socket timeout
     */
    public function __construct($address, $port, $password, $timeout=2) {
        $this->address = $address;
        $this->port = $port;
        $this->password = $password;
        $this->timeout = $timeout;
    }
    
    
    /**
     * Initialize the UDP socket where to send RCON commands.
     * 
     * @author Daniele Pantaleone
     * @throws RconException If the socket fails in being created
     */
    private function connect() {
        
        // create the UDP socket where to send data
        $this->socket = fsockopen("udp://{$this->address}",
                                  $this->port,
                                  $this->errno,
                                  $this->errstr,
                                  $this->timeout);
        
        if (!$this->socket)
            throw new RconException("could not connect to address {$this->address} on port {$this->port}", 
                                    $this->errno, 
                                    $this->errstr);
        
    }
    
    
    /**
     * Close a previously initialized UDP socket.
     * Will do nothing if the socket is already closed.
     *
     * @author Daniele Pantaleone
     */
    private function disconnect(){
        
        if (!is_null($this->socket)) {
            fclose($this->socket);
            $this->socket = NULL;
        }
       
    }
    
    
    /**
     * Read a server response from the UDP socket.
     * Will return NULL if the server response is not valid.
     * 
     * @author Daniele Pantaleone
     * @throws RconException If the UDP socket has not been correctly initialized
     * @return The server response as a string if it's valid otherwise NULL
     */
    private function read() {
        
        if (is_null($this->socket))
            throw new RconException("could not read response: UDP socket is NULL");
        
        stream_set_timeout($this->socket, 0, $this->timeout * 100000);
        
        $response = '';
        while ($buffer = fread($this->socket, 4096)) {
            list($header, $content) = explode("\n", $buffer, 2);
            $response .= $content;
        }
        
        $response = trim($response);
        
        if (empty($response))
            return NULL;
        
        return preg_replace("/\^./","", $response);
           
    }
    
    
    /**
     * Write a command con the UDP socket.
     * 
     * @author Daniele Pantaleone
     * @param  string $command The command to be executed
     * @throws RconException If the UDP socket has not been initialized
     */
    private function write($command) {
        
        if (is_null($this->socket))
            throw new RconException("could not send command {$command}: UDP socket is NULL");
        
        fwrite($this->socket, str_repeat(chr(255), 4) . "rcon {$this->password} {$command}\n");
        
    }
    
    
    /**
     * Send an RCON command. Will return the server response if
     * specified in the method execution. Will return NULL if the server
     * response is not valid or if we are not supposed to retrieve it
     * 
     * @param  string $command The command to be executed
     * @param  boolean $read Whether to return the server response or not (optional)
     * @throws RconException If the command couldn't be sent to the server
     * @return The server response to the given RCON command is specified in the
     *         method execution or NULL if the server response is not valid or if
     *         we are not supposed to retrieve it
     */
    public function send($command, $read=FALSE) {
        
        $this->connect();
        $this->write($command);
        
        if (!$read) {
            $this->disconnect();
            return NULL;
        }
        
        $res = $this->read();
        $this->disconnect();
        
        return $res;

    }
        
}
