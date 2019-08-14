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

abstract class Fontera_GraphicMail_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Define helper
     * 
     * @return Fontera_GraphicMail_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('fontera_graphicmail');
    }
}