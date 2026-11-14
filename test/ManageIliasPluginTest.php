<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of ManageIlias for BASE3 Framework.
 *
 * ManageIlias provides ILIAS-related management controls.
 * It integrates ILIAS operations within Base3Manager.
 * Host-specific operations retain their existing owners.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/

namespace Base3\ManageIlias\Test;

use PHPUnit\Framework\TestCase;
use Base3\ManageIlias\ManageIliasPlugin;
use Base3\CoreFoundation\Api\IContainer;

class ManageIliasPluginTest extends TestCase
{
    public function testInitSetsServicesInContainer()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new ManageIliasPlugin($containerMock);
        $plugin->init();

        $this->assertTrue(true);
    }

    public function testCheckDependencies()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new ManageIliasPlugin($containerMock);
        $dependencies = $plugin->checkDependencies();

        $this->assertTrue(true);
    }
}

