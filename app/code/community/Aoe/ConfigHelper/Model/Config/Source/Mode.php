<?php

class Aoe_ConfigHelper_Model_Config_Source_Mode
{
    public function toOptionArray()
    {
        /** @var Aoe_ConfigHelper_Helper_Data $helper */
        $helper = Mage::helper('Aoe_ConfigHelper');

        return array(
            array(
                'value' => $helper::MODE_DISABLED,
                'label' => $helper->__('Disabled'),
            ),
            array(
                'value' => $helper::MODE_WARNING,
                'label' => $helper->__('Warning'),
            ),
            array(
                'value' => $helper::MODE_READONLY,
                'label' => $helper->__('Read-Only'),
            ),
        );
    }

    public function toOptionHash()
    {
        /** @var Aoe_ConfigHelper_Helper_Data $helper */
        $helper = Mage::helper('Aoe_ConfigHelper');

        return array(
            $helper::MODE_DISABLED => $helper->__('Disabled'),
            $helper::MODE_WARNING  => $helper->__('Warning'),
            $helper::MODE_READONLY => $helper->__('Read-Only'),
        );
    }
}
