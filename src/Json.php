<?php

/**
 * Copyright (c) 2015, Salvador Mendez
 * All rights reserved. 
 * 
 * This software is licensed by BSD 2-Clause License, you may obtain 
 * a copy of the license at the LICENSE file or at:
 * 
 * http://opensource.org/licenses/bsd-license.php
 * 
 * @author Salvador Mendez <salva@sgmendez.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD 2-Clause License
 * @copyright (c) 2015, Salvador Mendez
 * @package sgmendez/json
 * @version 1.0
 * 
 */

namespace Sgmendez\Json;

use BadFunctionCallException;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;
use LengthException;
use Exception;

class Json
{
    /**
     * Returns the JSON representation of a value
     * 
     * @param mixed $data Valid all type except a resource
     * @param integer $options Acepted values: JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     *                         JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     *                         JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE
     * @param integer $depth Set the maximum depth, must be greater than 0
     * @return mixed JSON encoded string on success or FALSE on failue
     */
    public function encode($data, $options = 0, $depth = 512)
    {
        $dataValid = $this->validateType('not_resource', $data);
        $optionsValid = $this->validateType('int', $options, '$options');
        $depthValid = $this->validateType('int', $depth, '$depth');
        
        $jsonData = json_encode($dataValid, $optionsValid, $depthValid);
        $jsonError = $this->checkJsonError();
        
        return $jsonData;
    }
    
    /**
     * 
     * @param string $data JSON string encoded
     * @param boolean $assoc TRUE return associative array, FALSE object
     * @param integer $depth Set the maximum depth, must be greater than 0
     * @param integer $options Acepted values: JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     *                         JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     *                         JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE
     * @return mixed $data Returns the value encoded in json in appropriate PHP type
     */
    public function decode($data, $assoc = true, $depth = 512, $options = 0)
    {
        $dataValid = $this->validateType('string', $data, '$data');
        $assocValid = $this->validateType('boolean', $assoc, '$assoc');
        $depthValid = $this->validateType('int', $depth, '$depth');
        $optionsValid = $this->validateType('int', $options, '$options');
        
        $data = json_decode($dataValid, $assocValid, $depthValid, $optionsValid);
        $jsonEror = $this->checkJsonError();
        
        return $data;
    }
    
    /**
     * Decode JSON data encoded in file
     * 
     * @param string $file
     * @param boolean $assoc
     * @param integer $depth
     * @param integer $options
     * @return mixed
     * @throws RuntimeException
     */
    public function decodeFile($file, $assoc = true, $depth = 512, $options = 0)
    {
        set_error_handler(
                create_function(
                        '$severity, $message, $file, $line', 'throw new ErrorException($message, $severity, $severity, $file, $line);'
                )
        );
        
        try
        {
            $jsonData = file_get_contents($file);
        }
        catch (Exception $e)
        {
            throw new RuntimeException(sprintf($e->getMessage()));
        }

        restore_error_handler();
        
        if(false === $jsonData)
        {
            throw new RuntimeException(sprintf('Unable to get file %s', $file));
        }
        
        return $this->decode($jsonData, $assoc, $depth, $options);
    }
    
    /**
     * Check if $jsonData is valid encoded JSON
     * 
     * @param string $jsonData
     * @return boolean
     */
    public function checkValidJsonData($jsonData)
    {
        try
        {
            $this->decode($jsonData);
            return true;
        } 
        catch (Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Check if $file content a valid encoded JSON data
     * 
     * @param string $file
     * @return boolean
     */
    public function checkValidFileJsonData($file)
    {
        try
        {
            $this->decodeFile($file);
            return true;
        } 
        catch (Exception $e) 
        {
            return false;
        }
    }
    
    /**
     * Check exists error for last execution json funtion
     * 
     * @return boolean
     * @throws BadFunctionCallException
     */
    protected function checkJsonError()
    {
        if(!function_exists('json_last_error'))
        {
            throw new BadFunctionCallException('json_last_error() function not exits');
        }
        
        $jsonError = json_last_error();
        if($jsonError !== JSON_ERROR_NONE)
        {
            $this->jsonErrorException($jsonError);
        }
        
        return true;
    }
    
    /**
     * Generate exception when $code is a json defined error
     * 
     * @param integer $code
     * @throws LengthException
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    protected function jsonErrorException($code)
    {
        $this->defineCodesErrorJson();
        
        switch($code)
        {
            case JSON_ERROR_DEPTH:
                throw new LengthException('The maximum stack depth has been exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new UnexpectedValueException('Invalid or malformed JSON');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new UnexpectedValueException('Control character error, possibly incorrectly encoded');
                break;
            case JSON_ERROR_SYNTAX:
                throw new RuntimeException('Syntax error');
                break;
            case JSON_ERROR_UTF8:
                throw new RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
            case JSON_ERROR_RECURSION:
                throw new RuntimeException('One or more recursive references in the value to be encoded');
                break;
            case JSON_ERROR_INF_OR_NAN:
                throw new RuntimeException('One or more NAN or INF values in the value to be encoded');
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                throw new UnexpectedValueException('A value of a type that cannot be encoded was given');
                break;
            default :
                $msg = (function_exists('json_last_error_msg')) ? json_last_error_msg() : 'Code ERROR not valid for JSON';
                throw new UnexpectedValueException($msg);
        }
    }
    
    /**
     * This const works PHP 5.5 only
     */
    private function defineCodesErrorJson()
    {
        if(!defined(JSON_ERROR_RECURSION))
        {
            define(JSON_ERROR_RECURSION, 6);
        }
        
        if(!defined(JSON_ERROR_INF_OR_NAN))
        {
            define(JSON_ERROR_INF_OR_NAN, 7);
        }
        
        if(!defined(JSON_ERROR_UNSUPPORTED_TYPE))
        {
            define(JSON_ERROR_UNSUPPORTED_TYPE, 8);
        }
    }
    
    /**
     * Validate type of $value
     * 
     * @param string $type Type of de value expect
     * @param mixec $value
     * @param string $name Name of the variable $value
     * @return mixed
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function validateType($type, $value, $name = '')
    {
        $valueType = gettype($value);
        
        $codeException = $this->checkType($type, $value);
        
        if(1 === $codeException)
        {
            throw new InvalidArgumentException(sprintf('%s argument only accepts %s. Input type was: %s', $name, $type, $valueType));
        }
        
        if(2 === $codeException)
        {
            throw new RuntimeException('The argument is a resource, not is a valid type');
        }
        
        if(3 === $codeException)
        {
            throw new RuntimeException(sprintf('Value not defined in validation for %s', $type));
        }
        
        return $value;
    }
    
    /**
     * Check type of $value
     * 
     * @param string $type Type expect for $value
     * @param mixed $value Value of the variable to check
     * @return int Code for exception
     */
    private function checkType($type, $value)
    {        
        switch($type)
        {
            case 'bool':
            case 'boolean':
                $codeException = (!is_bool($value)) ? 1 : 0;
                break;
            case 'int':
            case 'integer':
                $codeException = (!is_int($value)) ? 1 : 0;
                break;
            case 'string':
                $codeException = (!is_string($value)) ? 1 : 0;
                break;
            case 'not_resource':
                $codeException = (is_resource($value)) ? 2 : 0;
                break;
            default :
                $codeException = 3;
                break;
        }
        
        return $codeException;
    }
}