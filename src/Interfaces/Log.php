<?php

namespace TeaEagle\IikoTransport\Interfaces;

interface Log
{
	public function write($value, $params = null);
}