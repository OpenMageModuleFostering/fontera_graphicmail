<?php
/**
 * Fontera GraphicMail
 *
 * NOTICE OF LICENSE
 *
 * Private Proprietary Software (http://fontera.co.za/legal)
 *
 * @category   Fontera
 * @package    Fontera_GraphicMail
 * @copyright  Copyright (c) 2014 Fontera (http://www.fontera.com)
 * @license    http://fontera.co.za/legal  Private Proprietary Software
 * @author     Shaughn Le Grange - Hatlen <support@fontera.com>
 */

class Fontera_GraphicMail_Model_GraphicMail_Api extends Fontera_GraphicMail_Model_Abstract
{
    /**
     * Api response
     * 
     * @param string $function
     * @param array | bool $params
     * @param array | bool $addParams
     */
    public function apiResponse($function, $params = false, $addParams = false)
    {
        $helper = $this->_helper();
        $response = array();
        
        if (!$params) {
            $params = $helper->buildApiParams($function, $addParams);
        }
        
        $helper->debug('API Parameters:');
        $helper->debug($params);
        
        try {
            if ($params) {
                $client = new Zend_Http_Client();
                $client->setUri($helper->getGatewayUrl());
                $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
                $client->setParameterPost($params);
                
                $request = $client->request('POST');
                $responseBody = $request->getBody();
                
                // Is valid XML
                if ($helper->isValidXml($responseBody)) {
                    $response['state'] = true;
                    $response['object'] = $this->parseXml($responseBody);
                // Else return string response
                } else {
                    
                    $resultArray = $this->parseString($responseBody);
                    $helper->debug($resultArray);
                    
                    // If result code is 0, we return error as any API response code 0 is an error
                    if ($resultArray['code'] == 0) {
                        $response['state'] = false;
                    } else {
                        $response['state'] = true;
                    }
                    
                    $response['message'] = $resultArray['message'];
                }
            } else {
                $message = 'API parameters are invalid or not set.';
                $helper->debug($message);
                $response['state'] = false;
                $response['message'] = $message;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $helper->debug($message);
            $response['state'] = false;
            $response['message'] = $message;
        }
        
        return $response;
    }
    
    /**
     * XML to array
     * 
     * @param simpleXMLElement Object
     */
    public function xmlToArray($obj)
    {
        $arr = array();
        foreach ($obj->children() as $r) {
            $t = array();
            if(count($r->children()) == 0) {
                $arr[$r->getName()] = strval($r);
            } else {
                $arr[$r->getName()][] = $this->xmlToArray($r);
            }
        }
        
        return $arr;
    }
    
    /**
     * Parse XML
     * 
     * @param string $xml
     */
    public function parseXml($xml)
    {
        $helper = $this->_helper();
        
        $helper->debug('API Response:');
        $helper->debug($xml);
        
        $obj = simplexml_load_string($xml); // To simpleXMLElement Object
        $arr = $this->xmlToArray($obj);
        
        $result = new Varien_Object($arr); // To Varien_Object
        
        $helper->debug('API Parsed Response:');
        $helper->debug($result);
        
        return $result;
    }
    
    /**
     * Parse string
     * 
     * @param string $output
     */
    public function parseString($output)
    {
        $helper = $this->_helper();
        
        $arrayOutput = explode('|', $output);
        
        $result = array();
        
        if (isset($arrayOutput[0]) && isset($arrayOutput[1])) {
            $result['code'] = $arrayOutput[0];
            $result['message'] = $arrayOutput[1];
        } else {
            $result['code'] = 0;
            $result['message'] = $helper->__('Gateway returned malformed output.');
        }
        
        return $result;
    }
}