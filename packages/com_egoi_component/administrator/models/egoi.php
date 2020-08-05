<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Egoi model.
 */
class EgoiModelEgoi extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EGOI';

	
	public function getColumnIndexes() {
		
		$indexes = array();
		
		$indexes['apikey'] = 0;
		$indexes['addsubscribe_myaccounts'] = 1;
		$indexes['sync'] = 2;
		$indexes['te'] = 3;
		$indexes['list'] = 4;
		$indexes['groups'] = 5;
		$indexes['tag'] = 6;
		$indexes['client_id'] = 7;

		return $indexes;
	}
	
	/**
	 * Method to build an SQL query to load the e-goi configuration.
	 *
	 * @return      string  An SQL query
	 */
	public function getConfig() {
		
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('apikey', 'addsubscribe_myaccounts', 'sync', 'te', 'list', 'groups', 'tag', 'client_id')));
		$query->from($db->quoteName('#__egoi'));

		$db->setQuery($query);
		$row = $db->loadRow();
		
		return $row;
	}
	
	/**
	 * Method to build an SQL query to load the e-goi configuration.
	 *
	 * @return      string  An SQL query
	 */
	public function getUserDetails($userId) {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('last_name', 'first_name', 'phone_1', 'phone_2', 'fax')));
		$query->from($db->quoteName('#__virtuemart_userinfos'));
		$query->where($db->quoteName('virtuemart_user_id') . ' = ' . $db->quote($userId));

		$db->setQuery($query);
		$user = $db->loadObject();
		
		return $user;
	}
	
	/**
	 * Method to build an SQL query to load userfields out of E-goi configuration.
	 *
	 * @return      string  An SQL query
	 */
	public function getUserFieldDetails($userfieldId) {
		
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('name', 'registration', 'account')));
		$query->from($db->quoteName('#__virtuemart_userfields'));
		$query->where($db->quoteName('virtuemart_userfield_id') . ' = ' . $db->quote($userfieldId));

		$db->setQuery($query);
		$userfield = $db->loadObject();
		
		return $userfield;
	}
	
	public function getUserList() {
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('name', 'username', 'email')));
		$query->from($db->quoteName('#__users'));
		
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getFormFields() {
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__egoi_forms'));
		$query->where(
			$db->quoteName('area') . ' = ' . $db->quote('widget'),
			$db->quoteName('enable') . ' = ' . $db->quote('1')
		);
		
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	// E-goi Map Fields DB
	public function getUserMapFields($jm, $egoi) {

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__egoi_map_fields'));
		$query->where($db->quoteName('jm') . ' = ' . $db->quote($jm), 'OR', $db->quoteName('egoi') . ' = ' . $db->quote($egoi));
		
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function insertUserMapFields($params = array()) {

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$columns = array(
			$db->quoteName('jm'), 
			$db->quoteName('jm_name'), 
			$db->quoteName('egoi'), 
			$db->quoteName('egoi_name'),
			$db->quoteName('status')
		);
		$values = array(
			$db->quote($params['jm']), 
			$db->quote($params['jm_name']), 
			$db->quote($params['egoi']), 
			$db->quote($params['egoi_name']), 
			(int)$params['status']
		);
		$query->insert($db->quoteName('#__egoi_map_fields'))->columns($columns)->values(implode(',', $values));
		
		$db->setQuery($query);
		$result = $db->execute();

		if($result){
			return $this->getAllMappedFields();
		}
	}

	public function getAllMappedFields($option = false){

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__egoi_map_fields'));
		$query->order('id DESC');
		if(!$option){
			$query->setLimit('1');
		}

	    $db->setQuery($query);
	    return $db->loadObjectList();
	}

	public function deleteMapField($id = null){

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$conditions = array(
		    $db->quoteName('id') . ' = ' . (int)$id
		);

		$query->delete($db->quoteName('#__egoi_map_fields'));
		$query->where($conditions);

	    $db->setQuery($query);
	    $db->execute();

	    return true;
	}
	
	/**
	 * Method to build an SQL query to save the E-goi configuration.
	 * 
	 * $data data from display form
	 *
	 * @return string  An SQL query
	 */
	public function store($data, $validate = false)
    {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$apikey = $data['apikey'];
		$addsubscribe_myaccounts = $data['addsubscribe_myaccounts'];
		$sync = $data['sync'];
		$te = $data['te'];
		$list = $data['list'];
		$group = $data['groups'];
		$tag = $data['tag'];
		$client_id = $data['client_id'];

		// Fields to updates
		$fields = array(
			$db->quoteName('apikey') . " = '" . $apikey . "'",
			$db->quoteName('addsubscribe_myaccounts') . " = '" . $addsubscribe_myaccounts. "'",
			$db->quoteName('sync') . " = '" . $sync. "'",
			$db->quoteName('te') . " = '" . $te. "'",
			$db->quoteName('list') . " = '" . $list. "'",
			$db->quoteName('groups') . " = '" . $group . "'",
			$db->quoteName('tag') . " = '" . $tag . "'",
			$db->quoteName('client_id') . " = '" . $client_id . "'",
		);
		
		$query->update($db->quoteName('#__egoi'))->set($fields);
		$db->setQuery($query);
		$db->query();

	}

	public function store_forms($data, $id){

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$enable = $data['enable'];
		$form_title = $data['form_title'];
		$show_title = $data['show_title'];

		$content = $data['content'];
		$style_w = $data['style_w'];
		$style_h = $data['style_h'];
		$form_type = $data['form_type'];
		$list = $data['list'];
		$area = $data['area'];
		$estado = $data['estado'];

		$exists = 1;
		$sql = "SELECT * FROM `#__egoi_forms` WHERE id=$id";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		if(!$rows[0]){
			$exists = 0;
		}

		if($exists){
			$fields = array(
				$db->quoteName('enable') . " = " . $db->quote($enable),
				$db->quoteName('form_title') . ' = ' . $db->quote($form_title),
				$db->quoteName('show_title') . ' = ' . $db->quote($show_title),
				$db->quoteName('content') . ' = ' . $db->quote($content),
				$db->quoteName('style_w') . ' = ' . $db->quote($style_w),
				$db->quoteName('style_h') . ' = ' . $db->quote($style_h),
				$db->quoteName('form_type') . ' = ' . $db->quote($form_type),
				$db->quoteName('list') . ' = ' . $db->quote($list),
				$db->quoteName('area') . ' = ' . $db->quote($area),
				$db->quoteName('estado') . ' = ' . $db->quote($estado)
			);

			$conditions = array(
			    $db->quoteName('id') . ' = '.$id
			);
			$query->update($db->quoteName('#__egoi_forms'))->set($fields)->where($conditions);
		}else{
			$columns = array($db->quoteName('id'), $db->quoteName('enable'), $db->quoteName('form_title'), $db->quoteName('show_title'), $db->quoteName('content'), $db->quoteName('style_w'), $db->quoteName('style_h'), $db->quoteName('form_type'), $db->quoteName('list'), $db->quoteName('area'), $db->quoteName('estado'));
			$values = array($id, $enable, $db->quote($form_title), $db->quote($show_title), $db->quote($content), $db->quote($style_w), $db->quote($style_h), $db->quote($form_type), $db->quote($list), $db->quote($area), '1');
			$query->insert($db->quoteName('#__egoi_forms'))->columns($columns)->values(implode(',', $values));
		}

		$db->setQuery($query);
		$result = $db->execute();

	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'Egoi', $prefix = 'EgoiTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true){
		
		$app = JFactory::getApplication();
		$form = $this->loadForm('com_egoi.egoi', 'egoi', array('control' => 'jform', 'load_data' => $loadData));
        
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData(){
		
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_egoi.edit.egoi.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.0
	 */
	protected function prepareTable($table){
		
		jimport('joomla.filter.output');
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__egoi');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}

	public function getLanguages() {	
		
		$languages = array();
		$db = JFactory::getDbo(); 
		$query = $db->getQuery(true);

		$query->select('distinct(element) as element, manifest_cache');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('type') . " = " . $db->quote('language') . " AND " . $db->quoteName('enabled') . " = 1");
		$db->setQuery($query);

		$results = $db->loadObjectList();
		foreach ($results as $row) {
			$jsonData = json_decode($row->manifest_cache);

			$languages[$row->element] = $jsonData->{'name'};
		}
		
		return $languages;
	}

	public function getFieldMap($name = false, $field = false){
		
		$db = JFactory::getDbo(); 
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__egoi_map_fields'));
		if($field){
			$query->where($db->quoteName('jm') . " = " . $db->quote($field));
		}else{
			$query->where($db->quoteName('egoi') . " = " . $db->quote($name));
		}
		$db->setQuery($query);
		$results = $db->loadObjectList();

        return $results[0];
	}
	

}