<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!defined("EgoiApiFactory")) {
	define("EgoiApiFactory",0);
	
	require_once "Api.php";
	require_once "RestImpl.php";
	require_once "SoapImpl.php";
	require_once "XmlRpcImpl.php";
	
	abstract class EgoiApiFactory {
	
		public static function getApi($protocol) {
			switch($protocol) {
				case Protocol::Rest:
					return new EgoiApiRestImpl();
				case Protocol::Soap;
					return new EgoiApiSoapImpl();
				case Protocol::XmlRpc:
					return new EgoiApiXmlRpcImpl();
			}
		}
	
	}
	
}
?>