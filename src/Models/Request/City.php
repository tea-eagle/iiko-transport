<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class City extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('cities');
        $request->setBody([
            'json' => [
                'organizationIds' => $this->app->organization->getOrganizationIds(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->cities) {
            throw new UnsetParamException("Request Error - Unset Cities");
        }

        return $result;
    }

    public function list()
    {
        $result = $this->result();

        $allCities = [];
        foreach ($result->cities as $key => $cities) {
            foreach ($cities->items as $key => $city) {
                if ($city->isDeleted === true) {
                    continue;
                }

                $newCity = new \stdClass();
                $newCity->organizationId = $cities->organizationId;
                $newCity->id = $city->id;
                $newCity->name = $city->name;
                $newCity->externalRevision = $city->externalRevision;
                $newCity->classifierId = $city->classifierId;

                $allCities[] = $newCity;
            }
        }

        return $allCities;
    }
}