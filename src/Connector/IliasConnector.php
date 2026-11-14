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

namespace Base3\ManageIlias\Connector;

use Base3\CoreFoundation\Api\IOutput;
use Base3\CoreFoundation\Api\IRequest;
use Base3\CoreFoundation\Configuration\Api\IConfiguration;
use Base3\CoreFoundation\Accesscontrol\Api\IAccesscontrol;

class IliasConnector implements IOutput {

    private $defaultPageSize = 10;

    public function __construct(
        private IAccesscontrol $accesscontrol,
        private IConfiguration $configuration,
        private IRequest $request
    ) {}

    // Implementation of IBase

    public static function getName(): string {
        return "iliasconnector";
    }

    // Implementation of IOutput

    public function getOutput(string $out = 'html', bool $final = false): string {
        if ($out !== "json") return null;
        if (!$this->accesscontrol->getUserId()) return null;

        $directories = $this->configuration->get('directories');
        $datadir = $directories['data'];

        $file = $datadir . DIRECTORY_SEPARATOR . 'manageilias' . DIRECTORY_SEPARATOR . 'iliases.json';
        if (!file_exists($file)) {
            return json_encode(['error' => true, 'message' => 'File not found']);
        }

        $json = file_get_contents($file);
        $iliases = json_decode($json, true);
        if (!is_array($iliass)) {
            return json_encode(['error' => true, 'message' => 'Invalid JSON format']);
        }

        // Boolesche Werte in Strings umwandeln
        array_walk_recursive($iliases, function (&$value, $key) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
        });

        // Details
        // TODO maybe transform values

        // Sortierung
        $sort = $this->request->get('sort', 'url');
        $direction = strtolower($this->request->get('direction', 'asc'));
        usort($iliases, function ($a, $b) use ($sort, $direction) {
            $aVal = strtolower($a[$sort] ?? '');
            $bVal = strtolower($b[$sort] ?? '');
            return ($direction === 'desc' ? -1 : 1) * strcmp($aVal, $bVal);
        });

        // Filter
        $filters = $this->request->get('filter', []);
        $iliases = array_filter($iliases, function ($ilias) use ($filters) {
            foreach ($filters as $key => $val) {
                if (!isset($ilias[$key])) return false;
                if (stripos($ilias[$key], $val) === false) return false;
            }
            return true;
        });

        // Paging
        $total = count($iliases);
        $pageSize = $this->defaultPageSize;
        $totalPages = ceil($total / $pageSize);
        $page = min(max(1, intval($this->request->get('page', 1))), $totalPages);
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($iliases, $offset, $pageSize);

        return json_encode([
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages,
            'data' => $pagedData
        ]);
    }

    public function getHelp(): string {
        return "Liefert eine Liste gecrawlter ILIAS-Installationen als JSON (aus iliases.json). Optional: ?sort=url&direction=asc&page=1&filter[key]=value";
    }
}
