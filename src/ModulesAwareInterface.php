<?php
/**
 * ModulesAwareInterface.php
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
 * Interface ModulesAwareInterface
 * @package Phalcon\Ext\Modules
 */
interface ModulesAwareInterface
{
    /**
     * @return Manager
     */
    public function getModulesManager();

    /**
     * @param Manager $modulesManager
     */
    public function setModulesManager(Manager $modulesManager);

    /**
     * @param $name
     *
     * @return null|ModuleInterface
     */
    public function getModule($name);
} 