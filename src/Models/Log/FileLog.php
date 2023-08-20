<?php

namespace TeaEagle\IikoTransport\Models\Log;

use TeaEagle\IikoTransport\Exceptions\FilenameException;
use TeaEagle\IikoTransport\Interfaces\Log;

class FileLog implements Log
{
	private $logDir;

	public function __construct()
	{
		$this->logDir = $_SERVER['DOCUMENT_ROOT'] . '/logs/';
	}

	public function write($value, $params = null)
	{
		if (!isset($params['filename'])) {
			throw new FilenameException("Flename error");
		}
		$filename = preg_replace('/[^0-9A-z\-]/', '', $params['filename']) . '_' . gmdate('Y_m_d') . '.log';
		if (!$filename) {
			throw new FilenameException("Filename error");
		}
		file_put_contents($this->logDir . $filename, json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
	}
}