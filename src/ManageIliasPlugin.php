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


namespace Base3\ManageIlias;

use Base3\CoreFoundation\Api\IContainer;
use Base3\Base3Manager\Plugin\AbstractPlugin;

class ManageIliasPlugin extends AbstractPlugin {

	// Implementation of IPlugin

	public function init() {
		$this->container
			->set(self::getName(), $this, IContainer::SHARED);
	}

	// Implementation of ICheck

	public function checkDependencies(): array {
		return array(
			"Check" => "Ok"
		);
	}
}
