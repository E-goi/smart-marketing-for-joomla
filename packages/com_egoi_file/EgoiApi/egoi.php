<?php
/**
 *
 * Egoi helper
 *
 * @package    Includes
 * @subpackage Egoi
 * @author E-goi
 * @link https://www.e-goi.com
 * @copyright Copyright (c) 2020 E-goi. All rights reserved.
 * @license MIT Licence
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'egoi' . PATH_SEPARATOR . get_include_path());

if (!class_exists('EgoiApiFactory')) {
    require('Egoi' . DIRECTORY_SEPARATOR . 'Factory.php');
}

/**
 * Helper class for E-goi
 *
 * For information on E-goi API usage, please visit the API documentation, at: http://www.e-goi.com/en/recursos/api/
 *
 * @package        Libraries
 * @subpackage    E-goi
 * @author        E-goi
 */
class EgoiUtil
{

    /**
     * @var string PLUGIN_KEY
     */

    const PLUGIN_KEY = '72ea3b4021b07c3c6e0d5dd0f4badbcb';
    
    /**
     * @var string $apiV3Uri
     */
    const API_V3_URL = 'https://api.egoiapp.com';

    /**
     * @var string
     */
    public $apiKey;


    public function __construct()
    {
        $egoiModel = JModelAdmin::getInstance('egoi', 'EgoiModel', array());
        $columnIndexes = $egoiModel->getColumnIndexes();
        $configData = $egoiModel->getConfig();
        $apiKey = $configData[$columnIndexes['apikey']];

        $this->setApiKey($apiKey);
    }

    /**
     * @return null
     */
    public function getApiKey()
    {
        return $this->apiKey ?: null;
    }

    /**
     * @param null $apiKey
     */
    public function setApiKey($apiKey=null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return array
     */
    private function getSoapParams()
    {
        return array(
            'plugin_key' => self::PLUGIN_KEY,
            'apikey' => $this->getApiKey()
        );
    }

    /**
     * @param $params
     * @return mixed
     * @throws Zend_XmlRpc_Client_FaultException
     */
    function setupCallBackAPI($params)
    {

        if(!empty($this->columnIndexes['apikey'])){
            $this->setApiKey($this->columnIndexes['apikey']);
        }
        $this->apiv3Call('RAWPOST', '/ping');

        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $apiKey = $this->configData[$this->columnIndexes['apikey']];
        $list = $this->configData[$this->columnIndexes['list']];

        require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'egoi' . DIRECTORY_SEPARATOR . 'Zend' . DIRECTORY_SEPARATOR . 'XmlRpc' . DIRECTORY_SEPARATOR . 'Client.php';

        $client = new Zend_XmlRpc_Client('http://api.e-goi.com/v2/xmlrpc.php');
        return $result = $client->call('editApiCallback', array($params));
    }

    /**
     * @param $params
     * @return array|mixed
     */
    function createListEgoi($params)
    {

        $this->apiv3Call('RAWPOST', '/ping');

        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;

        return $result = $api->createList($params);
    }

    /**
     * @param $params
     * @return array|mixed
     */
    function updateListEgoi($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;

        return $result = $api->updateList($params);
    }

    /**
     * @param $params
     * @return array|mixed
     */
    public function getListsFromEgoi($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        $result = $api->getLists($params);

        return $result;
    }

    /**
     * @return mixed
     */
    public function getTagsEgoi()
    {
        $result = $this->apiv3Call('GET', '/tags');

        if(empty($result)){
            $result = "[]";
        }

        return json_decode($result, true);
    }

    /**
     * @return mixed
     */
    public function getClientEgoi()
    {
        $result = $this->apiv3Call('GET', '/my-account');

        if(empty($result)){
            $result = "[]";
        }

        return json_decode($result, true);
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        $result = $this->apiv3Call('GET', '/tags');

        if(empty($result)){
            $result = "[]";
        }

        return json_decode($result, true);
    }

    function publishBulk($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;

        return $result = $api->addSubscriberBulk($params);
    }

    function getSubscriber($params, $option = false)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        if ($option) {
            $params['subscriber'] = 'all_subscribers';
        }

        return $result = $api->subscriberData($params);
    }


    function getAllSubscribers($params, $start = 0)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        $params['subscriber'] = 'all_subscribers';
        $params['limit'] = 1000;
        $params['start'] = $start;

        return $result = $api->subscriberData($params);
    }

    function publishSubscriber($params, $option = false)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;

        $result = $api->addSubscriber($params);

        if ($result['ERROR'] && $result['ERROR'] == 'EMAIL_ALREADY_EXISTS') {
            if ($option) {
                //print($result['ERROR']);
                //return(0);
                print('<span class="alert alert-danger">Subscriber Already exists!</span>');
                exit;
            }
            unset($params['status']);
            $params['subscriber'] = $params['email'];
            $result = $api->editSubscriber($params);
        }

        return $result;
    }

    function removeSubscriber($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        $result = $api->removeSubscriber($params);

        return $result;
    }

    function getForms($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        $result = $api->getForms($params);

        return $result;
    }

    function getExtraFields($params)
    {
        $this->apiv3Call('RAWPOST', '/ping');
        $api = EgoiApiFactory::getApi(Protocol::XmlRpc);
        $params['plugin_key'] = self::PLUGIN_KEY;
        $result = $api->getExtraFields($params);

        return $result;
    }

    /**********************************************/
    /********** APIV3 AND TRANSACTIONAL ***********/
    /**********************************************/

    /**
     * @param $method
     * @param $url
     * @param array $data
     *
     * @return array|bool|mixed
     */
    public function apiv3Call($method, $url, $data = array()) {

        $headers = array(
            "cache-control: no-cache",
            "Apikey: " . $this->getApiKey(),
            "Pluginkey: " . self::PLUGIN_KEY,
            "Content-Type: application/json",
            "Accept: application/json"
        );

        $url = self::API_V3_URL . $url;

        return $this->call($method, $headers, $url, $data);

    }

    /**
     * @param $method
     * @param $url
     * @param array $data
     *
     * @return array|bool|mixed
     */
    public function transactionalCall($method, $url, $data = array()) {

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json"
        );

        $data = array_merge($data, array(
            'apikey' => $this->getApiKey(),
            'pluginkey' => self::PLUGIN_KEY
        ));

        $url = self::API_TRANSACTIONAL_URL . $url;

        return $this->call($method, $headers, $url, $data);

    }

    /**
     *
     * API V3 / Transactional curl wrapper
     *
     * @param string $method
     * @param array $headers
     * @param string $url
     * @param array $data
     *
     * @return array|bool|mixed
     */
    private function call($method, $headers = array('Content-Type: application/json'), $url, $data = array()) {

        if(empty($method) || empty($url)) {
            return false;
        }

        $curl = curl_init();

        switch ($method) {
            case "GET":
                if (!empty($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($data));
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($data));
                } else {
                    return false;
                }
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($data)) {
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
                }
                break;
            case "RAWDELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                } else {
                    return false;
                }
                break;
            case "OPTIONS":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                curl_setopt($curl, CURLOPT_HEADER, 1);
                break;
            case "RAWPOST":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            default:
                if (!empty($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response      = curl_exec($curl);

        curl_close($curl);
        return $response;
    }




}

