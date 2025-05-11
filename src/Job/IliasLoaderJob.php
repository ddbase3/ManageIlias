<?php

namespace ManageIlias\Job;

use Base3\Worker\Api\IJob;
use Base3\Configuration\Api\IConfiguration;
use Base3\Api\ICheck;

class IliasLoaderJob implements IJob, ICheck {

	private $nextRun = 6 * 3600;

	public function __construct(private IConfiguration $configuration) {}

	// Implementation of IBase

	public function getName(): string {
		return 'iliasloaderjob';
	}

	// Implementation of IJob

	public function isActive() {
		return true;
	}

	public function getPriority() {
		return 1;
	}

	public function go() {
		$dataDir = $this->getDataDir();
		if (!strlen($dataDir)) return 'data dir undefined';

		$nextRunFile = $this->getNextRunFile();
		$now = time();
		$nextRun = is_file($nextRunFile) ? intval(file_get_contents($nextRunFile)) : 0;
		if ($now < $nextRun) return 'skipped (next run: ' . date('c', $nextRun) . ')';
		file_put_contents($nextRunFile, strval($now + $this->nextRun));

		$result = $this->getIliases();
		return $result;
	}

	// Implementation of ICheck

	public function checkDependencies() {
		return array(
			'manageilias_dir_defined' => strlen($this->getDataDir()) ? 'Ok' : 'manageilias dir not defined',
			'manageilias_dir_writable' => is_writable($this->getDataDir()) ? 'Ok' : 'manageilias dir not writable'
		);
	}

	// Private methods

	private function getDataDir() {
		$directories = $this->configuration->get('directories');
		return isset($directories['data'])
			? $directories['data'] . DIRECTORY_SEPARATOR . 'manageilias' . DIRECTORY_SEPARATOR
			: '';
	}

	private function getNextRunFile(): string {
		return $this->getDataDir() . 'nextrun';
	}

private function getIliases(): string {
    $dataDir = $this->getDataDir();
    if (!strlen($dataDir)) return 'data dir undefined';

    $configFile = $dataDir . 'manageilias-config.ini';
    if (!is_file($configFile)) return 'config file missing';

    $configuration = parse_ini_file($configFile, true);
    $iliases = [];

    foreach ($configuration as $group => $entries) {
        if (!isset($entries['url'])) continue;
        $urls = is_array($entries['url']) ? $entries['url'] : [$entries['url']];
        foreach ($urls as $url) {

            $info = [ 'group' => $group, 'url' => $url ];

            // $endpoint = $url . 'base3.de?name=base3iliascheck&out=json';
            $endpoint = $url . 'base3.php?name=base3iliasendpoint&out=json';
echo $endpoint."\n";
            $content = $this->fetchUrl($endpoint);

            if ($content === false) {
                $info['error'] = 'Failed to fetch data from ' . $endpoint;
            } else {
                $data = json_decode($content, true);
                if ($data !== null) {
                    $info = array_merge($info, $data);
                } else {
                    $info['error'] = 'Invalid JSON response from ' . $endpoint;
                    $info['response'] = substr($content, 0, 200);
                }
            }

            $iliases[] = $info;
        }
    }

    file_put_contents($dataDir . 'iliases.json', json_encode($iliases, JSON_PRETTY_PRINT));
    return 'done';
}

private function fetchUrl(string $url): string|false {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Connection: close'
        ],
        CURLOPT_COOKIE => '', // explizit KEINE Cookies senden
        CURLOPT_HEADER => true, // HTTP-Header mitliefern
        // CURLOPT_VERBOSE => true,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return "cURL failed: $error";
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
        return "HTTP error: $httpCode";
    }

    return $response;
}

}
