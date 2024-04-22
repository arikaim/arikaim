<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

use Arikaim\Core\Db\Model;
use Closure;

/**
 * CRUD trait
*/
trait Crud 
{        
    /**
     * Before read
     *
     * @var Closure|null
     */
    protected $beforeReadCallback = null;

    /**
     * Before update
     *
     * @var Closure|null
     */
    protected $beforeUpdateCallback = null;

    /**
     * After update
     *
     * @var Closure|null
     */
    protected $afterUpdateCallback = null;

    /**
     * Before create
     *
     * @var Closure|null
     */
    protected $beforeCreateCallback = null;

    /**
     * After create
     *
     * @var Closure|null
     */
    protected $afterCreateCallback = null;

    /**
     * Before delete
     *
     * @var Closure|null
     */
    protected $beforeDeleteCallback = null;

    /**
     * After delete
     *
     * @var Closure|null
     */
    protected $afterDeleteCallback = null;

    /**
     * Set before read
     *
     * @param Closure $callback
     * @return void
     */
    protected function onBeforeRead(Closure $callback): void
    {
        $this->beforeReadCallback = $callback;
    }

    /**
     * Set before update
     *
     * @param Closure $callback
     * @return void
     */
    protected function onBeforeUpdate(Closure $callback): void
    {
        $this->beforeUpdateCallback = $callback;
    }

    /**
     * Set after update
     *
     * @param Closure $callback
     * @return void
     */
    protected function onAfterUpdate(Closure $callback): void
    {
        $this->afterUpdateCallback = $callback;
    }

    /**
     * Set before create
     *
     * @param Closure $callback
     * @return void
     */
    protected function onBeforeCreate(Closure $callback): void
    {
        $this->beforeCreateCallback = $callback;
    }

    /**
     * Set after create
     *
     * @param Closure $callback
     * @return void
     */
    protected function onAfterCreate(Closure $callback): void
    {
        $this->afterCreateCallback = $callback;
    }

    /**
     * Set before delete
     *
     * @param Closure $callback
     * @return void
     */
    protected function onBeforeDelete(Closure $callback): void
    {
        $this->beforeDeleteCallback = $callback;
    }

    /**
     * Set after delete
     *
     * @param Closure $callback
     * @return void
     */
    protected function onAfterDelete(Closure $callback): void
    {
        $this->afterDeleteCallback = $callback;
    }

    /**
     * Resolve callback
     *
     * @param mixed $data
     * @param Closure|null $callback
     * @return mixed
     */
    private function resolveCallback($data, ?Closure $callback, ?object $model = null)
    {
        return (\is_callable($callback) == true) ? $callback($data,$model) : $data;         
    }

    /**
     * Get default values
     *
     * @return array
     */
    protected function getDefaultValues(): array
    {
        return $this->defaultValues ?? [];
    } 

    /**
     * Get unique columns
     *
     * @return array
     */
    protected function getUniqueColumns(): array
    {
        return $this->uniqueColumns ?? [];
    } 

    /**
     * Get delete message name
     *
     * @return string
     */
    protected function getDeleteMessage(): string
    {
        return $this->deleteMessage ?? 'delete';
    }

    /**
     * Get update message name
     *
     * @return string
     */
    protected function getUpdateMessage(): string
    {
        return $this->updateMessage ?? 'update';
    }

    /**
     * Get create message name
     *
     * @return string
     */
    protected function getCreateMessage(): string
    {
        return $this->createMessage ?? 'create';
    }

    /**
     * Get read message name
     *
     * @return string
     */
    protected function getReadMessage(): string
    {
        return $this->readMessage ?? 'read';
    }

    /**
     * Apply default field values
     *
     * @param mixed $data
     * @return mixed
     */
    protected function applyDefaultValues($data) 
    {
        $defaultValues = $this->getDefaultValues();

        foreach ($data as $fieldName => $value) {
            if (empty($value) == true && \array_key_exists($fieldName,$defaultValues) == true) {               
                $data[$fieldName] = $defaultValues[$fieldName];
            }
        }

        return $data;
    }

