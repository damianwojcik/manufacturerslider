<?php
/**
* 2007-2018 PrestaShop
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
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
**/

if (!defined('_PS_VERSION_')) {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

class ManufacturerSlider extends Module implements PrestaHomeConfiguratorInterface
{
    use PrestaHomeHelpers, PrestaHomeConfiguratorBase;

    public function __construct()
    {
        $this->name = 'manufacturerslider';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'damianwojcik.it';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = 'ac04b3bb673c6f7184cef93d1a32874a';

        parent::__construct();

        $this->displayName = $this->l('Responsive Manufacturer Slider');
        $this->description = $this->l('Display all Manufacturers in slider.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }

        $this->setOptionsPrefix('manuslider');
    }

    public function setOptionsPrefix($custom = false)
    {
        $this->options_prefix = Tools::strtoupper(($custom ? $custom : $this->name)).'_';

        return $this;
    }

    public function install()
    {
        $this->renderConfigurationForm();
        $this->batchUpdateConfigs();

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
        $this->renderConfigurationForm();
        $this->deleteConfigs();

        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        $this->renderConfigurationForm();
        $this->_html = '<h2>'.$this->displayName.'</h2>';

        if (Tools::isSubmit('save'.$this->name)) {
            $this->renderConfigurationForm();
            $this->batchUpdateConfigs();

            $this->_clearCache('*');
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));

        }
        return $this->_html . $this->renderForm();
    }

    public function renderConfigurationForm()
    {
        if ($this->fields_form) {
            return;
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),

                'input' => array(

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Slides:'),
                        'name'  => $this->options_prefix.'ITEMS',
                        'default' => '3',
                        'desc' => $this->l('Number of displayed slides'),
                        'validate' => 'isUnsignedInt',
                    ),
        
                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Speed:'),
                        'name'  => $this->options_prefix.'SPEED',
                        'default' => '1500',
                        'desc' => $this->l('Duration of transition between slides (in ms)'),
                        'validate' => 'isUnsignedInt',
                    ),
        
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Loop:'),
                        'name' => $this->options_prefix.'LOOP',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'default' => '1'
                    ),
        
                    array(
                        'name' => 'separator',
                        'type' => 'html',
                        'html_content' => '<h2>'.$this->l('Troubleshooting').'</h2>',
                        'ignore' => true
                    ),
        
                    array(
                        'name' => 'separator',
                        'type' => 'html',
                        'html_content' => '<div>Contact author: <a href="mailto:khamian@gmail.com?subject=PS Manufacturer Slider - Problem" title="Contact">khamian@gmail.com</a></div>',
                        'ignore' => true
                    )
                ),

                'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right')
            ),
        );

        $this->fields_form[] = $fields_form;
    }

    public function hookdisplayHome($params)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT m.id_manufacturer, m.name
			FROM `'._DB_PREFIX_.'manufacturer` m
		');

        if (!empty($result)) {
            foreach ($result as &$manufacturer) {
                $manufacturer['image'] = $this->context->language->iso_code . '-default';
                $manufacturer['link'] = $this->context->link->getManufacturerLink($manufacturer['id_manufacturer']);
                $fileExist = file_exists(
                    _PS_MANU_IMG_DIR_ . $manufacturer['id_manufacturer'] . '-' .
                    ImageType::getFormatedName('medium') . '.jpg'
                );
                if ($fileExist) {
                    $manufacturer['imagesrc'] = _THEME_MANU_DIR_ . $manufacturer['id_manufacturer']
                    . '-' . ImageType::getFormatedName('medium') . '.jpg';
                } else {
                    $manufacturer['imagesrc'] = _THEME_MANU_DIR_ . $manufacturer['image']
                    . '-' . ImageType::getFormatedName('medium') . '.jpg';
                }
            }
        }

        $this->smarty->assign('all_manufacturers', $result);

        $this->context->smarty->assign(array(
            'manuslider_items' => Configuration::get($this->options_prefix.'ITEMS'),
            'manuslider_speed'  => Configuration::get($this->options_prefix.'SPEED'),
            'manuslider_loop'  => Configuration::get($this->options_prefix.'LOOP')
        ));

        return $this->display(__FILE__, 'manufacturerslider.tpl');
    }
    
    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/swiper.min.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/manufacturerslider.css', 'all');
        $this->context->controller->addJS(($this->_path).'views/js/swiper.min.js', 'all');
        $this->context->controller->addJS(($this->_path).'views/js/manufacturerslider.js', 'all');
    }
}
