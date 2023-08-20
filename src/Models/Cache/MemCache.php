<?php

namespace TeaEagle\IikoTransport\Models\Cache;

use TeaEagle\IikoTransport\Interfaces\Cache;

class MemCache implements Cache
{
	public $cache;

	public function __construct()
	{
		$this->cache = new \Memcache();
		$this->cache->connect('localhost', 11211);
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