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

class Fontera_GraphicMail_Model_Observer
{
    /**
     * Helper
     * 
     * @return Fontera_GraphicMail_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('fontera_graphicmail');
    }
    
    /**
     * Graphicmail model
     * 
     * @return Fontera_GraphicMail_Model_GraphicMail
     */
    protected function _graphicMail()
    {
        return Mage::getModel('fontera_graphicmail/graphicMail');
    }
    
    /**
     * Set Customer
     * 
     * After customer account has been successfully created, add the customer to GraphicMail and subscribe to newsletter.
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function setCustomer(Varien_Event_Observer $observer)
    {
        $helper = $this->_helper();
        
        try {
            $event = $observer->getEvent();
            $customer = $event->getCustomer();
            
            // Create user in GraphicMail.
            $graphicmail = $this->_graphicMail();
            $graphicmail->createUser($customer);
            
            // Subscribe to newsletter.
            if ($customer->getIsSubscribed()) {
                $graphicmail->subscribeNewsletter($customer);
            }
        // Fail silently
        } catch (Exception $e) {
            $helper->debug($e->getMessage());
        }
        
        return;
    }
    
    /**
     * Update Customer
     * 
     * Update the customer in GraphicMail.
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateCustomer(Varien_Event_Observer $observer)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $event = $observer->getEvent()->setCustomer($customer);
        
        $this->setCustomer($observer);
        
        return;
    }
    
    /**
     * Subscription Newsletter
     * 
     * Subscribe / unsubscribe customer to newsletter.
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function subscriptionNewsletter(Varien_Event_Observer $observer)
    {
        $helper = $this->_helper();
        
        try {
            $action = $observer->getControllerAction();
            /* @var $action Mage_Customer_AccountController */
            
            $request = $action->getRequest();
            /* @var $request Mage_Core_Controller_Request_Http */
            
            $params = $request->getParams();
            
            $graphicmail = $this->_graphicMail();
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            
            if ($customer->getId()) {
                // Subscribe to newsletter.
                if (isset($params['is_subscribed']) && $params['is_subscribed']) {
                    $graphicmail->subscribeNewsletter($customer);
                // Unsubscribe from newsletter.
                } else {
                    $graphicmail->unsubscribeNewsletter($customer);
                }
            }
        // Fail silently
        } catch (Exception $e) {
            $helper->debug($e->getMessage());
        }
        
        return;
    }
}