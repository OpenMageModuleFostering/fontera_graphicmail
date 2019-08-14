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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('mailinglistsGrid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * Prepare collection
     * 
     * @return Fontera_GraphicMail_Model_Mailinglists
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fontera_graphicmail/mailinglists')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare columns
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('fontera_graphicmail');
        
        $this->addColumn('entity_id', array(
            'header'    => $helper->__('ID'),
            'align'     => 'left',
            'width'     => '25px',
            'index'     => 'entity_id',
        ));
        
        $this->addColumn('setup_type', array(
            'header'    => $helper->__('Type'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'setup_type',
            'type'      => 'options',
            'options'   => $helper->getSetupTypesOptionSelect(),
        ));
        
        $this->addColumn('dataset_id', array(
            'header'    => $helper->__('DatasetID'),
            'align'     => 'left',
            'width'     => '100px',
            'index'     => 'dataset_id',
        ));
        
        $this->addColumn('mailinglist_id', array(
            'header'    => $helper->__('MailinglistID'),
            'align'     => 'left',
            'width'     => '100px',
            'index'     => 'mailinglist_id',
        ));
        
        $this->addColumn('action',
            array(
                'header'    => $helper->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => $helper->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'entity_id',
                'is_system' => true,
            ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Prepare mass action block
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ids');
        $this->getMassactionBlock()->setFormFieldName('ids');
        
        $helper = Mage::helper('fontera_graphicmail');
        
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => $helper->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => $helper->__('Are you sure?')
        ));
        
        return $this;
    }
    
    /**
     * Get row URL
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getEntityId()));
    }
}