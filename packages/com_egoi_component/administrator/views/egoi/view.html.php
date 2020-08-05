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

    protected $state;
    protected $item;
    protected $form;
	protected $viewData;
    
    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
		$this->viewData = array();
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }        
		$app = JFactory::getApplication();
		$this->viewData = $app->getUserState('com_egoi.egoiConfig');
		
		// default
		$this->syncronizeEgoi($_POST);
		if(isset($_GET['view']) && ($_GET['view'] == 'egoi') || (!isset($_GET['view']))) {
			$this->egoiAccount = $this->getAccountInfo();
		}
		
		// egoi_lists
		$this->egoiLists = $app->getUserState('com_egoi.egoiLists');
		$this->egoiTags = $app->getUserState('com_egoi.egoiTags');

		// egoi_subscribers
		JHtml::_('jquery.framework', true, true);
		$doc = JFactory::getDocument();
		$doc->addScript("components/com_egoi/assets/js/mapFieldsEgoi.js");
		$doc->addStyleSheet("components/com_egoi/assets/css/egoi.css");
		
		$this->subs = $this->getSubscribers($app);
		$this->roles = $this->getRoles();
		$this->mapped_fields = $this->getMappedFields();
		
		// egoi_forms
		$id = 0;
		if(isset($_GET['form']) && ($_GET['type']) && ($_GET['form'] <= 5)){
			$id = $_GET['form'];
		}else{
			$id = $_POST['form_id'];
			$_POST['content'] = $this->encodeContent($_POST['content']);
		}

		//get E-goi forms
		$this->egoi_forms = $this->getEgoiForms($id);
		$this->configIframe();
		$this->area = $this->filterArea();

		$this->forms = $this->getForms($id, $app, $_POST, $_POST['token_form']);
		$this->languages = $app->getUserState('com_egoi.languages');
		
		$this->addToolbar();

        parent::display($tpl);
    }
	
    public function setViewData($key, $value) {

		$this->viewData[$key] = $value;
	}

	private function filterArea(){

		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__egoi_forms` WHERE area='widget' and enable='1'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$count = $rows[0]->COUNT;
		
		return $count;
	}

	public function getAccountInfo()
    {
		$egoiHelper = new EgoiHelper();
		return $egoiHelper->getClientEgoi();
	}

    /**
     * @return mixed
     */
    public function getTags()
    {
        $egoiHelper = new EgoiHelper();
        return $egoiHelper->getTags();
    }

	public function getUsers() {
		
		// Joomla Users
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) AS COUNT FROM `#__users`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$count = $rows[0]->COUNT;
		
		return $count;

	}

	public function getEgoiSubscribers($listID){

		$count = 0;
		$egoiHelper = new EgoiHelper();
		$result = $egoiHelper->getListsFromEgoi();

		foreach ($result as $key => $value) {
			if($value['listnum'] == $listID){
				$count = $value['subs_activos'];
			}
		}

	    return $count;

	}

	public function syncronizeEgoi($post = array()){

		if(!empty($post)){
			if(isset($post['action']) && ($post['action'] == 'synchronize')){
				$all_subscribers = $this->getEgoiSubscribers($post['list']);
				
				$total[] = $all_subscribers;
				$total[] = $this->getUsers();

				echo json_encode($total);
				exit;
			}
		}
	}

	public function getSubscribers($app) {

		// subscribed users in E-goi
		$all_users = '';
		$this->egoiSubs = $app->getUserState('com_egoi.egoiSubs');
		if(!empty($this->egoiSubs['subscriber'])){
			foreach ($this->egoiSubs['subscriber'] as $key => $subs) {
				if($subs['STATUS'] != '2'){
					$all_emails .= $subs['EMAIL'].' - ';
				}
			}
		}
		$all_subs = array_filter(explode(' - ', $all_emails));

		return count($all_subs);
	}

	public function getRoles(){

		$db = JFactory::getDBO();
		$sql = "SELECT * FROM `#__usergroups`";

		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		return $rows;

	}

	public function getMappedFields(){

		$egoiModel = new EgoiModelEgoi();
		$data = $egoiModel->getAllMappedFields(1);
		return $data;
	}

	public function getForms($id = false, $app, $post = array(), $token = false){

		$db = JFactory::getDbo();
		if($token){

			$egoiModel = new EgoiModelEgoi();
			$egoiModel->store_forms($post, $id);
			$app->enqueueMessage(JText::_('COM_EGOI_SUCCESS_SAVE_DATA'), 'message');

			$sql = "SELECT * FROM `#__egoi_forms`";
			$db->setQuery($sql);
			$rows = $db->loadObjectList();

	        return $rows;

		}else{

			if($id){
				$sql = "SELECT * FROM `#__egoi_forms` WHERE id=$id";
			}else{
				$sql = "SELECT * FROM `#__egoi_forms`";
			}
		}

		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		if($id){
			return $rows[0];
		}
		return $rows;
		
	}

	private function getEgoiForms($id){

		$db = JFactory::getDbo();
		$sql = "SELECT * FROM `#__egoi_forms` WHERE id='$id' and form_type='iframe' and list!=''";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		$list = $rows[0]->list;

		if($list){
			$egoi = new EgoiHelper();
			$result = $egoi->getFormsFromEgoi($list);
			return $result;
		}
		return false;
	}

	private function configIframe(){

		$data = $_POST;
		$url_str = $data['url_egoi'];
		if(isset($url_str) && ($url_str)){
			$url_str = explode(' - ', $url_str);
			$url = $url_str[1];

		    echo '<iframe src="//'.$url.'" width="700" height="450" style="border: 0 none; max-height:450px;" onload="window.parent.parent.scrollTo(0,0);"></iframe>';
		    exit;
		}
		return false;
	}

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {

    	$layout = $_GET['layout'];
    	switch ($layout) {
    		case 'edit_forms':
    			$title = 'COM_EGOI_LEGEND_EGOI_FORMS';
    			break;
    		case 'edit_lists':
    			$title = 'COM_EGOI_LEGEND_EGOI_LISTS';
    			break;
    		case 'edit_subscribers':
    			$title = 'COM_EGOI_LEGEND_EGOI_SUBS';
    			break;
    		default:
    			$title = 'COM_EGOI_LEGEND_EGOI_ACCOUNT';
    			break;
    	}

        JToolBarHelper::title(JText::_($title), 'class-egoi');

    }

	public function editLists($tpl = null) {

		$app = JFactory::getApplication();
		$this->egoiLists = $app->getUserState('com_egoi.egoiLists');
		$this->setLayout("edit_lists");
		
		JToolBarHelper::title(JText::_('COM_EGOI_TITLE_EDIT_LISTS'));
		parent::display();
	}

	public function editSubscribers($tpl = null) {

		$app = JFactory::getApplication();
		$this->egoiSubscribers = $app->getUserState('com_egoi.egoiSubscribers');
		$this->setLayout("edit_subscribers");
		
		JToolBarHelper::title(JText::_('COM_EGOI_TITLE_EDIT_SUBSCRIBERS'));
		parent::display();
	}

	public function editForm($tpl = null) {

		$app = JFactory::getApplication();
		$this->fields = $app->getUserState('com_egoi.fields');
		$this->languages = $app->getUserState('com_egoi.languages');
		$this->setLayout("edit_form");
		
		JToolBarHelper::title(JText::_('COM_EGOI_TITLE_EDIT_SUBSCRIPTION_FORM'));
		parent::display();
	}

	private function encodeContent($content){
		
		return base64_encode(htmlentities($content));

	}

}
