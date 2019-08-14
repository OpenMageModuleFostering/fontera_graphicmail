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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('graphicmail_mailinglists_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fontera_graphicmail')->__('Manage Mailing Lists'));
    }
    
    /**
     * Before toHtml
     */
    protected function _beforeToHtml()
    {
        $helper = Mage::helper('fontera_graphicmail');
        
        $this->addTab('general_section', array(
            'label'     => $helper->__('General Information'),
            'title'     => $helper->__('General Information'),
            'content'   => $this->getLayout()->createBlock('fontera_graphicmail/adminhtml_mailinglists_edit_tab_general')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}