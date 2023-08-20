<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Request;

class Delivery extends Model
{
    private $id;
    private $externalNumber;
    private $phone;
    private $delivery = 'DeliveryByClient';
    private $address;
    private $comment;
    private $countGuest;
    private $products = [];
    private $payments = [];
    private $customerId;
    private $customerName;
    private $terminal;
    private $coupon;
    private $orderTypeId;

    /**
     * @return String
     */
    private function generateUniqueId()
    {
        return $this->id = App::get_guid();
    }

    public function setRealOrderId($id): void
    {
        $this->externalNumber = $id;
    }

    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function isDelivery($delivery = false): void
    {
        $this->delivery = $delivery === true ? 'DeliveryByCourier' : 'DeliveryByClient';
    }

    /**
     * @return boolean
     */
    private function isAddressRequired()
    {
        return $this->delivery == 'DeliveryByCourier';
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    public function setCountGuests($count): void
    {
        $this->countGuest = intval($count);
    }

    public function setProduct(Product $product): void
    {
        $this->products[] = clone $product;
    }

    public function setAddress(Address $address): void
    {
        $this->address = clone $address;
    }

    public function setPayment(Payment $payment): void
    {
        $this->payments[] = clone $payment;
    }

    public function setCustomer($name, $id = null): void
    {
        $this->customerId = $id;
        $this->customerName = $name;
    }

    public function setTerminal($terminal): void
    {
        $this->terminal = $terminal;
    }

    public function setCoupon($coupon): void
    {
        $this->coupon = $coupon;
    }

    public function setOrderType($orderTypeId): void
    {
        $this->orderTypeId = $orderTypeId;
    }

    public function toArray()
    {
        if (!$this->phone) {
            throw new UnsetAttributeException("Номер телефона не указан");
        }
        if (!$this->products) {
            throw new UnsetAttributeException("Товары не добавлены");
        }
        if ($this->isAddressRequired() && !$this->address) {
            throw new UnsetAttributeException("Адрес не заполнен");
        }

        $array = [];
        $array['organizationId'] = $this->app->organization->update();
        if ($this->terminal) {
            $array['terminalGroupId'] = $this->terminal;
        }
        if ($this->coupon) {
            $array['coupon'] = $this->coupon;
        }
        //$array['createOrderSettings'] = [
        //    'mode' => 'Async',
        //];
        $array['order'] = [];
        $array['order']['id'] = $this->generateUniqueId();
        if ($this->externalNumber) {
            $array['order']['externalNumber'] = $this->externalNumber;
        }

        $array['order']['phone'] = $this->phone;

        $array['order']['customer'] = [];
        $array['order']['customer']['type'] = 'regular';
        if ($this->customerId) {
            $array['order']['customer']['id'] = $this->customerId;
        }
        if ($this->customerName) {
            $array['order']['customer']['name'] = $this->customerName;
        }
        if ($this->address) {
            $array['order']['deliveryPoint'] = [];
            $array['order']['deliveryPoint']['address'] = $this->address->toArray();
        }
        if ($this->comment) {
            $array['order']['comment'] = $this->comment;
        }
        if ($this->countGuest) {
            $array['order']['guests'] = [];
            $array['order']['guests']['count'] = $this->countGuest;
            $array['order']['guests']['splitBetweenPersons'] = false;
        }
        $array['order']['items'] = [];
        foreach ($this->products as $product) {
            $array['order']['items'][] = $product->toArray();
        }
        if ($this->payments) {
            $array['order']['payments'] = [];
            foreach ($this->payments as $payment) {
                $array['order']['payments'][] = $payment->toArray();
            }
        }
        if ($this->orderTypeId) {
            $array['order']['orderTypeId'] = $this->orderTypeId;
        } else {
            $array['order']['orderServiceType'] = $this->delivery;
        }

        return $array;
    }

    public function send()
    {
        $request = new Request($this->app);
        $request->setUrl('deliveries/create');
        $request->setBody([
            'json' => $this->toArray(),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        return $request->post();
    }

    public function calculate() {
        $request = new Request($this->app);
        $request->setUrl('loyalty/iiko/calculate');
        $request->setBody([
            'json' => $this->toArray(),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        return $request->post();
    }
}