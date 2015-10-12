<?php

class Aoe_ConfigHelper_Block_Adminhtml_System_Config_Form_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::render($element);

        /** @var Aoe_ConfigHelper_Helper_Data $helper */
        $helper = Mage::helper('Aoe_ConfigHelper');

        if ($helper->getConfigPathHintsActive()) {
            $configCode = $this->getConfigCode($element);
            $id = str_replace('/', '_', $configCode);
            $prompt = 'onclick="var v=$(\'' . $id . '\').getValue(); window.prompt(\'Copy\', \'' . $configCode . ' = \' + v)"';
            $html = str_replace("<label for=", "<label $prompt for=", $html);
        }

        return $html;
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function getConfigCode(Varien_Data_Form_Element_Abstract $element)
    {
        if (isset($element->field_config->config_path)) {
            return (string)$element->field_config->config_path;
        }

        $configCode = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        $configCode = str_replace('[fields]', '', $configCode);
        $configCode = str_replace('groups[', '[', $configCode);
        $configCode = str_replace('][', '/', $configCode);
        $configCode = str_replace(']', '', $configCode);
        $configCode = str_replace('[', '', $configCode);
        $configCode = Mage::app()->getRequest()->getParam('section') . '/' . $configCode;

        return $configCode;
    }

}
