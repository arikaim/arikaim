<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Document;

/**
 * Document payments relation table trait
*/
trait DocumentPaymentsRelation 
{ 
    static $NOT_PAID = 0;
    static $PARTIAL_PAID = 1;
    static $PAID = 2;

    /**
     * Not paid status trait constant
     *
     * @return integer 0
     */
    public function NOT_PAID(): int
    {
        return Self::$NOT_PAID;
    }

    /**
     * Partial paid status trait constant
     *
     * @return integer 1
     */
    public function PARTIAL_PAID(): int
    {
        return Self::$PARTIAL_PAID;
    }

    /**
     * Full paid status trait constant
     *
     * @return integer 2
     */
    public static function PAID(): int
    {
        return Self::$PAID;
    }

    /**
     * Get payment status column name
     *
     * @return string
     */
    public function getPaymentStatusColumn(): string
    {
        return $this->statusColumn ?? 'payment_status';
    }

    /**
     * Get document payments model class
     *
     * @return string|null
     */
    public function getDocumentPaymentsClass(): ?string
    {
        return $this->documentPaymentsModel ?? null;
    }

    /**
     * Set payment status
     *
     * @param integer $status
     * @return boolean
     */
    public function setPaymentStatus(int $status): bool
    {
        $result = $this->update([
            $this->getPaymentStatusColumn() => $status
        ]);

        return ($result !== false);
    }

    /**
     * Document payments relation
     *
     * @return Relation|null
     */
    public function payments()
    {
        return $this->hasMany($this->getDocumentPaymentsClass(),'document_id');
    }

    /**
     * Get total payments
     *
     * @return float
     */
    public function getTotalPaid(): float
    {
        $result = $this->payments->where('document_id','=',$this->id)->sum('amount');

        return (empty($result) == true) ? 0.00 : (float)$result;
    }

    /**
     * total_paid attribute
     *
     * @return float
     */
    public function getTotalPaidAttribute()
    {
        return $this->getTotalPaid();
    }
   
    /**
     * Get payment due amount
     *
     * @return float
     */
    public function getPaymentsDue(): float
    {
        $due = $this->getTotal() - $this->getTotalPaid();

        return ($due < 0) ? 0.00 : $due;
    }

    /**
     * Return true if doc is paid
     *
     * @return boolean
     */
    public function isPaid(): bool
    {
       return (($this->getPaymentsDue() <= 0) && ($this->getTotal() > 0) );
    }
}
