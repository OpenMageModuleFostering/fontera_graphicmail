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

class Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form
     * 
     * @return Fontera_GraphicMail_Block_Adminhtml_Mailinglists_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('fontera_graphicmail');
        $graphicmail = Mage::getModel('fontera_graphicmail/graphicMail');
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('mailinglist_form', array('legend'=>$helper->__('General Information')));
        
        $fieldset->addField('setup_type', 'select', array(
            'label'     => $helper->__('Type'),
            'name'      => 'setup_type',
            'class'     => 'required-entry',
            'required'  => true,
            'values'    => $helper->getSetupTypesOptionSelect(),
            'note'      => $helper->__('Email addresses to be harvested in Magento.'),
        ));
        
        $fieldset->addField('mailinglist_id', 'select', array(
            'label'     => $helper->__('Mailing List'),
            'name'      => 'mailinglist_id',
            'class'     => 'required-entry',
            'required'  => true,
            'values'    => $graphicmail->getMailingListsOptionsArray(),
            'note'      => $helper->__('Mailing list options are fetched from GraphicMail. If there are no mailing list options available, log in to GraphicMail and create one.'),
        ));
        
        $dataset = $fieldset->addField('dataset_id', 'select', array(
            'label'     => $helper->__('Dataset'),
            'name'      => 'dataset_id',
            'class'     => 'required-entry',
            'required'  => true,
            'values'    => $graphicmail->getDatasetsOptionsArray(),
            'note'      => $helper->__('Dataset options are fetched from GraphicMail. If there are no dataset options available, please contact GraphicMail.'),
            'onchange'   => 'fetchDatasetFields()',
        ));
        
        /*$fieldset->addField('mapped_dataset_fields', 'text', array(
            'label'     => $helper->__('Map Dataset Fields'),
            'name'      => 'mapped_dataset_fields',
            'class'     => 'required-entry',
            'required'  => true,
        ));
        
        $form->getElement('mapped_dataset_fields')->setRenderer(
            $this->getLayout()->createBlock('fontera_graphicmail/adminhtml_mailinglists_edit_tab_general_datasetMapping')
        );*/
        
        $formValues = array();
        
        if(Mage::getSingleton('adminhtml/session')->getFonteraGraphicmailData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFonteraGraphicmailData());
            $formValues = Mage::getSingleton('adminhtml/session')->getFonteraGraphicmailData();
            Mage::getSingleton('adminhtml/session')->setFonteraGraphicmailData(null);
        } else if (Mage::registry('fontera_graphicmail_data')) {
            $form->setValues(Mage::registry('fontera_graphicmail_data')->getData());
            $formValues = Mage::registry('fontera_graphicmail_data')->getData();
        }
        
        if (empty($formValues)) {
            $params = '';
        } else {
            $params = "parameters: ".json_encode($formValues).",";
        }
        
        $afterHtml = "<script type=\"text/javascript\">
            document.observe('dom:loaded', function() {
                fetchDatasetFields();
            });
            
            function fetchDatasetFields() {
                var datasetIdEl = $('dataset_id');
                var formTableEl = $$('.form-list tbody')[0];
                
                if ((typeof(datasetIdEl) != 'undefined' && datasetIdEl != null) && (typeof(formTableEl) != 'undefined' && formTableEl != null)) {
                    
                    if (datasetIdEl.value != '') {
                        var url = '". $this->getUrl('graphicmail_admin/mailinglists/datasetMapping') . "dataset_id/' + datasetIdEl.value;
                        
                        new Ajax.Request(url, {
                            method: 'post',
                            ".$params."
                            evalScripts: true,
                            onLoading: function (transport) {},
                            onComplete: function(transport) {
                                var json = transport.responseText.evalJSON(true);
                                
                                if (json.status == 'success') {
                                    // Check if table already exists and update
                                    var datasetMappingTableEl = $('dataset_mapping_table');
                                    if (typeof(datasetMappingTableEl) != 'undefined' && datasetMappingTableEl != null) {
                                        
                                        var datasetMappingTableRowEl = datasetMappingTableEl.up().up();
                                        datasetMappingTableRowEl.insert(json.html);
                                        
                                    // Else add it
                                    } else {
                                        formTableEl.insert(json.html);
                                    }
                                } else {
                                    alert(json.message);
                                }
                            }
                        });
                    }
                }
            }
        </script>
        ";
        
        $dataset->setAfterElementHtml($afterHtml);
        
        return parent::_prepareForm();
    }
}