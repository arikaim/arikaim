<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App;

use Closure;

/**
 * System update
 */
class SystemUpdate 
{
    /**
     * Run system update
     *
     * @return bool
     */
    public static function run(?Closure $onProgress = null): bool
    {
        global $arikaim;

        $schema = $arikaim->get('db')->getCapsule()->schema();
        
        if ($schema->hasTable('jobs') == true) {
            // rename jobs tabel to queue
            $schema->rename('jobs','queue');
            if (\is_callable($onProgress) == true) {
                $onProgress("Rename system table 'jobs' to 'queue");
            }
        }
        
        return true;
    }

}
