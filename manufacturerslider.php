<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class ManufacturerSlider extends Module
{
	public function __construct()
	{
		$this->name = 'manufacturerslider';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'damianwojcik.it';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();	

		$this->displayName = $this->l('Responsive Manufacturer Slider');
		$this->description = $this->l('Display all Manufacturers in slider.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if (!Configuration::get('MYMODULE_NAME')) {
		  $this->warning = $this->l('No name provided');
		}
	}

	public function install()
	{
		if (!parent::install() ||			
			!$this->registerHook('header') ||
			!$this->registerHook('displayHome')	
		) {
			return false;
		}		
		return true;
	}

	public function uninstall()
	{
	  if (!parent::uninstall()) {
		return false;
	  }
	  return true;
	}

	public function hookdisplayHome($params)
	{	
		$id_lang=(int)$this->context->language->id;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT m.id_manufacturer, m.name
			FROM `'._DB_PREFIX_.'manufacturer` m
		');

		foreach ($result as &$row)
		{
			$row['id_image'] = Tools::file_exists_cache(_PS_MANU_IMG_DIR_.$row['id_manufacturer'].'.jpg') ? (int)$row['id_manufacturer'] : Language::getIsoById($id_lang).'-default-medium_default';
		}

		$this->smarty->assign('all_manufacturers', $result);

		return $this->display(__FILE__, 'manufacturerslider.tpl');
	}
	
	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'css/swiper.min.css', 'all');
		$this->context->controller->addCSS(($this->_path).'css/manufacturerslider.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/swiper.min.js', 'all');
		$this->context->controller->addJS(($this->_path).'js/manufacturerslider.js', 'all');
	}
}
