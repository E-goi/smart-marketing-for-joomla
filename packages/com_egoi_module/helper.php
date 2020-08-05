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
 
if (!class_exists('EgoiModelEgoi')) 
    require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'egoi.php');

if (!class_exists('EgoiUtil')) {
	require(JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'egoi' . DIRECTORY_SEPARATOR . 'EgoiApi' . DIRECTORY_SEPARATOR . 'egoi.php');
}

/**
 * Class ModEgoiHelper
 */
class ModEgoiHelper{

    /**
     * @param $params
     * @return mixed
     */
	public static function getFields($params)
    {
		
		$egoiModel = new EgoiModelEgoi();
        return $egoiModel->getFormFields();
    }

    /**
     * @param $post
     */
    public static function processForm($post)
    {

    	if(!empty($post) && ($post['lista'])) {
    		foreach ($post as $key => $value) {
			    if(strpos($key, 'email') !== false){
			    	$data['email'] = $value;
			    }else if(strpos($key, 'fname') !== false){
			    	$data['fname'] = $value;
			    }else if(strpos($key, 'lname') !== false){
			    	$data['lname'] = $value;
			    }else{
			    	$data[$key] = $value;
			    }
			}

			$client = self::getClient();
			$apikey = $client->apikey;

			if(filter_var($data['email'], FILTER_VALIDATE_EMAIL) == false){
				//print('MOD_EMAIL_ERROR');
				print('<span class="alert alert-danger">Invalid E-mail</span>');
				exit;
			}

			$data = array(
				'apikey' => $apikey,
				'listID' => $data['lista'],
				'email' => $data['email'],
				'first_name' => $data['fname'],
				'last_name' => $data['lname'],
				'status' => 1,
			);

			$egoiUtil = new EgoiUtil();
			$result = $egoiUtil->publishSubscriber($data, 1);

			if($result['UID']){
				//print('COM_SUBSCRIBER_CREATED');
				print('<span class="alert alert-success">Subscriber Succesfully created!</span>');
				exit;
			}
			//print('COM_SUBSCRIBER_EXISTS');
			print('<span class="alert alert-info">ERROR: E-mail do not exists!</span>');
			exit;
    	}
    }

    /**
     * @return mixed
     */
    private static function getClient()
    {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('apikey')));
		$query->from($db->quoteName('#__egoi'));

		$db->setQuery($query);
		$userfield = $db->loadObject();
		
		return $userfield;
	}

    /**
     * @param $content
     * @param false $option
     * @return string|string[]
     */
	public static function decode($content, $option = false)
    {

		if($option){
			$url = explode(' - ', base64_decode($content));
			return html_entity_decode('<iframe src="//'.$url[1].' style="border: 0 none;" onload="window.parent.parent.scrollTo(0,0);"></iframe>');
		}

		$str = array('<form ', 'action="', 'type="submit"', '</form>');
		$replace = array('<form id="egoi_subscribe"', 'data-action="', 'type="button" id="egoi_submit" style="width:100%;"', '</form><p><div id="egoi_result"></div></p>');
		return str_replace($str, $replace, html_entity_decode(base64_decode($content)));
	}
}