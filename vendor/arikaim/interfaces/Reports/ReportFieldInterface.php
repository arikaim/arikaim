<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Reports;

/**
 * Report field interface
 */
interface ReportFieldInterface 
{  
    /**
     * Get field calc type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get data column name
     *
     * @return string|null
     */
    public function getDataClumn(): ?string; 

    /**
     * Save summary value
     *
     * @param mixed $value
     * @param string $period
     * @param integer $day
     * @param integer $month
     * @param integer $year
     * @return boolean
     */
    public function saveSummaryValue($value, string $period, ?int $day, ?int $month, ?int $year): bool;

    /**
     * Get summary data
     *
     * @param string $period
     * @param integer|null $day
     * @param integer|null $month
     * @param integer|null $year
     * @return array
     */
    public function getSummaryData(string $period, ?int $day = null, ?int $month = null, ?int $year = null): array;
}
