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
if (!class_exists('EgoiHelper')) 
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'egoi.php');
if (!class_exists('EgoiModelEgoi')) 
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'egoi.php');

class EgoiController extends JControllerLegacy {

	protected $columnIndexes = array();
	protected $configData = array();

	protected $egoiModel;
	protected $egoiHelper;

	protected $version = '1.0.1';
	
	/**
	 * Constructor
	 *
	 * @access public
	 * @author E-goi
	 */
	function __construct()
    {

		$this->egoiHelper = new EgoiHelper();
		$this->egoiModel = parent::getModel('egoi', 'EgoiModel', array());
		
		$this->host = $_SERVER['SERVER_NAME'];
		$lang = JFactory::getLanguage();
		$this->lang = $lang->getName();

		$this->columnIndexes = $this->egoiModel->getColumnIndexes();
		$this->configData = $this->egoiModel->getConfig();
        parent::__construct();
	}
	
    /**
     * Method to display a view.
     *
     * @param false $cachable
     * @param false $urlparams
     * @param false $option
     * @return $this
     */
    public function display($cachable = false, $urlparams = false, $option = false)
    {

		$this->cleanUpSessionData();

		$this->configData = $this->egoiModel->getConfig();
		$this->loadConfig();
		$this->loadListsFromEgoi();
		$this->loadTagsFromEgoi();

		if($_POST['get_map_egoi']){
			$this->getEgoiUserFields();
		}

		if($_POST['api_token']){
			$this->apply();
		}

		$setting = $_POST['edit_sett'];
		$key = $_POST['key'];
		if($key){
    		$this->apply($key);
    	}

    	if($setting){
    		$this->apply();
    	}

    	$list_token = $_POST['list_token'];
    	if($list_token){
    		$this->addList();
    	}

    	if($_POST['token_egoi_api']){
    		$this->mapFieldsEgoi();
    	}

    	$this->loadAllSubscribers();

        $view = JFactory::getApplication()->input->getCmd('view', 'egoi');
        JFactory::getApplication()->input->set('view', $view);
        parent::display($cachable, $urlparams);
        return $this;
    }

    /**
     * @return $this
     */
	public function addList()
    {
		
		$jinput = JFactory::getApplication()->input;
		$listName = $jinput->get('newList', '', 'STRING');
		$listLanguage = $jinput->get('newLanguage', '', 'STRING');
		
		$this->egoiHelper->createListEgoi($listName, $listLanguage);
		$this->columnIndexes = $this->egoiModel->getColumnIndexes();
		$this->configData = $this->egoiModel->getConfig();

		$this->loadConfig();
		$this->loadListsFromEgoi();

		parent::display($cachable, $urlparams);
	    return $this;
	}

    /**
     * @param false $key
     * @return $this|bool
     */
	public function apply($key = false)
    {

		if($key){
			return $this->importBulk();
		}
		$this->save();
		return $this;
	}

    /**
     * @param bool $redirect
     * @return $this|false
     */
	public function save($redirect = true)
    {

		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		$data = array();
		$validate = $jinput->get('api', '', 'INT');
		if($validate){
			$data['apikey'] = $jinput->get('apikey', '', 'STRING');
			$data['list'] = $jinput->get('egoi_list', '0', 'INT');
			$data['sync'] = $jinput->get('egoi_sync', '0', 'INT');
			$data['te'] = $jinput->get('egoi_te', '0', 'STRING');
			$data['groups'] = $jinput->get('egoi_role', '', 'STRING');
            $data['tag'] = $jinput->get('egoi_tags', '0', 'INT');
		} else {
			$data['apikey'] = $jinput->get('apikey', '', 'STRING');
			$data['list'] = $jinput->get('egoi_list', '', 'INT');
			$data['sync'] = $jinput->get('egoi_sync', '', 'INT');
			$data['te'] = $jinput->get('egoi_te', '', 'STRING');
			$data['groups'] = $jinput->get('egoi_role', '', 'STRING');
            $data['tag'] = $jinput->get('egoi_tags', '', 'INT');
		}

		$data['addsubscribe_myaccounts'] = $jinput->get('addsubscribe_myaccounts', '0', 'STRING');

		$this->egoiModel->store($data);
		$app->enqueueMessage(JText::_('COM_EGOI_SUCCESS_SAVE_DATA'), 'message');

		if(!empty($data['apikey'])){
            $account = $this->egoiHelper->getClientEgoi();
            $data['client_id'] = $account['general_info']['client_id'];
            $this->egoiModel->store($data);
        }

		if($redirect){
			$this->columnIndexes = $this->egoiModel->getColumnIndexes();
			$this->configData = $this->egoiModel->getConfig();

			$this->loadConfig();
			$this->loadListsFromEgoi();
            $this->loadTagsFromEgoi();
			$this->loadAllSubscribers();

			if($validate){
				$this->egoiAccount = $this->egoiHelper->getClientEgoi();
			}
	        return $this;
	    }
	    return false;
	}

