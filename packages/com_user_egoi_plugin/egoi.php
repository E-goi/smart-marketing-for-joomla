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

if (!class_exists( 'EgoiUtil' )) {
	require(JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'egoi' . DIRECTORY_SEPARATOR . 'EgoiApi' . DIRECTORY_SEPARATOR . 'egoi.php');
}

if (!class_exists('EgoiModelEgoi')) {
	require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'egoi.php');
}

/**
 * Smart Marketing for Joomla plugin
 *
 * @package		Plugin
 * @subpackage	User
 */
class plgUserEgoi extends JPlugin {

    /**
     * @var $egoiModel
     */
	private $egoiModel;


    /**
     * @param $user
     * @param $isnew
     * @param $success
     * @param $msg
     */
	public function onUserAfterSave($user, $isnew, $success, $msg)
    {
		
		if (!$success) {
			return;
		}

		$configData = $this->getConfig();
		$sync = $configData->sync;
		$group = $configData->groups;
		$tag = $configData->tag;

		if($sync){

			$this->egoiModel = new EgoiModelEgoi();

			$egoiUtil = new EgoiUtil();
			$email = $user['email'];
			$name = explode(' ', $user['name']);
			$username = $user['username'];

			if((!$group) || in_array($group, $user['groups'])){

				$data = array(
					'first_name' => $name[0] ? $name[0] : $user['name'],
					'last_name' => $name[1] ? $name[1] : $user['name'],
					'status' => 1,
				);

				$extra = $egoiUtil->getExtraFields(array('apikey' => $configData->apikey, 'listID' => $configData->list));
				if(!empty($extra['extra_fields'])){
                    foreach ($extra['extra_fields'] as $key_extra => $extra) {
                        $data['extra_'.$key_extra] = '';
                    }
                }

                if(!empty($data)){
                    foreach($data as $key => $value){
                        $row_new_value = $this->egoiModel->getFieldMap($key);
                        $jm = $row_new_value->jm;
                        if($row_new_value->id){
                            $data[$row_new_value->egoi] = $user[$jm];
                        }
                    }
				}

				$data['apikey'] = $configData->apikey;
				$data['listID'] = $configData->list;
				$data['email'] = $email;
				if(!empty($tag)){
                    $data['tags'] = array($tag);
                }

				$egoiUtil->publishSubscriber($data);
			}
		}
	}

    /**
     * @param $user
     * @param $success
     * @param $msg
     */
	public function onUserAfterDelete($user, $success, $msg)
    {

		if (!$success) {
			return;
		}

		$configData = $this->getConfig();
		$sync = $configData->sync;
		$group = $configData->groups;

		if($sync){

			$email = $user['email'];
			$name = $user['name'];
			$username = $user['username'];

			if((!$group) || in_array($group, $user['groups'])){

				$data = array(
					'apikey' => $configData->apikey,
					'listID' => $configData->list,
					'subscriber' => $email
				);

				$egoiUtil = new EgoiUtil();
				$result = $egoiUtil->removeSubscriber($data);
			}
		}
	}

    /**
     * @return mixed
     */
	public function getConfig() {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('apikey', 'addsubscribe_myaccounts', 'sync', 'te', 'list', 'groups', 'tag', 'client_id')));
		$query->from($db->quoteName('#__egoi'));

		$db->setQuery($query);
		$userfield = $db->loadObject();
		
		return $userfield;
	}
}