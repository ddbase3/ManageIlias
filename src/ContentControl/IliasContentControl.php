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


namespace Base3\ManageIlias\ContentControl;

use Base3\Base3Manager\ContentControl\AbstractContentControl;

class IliasContentControl extends AbstractContentControl {

        // Implementation of IBase

        public static function getName(): string {
                return "iliascontentcontrol";
        }

	// Implementation of AbstractContentControl

        protected function getPath(): string {
                return DIR_PLUGIN . 'ManageIlias';
        }

        protected function getTemplate(): string {
                return 'ContentControl/IliasContentControl.php';
        }

}

