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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $helper = Mage::helper('fontera_graphicmail');
        $this->_controller = 'adminhtml_mailinglists';
        $this->_blockGroup = 'fontera_graphicmail';
        $this->_headerText = $helper->__('Manage Mailing Lists');
        $this->_addButtonLabel = $helper->__('Add New Mailing List');
        parent::__construct();
    }
    
    /**
     * Prepare layout
     *
     * @return Fontera_GraphicMail_Block_Adminhtml_Mailinglists
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('fontera_graphicmail');
        $this->_addButton('export_customers', array(
            'label'   => $helper->__('Export Existing Customers'),
            'onclick' => "setLocation('{$this->getUrl('*/*/exportCustomers')}')",
        ));
        return parent::_prepareLayout();
    }
}