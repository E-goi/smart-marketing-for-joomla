<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.egoi
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     MIT License
 */

defined('_JEXEC') or die;

/**
 * E-goi Front Office Hooks plugin class.
 *
 * @since  1.0.1
 */
class PlgContentEgoi extends JPlugin
{

    /**
     *
     */
    public function onAfterDispatch()
    {

        $configData = $this->getConfig();
        $teEnabled = $configData->te;
        $list = $configData->list;

        if(!empty($teEnabled)){
            $user = JFactory::getUser();

            $data = '
                <script type="text/javascript">
                    var _egoiaq = _egoiaq || [];
                    (function(){
                        var u="https://egoimmerce.e-goi.com/";
                        var u2="https://cdn-te.e-goi.com/";
                        _egoiaq.push([\'setClientId\', "258299"]);
                        _egoiaq.push([\'setListId\', "' . (!empty($list) ? $list : '') . '"]);
                        _egoiaq.push([\'setSubscriber\', "' . (!empty($user->email) ? $user->email : '') . '"]);
                        _egoiaq.push([\'setCampaignId\', ""]);
                        _egoiaq.push([\'setTrackerUrl\', u+\'collect\']);
                        _egoiaq.push([\'trackPageView\']);
                        _egoiaq.push([\'enableLinkTracking\']);
                        var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];
                        g.type=\'text/javascript\';
                        g.defer=true;
                        g.async=true;
                        g.src=u2+\'egoimmerce.js\';
                        s.parentNode.insertBefore(g,s);
                    })();
                </script>';

            echo $data;
        }
    }

    /**
     * @return string
     */
	public function onContentAfterDisplay(){
		
		$db = JFactory::getDbo();
		$sql = "SELECT * FROM `#__egoi_forms` WHERE area='body' and enable='1'";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		if(!empty($rows)){
			return $this->decode($rows[0]->content, $rows[0]->form_type, $rows[0]->style_w, $rows[0]->style_h);
		}

	}

    /**
     * @return string
     */
	public function onContentAfterTitle(){
		
		$db = JFactory::getDbo();
		$sql = "SELECT * FROM `#__egoi_forms` WHERE area='header' and enable='1'";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		if(!empty($rows)){
			return $this->decode($rows[0]->content, $rows[0]->form_type, $rows[0]->style_w, $rows[0]->style_h);
		}

	}

    /**
     * @param $data
     * @param $type
     * @param string $w
     * @param string $h
     * @return string
     */
	private function decode($data, $type, $w = '', $h = ''){

		if($type == 'iframe'){
			$url = explode(' - ', base64_decode($data));
			return html_entity_decode('<iframe src="//'.$url[1].'" style="border: 0 none; width:'.$w.'px; height:'.$h.'px;" onload="window.parent.parent.scrollTo(0,0);"></iframe>');
		}
		return html_entity_decode(base64_decode($data));
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
        $clientData = $db->loadObject();

        return $clientData;
    }
}
