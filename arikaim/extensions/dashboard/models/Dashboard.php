<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;

/**
 * Dashboard model class
 */
class Dashboard extends Model  
{
    use Uuid,    
        Status,            
        Find;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'dashboard';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'component_name',
        'options',
        'status'           
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Find or create
     *
     * @param string $componentName
     * @return object|null
     */
    public function findOrCreate(string $componentName): ?object
    {
        $model = $this->findByColumn($componentName,'component_name');
        if ($model == null) {
            $model = $this->create([
                'component_name' => $componentName
            ]);
        }

        return $model;
    }

    /**
     * Hide dashboard panel 
     *
     * @param string $componentName
     * @return boolean
     */
    public function hidePanel(string $componentName): bool
    {
        $model = $this->findOrCreate($componentName);

        return ($model == null) ? false : $model->setStatus(0);
    }

    /**
     * Show dashboard panel 
     *
     * @param string $componentName
     * @return boolean
     */
    public function showPanel(string $componentName): bool
    {
        $model = $this->findOrCreate($componentName);

        return ($model == null) ? false : $model->setStatus(1);
    }

    /**
     * Return true if panel is hidden
     *
     * @param string $componentName
     * @return boolean
     */
    public function isHidden(string $componentName): bool
    {
        $model = $this->findByColumn($componentName,'component_name');

        return ($model == null) ? false : ($model->status != 1);
    }

    /**
     * Return true if panel is visible
     *
     * @param string $componentName
     * @return boolean
     */
    public function isVisible(string $componentName): bool
    {
        $model = $this->findByColumn($componentName,'component_name');
        
        return ($model == null) ? true : ($model->status == 1);
    }
}
