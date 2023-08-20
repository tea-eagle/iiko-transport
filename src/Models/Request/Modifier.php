<?php

namespace TeaEagle\IikoTransport\Models\Request;

class Modifier extends Model
{
    private $id;
    private $amount;
    private $group;
    private $price;

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group): void
    {
        $this->group = $group;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function toArray() {
        $array = [];
        if ($this->id) {
            $array['productId'] = $this->id;
        }
        if ($this->amount) {
            $array['amount'] = $this->amount;
        }
        if ($this->group) {
            $array['productGroupId'] = $this->group;
        }
        if ($this->price) {
            $array['price'] = $this->price;
        }
        return $array;
    }
}