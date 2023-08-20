<?php

namespace TeaEagle\IikoTransport;

use GuzzleHttp\Client as HttpClient;
use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use TeaEagle\IikoTransport\Models\Request\Delivery;
use TeaEagle\IikoTransport\Models\Request\Modifier;
use TeaEagle\IikoTransport\Models\Request\Payment;
use TeaEagle\IikoTransport\Models\Request\Product;
use TeaEagle\IikoTransport\Models\Request\CheckDelivery;

final class App extends Base
{
    public const REG_GUID = '/^\{?[A-z0-9]{8}-[A-z0-9]{4}-[A-z0-9]{4}-[A-z0-9]{4}-[A-z0-9]{12}\}?$/';

    public function __construct($login = null, $params = null)
    {
        if (is_null($login)) {
            throw new UnsetAttributeException("Unset login");
        }

        $this->login = $login;

        // Инициализация http клиента
        $this->httpClient = new HttpClient([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        // TODO Инициализация кеширования

        // TODO Инициализация логирования
    }

    public function newCheckDelivery() {
        $checkDelivery = new CheckDelivery();
        $checkDelivery->setApp($this);
        return $checkDelivery;
    }

    public function newOrder() {
        $order = new Delivery();
        $order->setApp($this);
        return $order;
    }

    public function newProduct() {
        $product = new Product();
        $product->setApp($this);
        return $product;
    }

    public function newModifier() {
        $modifier = new Modifier();
        $modifier->setApp($this);
        return $modifier;
    }

    public function newPayment() {
        $payment = new Payment();
        $payment->setApp($this);
        return $payment;
    }

    public function newAddress() {
        $address = new Address();
        $address->setApp($this);
        return $address;
    }

    public static function get_guid() {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        }
        else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }
}