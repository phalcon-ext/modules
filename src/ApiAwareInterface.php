<?php
/**
 * ApiManagerInterface.php
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

use Phalcon\Ext\Modules\Api\Manager as ApiManager;

/**
 * Interface ApiAwareInterface
 * @package Phalcon\Ext\Modules
 */
interface ApiAwareInterface
{
    /**
     * @return ApiManager
     */
    public function api();

    /**
     * @param ApiManager $apiManager
     */
    public function registerApiServices(ApiManager $apiManager = null);
} 