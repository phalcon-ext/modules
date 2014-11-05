<?php
/**
 * Manager.php
 * ----------------------------------------------
 *
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2014, CKGroup.ru
 *
 * ----------------------------------------------
 * All Rights Reserved.
 * ----------------------------------------------
 */
namespace Phalcon\Ext\Modules\Api;

use Phalcon\Ext\Modules\ModuleInterface;
use Phalcon\DI\ServiceInterface;

/**
 * Class ApiManager
 * @package Phalcon\Ext\Module
 */
class Manager implements ManagerInterface
{
    /**
     * @var ModuleInterface
     */
    protected $_module;

    /**
     * @var ServiceInterface[]
     */
    protected $_services;

    /**
     * @var array
     */
    protected $_sharedInstances;

    /**
     * @var bool
     */
    protected $_readOnly = false;

    /**
     * @param ModuleInterface $module
     */
    public function __construct(ModuleInterface $module)
    {
        $this->_module = $module;
    }

    /**
     * @return ModuleInterface
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Registers a service in the service container
     *
     * @param string $name
     * @param mixed $definition
     * @param boolean $shared
     *
     * @return \Phalcon\DI\ServiceInterface
     */
    public function set($name, $definition, $shared = null)
    {
        if ($this->_readOnly) {
            $this->throwReadOnly();
        }

        /** @var $widget \Phalcon\DI\Service */
        $widget = $this->getModule()->getDI()->get('\Phalcon\DI\Service', [$name, $definition, $shared]);
        $this->_services[$name] = $widget;

        return $this;
    }

    /**
     * Removes a service from the service container
     *
     * @param string $name
     */
    public function remove($name)
    {
        if ($this->_readOnly) {
            $this->throwReadOnly();
        }

        unset($this->_services[$name]);
    }

    /**
     * Resolves the service based on its configuration
     *
     * @param string $name
     * @param array $parameters
     *
     * @return object
     */
    public function get($name, $parameters = null)
    {
        if ($this->has($name)) {
            return $this->getService($name)
                ->resolve($parameters, $this->getModule()->getDI());
        } else {
            $this->throwNotFound($name);
            return false;
        }
    }

    /**
     * Resolves a shared service based on their configuration
     *
     * @param string $name
     * @param array $parameters
     *
     * @return object
     */
    public function getShared($name, $parameters = null)
    {
        if (!isset($this->_sharedInstances[$name])) {
            $this->_sharedInstances[$name] = $this->get($name, $parameters);
        }

        return $this->_sharedInstances[$name];
    }

    /**
     * Check whether the DI contains a service by a name
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->_services[$name]);
    }

    /**
     * set state readOnly API container
     */
    public function setReadOnly()
    {
        $this->_readOnly = true;
    }

    /**
     * @param string $name
     * @return ServiceInterface
     */
    protected function getService($name)
    {
        return $this->_services[$name];
    }

    /**
     * @throws \RuntimeException
     */
    protected function throwReadOnly()
    {
        throw new \RuntimeException('Api service readOnly');
    }

    /**
     * @param string $name
     * @throws \RuntimeException
     */
    protected function throwNotFound($name)
    {
        throw new \RuntimeException('Service "' . $name . '" wasn\'t found in the container');
    }
}