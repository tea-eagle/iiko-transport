<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Exceptions\RequestException;
use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Customer extends Model
{
    /**
     * @param $search
     * @param $type phone or id
     * @return mixed
     * @throws UnsetParamException
     * @throws RequestException
     * @throws UnsetAttributeException
     */
    public function info($search)
    {
        if (!$search) {
            throw new UnsetAttributeException("Строка поиска пуста");
        }

        if (preg_match(APP::REG_GUID, $search)) {
            $type = 'id';
        } else {
            $type = 'phone';
        }

        $request = new Request($this->app);
        $request->setUrl('loyalty/iiko/customer/info');
        $request->setBody([
            'json' => [
                $type => $search,
                'type' => $type,
                'organizationId' => $this->app->organization->update(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->id) {
            throw new UnsetParamException("Клиент не найден");
        }

        return $result;
    }

    public function balance($id)
    {
        if (!$id) {
            throw new UnsetAttributeException("Id не указан");
        }

        $request = new Request($this->app);
        $request->setUrl('loyalty/iiko/customer/info');
        $request->setBody([
            'json' => [
                'id' => $id,
                'type' => 'id',
                'organizationId' => $this->app->organization->update(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->id) {
            throw new UnsetParamException("Клиент не найден");
        }

        if (empty($result->walletBalances)) {
            throw new UnsetParamException("Бонусная система не привязана");
        }

        return $result->walletBalances[0]->balance;
    }

    public function createOrUpdate($phone, $name = null)
    {
        if (!$phone) {
            throw new UnsetAttributeException("Не указан номер телефона");
        }

        $body = [
            'phone' => $phone,
            'organizationId' => $this->app->organization->update(),
        ];

        if(!is_null($name)) {
            $body['name'] = $name;
        }

        $request = new Request($this->app);
        $request->setUrl('loyalty/iiko/customer/create_or_update');
        $request->setBody([
            'json' => $body,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->id) {
            throw new UnsetParamException("Клиент не найден");
        }

        return $result->id;
    }

    public function update($id, $phone, $name)
    {
        if (!$id || !preg_match(App::REG_GUID, $id)) {
            throw new UnsetAttributeException("Id пустой или с ошибкой");
        }

        if (!$phone) {
            throw new UnsetAttributeException("Не указан номер телефона");
        }

        if (!$name) {
            throw new UnsetAttributeException("Не указано имя");
        }

        $request = new Request($this->app);
        $request->setUrl('loyalty/iiko/customer/create_or_update');
        $request->setBody([
            'json' => [
                'id' => $id,
                'phone' => $phone,
                'name' => $name,
                'organizationId' => $this->app->organization->update(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->id) {
            throw new UnsetParamException("Клиент не найден");
        }

        return $result->id;
    }
}