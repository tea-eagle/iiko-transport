<?php

namespace TeaEagle\IikoTransport\Interfaces;

interface Cache
{
	public function get($key);
	public function set($key, $value);
	public function delete($key);
	public function flush();
}