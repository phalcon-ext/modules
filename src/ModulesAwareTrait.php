<?php
/**
 * ModulesAwareTrait.php
 * ----------------------------------------------
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2014, CKGroup.ru
 *
 * ----------------------------------------------
 * All Rights Reserved. 
 * ----------------------------------------------
 */
namespace Phalcon\Ext\Modules;

/**
 * Class ModuleManagerAwareTrait
 * @package Demedis\Core\Components
 */
trait ModulesAwareTrait
{
    /**
     * @var Manager
     */
    private $_modulesManager;

    /**
     * @return Manager
     */
    public function getModulesManager()
    {
        if ($this->_modulesManager) {
            return $this->_modulesManager;
        } else {
            return $this->getDI()->get('modules');
        }
    }

    /**
     * @param Manager $modulesManager
     */
    public function setModulesManager(Manager $modulesManager)
    {
       $this->_modulesManager = $modulesManager;
    }

    /**
     * @param $name
     *
     * @return null|ModuleInterface
     */
    public function getModule($name)
    {
        return $this->getModulesManager()->get($name);
    }
} 