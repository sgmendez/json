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

namespace Sgmendez\Json\Tests;

require_once __DIR__ . '/../src/Json.php';

use Sgmendez\Json\Json;
use InvalidArgumentException;
use RuntimeException;
use LengthException;
use Exception;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected $json;
    protected $resource;
    protected $array;

    protected function setUp()
    {
        $this->json = new Json();
        $this->resource = curl_init('http://google.com');
        $this->array = array('prueba', 'ejemplo');
    }
    
    public function testEncodeFailData()
    {        
        try
        {
            $this->json->encode($this->resource);
        } 
        catch (RuntimeException $ex) 
        {
            return;
        }
        
        $this->fail('An expected RuntimeException has not been thrown');
    }
    
    public function testEncodeFailOptions()
    {
        try
        {
            $this->json->encode($this->array,  'no_valido');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$options argument only accepts int*\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testEncodeFailDepth()
    {
        try
        {
            $this->json->encode($this->array,  0, 'novalido');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$depth argument only accepts int*\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testEncode()
    {
        try
        {
            $jsonData = $this->json->encode($this->array);
        } 
        catch (Exception $ex) 
        {
            $this->fail('Not expect exception: '.$ex->getMessage());
        }
        
        $this->assertJsonStringEqualsJsonString(json_encode($this->array), $jsonData);
    }
    
    public function testEncodeDepthExceded()
    {
        try
        {
            $arrayDepth = array(array(array('prueba')));
            $jsonData = $this->json->encode($arrayDepth, 0, 1);
        } 
        catch(LengthException $ex) 
        {
            $this->assertEquals('The maximum stack depth has been exceeded', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected LengthException has not been thrown');
    }
    
    public function testDecodeFailData()
    {
        try
        {
            $this->json->decode($this->array);
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$data argument only accepts string\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testDecodeAssocFail()
    {
        try
        {
            $this->json->decode(json_encode($this->array), 'novalido');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$assoc argument only accepts bool(.*)\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testDecodeDepthFail()
    {
        try
        {
            $this->json->decode(json_encode($this->array), true, 'novalido');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$depth argument only accepts int*\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testDecodeOptionsFail()
    {
        try
        {
            $this->json->decode(json_encode($this->array), true, 512, 'novalido');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertRegExp('/\$options argument only accepts int*\. Input type was: */i', $ex->getMessage());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
    
    public function testDecodeArray()
    {        
        try
        {
            $jsonData = '{"id": 123456789, "user": "User"}';
            $arrayData = $this->json->decode($jsonData);
        } 
        catch (Exception $ex) 
        {
            $this->fail('Not expect exception: '.$ex->getMessage());
        }
        
        $this->assertEquals('array', gettype($arrayData));
        $this->assertArrayHasKey('id', $arrayData);
        $this->assertArrayHasKey('user', $arrayData);
    }
    
    public function testDecodeObject()
    {
        try
        {
            $jsonData = '{"id": 123456789, "user": "User"}';
            $objData = $this->json->decode($jsonData, false);
        } 
        catch (Exception $ex) 
        {
            $this->fail('Not expect exception: '.$ex->getMessage());
        }
        
        $this->assertEquals('stdClass', get_class($objData));
        $this->assertNotEmpty($objData->id);
        $this->assertNotEmpty($objData->user);
    }
    
    public function testDecodeFile()
    {
        try
        {
            $arrayData = $this->json->decodeFile('example.json');
        } 
        catch (Exception $ex) 
        {
            $this->fail('Not expect exception: '.$ex->getMessage());
        }
        
        $this->assertEquals('array', gettype($arrayData));
        $this->assertArrayHasKey('id', $arrayData);
        $this->assertArrayHasKey('user', $arrayData);
        $this->assertArrayHasKey('code', $arrayData);
    }
    
    public function testCheckValidJsonData()
    {
        $jsonValid = '{"id": 123456789, "user": "User"}';
        $checkTrue = $this->json->checkValidJsonData($jsonValid);
        
        $this->assertTrue($checkTrue);
        
        $jsonNoValid = array('novalido');
        $checkFalse = $this->json->checkValidJsonData($jsonNoValid);
        
        $this->assertFalse($checkFalse);
    }
    
    public function testCheckValidJsonFile()
    {
        $chekTrue = $this->json->checkValidFileJsonData('example.json');
        $this->assertTrue($chekTrue);
        
        $chekFalse = $this->json->checkValidFileJsonData('http://google.com');
        $this->assertFalse($chekFalse);
    }
}
