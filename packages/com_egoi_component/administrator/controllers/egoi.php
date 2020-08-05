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

jimport('joomla.application.component.controllerform');

if (!class_exists('EgoiHelper')) {
    require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'egoi.php');
}

if (!class_exists('EgoiModelEgoi')) {
    require (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_egoi' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'egoi.php');
}

/**
 * Egoi controller class.
 */
class EgoiControllerEgoi extends JControllerForm
{
    protected $columnIndexes = array();
    protected $configData = array();
    protected $view;
    protected $egoiModel;
    protected $egoiHelper;

    /**
     * EgoiControllerEgoi constructor.
     */
    public function __construct()
    {
        $this->egoiHelper = new EgoiHelper();

        $this->egoiModel = parent::getModel('egoi', 'EgoiModel', array());

        $this->columnIndexes = $this->egoiModel->getColumnIndexes();
        $this->configData = $this->egoiModel->getConfig();

        parent::__construct();
    }

    /**
     * display
     */
    public function display()
    {
        $this->cleanUpSessionData();

        $this->loadConfig();
        $this->loadListsFromEgoi();
        $this->loadTagsFromEgoi();
        $this->loadAccountFromEgoi();

        parent::display();
    }

    /**
     * apply
     */
    public function apply()
    {
        $this->save();
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

        if ($validate) {
            $data['apikey'] = $jinput->get('apikey', '', 'STRING');
            $data['list'] = $jinput->get('egoi_list', '0', 'INT');
            $data['sync'] = $jinput->get('egoi_sync', '0', 'INT');
            $data['te'] = $jinput->get('egoi_te', '0', 'STRING');
            $data['group'] = $jinput->get('egoi_role', '', 'STRING');
            $data['tag'] = $jinput->get('egoi_tags', '0', 'INT');
        } else {
            $data['apikey'] = $jinput->get('apikey', '', 'STRING');
            $data['list'] = $jinput->get('egoi_list', '', 'INT');
            $data['sync'] = $jinput->get('egoi_sync', '', 'INT');
            $data['te'] = $jinput->get('egoi_te', '', 'STRING');
            $data['group'] = $jinput->get('egoi_role', '', 'STRING');
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

        if ($redirect) {
            $this->columnIndexes = $this->egoiModel->getColumnIndexes();
            $this->configData = $this->egoiModel->getConfig();

            $this->loadConfig();
            $this->loadListsFromEgoi();
            $this->loadTagsFromEgoi();
            $this->loadAllSubscribers();

            return $this;
        }
        return false;
    }

    /**
     * cleanUpSessionData
     */
    public function cleanUpSessionData()
    {
        $app = JFactory::getApplication();

        $app->setUserState('com_egoi.egoiAccount', null);
        $app->setUserState('com_egoi.egoiLists', null);
        $app->setUserState('com_egoi.egoiConfig', null);
        $app->setUserState('com_egoi.egoiColumnIndexes', null);
        $app->setUserState('com_egoi.fields', null);
        $app->setUserState('com_egoi.languages', null);
        $app->setUserState('com_egoi.egoiTags', null);
    }

    /**
     * loadListsFromEgoi
     */
    public function loadListsFromEgoi()
    {
        $app = JFactory::getApplication();
        $app->setUserState('com_egoi.egoiLists', $this->egoiHelper->getListsFromEgoi());
    }

    /**
     * Load Tags From Egoi
     */
    public function loadTagsFromEgoi()
    {

        $app = JFactory::getApplication();
        $app->setUserState('com_egoi.egoiTags', $this->egoiHelper->getTagsFromEgoi());
    }

    /**
     * loadAccountFromEgoi
     */
    public function loadAccountFromEgoi()
    {
        $app = JFactory::getApplication();
        $app->setUserState('egoi.egoiAccount', $this->egoiHelper->getClientEgoi());

    }

    /**
     * loadColumnIndexes
     */
    public function loadColumnIndexes()
    {
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
        $this->loadTagsFromEgoi();

        parent::display($cachable, $urlparams);
        return $this;
    }

}