<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Arikaim\Core\Interfaces\Job\JobInterface;

use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\OptionsAttribute;

/**
 * Jobs registry database model
 */
class JobsRegistry extends Model 
{
    use Uuid,
        Find,
        OptionsAttribute,
        DateCreated;
 
    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'name',
        'title',
        'description',
        'category',
        'handler_class',      
        'package_name',
        'package_type',
        'date_created',
        'properties'
    ];
    
    protected $optionsColumnName = 'properties';

    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'jobs_registry';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Save job in registry
     *
     * @param JobInterface $job
     * @param string|null  $packageName
     * @param string|null  $packageType
     * @return boolean
     */
    public function saveJob(JobInterface $job, ?string $packageName, ?string $packageType = null): bool
    {
        $data = [
            'title'         => $job->descriptor()->getValue('title'),
            'description'   => $job->descriptor()->getValue('descriptioon'),
            'category'      => $job->descriptor()->getValue('category'),
            'handler_class' => \get_class($job),
            'name'          => $job->getName(),
            'package_name'  => $packageName,
            'package_type'  => $packageType
        ];

        $model = $this->findJob($job->getName());
        
        $result = ($model == null) ? $this->create($data) : $model->update($data);

        return ($result !== false);
    }

    /**
     * Filter jobs by package
     *
     * @param Builder     $query
     * @param string|null $packageName
     * @param string|null $packageType
     * @return Builder
     */
    public function scopePackageQuery($query, ?string $packageName, ?string $packageType = null)
    {
        $query = (empty($packageName) == true) ? $query : $query->where('package_name','=',$packageName);

        return (empty($packageType) == false) ? $query->where('package_type','=',$packageType) : $query;
    }

    /**
     * Delete jobs
     *
     * @param string $packageName
     * @param string $packageType
     * @return boolean
     */
    public function deleteJobs(string $packageName, string $packageType): bool
    {
        $result = $this
            ->where('package_name','=',$packageName)
            ->where('package_type','=',$packageType)->delete();

        return ($result !== false);
    }

    /**
     * Delete job
     *
     * @param string|integer $id
     * @return boolean
     */
    public function deleteJob($name): bool
    {
        $model = $this->findJob($name);

        return ($model != null) ? (bool)$model->delete() : false;
    }

    /**
     * Find job
     *
     * @param string|integer $name Job id, uiid or name
     * @return object|null
     */
    public function findJob($name): ?object
    {
        $model = $this->findById($name);

        return ($model == null) ? $this->findByColumn($name,['name','handler_class']) : $model;        
    }

    /**
     * Return true if job exists
     *
     * @param string|integer $name Job id, uiid, name
     * @return boolean
     */
    public function hasJob($name): bool
    {
        $model = $this->findJob($name);  
        return ($model != null);
    }
}
