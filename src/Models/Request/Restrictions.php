<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Restrictions extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('delivery_restrictions');
        $request->setBody([
            'json' => [
                'organizationIds' => $this->app->organization->getOrganizationIds(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->deliveryRestrictions) {
            throw new UnsetParamException("Request Error - Delivery Restrictions");
        }

        return $result;
    }

    public function list($params = null)
    {
        $result = $this->result();

        $restrictions = [];
        $isset = [];
        foreach ($result->deliveryRestrictions as $deliveryRestriction) {
            foreach ($deliveryRestriction->restrictions as $restriction) {
                $hash = md5(json_encode($restriction));
                if (!isset($isset[$hash])) {
                    $isset[$hash] = true;
                } else {
                    continue;
                }
                $restrictions[] = $restriction;
            }
        }

        return $restrictions;
    }

    public function deliveryZones($params = null)
    {
        $result = $this->result();

        $deliveryZones = [];
        $isset = [];
        foreach ($result->deliveryRestrictions as $deliveryRestriction) {
            foreach ($deliveryRestriction->deliveryZones as $deliveryZone) {
                $hash = md5(json_encode($deliveryZone));
                if (!isset($isset[$hash])) {
                    $isset[$hash] = true;
                } else {
                    continue;
                }
                $deliveryZones[] = $deliveryZone;
            }
        }

        return $deliveryZones;
    }
}