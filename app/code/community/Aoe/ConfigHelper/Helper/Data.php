<?php

class Aoe_ConfigHelper_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_HINTS_ACTIVE = 'dev/aoe_confighelper/hinting_active';
    const XML_PATH_RESTRICTION_MODE = 'dev/aoe_confighelper/restriction_mode';
    const XML_PATH_RESTRICTED_PATHS = 'global/aoe_confighelper/restricted_paths';
    const XML_PATH_RESTRICTED_PATHS_ADD = 'dev/aoe_confighelper/restricted_paths_add';
    const XML_PATH_RESTRICTED_PATHS_REMOVE = 'dev/aoe_confighelper/restricted_paths_remove';

    const MODE_WARNING = 'warning';
    const MODE_READONLY = 'readonly';

    protected $restrictedPaths;

    public function getConfigPathHintsActive()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_HINTS_ACTIVE, Mage_Core_Model_Store::ADMIN_CODE);
    }

    public function getConfigPathRestrictionMode()
    {
        $mode = Mage::getStoreConfig(self::XML_PATH_RESTRICTION_MODE, Mage_Core_Model_Store::ADMIN_CODE);

        return (($mode === self::MODE_WARNING || $mode === self::MODE_READONLY) ? $mode : '');
    }

    public function getConfigPathMode($path, $website, $store)
    {
        $restrictedPaths = $this->getRestrictedConfigPaths();
        if (empty($restrictedPaths)) {
            return '';
        }

        if (!in_array(trim($path, ' /'), $restrictedPaths)) {
            return '';
        }

        return $this->getConfigPathRestrictionMode();
    }

    protected function getRestrictedConfigPaths()
    {
        if (!is_array($this->restrictedPaths)) {
            $restrictedPaths = array();

            $restrictedPathsNode = Mage::app()->getConfig()->getNode(self::XML_PATH_RESTRICTED_PATHS);
            if ($restrictedPathsNode instanceof Mage_Core_Model_Config_Element) {
                $paths = $restrictedPathsNode->asArray();
                if (is_array($paths)) {
                    $paths = $this->flattenPathArray($paths);
                    $restrictedPaths = array_keys($paths);
                }
            }

            $addPaths = $this->parsePaths(Mage::getStoreConfig(self::XML_PATH_RESTRICTED_PATHS_ADD, Mage_Core_Model_Store::ADMIN_CODE));
            $restrictedPaths = array_unique(array_merge($restrictedPaths, $addPaths));

            $removePaths = $this->parsePaths(Mage::getStoreConfig(self::XML_PATH_RESTRICTED_PATHS_REMOVE, Mage_Core_Model_Store::ADMIN_CODE));
            $restrictedPaths = array_filter(array_diff($restrictedPaths, $removePaths));

            $this->restrictedPaths = $restrictedPaths;
        }

        return $this->restrictedPaths;
    }

    /**
     * @param array  $source
     * @param string $pathPrefix
     *
     * @return array
     *
     * @throws Exception
     */
    protected function flattenPathArray(array $source, $pathPrefix = '')
    {
        $result = array();

        foreach ($source as $key => $value) {
            $path = $pathPrefix . $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenPathArray($value, $path . '/'));
            } elseif (is_scalar($value)) {
                $result[$path] = $value;
            } else {
                throw new Exception('Cannot flatten non-scalar/non-array values');
            }
        }

        return $result;
    }

    /**
     * @param string $pathString
     *
     * @return string[]
     */
    protected function parsePaths($pathString)
    {
        $pathString = trim($pathString);
        $pathString = str_replace(array("\n", "\r", "\t"), ',', $pathString);

        $paths = explode(',', $pathString);
        $paths = array_map(
            function ($value) {
                return trim($value, ' /');
            },
            $paths
        );

        return array_filter($paths);
    }
}
