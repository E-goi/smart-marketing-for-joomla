<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

// no direct access
defined('_JEXEC') or die;
// Include dependancies
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('Egoi');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
