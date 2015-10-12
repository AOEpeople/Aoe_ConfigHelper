<?php

class Aoe_ConfigHelper_Block_Adminhtml_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{
    /**
     * Init fieldset fields
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param Varien_Simplexml_Element          $group
     * @param Varien_Simplexml_Element          $section
     * @param string                            $fieldPrefix
     * @param string                            $labelPrefix
     *
     * @return $this
     */
    public function initFields($fieldset, $group, $section, $fieldPrefix = '', $labelPrefix = '')
    {
        parent::initFields($fieldset, $group, $section, $fieldPrefix, $labelPrefix);

        /** @var Aoe_ConfigHelper_Helper_Data $helper */
        $helper = Mage::helper('Aoe_ConfigHelper');

        // Return early if restrictions are disabled
        if ($helper->getConfigPathRestrictionMode() === '') {
            return $this;
        }

        foreach ($group->fields as $fields) {
            foreach ((array)$fields as $field) {
                // Generate the field element ID
                $id = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $field->getName();

                // Grab the field off the fieldset
                $fieldElement = $fieldset->getElements()->searchById($id);
                if (!$fieldElement) {
                    continue;
                }

                // Generate the field config path
                $path = (string)$field->config_path ?: ($section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $field->getName());

                // Look up the mode for a config path
                $mode = $helper->getConfigPathMode($path, $this->getWebsiteCode(), $this->getStoreCode());

                if ($mode === $helper::MODE_READONLY) {
                    $tooltip = htmlspecialchars($helper->__('This value is managed externally. Changes are not permitted.'));
                    $scopeLabel = $helper->__('[READ-ONLY / EXTERNAL]');
                    $scopeLabel = "<span style=\"color: red;\" title=\"${tooltip}\">${scopeLabel}</span>";
                    $fieldElement->setScopeLabel($fieldElement->getScopeLabel() . '&nbsp;' . $scopeLabel);
                    $fieldElement->setDisabled(true);
                    $fieldElement->setCanUseWebsiteValue(false);
                    $fieldElement->setCanUseDefaultValue(false);
                } elseif ($mode === $helper::MODE_WARNING) {
                    $tooltip = htmlspecialchars($helper->__('This value is managed externally. Any manual changes are subject to being reverted.'));
                    $scopeLabel = $helper->__('[WARNING / EXTERNAL]');
                    $scopeLabel = "<span style=\"color: red;\" title=\"${tooltip}\">${scopeLabel}</span>";
                    $fieldElement->setScopeLabel($fieldElement->getScopeLabel() . '&nbsp;' . $scopeLabel);
                }
            }
        }

        return $this;
    }
}
