<?php

namespace TeaEagle\IikoTransport;

use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Interfaces\Log as LogInterface;

final class Log
{
	private $log;

	private function __construct(LogInterface $log) {
		$this->log = $log;
	}

	public function write($type, $value, $params)
	{
		return $this->log->write($value, $params);
	}
}