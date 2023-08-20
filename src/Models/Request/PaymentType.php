<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class PaymentType extends Model
{
    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('payment_types');
        $request->setBody([
            'json' => [
                'organizationIds' => $this->app->organization->getOrganizationIds(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->paymentTypes) {
            throw new UnsetParamException("Request Error - Unset Payment Types");
        }

        return $result;
    }

    public function list()
    {
        $result = $this->result();

        $paymentTypes = [];
        foreach ($result->paymentTypes as $key => $paymentType) {
            if ($paymentType->isDeleted === true) {
                continue;
            }
            $paymentTypes[] = $paymentType;
        }

        return $paymentTypes;
    }
}