<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\Interfaces\Model as ModelInterface;
use TeaEagle\IikoTransport\App;

class Model implements ModelInterface
{
	public $app;
	
	public function setApp(App $app)
	{
		$this->app = $app;
	}
}