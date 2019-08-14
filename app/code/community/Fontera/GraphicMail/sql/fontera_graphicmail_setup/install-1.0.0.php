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

$installer = $this;
$installer->startSetup();

/**
 * 1. Create table 'fontera_graphicmail/mailinglists'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('fontera_graphicmail/mailinglists'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('setup_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Setup Type')
    ->addColumn('dataset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Dataset ID')
    ->addColumn('mailinglist_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Mailinglist ID')
    ->addColumn('mapped_dataset_fields', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Mapped Dataset Fields')
    ->setComment('Graphic Mail Mailing Lists');
$installer->getConnection()->createTable($table);

$installer->endSetup();