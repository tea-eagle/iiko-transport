<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Organization extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('organizations');
        $request->setBody([
            'json' => [
                'returnAdditionalInfo' => false,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->organizations) {
            throw new UnsetParamException("Request Error - Unset Organizations");
        }

        return $result;
    }

	public function update()
	{
        if ($this->app->current_organization) {
            return $this->app->current_organization;
        }

        $result = $this->result();

		$organization = array_shift($result->organizations);
		if (!isset($organization->id)) {
			throw new UnsetParamException("Unset Organization ID");
		}

		return $organization->id;
	}

    public function list()
    {
        $result = $this->result();

        return $result->organizations;
    }

    public function getOrganizationIds()
    {
        $organizationsIds = [];

        $organizations = $this->list();

        foreach ($organizations as $key => $organization) {
            $organizationsIds[] = $organization->id;
        }

        return $organizationsIds;
    }
}