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

class Fontera_GraphicMail_Adminhtml_System_Config_TestconnectionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Test for connection to server
     */
    public function testAction()
    {
        $helper = Mage::helper('fontera_graphicmail');
        
        $params = $this->getRequest()->getParams();
        $apiParams = array(
            'Username'  => $params['Username'],
            'Password'  => $params['Password'],
            'Function'  => 'get_profile',
            'SID'   => $params['SID'],
        );
        
        $api = Mage::getModel('fontera_graphicmail/graphicMail_api');
        $response = $api->apiResponse('get_profile', $apiParams);
        
        if ($response['state']) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'error';
            $result['message'] = $response['message'];
        }
        
        return $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}
