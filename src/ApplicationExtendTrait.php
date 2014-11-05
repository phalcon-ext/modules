<?php
/**
 * ApplicationExtendTrait.php
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

use Phalcon\DiInterface;
use Phalcon\Events\ManagerInterface;

/**
 * Class ApplicationExtendTrait
 *
 * @property array $_modules
 *
 * @method DiInterface getDI()
 * @method ManagerInterface getEventsManager()
 *
 * @package Phalcon\Ext\Modules
 */
trait ApplicationExtendTrait
{
    /**
     * @internal param $event
     * @internal param $source
     * @internal param $moduleName
     */
    public function beforeStartModule()
    {
        $moduleName = func_get_arg(2);

        /** @var $modulesManager \Phalcon\Ext\Modules\Manager*/
        $modulesManager = $this->getDI()->get('modules');

        if ($modulesManager->has($moduleName)) {

            $this->_modules[$moduleName] = function ($di) use ($modulesManager, $moduleName) {

                $modulesManager->setDI($di);
                $module = $modulesManager->get($moduleName);

                if (method_exists($module, 'beforeStartModule')) {
                    call_user_func_array([$module, 'beforeStartModule'], func_get_args());
                }

                /** @var $eventsManager \Phalcon\Events\ManagerInterface */
                if ($eventsManager = $this->getEventsManager()) {
                    $eventsManager->attach('application:afterStartModule', $module);
                }

                return $module;
            };
        }
    }

    /**
     * @param array $modules
     * @param null $merge
     */
    public function registerModules($modules, $merge = null)
    {
        /** @var $modulesManager \Phalcon\Ext\Modules\Manager*/
        $modulesManager = $this->getDI()->get('modules');
        $modulesManager->setModules($modules, $merge);
    }

    /**
     * @param \Phalcon\Events\ManagerInterface $eventsManager
     */
    public function setEventsManager($eventsManager)
    {
        parent::setEventsManager($eventsManager);

        if (is_object($eventsManager = $this->getEventsManager())) {
            $eventsManager->attach('application:beforeStartModule', $this);
        }
    }

    /**
     * Return the modules registered in the application
     *
     * @return array
     */
    public function getModules()
    {
        /** @var $modulesManager \Phalcon\Ext\Modules\Manager*/
        $modulesManager = $this->getDI()->get('modules');
        $modulesManager->getModules();
    }
} 