    /**
     * Check unique columns
     *
     * @param Model|object $model
     * @param Collection $data
     * @param int $excludeId
     * @return bool
     */
    protected function checkColumn($model, $data, $excludeId = null): bool
    {
        $columns = $this->getUniqueColumns();

        foreach ($columns as $column) {
            $value = $data->get($column);
            if (empty($value) == false) {
                $found = $model->where($column,'=',$value)->first();
                if ($found !== null) {
                    if (empty($excludeId) == true) {
                        return false;
                    } elseif ($found->id != $excludeId) {
                        return false;
                    }                   
                } 
            }           
        }

        return true;
    } 

    /**
     * Read model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function read($request, $response, $data)
    {
        $data
            ->addRule('text:min=1|required','uuid')           
            ->validate(true); 
        
        $uuid = $data->get('uuid');
        $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);            
        if ($model == null) {
            $this->error('errors.' . $this->getReadMessage(),'Error read model');
            return;
        }
            
        $data = $this->resolveCallback($data,$this->beforeReadCallback,$model);

        $this
            ->message($this->getReadMessage())
            ->setResultFields($model->toArray());                        
    }

    /**
     * Update model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function update($request, $response, $data)
    {
        $data
            ->addRule('text:min=1|required','uuid')           
            ->validate(true);    
 
        $uuid = $data->get('uuid');
        $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
        if ($model == null) {
            $this->error('errors.id','Not valid model');
            return;
        }

        $result = $this->checkColumn($model,$data,$model->id);
        if ($result == false) {
            $this->error('errors.' . $this->getUpdateMessage(),'Error update');
            return;
        }   

        $data = $this->applyDefaultValues($data);  
        // before update            
        $data = $this->resolveCallback($data,$this->beforeUpdateCallback,$model);

        $result = (bool)$model->update($data->toArray());
        if ($result == false) {
            $this->error('errors.' . $this->getUpdateMessage(),'Error update');
            return;
        } 
        // after update
        $data = $this->resolveCallback($data,$this->afterUpdateCallback,$model);

        $this
            ->message($this->getUpdateMessage())
            ->field('uuid',$uuid);                          
    }

    /**
     * Create model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function create($request, $response, $data)
    {
        $data                  
            ->validate(true);    

        $model = Model::create($this->getModelClass(),$this->getExtensionName());
        if ($model == null) {
            $this->error('errors.id','Not valid model');
            return;
        }

        $result = $this->checkColumn($model,$data);
        if ($result == false) {
            $this->error('errors.' . $this->getUpdateMessage(),'Error update');
            return;
        }   

        $data = $this->applyDefaultValues($data);
        // before create
        $data = $this->resolveCallback($data,$this->beforeCreateCallback,$model);

        $createdModel = $model->create($data->toArray());
        if ($createdModel == null) {
            $this->error('errors.' . $this->getCreateMessage(),'Error create');
            return;
        } 

        // after create
        $data = $this->resolveCallback($data,$this->afterCreateCallback,$createdModel);

        $this
            ->message($this->getCreateMessage())
            ->field('uuid',$createdModel->uuid);                          
    }

    /**
     * Delete model
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deleteController($request, $response, $data)
    {
        $data
            ->addRule('text:min=1|required','uuid')           
            ->validate(true);  

        $uuid = $data->get('uuid');
        $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
        if ($model == null) {
            $this->error('errors.id','Not valid model');
            return;
        }
        // before delete
        $data = $this->resolveCallback($data,$this->beforeDeleteCallback,$model);
        $result = (bool)$model->delete();
            
        if ($result === false) {
            $this->error('errors.' . $this->getDeleteMessage(),'Error delete');
            return;
        }
        
        // after delete
        $data = $this->resolveCallback($data,$this->afterDeleteCallback,$model);

        $this
            ->message($this->getDeleteMessage())
            ->field('uuid',$uuid);                          
    }
}
