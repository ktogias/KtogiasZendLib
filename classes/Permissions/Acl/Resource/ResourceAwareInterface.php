<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Permissions\Acl\Resource;

/**
 *
 * @author ktogias
 */
interface ResourceAwareInterface {
    /**
     * @return \KtogiasZendLib\Permissions\Acl\Resource\ResourceInterface
     */
    public function getResource();
}
