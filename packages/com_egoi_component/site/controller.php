<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license		MIT License
 * @author      E-goi
 */
// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
if (!class_exists('EgoiHelper')) 
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'egoi.php');


class EgoiController extends JControllerLegacy {
    
    /**
     * <-FrontOffice->
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JControllerLegacy		This object to support chaining.
     * @since	1.5
     */
    
    public function display($cachable = false, $urlparams = false) {

        require_once JPATH_COMPONENT . '/helpers/egoi.php';
        parent::display($cachable, $urlparams);
    }
	
	public function callback() {
		
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		$egoiXmlData = reset($_POST);
		$egoiHelper = new EgoiHelper();
		
		$egoiHelper->updateUserFromEgoi($egoiXmlData);
	}
	
	function subscribeAjax() {
		
		jimport('joomla.application.module.helper');
		$input  = JFactory::getApplication()->input;
		$module = JModuleHelper::getModule('egoi');
		$params = new JRegistry();
		$params->loadString($module->params);
		$sucesso = true;
		$data = array();
		
		$data['first_name'] = JRequest::getVar('first_name');
		$data['last_name'] = JRequest::getVar('last_name');
		$data['email'] = JRequest::getVar('email');
		$data['birth_date'] = JRequest::getVar('birth_date');
		
		$egoiHelper = new EgoiHelper();
		$result = $egoiHelper->publishSubscriber($data);
		
		if (!$result['ERROR']) {
			echo JText::_('COM_EGOI_SUBSCRIBE_MESSAGE_SUCCESS');
		} else {
			file_put_contents(JPATH_SITE . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'egoi.log', $this->getTimestamp() . ' - ERROR - ' . print_r($result, true) . PHP_EOL, FILE_APPEND);
			echo JText::_('COM_EGOI_SUBSCRIBE_MESSAGE_ERROR');
		}
	}
	
	function getTimestamp() {
		
		list($usec, $microseconds) = explode(" ", microtime());
		return date('Y-m-d\TH:i:s') . '.' . $microseconds;
	}

	function getConfig() {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('enable', 'form_title', 'content', 'hide', 'style_w', 'style_h', 'form_type', 'estado')));
		$query->from($db->quoteName('#__egoi_forms'));

		$db->setQuery($query);
		$form_fields = $db->loadObject();
		
		return $form_fields;
	}
}
