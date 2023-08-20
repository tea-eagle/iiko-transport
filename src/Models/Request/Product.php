<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\App;
use TeaEagle\IikoTransport\Cache;
use TeaEagle\IikoTransport\Exceptions\UnsetParamException;
use TeaEagle\IikoTransport\Request;
use TeaEagle\IikoTransport\Models\Request\Modifier;

class Product extends Model
{
    private $id;
    private $type = 'Product';
    private $amount;
    private $price;
    private $modifiers = [];

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
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function setModifier(Modifier $modifier): void
    {
        $this->modifiers[] = $modifier;
    }

    public function toArray() {
        $array = [];
        if ($this->id) {
            $array['productId'] = $this->id;
        }
        if ($this->type) {
            $array['type'] = $this->type;
        }
        if ($this->amount) {
            $array['amount'] = $this->amount;
        }
        if ($this->price) {
            $array['price'] = $this->price;
        }
        if ($this->modifiers) {
            $array['modifiers'] =[];
            foreach ($this->modifiers as $modifier) {
                $array['modifiers'][] = $modifier->toArray();
            }
        }
        return $array;
    }

    public function result()
    {
        $request = new Request($this->app);
        $request->setUrl('nomenclature');
        $request->setBody([
            'json' => [
                'organizationId' => $this->app->organization->update(),
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->app->token->update(),
            ],
        ]);
        $result = $request->post();

        if (!$result->products) {
            throw new UnsetParamException("Request Error - Unset Products");
        }

        return $result;
    }

    private function update()
    {
        $result = $this->result();

        $obj = new \stdClass();
        $obj->products = $result->products;
        $obj->products = array_values(array_filter($obj->products, function($product) {
            return !$product->isDeleted;
        }));
        $obj->groups = $result->groups;
        $obj->groups = array_values(array_filter($obj->groups, function($group) {
            return !$group->isDeleted;
        }));
        $obj->categories = $result->productCategories;
        $obj->categories = array_values(array_filter($obj->categories, function($category) {
            return !$category->isDeleted;
        }));
        $obj->sizes = $result->sizes;

        return $obj;
    }

    public function list()
    {
        $data = $this->update();
        return $data->products;
    }

    public function groups()
    {
        $data = $this->update();
        return $data->groups;
    }

    public function categories()
    {
        $data = $this->update();
        return $data->categories;
    }

    public function sizes()
    {
        $data = $this->update();
        return $data->sizes;
    }
}