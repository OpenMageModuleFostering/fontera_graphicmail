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

class Fontera_GraphicMail_Adminhtml_MailinglistsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get adminhtml session
     * 
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
    
    /**
     * Get helper
     * 
     * @return Fontera_GraphicMail_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('fontera_graphicmail');
    }
    
    /**
     * Get GraphicMail model
     * 
     * @return Fontera_GraphicMail_Model_GraphicMail
     */
    protected function _graphicMailModel()
    {
        return Mage::getModel('fontera_graphicmail/graphicMail');
    }
    
    /**
     * Get mailinglists model
     * 
     * @return Fontera_GraphicMail_Model_Mailinglists
     */
    protected function _mailinglistsModel()
    {
        return Mage::getModel('fontera_graphicmail/mailinglists');
    }
    
    /**
     * Init action
     * 
     * @return Fontera_GraphicMail_Adminhtml_MailinglistsController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('fontera/graphicmail/mailinglists')
            ->_addBreadcrumb($this->_helper()->__('Manage Mailing Lists'), $this->_helper()->__('Manage Mailing Lists'));
        return $this;
    }
    
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }
    
    /**
     * Edit action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_mailinglistsModel()->load($id);
        
        if ($model->getId() || $id == 0) {
            $data = $this->_getSession()->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            
            Mage::register('fontera_graphicmail_data', $model);
            
            $this->loadLayout();
            $this->_setActiveMenu('fontera/graphicmail/mailinglists');
            $this->_addBreadcrumb($this->_helper()->__('Manage Mailing Lists'), $this->_helper()->__('Manage Mailing Lists'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            
            $this->_addContent($this->getLayout()->createBlock('fontera_graphicmail/adminhtml_mailinglists_edit'))
                ->_addLeft($this->getLayout()->createBlock('fontera_graphicmail/adminhtml_mailinglists_edit_tabs'));
            
            $this->renderLayout();
        } else {
            $this->_helper()->debug($this->_helper()->__('Mailing list: %s does not exist.', $id));
            $this->_getSession()->addError($this->_helper()->__('Mailing list does not exist.'));
            $this->_redirect('*/*/');
        }
    }
    
    /**
     * New action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    /**
     * Save action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->_mailinglistsModel();
            
            $id = $this->getRequest()->getParam('id');
            
            // Check mapped dataset fields.
            if (isset($data['mapped_dataset_fields'])) {
                
                $mappedFields = $data['mapped_dataset_fields'];
                
                foreach ($mappedFields as $key => $mappedField) {
                    if (isset($mappedField['delete']) && $mappedField['delete']) {
                        unset($mappedFields[$key]);
                    }
                }
                
                // After unsetting, we need to check if there are mapped dataset fields still set.
                if (empty($mappedFields)) {
                    $message = $this->_helper()->__('Map Dataset Fields is a required field.');
                    $this->_getSession()->addError($message);
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }
                
                $data['mapped_dataset_fields'] = serialize($mappedFields);
            // Throw error if no mapped dataset fields are set.
            } else {
                $message = $this->_helper()->__('Map Dataset Fields is a required field.');
                $this->_getSession()->addError($message);
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
            
            // Check setup type. There can only be one.
            $collection = $model->getCollection()
                ->addFieldToFilter('setup_type', array('eq' => $data['setup_type']));
                
            if (count($collection) > 0) {
                if ($collection->getFirstItem()->getId() != $id) {
                    $message = $this->_helper()->__('You have already setup a mailing list with the same type. You can only setup one mailing list per type in Magento.');
                    $this->_getSession()->addError($message);
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }
            }
            
            $model->setData($data)->setId($id);
            
            try {
                $model->save();
                $message = $this->_helper()->__('Mailing list was saved successfully.');
                $this->_helper()->debug($message);
                $this->_getSession()->addSuccess($message);
                $this->_getSession()->setFormData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_helper()->exception($e);
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        
        $message = $this->_helper()->__('Unable to find mailing list to save.');
        $this->_helper()->debug($message);
        $this->_getSession()->addError($message);
        $this->_redirect('*/*/');
    }
    
    /**
     * Delete action
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        
        if($id > 0) {
            $model = $this->_mailinglistsModel();
            
            try {
                $model->setId($id)->delete();
                
                $message = $this->_helper()->__('Cron job was deleted successfully.');
                $this->_helper()->debug($message);
                $this->_getSession()->addSuccess($message);
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                $this->_helper()->exception($e);
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * Mass delete action
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        
        if(!is_array($ids)) {
            $message = $this->_helper()->__('Please select one or more mailing lists.');
            $this->_helper()->debug($message);
            $this->_getSession()->addError($message);
        } else {
            try {
                $deletedCount = 0;
                foreach ($ids as $id) {
                    $model = $this->_mailinglistsModel()->load($id);
                    $model->delete();
                    
                    $deletedCount++;
                }
            } catch (Exception $e) {
                $this->_helper()->exception($e);
                $this->_getSession()->addError($e->getMessage());
            }
            
            $message = $this->_helper()->__('%s of %d mailing lists were successfully deleted.', $deletedCount, count($ids));
            $this->_helper()->debug($message);
            $this->_getSession()->addSuccess($message);
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Dataset Mapping action
     */
    public function datasetMappingAction()
    {
        $response = array(
            'status' => ''
        );
        
        $params = $this->getRequest()->getParams();
        
        $form = new Varien_Data_Form();
        
        $form->addField('mapped_dataset_fields', 'text', array(
            'label'     => $this->_helper()->__('Map Dataset Fields'),
            'name'      => 'mapped_dataset_fields',
            'class'     => 'required-entry',
            'required'  => true,
        ));
        
        // We need to set existing data if any
        $mappedDatasetFields = '';
        if (isset($params['mapped_dataset_fields'])) {
            $mappedDatasetFields = $params['mapped_dataset_fields'];
        }
        
        $datasetId = '';
        if (isset($params['dataset_id'])) {
            $datasetId = $params['dataset_id'];
        }
        
        // Set form element
        $form->getElement('mapped_dataset_fields')->setRenderer(
            $this->getLayout()->createBlock('fontera_graphicmail/adminhtml_mailinglists_edit_tab_general_datasetMapping')
        )->setValue($mappedDatasetFields)->setDatasetId($datasetId);
        
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('fontera_graphicmail_mailinglists_datasetmapping');
        if ($block) {
            $block->setElement($form->getElement('mapped_dataset_fields'));
            $response['html'] = $block->toHtml();
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
            $response['message'] = $this->_helper()->__('An internal error occured. Could not retrieve the dataset mapping fields.');
        }
        
        $this->getResponse()->setBody(json_encode($response));
    }
    
    /**
     * Export customers action
     *
     * @return
     */
    public function exportCustomersAction()
    {
        $model = $this->_graphicMailModel();
        $result = $model->exportCustomers();
        
        if ($result['state']) {
            if (isset($result['message'])) {
                $message = $result['message'];
            } else {
                $message = $this->_helper()->__('Customers were exported to GraphicMail successfully.');
            }
            
            $this->_getSession()->addSuccess($message);
        } else {
            if (isset($result['message'])) {
                $message = $result['message'];
            } else {
                $message = $this->_helper()->__('An error occured.');
            }
            
            $this->_getSession()->addError($message);
        }
        
        $this->_redirect('*/*/index');
    }
}