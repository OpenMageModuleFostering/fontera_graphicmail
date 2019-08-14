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

class Fontera_GraphicMail_Model_GraphicMail extends Fontera_GraphicMail_Model_Abstract
{
    /**
     * API Model
     */
    private $apiModel = false;
    
    /**
     * Get API model
     * 
     * @return Fontera_GraphicMail_Model_GraphicMail_Api
     */
    public function getApiModel()
    {
        if (!$this->apiModel) {
            $this->apiModel = Mage::getModel('fontera_graphicmail/graphicMail_api');
        }
        
        return $this->apiModel;
    }
    
    /**
     * Get datasets
     * 
     * @return
     */
    public function getDatasets()
    {
        $data = array();
        
        $api = $this->getApiModel();
        $result = $api->apiResponse('get_datasets');
        
        if ($result['state']) {
            $obj = $result['object'];
            $data = $obj->getData('dataset');
        }
        
        return $data;
    }
    
    /**
     * Get datasets options getter
     *
     * @return array
     */
    public function getDatasetsOptionsArray()
    {
        $options = array();
        
        $datasets = $this->getDatasets();
        
        if ($datasets && count($datasets) > 0) {
            $options[] = array('value' => '', 'label' => $this->_helper()->__('Select a Dataset'));
            
            foreach ($datasets as $dataset) {
                if (isset($dataset['datasetid']) && isset($dataset['name'])) {
                    $options[] = array('value' => $dataset['datasetid'], 'label' => $dataset['name']);
                }
            }
        // As per GraphicMail, there will always be at least 1 dataset, but we will notify the user just incase.
        } else {
            $options[] = array('value' => '', 'label' => $this->_helper()->__('No datasets available.'));
        }
        
        return $options;
    }
    
    /**
     * Get mailing lists
     * 
     * @return
     */
    public function getMailingLists()
    {
        $data = array();
        
        $api = $this->getApiModel();
        $result = $api->apiResponse('get_mailinglists');
        
        if ($result['state']) {
            $obj = $result['object'];
            $data = $obj->getData('mailinglist');
        }
        
        return $data;
    }
    
    /**
     * Get mailing lists options getter
     *
     * @return array
     */
    public function getMailingListsOptionsArray()
    {
        $options = array();
        
        $mailinglists = $this->getMailingLists();
        
        if ($mailinglists && count($mailinglists) > 0) {
            $options[] = array('value' => '', 'label' => $this->_helper()->__('Select a Mailing List'));
            
            foreach ($mailinglists as $mailinglist) {
                if (isset($mailinglist['mailinglistid']) && isset($mailinglist['description'])) {
                    $options[] = array('value' => $mailinglist['mailinglistid'], 'label' => $mailinglist['description']);
                }
            }
        } else {
            $options[] = array('value' => '', 'label' => $this->_helper()->__('No mailing lists available.'));
        }
        
        return $options;
    }
    
    /**
     * Get dataset columns
     * 
     * @param int $datasetId
     * @return array $data
     */
    public function getDatasetColumns($datasetId)
    {
        $data = array();
        
        if ($datasetId != null) {
            $addParams = array('DatasetID' => $datasetId);
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('get_dataset_columns', false, $addParams);
            
            if ($result['state']) {
                $obj = $result['object'];
                $data = $obj->getData();
                $data['Email'] = 'Email Address';
                $data['MobileNumber'] = 'Mobile Number';
                
                // Unset non required values
                unset($data['datasetid']);
                unset($data['name']);
            }
        }
        
        return $data;
    }
    
    /**
     * Create user
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @return void
     */
    public function createUser($customer)
    {
        $type = 2;
        $helper = $this->_helper();
        $datasetMapping = $helper->getDatasetMapping($type);
        
        // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
        if (isset($datasetMapping['dataset_id']) && isset($datasetMapping['mapped_fields'])) {
            $addParams = array(
                'DatasetID' => $datasetMapping['dataset_id'],
            );
            
            $customerAddress = array();
            foreach ($customer->getAddresses() as $address) {
                $customerAddress = $address->toArray();
            }
            
            foreach ($datasetMapping['mapped_fields'] as $field) {
                $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                
                // Add customer params
                $addParams[$field['graphicmail_field']] = $customer->getData($field['magento_field']);
                
                // Add customer address params
                if (isset($customerAddress[$field['magento_field']])) {
                    $addParams[$field['graphicmail_field']] = $customerAddress[$field['magento_field']];
                }
            }
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('post_insertdata', false, $addParams);
            
            // we do not need to do anything with the result or return a response, since this is a background process.
        }
    }
    
