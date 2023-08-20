<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Request;
use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Exceptions\DatetimeFormatException;

class CheckDelivery extends Model
{
    private $city;
    private $street;
    private $house;
    private $latitude;
    private $longitude;
    private $products = [];
    private $sum;
    private $deliveryDate;

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @param mixed $house
     */
    public function setHouse($house): void
    {
        $this->house = $house;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    /**
     * @param mixed $longitude
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }

    /**
     * @param mixed $deliveryDate
     */
    public function setDeliveryDate($deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function toArray() {
        $array = [];
        $array['organizationIds'] = $this->app->organization->getOrganizationIds();

        if ($this->latitude && $this->longitude) {
            $array['orderLocation']['latitude'] = $this->latitude;
            $array['orderLocation']['longitude'] = $this->longitude;
        } else if ($this->city && $this->street && $this->house) {
            $array['deliveryAddress'] = [];
            $array['deliveryAddress']['city'] = $this->city;
            $array['deliveryAddress']['streetName'] = $this->street;
            $array['deliveryAddress']['house'] = $this->house;
        } else {
            throw new UnsetAttributeException('Не переданы данные для определения терминала доставки');
        }

        if (!is_array($this->products) || empty($this->products)) {
            throw new UnsetAttributeException('Не переданы товары');
        }

        $array['orderItems'] = $this->products;

        if (empty($this->sum)) {
            throw new UnsetAttributeException('Не передана сумма');
        }

        $array['deliverySum'] = $this->sum;

        if ($this->deliveryDate) {
            if (!preg_match('/^\d{4}\-\d{2}\-\d{2}\s\d{2}\:\d{2}\:\d{2}\.\d{3}$/', $this->deliveryDate)) {
                throw new DatetimeFormatException('Формат даты не соответствует: <yyyy-MM-dd HH:mm:ss.fff>');
            }
            $array['deliveryDate'] = $this->deliveryDate;
        }

        $array['isCourierDelivery'] = true;

        return $array;
    }

    public function send()
    {
        $request = new Request($this->app);
        $request->setUrl('delivery_restrictions/allowed');
        $request->setBody([
            'json' => $this->toArray(),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        return $request->post();
    }
}