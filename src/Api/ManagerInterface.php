<?php
/**
 * ManagerInterface.php
 * ----------------------------------------------
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

/**
 * Interface ApiManagerInterface
 * @package Phalcon\Ext\Modules\Api
 */
interface ManagerInterface
{
    /**
     * @param ModuleInterface $module
     */
    public function __construct(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * Registers a service in the service container
     *
     * @param string $name
     * @param mixed $definition
     * @param boolean $shared
     *
     * @return \Phalcon\DI\ServiceInterface
     */
    public function set($name, $definition, $shared = null);

    /**
     * Removes a service from the service container
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Resolves the service based on its configuration
     *
     * @param string $name
     * @param array $parameters
     *
     * @return object
     */
    public function get($name, $parameters = null);

    /**
     * Resolves a shared service based on their configuration
     *
     * @param string $name
     * @param array $parameters
     *
     * @return object
     */
    public function getShared($name, $parameters = null);

    /**
     * Check whether the DI contains a service by a name
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name);

    /**
     * set state readOnly API container
     */
    public function setReadOnly();
}
