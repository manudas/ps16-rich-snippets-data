<?php
/**
* Override de front controller por el módulo de rich snippets de Manu
*/

class FrontController extends FrontControllerCore
{

    /**
     * Compiles and outputs page header section (including HTML <head>)
     *
	 * FUNCIONA PARA PS 1.6 Y TEMA POR DEFECTO boot-strap.
	 * SE IGNORA SI FUCNIONA PARA VERSIONES POSTERIORES.
	 * EN TODO CASO SE PUEDE AÑADIR A POSTERIORES VERSIONES LO INDICADO COMO
	 * "AÑADIDO POR MANU" O "MODIFICADO POR MANU"
	 *
     * @param bool $display If true, renders visual page header section
     * @deprecated 1.5.0.1
     */
    public function displayHeader($display = true)
    {
        Tools::displayAsDeprecated();

        $this->initHeader();
        $hook_header = Hook::exec('displayHeader');
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCss($this->css_files);
            }

            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE')) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        // Call hook before assign of css_files and js_files in order to include correctly all css and javascript files
        $this->context->smarty->assign(array(
            'HOOK_HEADER'       => $hook_header,
            'HOOK_TOP'          => Hook::exec('displayTop'),
            'HOOK_LEFT_COLUMN'  => ($this->display_column_left  ? Hook::exec('displayLeftColumn') : ''),
            'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
            'HOOK_FOOTER'       => Hook::exec('displayFooter')
        ));
		
        $this->context->smarty->assign(array(
            'css_files' => $this->css_files,
            'js_files'  => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files
        ));

        $this->display_header = $display;
		
		// MODIFICADO POR MANU
        $this->smartyOutputContent(_PS_MODULE_DIR_.'views/templates/front/rich-snippets-default-bootstrap-header.tpl');	
		// $this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
		// FIN MODIFICADO POR MANU
    }



    /**
     * Compiles and outputs full page content
     * 
	 * FUNCIONA PARA PS 1.6 Y TEMA POR DEFECTO boot-strap.
	 * SE IGNORA SI FUCNIONA PARA VERSIONES POSTERIORES.
	 * EN TODO CASO SE PUEDE AÑADIR A POSTERIORES VERSIONES LO INDICADO COMO
	 * "AÑADIDO POR MANU" O "MODIFICADO POR MANU"
	 *
     * @return bool
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        Tools::safePostVars();

        // assign css_files and js_files at the very last time
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCss($this->css_files);
            }
            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE') && !$this->useMobileTheme()) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        $this->context->smarty->assign(array(
            'css_files'      => $this->css_files,
            'js_files'       => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files,
            'js_defer'       => (bool)Configuration::get('PS_JS_DEFER'),
            'errors'         => $this->errors,
            'display_header' => $this->display_header,
            'display_footer' => $this->display_footer,
        ));
		
		
        $layout = $this->getLayout();
        if ($layout) {
	
			/* modificado por manu */
			$layout = _PS_MODULE_DIR_.'fragmentosenriquecidosmanu/views/templates/front/layout.tpl';
			// die($layout);
			/* fin modificaciones de manu */
            if ($this->template) {
                $template = $this->context->smarty->fetch($this->template);
            } else {
                // For retrocompatibility with 1.4 controller

                ob_start();
                $this->displayContent();
                $template = ob_get_contents();
                ob_clean();
            }
            $this->context->smarty->assign('template', $template);
			$this->context->smarty->assign('module_folder', _PS_MODULE_DIR_);
            $this->smartyOutputContent($layout);
        } else {
            Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
            if ($this->display_header) {
				// MODIFICADO POR MANU
				$this->smartyOutputContent(_PS_MODULE_DIR_.'views/templates/front/rich-snippets-default-bootstrap-header.tpl');	
				// $this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
				// FIN MODIFICADO POR MANU
			}

            if ($this->template) {
                $this->smartyOutputContent($this->template);
            } else { // For retrocompatibility with 1.4 controller
                $this->displayContent();
            }

            if ($this->display_footer) {
                $this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
            }
        }

        return true;
    }
}
