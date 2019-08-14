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

class Fontera_GraphicMail_Helper_Data extends Fontera_GraphicMail_Helper_Sc
{
    /**
     * General settings
     *
     * @var string
     */
    protected $_generalSettings = 'general_settings';
    
    /**
     * Account settings
     *
     * @var string
     */
    protected $_accountSettings = 'account_settings';
    
    /**
     * Gateway URL
     *
     * @var string
     */
    protected $_gatewayUrl = 'https://www.graphicmail.co.za/api.aspx';
    
    /**
     * Retrieve lowecase module name without namespace
     *
     * @return string
     */
    protected function getModName()
    {
        $moduleName = $this->_getModuleName();
        $moduleNameArray = explode('_',$moduleName);
        $namespace = $moduleNameArray[0];
        $modname = $moduleNameArray[1];
        
        return strtolower($modname);
    }
    
    /**
     * Debug Logging
     * 
     * @param mixed | string $message
     * @return
     */
    public function debug($message)
    {
        if ($this->isDebugMode()) {
            Mage::log($message, false, $this->_getModuleName().'-debug.log');
        }
    }
    
    /**
     * Exception logging
     * 
     * @param Exception $e
     * @return
     */
    public function exception(Exception $e)
    {
        Mage::log("\n" . $e->__toString(), Zend_Log::ERR, $this->_getModuleName().'-exception.log');
    }
    
    /**
     * Is debug mode active
     * 
     * @return bool
     */
    public function isDebugMode()
    {
        return Mage::getStoreConfig($this->getModName() . '/' . $this->_generalSettings . '/debug');
    }
    
    /**
     * Get Gateway URL
     * 
     * @return string
     */
    public function getGatewayUrl()
    {
        return $this->_gatewayUrl;
    }
    
    /**
     * Get account username
     * 
     * @return string
     */
    public function getAccountUsername()
    {
        return Mage::getStoreConfig($this->getModName() . '/' . $this->_accountSettings . '/username');
    }
    
    /**
     * Get account password
     * 
     * @return string
     */
    public function getAccountPassword()
    {
        return Mage::getStoreConfig($this->getModName() . '/' . $this->_accountSettings . '/password');
    }
    
    /**
     * Get account SID
     * 
     * @return string | int
     */
    public function getAccountSid()
    {
        return Mage::getStoreConfig($this->getModName() . '/' . $this->_accountSettings . '/sid');
    }
    
    /**
     * Build API params
     * 
     * @param string $function
     * @param array | bool $addParams 
     * @return array | bool
     */
    public function buildApiParams($function, $addParams)
    {
        $username = $this->getAccountUsername();
        $password = $this->getAccountPassword();
        $sid = $this->getAccountSid();
        
        if (($function != '') && ($username != '') && ($password != '') && ($sid != '')) {
            $params = array(
                'Username'  => $username,
                'Password'  => $password,
                'Function'  => $function,
            );
            
            if ($addParams) {
                foreach ($addParams as $k => $v) {
                    $params[$k] = $v;
                }
            }
            
            $params['SID'] = $sid;
            
            return $params;
        } else {
            return false;
        }
    }
    
    /**
     * Check if is valid xml
     * 
     * @param string $xml
     * @return mixed
     */
    public function isValidXml($xml)
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);
        $errors = libxml_get_errors();
        
        return empty($errors);
    }
    
    /**
     * Get setup types option select
     * 
     * @return array
     */
    public function getSetupTypesOptionSelect()
    {
        $options = array(
            '1' => $this->__('Newsletter Subscriptions'),
            '2' => $this->__('New Customer'),
        );
        
        return $options;
    }
    
    /**
     * Get dataset mapping for a specific form type
     * 
     * @param int $type
     * @return bool | array
     */
    public function getDatasetMapping($type)
    {
        $collection = Mage::getModel('fontera_graphicmail/mailinglists')->getCollection()
            ->addFieldToFilter('setup_type', array('eq' => $type));
        
        // We fetch the first item as there will only be one with that type, if it was setup.
        $item = $collection->getFirstItem();
        $data = $item->getData();
        
        if (!empty($data)) {
           if (isset($data['mapped_dataset_fields'])) {
                $mappedFields = @unserialize($data['mapped_dataset_fields']);
                if (($mappedFields != false) && (is_array($data))) {
                    $result = array(
                        'dataset_id'    => $data['dataset_id'],
                        'mailinglist_id'    => $data['mailinglist_id'],
                        'mapped_fields' => $mappedFields,
                    );
                    
                    return $result;
                }
            }
        }
        
        return false;
    }
}