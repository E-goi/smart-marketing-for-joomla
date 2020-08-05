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

jimport('joomla.application.component.modeladmin');

if (!class_exists('EgoiModelEgoi')) 
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'egoi.php');

if (!class_exists( 'EgoiUtil' )) {
	require(JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'egoi' . DIRECTORY_SEPARATOR . 'EgoiApi' . DIRECTORY_SEPARATOR . 'egoi.php');
}

/**
 * Egoi helper.
 */
class EgoiHelper {

	protected $columnIndexes = array();
	protected $configData = array();
	protected $egoiModel;
	
	/**
	 * Method to display the view
	 *
	 * @access public
	 * @author E-goi
	 */
	public function __construct() {
		$this->refreshData();
	}
	
    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        
        JSubMenuHelper::addEntry(
			JText::_('COM_EGOI_TITLE_EGOIS'),
			'index.php?option=com_egoi&view=egois',
			$vName == 'egois'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_EGOI_TITLE_CONFIGURAESGEARISS'),
			'index.php?option=com_egoi&view=configuraesgeariss',
			$vName == 'configuraesgeariss'
		);

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.0
     */
    public static function getActions() {
        
        $user = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_egoi';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

	/**
	 * It is MANDATORY that ALL functions in this class call this method as the first command, so that we make sure we have 
	 * fresh data from the database.
	 *
	 * This is necessary because the components that use this class may hold an instance with old data, and therefore end up 
	 * getting data that is not up-to-date.
	 *
	 * @author E-goi
	 */
	public function refreshData() {
		
		$this->egoiModel = JModelAdmin::getInstance('egoi', 'EgoiModel', array());
		$this->columnIndexes = $this->egoiModel->getColumnIndexes();
		$this->configData = $this->egoiModel->getConfig();
	}
	
	public function getUserfieldFlag($userfieldFlagName, $egoiFlagIdName) {
		
		$this->refreshData();
		$egoiFlagID = $this->configData[$this->columnIndexes[$egoiFlagIdName]];
		
		if ($egoiFlagID > 0) {
			$userfield = $this->egoiModel->getUserFieldDetails($egoiFlagID);
			
			return $userfield->$userfieldFlagName;
		}
		
		return 0;
	}
	
	public function setupCallBackAPI() {
		
		$this->refreshData();
		$app = JFactory::getApplication();

		try {
			$apiKey = $this->configData[$this->columnIndexes['apikey']];
			$list = $this->configData[$this->columnIndexes['list']];
			
			$params = array(
				'apikey' => $apiKey,
				'listID' => $list,
				'callback_url' => JURI::root() . 'index.php?option=com_egoi&task=callback',
				'notif_api_1' => '0',
				'notif_api_2' => '0',
				'notif_api_3' => '0',
				'notif_api_4' => '0',
				'notif_api_5' => '0',
				'notif_api_6' => '0',
				'notif_api_7' => '0',
				'notif_api_8' => '0',
				'notif_api_9' => '0',
				'notif_api_10' => '0',
				'notif_api_11' => '0',
				'notif_api_12' => '1',
				'notif_api_13' => '0',
				'notif_api_14' => '0',
				'notif_api_15' => '0',
				'notif_api_16' => '0'
			);

			$client = new EgoiUtil();
			$result = $client->setupCallBackAPI($params);
		} catch (Exception $e) {
			$error = $e->getMessage();

			$app->enqueueMessage($error, 'error');
		}

		if ($result['ERROR']) {
			$app->enqueueMessage(JText::_('COM_EGOI_ERROR_SETUP_CALLBACK') . ': ' . JText::_($result['ERROR']) . '. ' . JText::_('COM_EGOI_ERROR_SETUP_CALLBACK_MESSAGE'), 'error');
		}
	}
	
	public function storeUserFields($egoiFieldId, $subscribetext, $userFieldName, $flagArray, $default = null) {
		
		$this->refreshData();
		
		// Title is a mandatory field. Therefore, if field "$subscribetext" is empty, we save it as XX.
		$title = "XX";
		if ($subscribetext) {
			$title = $subscribetext;
		}
	
		$userFieldsModel = JModel::getInstance('userfields');

		$userFieldsData = array();
		
		$userFieldsData['virtuemart_userfield_id'] = $egoiFieldId;
		$userFieldsData['name'] = $userFieldName;
		if ('egoi_addsubscribe_myaccounts' === $userFieldName) {
			$userFieldsData['title'] = $title;
		} else {
			$userFieldsData['title'] = 'Inner field';
		}
		$userFieldsData['type'] = 'checkbox';
		$userFieldsData['published'] = 1;
		
		if ($default) {
			$userFieldsData['default'] = $default;
		}

		$registrationFlag = 0;
		$accountFlag = 0;
		$shipmentFlag = 0;
		
		if ('1' === $flagArray[0]) {
			$registrationFlag = 1;
		}
		
		if ('1' === $flagArray[1]) {
			$accountFlag = 1;
		}
		
		if ('1' === $flagArray[2]) {
			$shipmentFlag = 1;
		}

		$userFieldsData['registration'] = $registrationFlag;
		$userFieldsData['account'] = $accountFlag;
		$userFieldsData['shipment'] = $shipmentFlag;

		return $userFieldsModel->store($userFieldsData);
	}
	
	public function createListEgoi($listName, $listLanguage) {
		
		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		
		if (!$apiKey) {
			$app->enqueueMessage(JText::_('COM_EGOI_ERROR_API_KEY_NOT_CONFIGURED') . ': ' . JText::_($result['ERROR']) . '.', 'error');
			return;
		}
		
		try {
			$params = array(
				'apikey' => $apiKey,
				'nome' => $listName,
				'idioma_lista' => $listLanguage,
				'canal_email' => '1',
				'canal_sms' => '1',
				'canal_fax' => '1',
				'canal_voz' => '1',
			);
			
			$api = new EgoiUtil();
			$result = $api->createListEgoi($params);

			if ($result['ERROR']) {
				$app->enqueueMessage(JText::_('COM_EGOI_ERROR_CREATE_LIST') . ': ' . JText::_($result['ERROR']) . '.', 'error');
			}else{
				$app->enqueueMessage(JText::_('COM_EGOI_SUCCESS') . ': ' . JText::_('COM_EGOI_SUCCESS_MSG') . '.', 'success');
			}

		} catch (Exception $e) {
			
			$error = $e->getMessage();
			$app->enqueueMessage($error, 'error');
		}
	}


	public function getListsFromEgoi() {
		
		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		
		if (!$apiKey) {
			return array();
		}
		
		try {
			$params = array(
				'apikey' => $apiKey
			);

			$api = new EgoiUtil();
			$result = $api->getListsFromEgoi($params);

			if ($result['ERROR']) {
				$app->enqueueMessage(JText::_('COM_EGOI_ERROR_GET_LIST') . ': ' . JText::_($result['ERROR']) . '.', 'error');
			} else {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();

			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}

	public function getFormsFromEgoi($list){

		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		
		if (!$apiKey) {
			return array();
		}
		
		try {
			$params = array(
				'apikey' => $apiKey,
				'listID' => $list
			);

			$api = new EgoiUtil();
			$result = $api->getForms($params);

			if ($result['ERROR']) {
				$app->enqueueMessage(JText::_('COM_EGOI_ERROR_GET_FORMS') . ': ' . JText::_($result['ERROR']) . '.', 'error');
			} else {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();

			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}


    /**
     * @return array|mixed
     */
    public function getTagsFromEgoi()
    {

        $app = JFactory::getApplication();

        try {
            $api = new EgoiUtil();
            $result = $api->getTagsEgoi();

            if (empty($result)) {
                $app->enqueueMessage(JText::_('EGOI_ERROR_CLIENT') . '.', 'error');
            } else {
                return !empty($result['items']) ? $result['items'] : [];
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $app->enqueueMessage($error, 'error');
        }

        return array();
    }

    /**
     * @return array|mixed
     */
	public function getClientEgoi()
    {

        $app = JFactory::getApplication();

		try {
			$api = new EgoiUtil();
			$result = $api->getClientEgoi();

			if (empty($result)) {
				$app->enqueueMessage(JText::_('EGOI_ERROR_CLIENT') . '.', 'error');
			} else {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}

	public function getTags()
    {

        $app = JFactory::getApplication();

        try {
            $api = new EgoiUtil();
            $result = $api->getTags();

            if (empty($result)) {
                $app->enqueueMessage(JText::_('EGOI_ERROR_CLIENT') . '.', 'error');
            } else {
                return $result;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $app->enqueueMessage($error, 'error');
        }

        return array();
    }

	public function getSubscriber($option = false) {
		
		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		$list = $this->configData[$this->columnIndexes['list']];
		
		if (!$apiKey) {
			return array();
		}
		
		try {
			$params = array(
				'apikey' => $apiKey,
			);
			$params['listID'] = $list;

			$api = new EgoiUtil();
			$result = $api->getSubscriber($params, $option);

			if ($result['ERROR']) {
				if($_GET['layout'] == 'edit_subscribers'){
					$app->enqueueMessage(JText::_('EGOI_ERROR_CLIENT') . ': ' . JText::_('EGOI_ERROR_LIST'), 'warning');
				}
			} else {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}

	public function getAllSubscribers($list, $start){

		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];

		if (!$apiKey) {
			return array();
		}

		try {
			$params = array(
				'apikey' => $apiKey,
			);
			$params['listID'] = $list;

			$api = new EgoiUtil();
			$result = $api->getAllSubscribers($params);

			if (!$result['ERROR']) {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}

	public function getExtraFields(){

		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		$list = $this->configData[$this->columnIndexes['list']];

		if (!$apiKey) {
			return array();
		}

		try {
			$params = array(
				'apikey' => $apiKey,
				'listID' => $list
			);

			$api = new EgoiUtil();
			$result = $api->getExtraFields($params);

			if (!$result['ERROR']) {
				return $result;
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
			$app->enqueueMessage($error, 'error');
		}
		
		return array();
	}
	
	
	public function publishBulk($subscribers) {
		
		$this->refreshData();
		$app = JFactory::getApplication();
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		$list = $this->configData[$this->columnIndexes['list']];
		$tag = $this->configData[$this->columnIndexes['tag']];

		try {
			$params = array(
				'apikey' => $apiKey,
				'listID' => $list,
				'compareField' => 'email',
				'operation' => '2',
				'subscribers' => $subscribers,
				'tags' => array(
                    $tag
                ),
			);

			$api = new EgoiUtil();
			$result = $api->publishBulk($params);
		} catch (Exception $e) {
			$error = $e->getMessage();

			$app->enqueueMessage($error, 'error');
		}

		if ($result['ERROR']) {
			$app->enqueueMessage(JText::_('COM_EGOI_ERROR_PUBLISHING') . ': ' . JText::_($result['ERROR']) . '. ' . JText::_('COM_EGOI_USER_NOT_PUBLISHED_MESSAGE_USERS'), 'error');
		} else {
			$app->enqueueMessage(JText::_('COM_EGOI_SUCCESS_PUBLISHING_USERS'), 'message');
		}
	}

	public function publishSubscriber($data) {
		
		$this->refreshData();
		$app	= JFactory::getApplication();
		$isAdminInterface = $app->isAdmin();
		
		$apiKey = $this->configData[$this->columnIndexes['apikey']];
		$list = $this->configData[$this->columnIndexes['list']];

		$email      = $data['email'];
		$first_name = $data['first_name'];
		$last_name  = $data['last_name'];
		$telephone  = $data['phone_1'];
		$cellphone  = $data['phone_2'];
		$fax        = $data['fax'];
		$birth_date = $data['birth_date'];
		
		try {
			$params = array(
				'apikey' => $apiKey,
				'listID' => $list,
				'email' => $email,
				'first_name' => $first_name,
			);
			
			if ($last_name) {
				$params['last_name'] = $last_name;
			}
			
			if ($telephone) {
				$params['telephone'] = $telephone;
			}

			if ($cellphone) {
				$params['cellphone'] = $cellphone;
			}

			if ($fax) {
				$params['fax'] = $fax;
			}

			if ($birth_date) {
				$params['birth_date'] = $birth_date;
			}

			$params['status'] = 1;

			$api = new EgoiUtil();
			$result = $api->publishSubscriber($params);
			
		} catch (Exception $e) {
			
			if ($isAdminInterface) {
				$error = $e->getMessage();
				$app->enqueueMessage($error, 'error');
			}
			
			$data['ERROR'] = JText::_($error);

			return $data;
		}

		if ($result['ERROR']) {
			if ($isAdminInterface) {
				$app->enqueueMessage(JText::_('COM_EGOI_ERROR_PUBLISHING') . ': ' . JText::_($result['ERROR']) . '. ' . JText::_('COM_EGOI_USER_NOT_PUBLISHED_MESSAGE'), 'error');
			}
			
			$data['ERROR'] = JText::_($result['ERROR']);
		} else {
			if ($isAdminInterface) {
				$app->enqueueMessage(JText::_('COM_EGOI_SUCCESS_PUBLISHING'), 'message');
			}
		}
		
		return $data;
	}
	
}
