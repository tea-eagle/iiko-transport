<?php

namespace TeaEagle\IikoTransport\Models\Log;

use TeaEagle\IikoTransport\Exceptions\DatabaseException;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Interfaces\Log;

class DatabaseLog implements Log
{
	private $connect;

	public function __construct($host, $username, $password, $basename, $charset)
	{
		$this->connect = new \Mysqli($host, $username, $password, $basename);
		if (!$this->connect || $this->connect->connect_error) {
			$msg = "Database connect error";
			if (isset($this->connect->connect_error)) {
				$msg .= ' ' . $this->connect->connect_error;
			}
			throw new DatabaseException($msg);
		}
		$this->connect->set_charset($charset);
	}

	public function write($value, $params = null)
	{
		if (!isset($params['url'])) {
			throw new UnsetParamException("Unset url");
		}
		if (!isset($params['result'])) {
			throw new UnsetParamException("Unset result");
		}
		$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
		$stmt = $this->connect->prepare("
			INSERT INTO `iiko_history` (`date`, `url`, `data`, `result`) VALUES (?, ?, ?, ?)
		");
		$value = serialize(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		$params['result'] = serialize($params['result']);
        $stmt->bind_param('ssss', ...[
            $dateTime->format('Y-m-d H:i:s'),
            $params['url'],
            $value,
            $params['result'],
        ]);
        $stmt->execute();
        $stmt->close();
	}
}