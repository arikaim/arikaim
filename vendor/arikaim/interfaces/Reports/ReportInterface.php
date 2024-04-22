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
 * Report interface
 */
interface ReportInterface 
{  
    const CALC_TYPE_AVG          = 'avg';
    const CALC_TYPE_SUM          = 'sum';
    const CALC_TYPE_COUNT        = 'count';
    const CALC_TYPE_CUSTOM       = 'custom';
    const CALC_TYPE_SINGLE_VALUE = 'single';

    const CALC_PERIOD_DAILY   = 'daily';
    const CALC_PERIOD_WEEKLY  = 'weekly';
    const CALC_PERIOD_MONTHLY = 'monthly';
    const CALC_PERIOD_YEARLY  = 'yearly';
    const CALC_PERIOD_ALL     = 'all';

    /**
     * Get report data
     *   
     * @param string $period
     * @param integer|null $day
     * @param integer|null $month
     * @param integer|null $year
     * @return array
     */
    public function getReportData(
        string $period, 
        ?int $day = null, 
        ?int $month = null, 
        ?int $year = null
    ): array;
}