    /**
     * Subscribe newsletter
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @return void
     */
    public function subscribeNewsletter($customer)
    {
        $type = 1;
        $helper = $this->_helper();
        $datasetMapping = $helper->getDatasetMapping($type);
        
        // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
        if (isset($datasetMapping['mailinglist_id']) && isset($datasetMapping['mapped_fields'])) {
            $addParams = array(
                'MailinglistID' => $datasetMapping['mailinglist_id'],
            );
            
            foreach ($datasetMapping['mapped_fields'] as $field) {
                $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                
                $addParams[$field['graphicmail_field']] = $customer->getData($field['magento_field']);
            }
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('post_subscribe', false, $addParams);
            
            // we do not need to do anything with the result or return a response, since this is a background process.
        }
    }
    
    /**
     * Unsubscribe newsletter
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @return void
     */
    public function unsubscribeNewsletter($customer)
    {
        $type = 1;
        $helper = $this->_helper();
        $datasetMapping = $helper->getDatasetMapping($type);
        
        // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
        if (isset($datasetMapping['mailinglist_id']) && isset($datasetMapping['mapped_fields'])) {
            $addParams = array(
                'MailinglistID' => $datasetMapping['mailinglist_id'],
            );
            
            foreach ($datasetMapping['mapped_fields'] as $field) {
                $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                
                $addParams[$field['graphicmail_field']] = $customer->getData($field['magento_field']);
            }
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('post_unsubscribe', false, $addParams);
            
            // we do not need to do anything with the result or return a response, since this is a background process.
        }
    }
    
    /**
     * Subscribe newsletter guest
     * 
     * @param string $email
     * @return void
     */
    public function subscribeNewsletterGuest($email)
    {
        $type = 1;
        $helper = $this->_helper();
        $datasetMapping = $helper->getDatasetMapping($type);
        
        // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
        if (isset($datasetMapping['mailinglist_id']) && isset($datasetMapping['mapped_fields'])) {
            $addParams = array(
                'MailinglistID' => $datasetMapping['mailinglist_id'],
            );
            
            foreach ($datasetMapping['mapped_fields'] as $field) {
                $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                
                if ($field['graphicmail_field'] == 'Email') {
                    $addParams[$field['graphicmail_field']] = trim($email);
                }
                
                if ($field['magento_field'] == 'firstname') {
                    $addParams[$field['graphicmail_field']] = 'Guest';
                }
                
                if ($field['magento_field'] == 'lastname') {
                    $addParams[$field['graphicmail_field']] = 'Guest';
                }
            }
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('post_subscribe', false, $addParams);
            
            // we do not need to do anything with the result or return a response, since this is a background process.
        }
    }
    
    /**
     * Unsubscribe newsletter guest
     * 
     * @param string $email
     * @return void
     */
    public function unsubscribeNewsletterGuest($email)
    {
        $type = 1;
        $helper = $this->_helper();
        $datasetMapping = $helper->getDatasetMapping($type);
        
        // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
        if (isset($datasetMapping['mailinglist_id']) && isset($datasetMapping['mapped_fields'])) {
            $addParams = array(
                'MailinglistID' => $datasetMapping['mailinglist_id'],
            );
            
            foreach ($datasetMapping['mapped_fields'] as $field) {
                $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                
                if ($field['graphicmail_field'] == 'Email') {
                    $addParams[$field['graphicmail_field']] = trim($email);
                }
            }
            
            $api = $this->getApiModel();
            $result = $api->apiResponse('post_unsubscribe', false, $addParams);
            
            // we do not need to do anything with the result or return a response, since this is a background process.
        }
    }
    
