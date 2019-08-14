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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $helper = Mage::helper('fontera_graphicmail');
        $this->_objectId = 'entity_id';
        $this->_controller = 'adminhtml_mailinglists';
        $this->_blockGroup = 'fontera_graphicmail';
        $this->_updateButton('save', 'label', $helper->__('Save'));
        $this->_updateButton('delete', 'label', $helper->__('Delete'));
    }
    
    /**
     * Get header text
     */
    public function getHeaderText()
    {
        $helper = Mage::helper('fontera_graphicmail');
        
        if(Mage::registry('graphicmail_mailinglists_data') && Mage::registry('graphicmail_mailinglists_data')->getId()) {
            return $helper->__("Edit Mailing List - '%s'", $this->htmlEscape(Mage::registry('graphicmail_mailinglists_data')->getId()));
        } else {
            return $helper->__('Add Mailing List');
        }
    }
}