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

class ManufacturerSlider extends Module
{
    public function __construct()
    {
        $this->name = 'manufacturerslider';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'dwojcik.pro';
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
        if (property_exists($this, 'fields_form')) {
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
                        'name' => 'separator',
                        'type' => 'html',
                        'html_content' => '<h2>'.$this->l('Global Settings').'</h2>',
                        'ignore' => true
                    ),

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Slides:'),
                        'name'  => $this->options_prefix.'ITEMS',
                        'default' => '3',
                        'desc' => $this->l('Number of displayed slides (default or more than 1170px wide)'),
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
                        'html_content' => '<h2>'.$this->l('Responsive').'</h2>',
                        'ignore' => true
                    ),

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Laptops - items:'),
                        'name'  => $this->options_prefix.'ITEMS_1170',
                        'default' => '3',
                        'desc' => $this->l('Number of displayed slides if screen width is less than 1170px'),
                        'validate' => 'isUnsignedInt',
                    ),

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Tablets - items:'),
                        'name'  => $this->options_prefix.'ITEMS_992',
                        'default' => '3',
                        'desc' => $this->l('Number of displayed slides if screen width is less than 992px'),
                        'validate' => 'isUnsignedInt',
                    ),

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Small Tablets - items:'),
                        'name'  => $this->options_prefix.'ITEMS_600',
                        'default' => '2',
                        'desc' => $this->l('Number of displayed slides if screen width is less than 600px'),
                        'validate' => 'isUnsignedInt',
                    ),

                    array(
                        'type'  => 'text',
                        'lang'  => false,
                        'label' => $this->l('Phones - items:'),
                        'name'  => $this->options_prefix.'ITEMS_460',
                        'default' => '1',
                        'desc' => $this->l('Number of displayed slides if screen width is less than 460px'),
                        'validate' => 'isUnsignedInt',
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
            'manuslider_items_1170'  => Configuration::get($this->options_prefix.'ITEMS_1170'),
            'manuslider_items_992'  => Configuration::get($this->options_prefix.'ITEMS_992'),
            'manuslider_items_600'  => Configuration::get($this->options_prefix.'ITEMS_600'),
            'manuslider_items_460'  => Configuration::get($this->options_prefix.'ITEMS_460')
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

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->renderConfigurationForm();

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'save'.$this->name;
        $helper->toolbar_btn =  array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            )
        );
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($this->fields_form);
    }

    public function getConfigFieldsValues()
    {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);

        $fields_values = array();
        foreach ($this->fields_form as $f) {
            foreach ($f['form']['input'] as $input) {
                if (isset($input['ignore']) && $input['ignore'] == true) {
                    continue;
                }

                if (isset($input['lang']) && $input['lang'] == true) {
                    foreach (Language::getLanguages(false) as $lang) {
                        $values = Tools::getValue($input['name'].'_'.$lang['id_lang'], (Configuration::hasKey($input['name'], $lang['id_lang']) ? Configuration::get($input['name'], $lang['id_lang'], (int)$id_shop_group, (int)$id_shop) : $input['default']));
                        $fields_values[$input['name']][$lang['id_lang']] = $values;
                    }
                } else {
                    if ($input['type'] == 'checkbox' && isset($input['values'])) {
                        $input['name'] = str_replace(array('[]'), array(''), $input['name']);

                        $values = (Configuration::hasKey($input['name'], null, (int)$id_shop_group, (int)$id_shop) ? Tools::jsonDecode(Configuration::get($input['name']), true) : $input['default']);

                        if (is_array($values)) {
                            foreach ($input['values']['query'] as $id_cms => $val) {
                                if (in_array($id_cms, $values)) {
                                    $fields_values[$input['name'].'[]_'.$id_cms] = $id_cms;
                                }
                            }
                        }
                    } else {
                        $values = Tools::getValue($input['name'], (Configuration::hasKey($input['name'], null, (int)$id_shop_group, (int)$id_shop) ? Configuration::get($input['name']) : $input['default']));
                        $fields_values[$input['name']] = $values;
                    }
                }
            }
        }

        return $fields_values;
    }

    public function batchUpdateConfigs()
    {
        foreach ($this->fields_form as $f) {
            foreach ($f['form']['input'] as $input) {
                $input['name'] = str_replace(array('[]'), array(''), $input['name']);

                if (isset($input['ignore']) && $input['ignore'] == true) {
                    continue;
                }

                if (isset($input['lang']) && $input['lang'] == true) {
                    $data = array();
                    foreach (Language::getLanguages(false) as $lang) {
                        $val = Tools::getValue($input['name'].'_'.$lang['id_lang'], $input['default']);
                        $data[$lang['id_lang']] = $val;
                    }

                    if (isset($input['callback']) && method_exists($this, $input['callback'])) {
                        $data[$lang['id_lang']] = $this->{$input['callback']}($data[$lang['id_lang']]);
                    }

                    Configuration::updateValue(trim($input['name']), $data, true);
                } else {
                    $val = Tools::getValue($input['name'], $input['default']);
                    if (isset($input['callback']) && method_exists($this, $input['callback'])) {
                        $val = $this->{$input['callback']}($val);
                    }
                    Configuration::updateValue($input['name'], $val, true);
                }
            }
        }

        return true;
    }
}
