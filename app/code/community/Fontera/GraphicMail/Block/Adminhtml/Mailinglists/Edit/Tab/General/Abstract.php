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

abstract class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General_Abstract
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element instance
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;
    
    /**
     * Magento fields cache
     *
     * @var array
     */
    protected $_magentoFields;
    
    /**
     * Graphicmail fields cache
     *
     * @var array
     */
    protected $_graphicmailFields;
    
    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    /**
     * Set form element instance
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General_Abstract
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }
    
    /**
     * Retrieve form element instance
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }
    
    /**
     * Get and prepare row values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $dataRaw = $this->getElement()->getValue();
        
        // Check if string is serialized
        $data = @unserialize($dataRaw);
        if ($data != false) {
            if (is_array($data)) {
                $values = $this->_sortValues($data);
            }
        }
        
        return $values;
    }
    
    /**
     * Get and prepare dataset_id
     *
     * @return array
     */
    public function getDatasetId()
    {
        $datasetId = $this->getElement()->getDatasetId();
        
        return $datasetId;
    }
    
    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        return $data;
    }
    
    /**
     * Get Magento fields
     *
     * @param string|null $attrCode (return label by attribute code)
     * @return array|string
     */
    public function getMagentoFields($attrCode = null)
    {
        if ($this->_magentoFields === null) {
            // Set initial values
            $this->_magentoFields = $this->_getInitial();
            
            $excludedFields = $this->getExcludedMagentoFields();
            
            // Customer attribute collection
            $customerAttrCol = Mage::getModel('customer/entity_attribute_collection')
                ->addVisibleFilter();
            
            foreach ($customerAttrCol as $customerAttr) {
                /** @var $customerAttr Mage_Eav_Model_Entity_Attribute */
                if (!isset($excludedFields[$customerAttr->getAttributeCode()])) {
                    $this->_magentoFields['customer_'.$customerAttr->getAttributeCode()] = 'Customer: '.$customerAttr->getFrontendLabel().' ('.$customerAttr->getAttributeCode().')';
                }
            }
            
            // Customer address attribute collection
            $addressAttrCol = Mage::getModel('customer/entity_address_attribute_collection')
                ->addVisibleFilter();
            
            foreach ($addressAttrCol as $addressAttr){
                /** @var $addressAttr Mage_Eav_Model_Entity_Attribute */
                if (!isset($excludedFields[$addressAttr->getAttributeCode()])) {
                    $this->_magentoFields['address_'.$addressAttr->getAttributeCode()] = 'Address: '.$addressAttr->getFrontendLabel().' ('.$addressAttr->getAttributeCode().')';
                }
            }
        }
        
        if ($attrCode !== null) {
            return isset($this->_magentoFields[$attrCode]) ? $this->_magentoFields[$attrCode] : array();
        }
        
        return $this->_magentoFields;
    }
    
    /**
     * Get default value for Magento fields
     *
     * @return string | null
     */
    public function getDefaultMagentoFields()
    {
        return '';
    }
    
    /**
     * Get excluded Magento fields
     *
     * @return array
     */
    public function getExcludedMagentoFields()
    {
        return array(
            'created_in'                => 'created_in',
            'disable_auto_group_change' => 'disable_auto_group_change',
        );
    }
    
    /**
     * Get Graphicmail fields
     *
     * @param string|null $attrCode (return label by attribute code)
     * @param int $datasetId
     * @return array|string
     */
    public function getGraphicmailFields($attrCode = null)
    {
        $datasetId = $this->getDatasetId();
        if (($this->_graphicmailFields === null) && ($datasetId != '')) {
            // Set initial values
            $this->_graphicmailFields = $this->_getInitial();
            
            // Fetch GraphicMail fields
            $graphicmail = Mage::getModel('fontera_graphicmail/graphicMail');
            $datasetColumns = $graphicmail->getDatasetColumns($datasetId);
            
            foreach ($datasetColumns as $attributeCode => $attributeLabel) {
                $this->_graphicmailFields[$attributeCode] = $attributeLabel.' ('.$attributeCode.')';
            }
        }
        
        if ($attrCode !== null) {
            return isset($this->_graphicmailFields[$attrCode]) ? $this->_graphicmailFields[$attrCode] : array();
        }
        
        return $this->_graphicmailFields;
    }
    
    /**
     * Get default value for graphicmail fields
     *
     * @return string | null
     */
    public function getDefaultGraphicmailFields()
    {
        return '';
    }
    
    /**
     * Retrieve list of initial values if we want to set it
     *
     * @return array
     */
    protected function _getInitial()
    {
        return array();
    }
    
    /**
     * Retrieve 'add item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}