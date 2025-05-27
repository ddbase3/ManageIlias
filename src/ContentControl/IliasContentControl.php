<?php declare(strict_types=1);

namespace ManageIlias\ContentControl;

use Base3Manager\ContentControl\AbstractContentControl;

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

