<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Street extends Model
{
    public function list(
        $availableOrganizations = null,
        $availableCities = null,
        $cached = true
    )
    {
        $allCities = [];

        $cities = $this->app->city->list();

        foreach ($cities as $key => $city) {

            if (!is_null($availableOrganizations) && is_array($availableOrganizations) && !in_array($city->organizationId, $availableOrganizations)) {
                continue;
            }

            if (!is_null($availableCities) && is_array($availableCities) && !in_array($city->id, $availableCities)) {
                continue;
            }

            $request = new Request($this->app);
            $request->setUrl('streets/by_city');
            $request->setBody([
                'json' => [
                    'organizationId' => $city->organizationId,
                    'cityId' => $city->id,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->app->token->update(),
                ],
            ]);
            $result = $request->post();

            if (!property_exists($result, 'streets')) {
                throw new UnsetParamException("Request Error - Unset Streets");
            }

            foreach ($result->streets as $key => $street) {
                if ($street->isDeleted === true) {
                    continue;
                }

                $newStreet = new \stdClass();
                $newStreet->organizationId = $city->organizationId;
                $newStreet->cityId = $city->id;
                $newStreet->id = $street->id;
                $newStreet->name = $street->name;
                $newStreet->externalRevision = $street->externalRevision;
                $newStreet->classifierId = $street->classifierId;

                $allCities[] = $newStreet;
            }
        }

        return $allCities;
    }
}