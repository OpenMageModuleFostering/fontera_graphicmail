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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General_DatasetMapping extends Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General_Abstract
{
    /**
     * Internal constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->setTemplate('fontera_graphicmail/mailinglists/dataset_mapping.phtml');
    }
    
    /**
     * Prepare global layout
     *
     * Add "Add Dataset Field" button to layout
     *
     * @return Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General_DatasetMapping
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('fontera_graphicmail')->__('Add Dataset Field'),
                'onclick' => 'return datasetMappingControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_dataset_mapping_item_button');
        
        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
}