<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Token extends Model
{
	public function update()
	{
		// Ищу актуальный токен в кеше
		// $cache = $app->cache_attached[0];
		// if ($cache) {
		// 	$result = $cache->get($this->app->getHash('token'));
		// 	$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
		// 	if ($result && isset($result->token, $result->time) && $result->time > $dateTime->getTimestamp()) {
		// 		return $result->token;
		// 	}
		// }

        $request = new Request($this->app);
        $request->setUrl('access_token');
        $request->setBody([
            'json' => [
                'apiLogin' => $this->app->login,
            ],
        ]);
        $result = $request->post();

		if (!$result->token) {
			throw new UnsetParamException("Request Error - Unset Token");
		}

		// Сохраняю токен в кеш на 50 минут
		// if ($cache) {
		// 	$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
		// 	$dateTime->add(new \DateInterval('PT50M'));

		// 	$obj = new \stdClass();
		// 	$obj->token = $result->token;
		// 	$obj->time = $dateTime->getTimestamp();

		// 	$cache->set($this->app->getHash('token'), $obj);
		// }

		return $result->token;
	}
}