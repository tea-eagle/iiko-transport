<?php

namespace TeaEagle\IikoTransport;

use TeaEagle\IikoTransport\Interfaces\Cache as CacheInterface;
use TeaEagle\IikoTransport\Interfaces\Log as LogInterface;

class Base
{
	public $login;
	public $auth_token;
	public $current_organization;
	public $current_terminal;
	public $httpClient;
	public $entities = [];
	public $cache_attached = [];
	public $log_attached = [];

	public function __get($key) {
        $oldkey = $key;
        $key = ucfirst($key);
        if (array_key_exists($key, $this->entities)) {
            return $this->entities[$key];
        } else {
            $class = '\\TeaEagle\\IikoTransport\\Models\\Request\\'.$key;
            $rc = new \ReflectionClass($class);
            if (class_exists($class) && $rc->implementsInterface('\TeaEagle\IikoTransport\Interfaces\Model')) {
                $object = $this->entities[$key] = new $class;
                $object->setApp($this);
                return $object;
            } else {
                throw new \Exception('Свойство не найдено');
            }
        }
        if (property_exists($this, $oldkey)) {
            return null;
        }
    }
	
	public function cache_attach(CacheInterface $cache)
    {
        $this->cache_attached[] = $cache;
    }

    public function log_attach(LogInterface $log)
    {
        $this->log_attached[] = $log;
    }

    public function setOrganization($organization)
    {
        $this->current_organization = $organization;
    }

    public function setTerminal($terminal)
    {
        return $this->current_terminal = $terminal;
    }

    public function getHash($key)
    {
        return md5("{$this->login}:{$key}");
    }

    public function updateToken()
    {
        $this->auth_token = $this->token->update();

        return $this->auth_token;
    }
}