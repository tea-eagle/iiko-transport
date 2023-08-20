<?php

namespace TeaEagle\IikoTransport;

use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Interfaces\Cache as CacheInterface;

final class Cache
{
	private $cache;

	private function __construct(CacheInterface $cache) {
		$this->cache = $cache;
	}

	public function get($key)
	{
		return $this->cache->get($key);
	}

	public function set($key, $value)
	{
		$this->cache->set($key, $value);
	}

	public function delete($key)
	{
		$this->cache->delete($key);
	}

	public function flush()
	{
		$this->cache->flush();
	}
}