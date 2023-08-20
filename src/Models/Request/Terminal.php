<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class Terminal extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('terminal_groups');
        $request->setBody([
            'json' => [
                'organizationIds' => $this->app->organization->getOrganizationIds(),
                'includeDisabled' => false,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->terminalGroups) {
            throw new UnsetParamException("Request Error - Unset Terminals");
        }

        return $result;
    }

    public function list($params = null)
    {
        if ($this->app->current_terminal) {
            return $this->app->current_terminal;
        }

        $result = $this->result();

        $terminals = [];
        foreach ($result->terminalGroups as $key => $terminalGroup) {
            $terminals = array_merge($terminals, $terminalGroup->items);
        }

        return $terminals;
    }
}