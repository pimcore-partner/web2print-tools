<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Web2PrintToolsBundle\FavoriteOutputDefinition;

/**
 * @method self|null load()
 * @method int getTotalCount()
 */
class Listing extends \Pimcore\Model\Listing\AbstractListing
{
    /**
     * @var array
     */
    public $outputDefinitions;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isValidOrderKey($key): bool
    {
        if ($key == 'id' || $key == 'o_classId' || $key == 'description') {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getOutputDefinitions()
    {
        if (empty($this->outputDefinitions)) {
            $this->load();
        }

        return $this->outputDefinitions;
    }

    /**
     * @param array $outputDefinitions
     *
     * @return void
     */
    public function setOutputDefinitions($outputDefinitions)
    {
        $this->outputDefinitions = $outputDefinitions;
    }
}
