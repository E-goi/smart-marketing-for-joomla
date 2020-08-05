<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');
if (!class_exists('EgoiHelper')) 
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'egoi.php');

/**
 * View to edit
 */
class EgoiViewEgoi extends JViewLegacy {

    protected $form;
	protected $viewData;
    
    /**
     * <-FrontOffice->
     * Display the view
     */
    public function display($tpl = null) {
	
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__egoi_forms', 'egoi_forms'));
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		$app = JFactory::getApplication();
		$this->fields = $app->getUserState('com_egoi.fields');
		$this->languages = $app->getUserState('com_egoi.languages');

		// Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	throw new Exception(implode("\n", $errors));
        }

        if(is_array($results)){
        	foreach ($results as $key => $value) {
        		if($value->enable == 1) {
        			if($_GET['form'] == $value->id){
        				$this->content = $this->decode($value->content);
        				if($value->show_title){
        					$this->title = $value->form_title;
        				}else{
        					$this->title = '';
        				}
	        			parent::display($tpl);
        			}
        		}
        	}		
	    }
    }

	public function getSubscribers($app) {

		// subscribed users in E-goi
		$all_users = '';
		$this->egoiSubs = $app->getUserState('com_egoi.egoiSubs');
		foreach ($this->egoiSubs['subscriber'] as $key => $subs) {
			if($subs['STATUS'] != '2'){
				$all_emails .= $subs['EMAIL'].' - ';
			}
		}
		$all_subs = array_filter(explode(' - ', $all_emails));

		return count($all_subs);
	}

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {

        JToolBarHelper::title(JText::_('COM_EGOI_TITLE_EGOI'), 'egoi.png');

    }

    private function decode($content){
    	return html_entity_decode(base64_decode($content));
    }

}
