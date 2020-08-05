<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JControllerLegacy::getInstance('Egoi');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
