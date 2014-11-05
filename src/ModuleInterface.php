<?php
/**
 * ModuleInterface.php
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

use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Interface ModuleInterface
 * @package Phalcon\Ext\Modules
 */
interface ModuleInterface extends ModuleDefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * Registers an autoloader related to the module
     */
    public function registerAutoloaders();

    /**
     * Registers services related to the module
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function registerServices($dependencyInjector);

    /**
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function init($dependencyInjector);
} 