    /**
     * @return bool
     */
	public function importBulk()
    {

		$subscribers = array();
		$egoiHelper = new EgoiHelper();
		$userStatus = 1;
		
		// get all users from administration
		$userList = $this->egoiModel->getUserList();
		foreach ($userList as $row) {
			$name = explode(' ', $row->name);
			$user = array(
				'first_name' => $name[0] ? $name[0] : $row->name,
				'last_name' => $name[1] ? $name[1] : $row->name,
				'status' => $userStatus
			);
			$extra = $egoiHelper->getExtraFields();

			if(!empty($extra['extra_fields'])){
                foreach ($extra['extra_fields'] as $key_extra => $extra) {
                    $user['extra_'.$key_extra] = '';
                }
            }

			foreach($user as $key => $value){
				$row_new_value = $this->egoiModel->getFieldMap($key);
				$jm = $row_new_value->jm;
				if($row_new_value->id){
					$user[$row_new_value->egoi] = $row->$jm;
				}
			}

			$user['subscriber'] = $row->email;
			$user['email'] = $row->email;

			array_push($subscribers, $user);
		}

		$egoiHelper->publishBulk($subscribers);
		return true;
	}

    /**
     * mapFieldsEgoi
     */
	public function mapFieldsEgoi()
    {

		$id = (int)$_POST["id_egoi"];
		$token = (int)$_POST["token_egoi_api"];
		$jm = $_POST["jm"];
		$egoi = $_POST["egoi"];

		if(isset($token) && ($jm) && ($egoi)){

			$jm_name = $_POST["jm_name"];
			$egoi_name = $_POST["egoi_name"];

			$exists = $this->egoiModel->getUserMapFields($jm, $egoi);
			if (!$exists[0]){

				$values = array(
					'jm' => $jm,
					'jm_name' => $jm_name,
					'egoi' => $egoi,
					'egoi_name' => $egoi_name,
					'status' => '1'
				);

				// insert this values and then fetch all
				$rows = $this->egoiModel->insertUserMapFields($values);
	         	foreach ($rows as $post){
					echo "<div id='egoi_fields_".$post->id."'>
					<div class='col-map-field egoi-35'>".$post->jm_name."</div> 
					<div class='col-map-field egoi-35'>".$post->egoi_name."</div>
					<div style='padding-top: 10px;'>
						<button type='button' id='field_".$post->id."' class='egoi_fields btn btn-info' data-target='".$post->id."'>Delete</button>
					</div>";
	         	}
	         	echo "</table>";

	        }else{
	        	echo 'ERROR';
	        }

			exit;

		} else if(isset($id) && ($id != '')) {
			$this->egoiModel->deleteMapField($id);
			exit;
		}
	}

    /**
     * getEgoiUserFields
     */
	public function getEgoiUserFields()
    {

		$egoiHelper = new EgoiHelper();
		$egoi_fields = array(
			'first_name' => 'First name',
			'last_name' => 'Last name',
			'surname' => 'Surname',
			'cellphone' => 'Mobile',
			'telephone' => 'Telephone',
			'birth_date' => 'Birth Date'
		);

		$extra = $egoiHelper->getExtraFields();
		foreach($extra['extra_fields'] as $key => $extra_field){
			$egoi_fields['extra_'.$key] = $extra_field['NAME'];
		}

		$option = '<option value="">Select field</option>';
		foreach($egoi_fields as $key => $field){
			$option .= '<option value="'.$key.'">'.$field.'</option>'.PHP_EOL;
		}

		echo($option);
		exit;
	}

    /**
     * cleanUpSessionData
     */
	public function cleanUpSessionData()
    {
		$app = JFactory::getApplication();
		$app->setUserState('com_egoi.egoiLists', null);
		$app->setUserState('com_egoi.egoiConfig', null);
		$app->setUserState('com_egoi.egoiColumnIndexes', null);
		$app->setUserState('com_egoi.fields', null);
		$app->setUserState('com_egoi.languages', null);
	}

    /**
     * Load Lists from Egoi
     */
	public function loadListsFromEgoi()
    {
		$app = JFactory::getApplication();
		$app->setUserState('com_egoi.egoiLists', $this->egoiHelper->getListsFromEgoi());
	}

    /**
     * Load Tags from Egoi
     */
    public function loadTagsFromEgoi()
    {
        $app = JFactory::getApplication();
        $app->setUserState('com_egoi.egoiTags', $this->egoiHelper->getTagsFromEgoi());
    }

    /**
     * loadColumnIndexes
     */
	public function loadColumnIndexes() {
		
		$app = JFactory::getApplication();
		$app->setUserState('com_egoi.egoiColumnIndexes', $this->columnIndexes);
	}

    /**
     * loadAllSubscribers
     */
	public function loadAllSubscribers()
    {
		
		$app = JFactory::getApplication();
		$app->setUserState('com_egoi.egoiSubs', $this->egoiHelper->getSubscriber(1));
	}

    /**
     * loadConfig
     */
	public function loadConfig()
    {

		$app = JFactory::getApplication();
		
		$viewData = array();
		$viewData['apikey'] = $this->configData[$this->columnIndexes['apikey']];
		$viewData['sync'] = $this->configData[$this->columnIndexes['sync']];
		$viewData['te'] = $this->configData[$this->columnIndexes['te']];
		$viewData['list'] = $this->configData[$this->columnIndexes['list']];
		$viewData['group'] = $this->configData[$this->columnIndexes['groups']];
		$viewData['tag'] = $this->configData[$this->columnIndexes['tag']];
		$viewData['client_id'] = $this->configData[$this->columnIndexes['client_id']];

		$app->setUserState('com_egoi.egoiConfig', $viewData);
		
	}

}
