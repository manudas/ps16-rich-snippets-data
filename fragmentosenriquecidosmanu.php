<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class fragmentosenriquecidosmanu extends Module
{
	public function __construct()
	{
		$this->name = 'fragmentosenriquecidosmanu';
		$this->author = 'Manu';
		$this->tab = 'front_office_features';
		$this->version = '1.4.0';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Bloque de fragmentos enriquecidos de Manu');
		$this->description = $this->l('Te permite introducir los microdata asociados a la tienda');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return parent::install()					&& Configuration::updateValue('BLOCKRICHSNIPPETS_DATATYPE', '') // normalmente STORE o FLORIST para una floristería
			&& Configuration::updateValue('BLOCKRICHSNIPPETS_TELNUMBER', '')						&& Configuration::updateValue('BLOCKRICHSNIPPETS_STORENAME', '')
			&& Configuration::updateValue('BLOCKRICHSNIPPETS_URL', '')						&& Configuration::updateValue('BLOCKRICHSNIPPETS_SAMEAS', '')						&& Configuration::updateValue('BLOCKRICHSNIPPETS_LOGO', '')			
			&& $this->registerHook('displayHeader');
	}

	public function uninstall()
	{
		// Delete configuration
		return Configuration::deleteByName('BLOCKRICHSNIPPETS_DATATYPE') 			&& Configuration::deleteByName('BLOCKRICHSNIPPETS_TELNUMBER') 			&& Configuration::deleteByName('BLOCKRICHSNIPPETS_STORENAME') 			&& Configuration::deleteByName('BLOCKRICHSNIPPETS_URL') 			&& Configuration::deleteByName('BLOCKRICHSNIPPETS_SAMEAS')			&& Configuration::deleteByName('BLOCKRICHSNIPPETS_LOGO')			&& parent::uninstall();
	}

	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('BLOCKRICHSNIPPETS_DATATYPE', Tools::getValue('BLOCKRICHSNIPPETS_DATATYPE'));						Configuration::updateValue('BLOCKRICHSNIPPETS_TELNUMBER', Tools::getValue('BLOCKRICHSNIPPETS_TELNUMBER'));
			Configuration::updateValue('BLOCKRICHSNIPPETS_STORENAME', Tools::getValue('BLOCKRICHSNIPPETS_STORENAME'));			Configuration::updateValue('BLOCKRICHSNIPPETS_URL', Tools::getValue('BLOCKRICHSNIPPETS_URL'));						Configuration::updateValue('BLOCKRICHSNIPPETS_SAMEAS', Tools::getValue('BLOCKRICHSNIPPETS_SAMEAS'));			Configuration::updateValue('BLOCKRICHSNIPPETS_LOGO', Tools::getValue('BLOCKRICHSNIPPETS_LOGO'));								
			$this->_clearCache('rich-snippets-default-bootstrap-header.tpl');
			$html .= $this->displayConfirmation($this->l('Configuration updated'));
		}

		$html .= $this->renderForm();

		return $html;
	}

	public function hookDisplayHeader($params)
	{					$this->context->smarty->assign(array(            'BLOCKRICHSNIPPETS_DATATYPE'      => Configuration::get('BLOCKRICHSNIPPETS_DATATYPE'),            'BLOCKRICHSNIPPETS_TELNUMBER'       => Configuration::get('BLOCKRICHSNIPPETS_TELNUMBER'),            'BLOCKRICHSNIPPETS_STORENAME'       => Configuration::get('BLOCKRICHSNIPPETS_STORENAME'),            'BLOCKRICHSNIPPETS_URL'         => Configuration::get('BLOCKRICHSNIPPETS_URL'),            'BLOCKRICHSNIPPETS_SAMEAS' => Configuration::get('BLOCKRICHSNIPPETS_SAMEAS'),            'BLOCKRICHSNIPPETS_LOGO' => Configuration::get('BLOCKRICHSNIPPETS_LOGO'),        ));
	}

	public function renderForm()
	{		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('This block configures the microdata of your site. You need to use the default-bootstrap theme or change your header.tpl file adding the "changes by manu" data you can find in the views/templates/front/rich-snippets-default-bootstrap-header.tpl').'<br/><br/>',
				'input' => array(
					array(						'type' => 'text',						'label' => $this->l('Tipo de fragmento enriquecido.'),						'name' => 'BLOCKRICHSNIPPETS_DATATYPE',						'desc' => $this->l('Normalmente STORE o FLORIST'),					),										array(						'type' => 'text',						'label' => $this->l('Nombre del objeto para el que se crea el framgento enriquecido'),						'name' => 'BLOCKRICHSNIPPETS_STORENAME',						'desc' => $this->l('Normalmente Coronafuneral, Telerosa, Envioderosas, etc...'),					),										array(						'type' => 'text',						'label' => $this->l('Número de teléfono'),						'name' => 'BLOCKRICHSNIPPETS_TELNUMBER',					),					array(						'type' => 'text',						'label' => $this->l('La url del sitio para el que se crea la microdata'),						'name' => 'BLOCKRICHSNIPPETS_URL',												'desc' => $this->l('La dirección web de Coronafuneral, Telerosa, Envioderosas, etc...'),					),					array(						'type' => 'text',						'label' => $this->l('La url del sitio idéntico'),						'name' => 'BLOCKRICHSNIPPETS_SAMEAS',												'desc' => $this->l('Normalmente la dirección web del blog o de otro sitio relacionado...'),					),					array(						'type' => 'text',						'label' => $this->l('La url del logo para el cual se crea esta microdata'),						'name' => 'BLOCKRICHSNIPPETS_LOGO',												'desc' => $this->l('Normalmente la dirección web de la imagen del logo de Telerosa, Coronafuneral, etc...'),					),										
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}				public function getConfigFieldsValues()	{		return array(			'BLOCKRICHSNIPPETS_DATATYPE' => Tools::getValue('BLOCKRICHSNIPPETS_DATATYPE', Configuration::get('BLOCKRICHSNIPPETS_DATATYPE')),						'BLOCKRICHSNIPPETS_TELNUMBER' => Tools::getValue('BLOCKRICHSNIPPETS_TELNUMBER', Configuration::get('BLOCKRICHSNIPPETS_TELNUMBER')),			'BLOCKRICHSNIPPETS_STORENAME' => Tools::getValue('BLOCKRICHSNIPPETS_STORENAME', Configuration::get('BLOCKRICHSNIPPETS_STORENAME')),						'BLOCKRICHSNIPPETS_URL' => Tools::getValue('BLOCKRICHSNIPPETS_URL', Configuration::get('BLOCKRICHSNIPPETS_URL')),						'BLOCKRICHSNIPPETS_SAMEAS' => Tools::getValue('BLOCKRICHSNIPPETS_SAMEAS', Configuration::get('BLOCKRICHSNIPPETS_SAMEAS')),						'BLOCKRICHSNIPPETS_LOGO' => Tools::getValue('BLOCKRICHSNIPPETS_LOGO', Configuration::get('BLOCKRICHSNIPPETS_LOGO'))		);	}
}
