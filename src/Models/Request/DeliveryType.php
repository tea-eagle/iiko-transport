<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class DeliveryType extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('deliveries/order_types');
        $request->setBody([
            'json' => [
                'organizationIds' => $this->app->organization->getOrganizationIds(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!property_exists($result, 'orderTypes')) {
            throw new UnsetParamException("Request Error - Delivery Types");
        }

        return $result;
    }

    public function list($params = null)
    {
        $result = $this->result();

        $deliveryTypes = [];
        $isset = [];
        foreach ($result->orderTypes as $orderTypes) {
            foreach ($orderTypes->items as $orderType) {
                $hash = md5(json_encode($orderType));
                if (!isset($isset[$hash])) {
                    $isset[$hash] = true;
                } else {
                    continue;
                }
                if ($orderType->isDeleted) {
                    continue;
                }
                $deliveryTypes[] = $orderType;
            }
        }

        return $deliveryTypes;
    }
}