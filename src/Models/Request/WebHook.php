<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;

class WebHook extends Model
{
    public function set()
    {
        $domain = $_SERVER['SERVER_NAME'];

        $result = [];

        $organizations = $this->app->organization->getOrganizationIds();

        if ($organizations) {
            foreach ($organizations as $key => $organization) {
                $request = new Request($this->app);
                $request->setUrl('webhooks/update_settings');
                $request->setBody([
                    'json' => [
                        "organizationId" => $organization,
                        "webHooksUri" => "https://{$domain}/callback/v1/iiko",
                        "authToken" => "",
                        "webHooksFilter" => [
                            "deliveryOrderFilter" => [
                                "orderStatuses" => [
                                    "Unconfirmed",
                                    "WaitCooking",
                                    "ReadyForCooking",
                                    "CookingStarted",
                                    "CookingCompleted",
                                    "Waiting",
                                    "OnWay",
                                    "Delivered",
                                    "Closed",
                                    "Cancelled",
                                ],
                                "itemStatuses" => [
                                    "Added",
                                    "PrintedNotCooking",
                                    "CookingStarted",
                                    "CookingCompleted",
                                    "Served",
                                ],
                                "errors" => true,
                            ],
                            "tableOrderFilter" => [
                                "orderStatuses" => [
                                    "New",
                                    "Bill",
                                    "Closed",
                                    "Deleted",
                                ],
                                "itemStatuses" => [
                                    "Added",
                                    "PrintedNotCooking",
                                    "CookingStarted",
                                    "CookingCompleted",
                                    "Served",
                                ],
                                "errors" => true,
                            ],
                            "reserveFilter" => [
                                "updates" => true,
                                "errors" => true,
                            ],
                            "stopListUpdateFilter" => [
                                "updates" => true,
                            ],
                            "personalShiftFilter" => [
                                "updates" => true,
                            ]
                        ]
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->app->token->update(),
                    ],
                ]);
                $result[] = $request->post();
            }
        }

        return $result;
    }
}