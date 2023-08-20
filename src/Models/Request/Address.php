<?php

namespace TeaEagle\IikoTransport\Models\Request;

class Address extends Model
{
    private $city;
    private $street;
    private $house;
    private $flat;
    private $entrance;
    private $floor;
    private $doorphone;
    private $building;

    /**
     * @param string $street
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @param string $house
     */
    public function setHouse($house): void
    {
        $this->house = $house;
    }

    /**
     * @param string $flat
     */
    public function setFlat($flat): void
    {
        $this->flat = $flat;
    }

    /**
     * @param string $entrance
     */
    public function setEntrance($entrance): void
    {
        $this->entrance = $entrance;
    }

    /**
     * @param string $floor
     */
    public function setFloor($floor): void
    {
        $this->floor = $floor;
    }

    /**
     * @param string $doorphone
     */
    public function setDoorphone($doorphone): void
    {
        $this->doorphone = $doorphone;
    }

    /**
     * @param string $building
     */
    public function setBuilding($building): void
    {
        $this->building = $building;
    }

    public function toArray() {
        $array = [];
        if ($this->city) {
            $array['street'] = [];
            $array['street']['city'] = $this->city;
        }
        if ($this->street) {
            if (!isset($array['street'])) {
                $array['street'] = [];
            }
            $array['street']['name'] = $this->street;
        }
        if ($this->house) {
            $array['house'] = $this->house;
        }
        if ($this->building) {
            $array['building'] = $this->building;
        }
        if ($this->flat) {
            $array['flat'] = $this->flat;
        }
        if ($this->entrance) {
            $array['entrance'] = $this->entrance;
        }
        if ($this->floor) {
            $array['floor'] = $this->floor;
        }
        if ($this->doorphone) {
            $array['doorphone'] = $this->doorphone;
        }
        return $array;
    }
}