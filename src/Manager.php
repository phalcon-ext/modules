<?php
/**
 * Manager.php
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

use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Mvc\User\Component;
use Phalcon\Ext\Modules\Api\Manager as ApiManagerInterface;

/**
 * Class ModulesManager
 * @package Phalcon\Ext\Modules
 */
class Manager extends Component
{
    /**
     * @var array
     */
    protected $_modules = [];

    /**
     * @var ModuleInterface[]
     */
    protected $_modulesLoaded = [];

    /**
     * @param array $modules
     */
    public function __construct($modules = null)
    {
        if ($modules != null) {
            $this->setModules($modules);
        }
    }

    /**
     * Gets a specific module by name
     *
     * @param $name
     *
     * @return ModuleInterface|null
     */
    public function get($name)
    {
        return $this->loadModule($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_modules[$name]) || isset($this->_modulesLoaded[$name]);
    }

    /**
     * Load the provided modules.
     *
     * @return $this
     */
    public function loadModules()
    {
        foreach (array_keys($this->getModules()) as $module) {
            $this->loadModule($module);
        }

        return $this;
    }

    /**
     * Set an array or Traversable of module names that this module manager should load.
     *
     * @param array $modules
     * @param bool $merge
     *
     * @return $this
     */
    public function setModules(Array $modules, $merge = false)
    {
        if ($merge) {
            $this->_modules = array_merge($this->_modules, $modules);
        } else {
            $this->_modules = $modules;
        }

        return $this;
    }

    /**
     * Get the array of module names that this manager should load
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * Get an array of the loaded modules.
     *
     * @return array
     */
    public function getModulesLoaded()
    {
        return $this->_modulesLoaded;
    }

    /**
     * @return EventsManagerInterface
     */
    public function getEventsManager()
    {
        if (!$this->_eventsManager) {
            return $this->getDI()->get('eventsManager');
        } else {
            return $this->_eventsManager;
        }
    }

    /**
     * @param $name
     *
     * @return ModuleInterface|null
     */
    protected function loadModule($name)
    {
        if (isset($this->_modulesLoaded[$name])) {
            return $this->_modulesLoaded[$name];
        }

        $eventsManager = $this->getEventsManager();

        if (is_object($eventsManager)) {
            $resultEvents = $eventsManager->fire('modules:beforeLoadModule', $this, [$name]);
        } else {
            $resultEvents = true;
        }

        if ($resultEvents !== false) {

            $moduleConfig = $this->_modules[$name];

            // load required modules for current module
            if (isset($moduleConfig['require'])) {
                $this->loadRequireModules($name, $moduleConfig['require']);
            }

            $module = $this->createModule($name);
            $this->_modulesLoaded[$name] = $module;

            if (is_object($eventsManager)) {
                $eventsManager->fire('modules:afterLoadModule', $this, [$name, $module]);
            }

            return $module;
        }

        return null;
    }

    /**
     * @param $currentName
     * @param array $require
     */
    protected function loadRequireModules($currentName, Array $require)
    {
        foreach ($require as $requireName => $version) {

            if ($currentName == $requireName) {
                continue;
            }

            if (!isset($this->_modulesLoaded[$requireName]) && !isset($this->_modules[$requireName])) {

                $err = 'Module "%s" is not loaded because it requires module "%s" version "%s"';
                throw new \RuntimeException(sprintf($err, $currentName, $requireName, $version));
            }

            $module = $this->loadModule($requireName);

            if (!$this->versionCompare($module->getVersion(), $version)) {

                $err = 'Module "%s" requires module "%s" version "%s"';
                throw new \RuntimeException(sprintf($err, $currentName, $requireName, $version));
            }
        }
    }

    /**
     * @param $name
     *
     * @return ApiAwareInterface|ModuleInterface
     * @throws \Exception
     */
    protected function createModule($name)
    {
        $module = $this->_modules[$name];

        if (is_array($module)) {

            if (isset($module['path'])) {

                if (!class_exists($module['className'], false)) {

                    if (is_file($module['path'])) {
                        include_once $module['path'];
                    } else {
                        $err = sprintf("Module definition path %s doesn't exist", $module['path']);
                        throw new \RuntimeException($err);
                    }
                }
            }

            $moduleObject = $this->getDI()->get($module['className']);

        } else {

            /**
             * A module definition object, can be a Closure instance
             */
            if ($module instanceof \Closure) {
                $moduleObject = call_user_func_array($module, [$this->getDI()]);
            } else {
                throw new \Exception('Invalid module definition');
            }
        }

        if ($moduleObject instanceof ModuleInterface) {
            $moduleObject->registerAutoloaders();
            $moduleObject->registerServices($this->getDI());
        }

        if ($moduleObject instanceof ApiAwareInterface) {

            /** @var  $apiManager ApiManagerInterface */
            $apiManager = $this->getDI()->get('\Phalcon\Ext\Modules\Api\Manager', [$moduleObject]);
            $moduleObject->registerApiServices($apiManager);
        }

        if (method_exists($moduleObject, 'init')) {
            $moduleObject->init($this->getDI());
        }

        return $moduleObject;
    }

    /**
     * @param $version1
     * @param $version2
     *
     * @return bool
     */
    protected function versionCompare($version1, $version2)
    {
        return version_compare($version1, $version2, '>=') >= 0;
    }
}