    /**
     * Export existing customers to GraphicMail
     * 
     * @return array
     */
    public function exportCustomers()
    {
        $type = 2;
        $helper = $this->_helper();
        
        $result = array('state' => false);
        
        try {
            $datasetMapping = $helper->getDatasetMapping($type);
            
            // If the dataset mapping is not set, it means it has not yet been setup, so essentially we do not need to do anything.
            if (isset($datasetMapping['dataset_id']) && isset($datasetMapping['mailinglist_id']) && isset($datasetMapping['mapped_fields'])) {
                $customerCollection = Mage::getModel('customer/customer')->getCollection();
                $customerCollection->addAttributeToSelect('*');
                
                $customersArray = array();
                $csvColsMapping = array();
                $csvData = array();
                
                foreach ($customerCollection as $customer) {
                    $customerData = array(
                        'customer' => $customer->getData()
                    );
                    
                    $addresses = $customer->getAddresses();
                    foreach ($addresses as $address) {
                        $customerData['address'] = $address->getData();
                    }
                    
                    $csvCol = 0;
                    foreach ($datasetMapping['mapped_fields'] as $field) {
                        $field['magento_field'] = str_replace('customer_', '', $field['magento_field']);
                        $field['magento_field'] = str_replace('address_', '', $field['magento_field']);
                        
                        $csvCol++;
                        $csvColsMapping[$field['graphicmail_field']] = $csvCol;
                        
                        // Add customer params
                        if (isset($customerData['customer'][$field['magento_field']])) {
                            $csvData[$field['graphicmail_field']] = $customerData['customer'][$field['magento_field']];
                        // Add customer address params
                        } else if (isset($customerData['address'][$field['magento_field']])) {
                            $csvData[$field['graphicmail_field']] = $customerData['address'][$field['magento_field']];
                        } else {
                            $csvData[$field['graphicmail_field']] = '';
                        }
                    }
                    
                    $customersArray[] = $csvData;
                }
                
                $addParams = array(
                    'DatasetID' => $datasetMapping['dataset_id'],
                    'MailinglistID' => $datasetMapping['mailinglist_id'],
                    'ImportMode' => 1, // Add and update
                );
                
                // Set column mapping in params
                foreach ($csvColsMapping as $colKey => $colVal) {
                    if ($colKey == 'Email') {
                        $colKey = 'EmailCol';
                    }
                    
                    $addParams[$colKey] = $colVal;
                }
                
                // Create csv
                if($fileUrl = $this->_createCsv($customersArray)) {
                    // Add filename to params
                    $addParams['FileUrl'] = $fileUrl;
                    
                    $api = $this->getApiModel();
                    $result = $api->apiResponse('post_import_dataset', false, $addParams);
                } else {
                    $result['message'] = $helper->__('The export could not be created.');
                }
            } else {
                $result['message'] = $helper->__('A mailing list has not been setup yet or configured correctly.');
            }
        } catch (Exception $e){
            $result['message'] = $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Create CSV
     * 
     * @param array $customers
     * @return bool | string
     */
    protected function _createCsv($customers)
    {
        $helper = $this->_helper();
        
        try {
            $baseDir = Mage::getBaseDir('media');
            $dir = $baseDir . DS . 'graphicmail';
            
            // Create dir if it does not exist
            if(!is_dir($dir)) {
                mkdir($dir,755);
            }
            
            // Real path
            $filename = $dir . DS . 'customers.csv';
            @unlink($filename);
            
            // Url path
            $fileUrl = Mage::getUrl('media' . DS . 'graphicmail' . DS . 'customers.csv', array(
                '_secure' => true,
            ));
            
            // Check if file exists and is writable
            if (file_exists($filename)) {
                if(!is_writable($filename)) {
                    $helper->debug('The file: '.$filename.' is not writable');
                    return false;
                }
            }
            
            // Open the file for appending
            $fh = fopen($filename, "at");
            
            // Lock the file for the write operation
            flock($fh, LOCK_EX);
            
            foreach ($customers as $customer) {
                foreach ($customer as $k => $v) {
                    $rows[1][$k] = $v;
                }
                
                if (function_exists("fputcsv")) {
                    fputcsv($fh, $rows[1], ",", "\"");
                } else {
                    // Clean up the strings for escaping and add quotes
                    foreach ($rows[1] as $val) {
                        $b[] = '"'.addslashes($val).'"';
                    }
                    
                    // Comma separate the values
                    $string = implode(",",$b);
                    
                    // Write the data to the file
                    fwrite($fh, $string ."\n",strlen($string));
                }
            }
            
            // Close file handle and release lock
            fclose($fh);
        } catch (Exception $e) {
            $helper->debug($e->getMessage());
            return false;
        }
        
        return $fileUrl;
    }
}