<?php

namespace TeaEagle\IikoTransport\Models\Request;

use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;

class Payment extends Model
{
    private $paymentType;
    private $sum;
    private $paymentTypeId;
    private $isProcessedExternally = false;
    private $phone;

    /**
     * @param mixed $sum
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }

    /**
     * @param mixed $paymentTypeId
     */
    public function setPaymentTypeId($paymentTypeId): void
    {
        $this->paymentTypeId = $paymentTypeId;
    }

    public function setIsCash(): void
    {
        $this->paymentType = 'Cash';
    }

    public function setIsCard(): void
    {
        $this->paymentType = 'Card';
    }

    public function setIsIikoCard(): void
    {
        $this->paymentType = 'IikoCard';
    }

    public function setIsProcessedExternally(): void
    {
        $this->isProcessedExternally = true;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function toArray(): array
    {
        $array = [];
        if ($this->paymentType) {
            $array['paymentTypeKind'] = $this->paymentType;
        }
        if ($this->sum) {
            $array['sum'] = $this->sum;
        }
        if ($this->paymentTypeId) {
            $array['paymentTypeId'] = $this->paymentTypeId;
        }
        $array['isProcessedExternally'] = $this->isProcessedExternally;
        if ($this->paymentType == 'IikoCard') {
            if (!$this->phone) {
                throw new UnsetAttributeException("Номер телефона не указан");
            }
            $array['paymentAdditionalData'] = [
                'credential' => $this->phone,
                'searchScope' => 'Phone',
                'type' => 'IikoCard',
            ];
        }
        return $array;
    }
}