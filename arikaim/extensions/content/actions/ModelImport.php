<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Actions;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Actions\Action;

use Arikaim\Core\Interfaces\ImportModelInterface;

/**
* Model import from Json action
*/
class ModelImport extends Action 
{
    /**
     * Init action
     *
     * @return void
    */
    public function init(): void
    {
    }

    /**
     * Run action
     *
     * @param mixed ...$params
     * @return bool
     */
    public function run(...$params)
    {
        global $arikaim;

        $update = $this->getOption('update',true);

        $fileName = $this->getOption('file_name');
        if (empty($fileName) == true) {
            $this->error("File name not set!");
            return false;
        }

        $content = $arikaim->get('storage')->read($fileName);
        if ($content === false) {
            $this->error("Not valid file name!");
            return false;
        }

        $item = \json_decode($content,true);

        $modelClass = $item['model_class'] ?? null;
        $schemaClass = $item['schema_class'] ?? $modelClass;
        $unique = $item['unique'] ?? [];
        $extension = $item['extension'] ?? null; 
        $data = $item['data'];

        $schema = Factory::createSchema($schemaClass,$extension);
        if ($schema == null) {
            $this->error("Not valid schema class or extension name!");
            return false;
        }

        if (($schema instanceof ImportModelInterface) == false) {
            $this->error("Db schema model not allow import!");
            return false;
        }

        $model = Model::create($modelClass,$extension);
        if ($model == null) {
           $this->error("Not valid model class or extension name!");
           return false;
        }

        $skipColumns = $schema->getSkipedImportColumns();
        $columns = $model->getFillable();
        $modelData = [];
        foreach ($columns as $column) {
            if (isset($data[$column]) == true && \in_array($column,$skipColumns) == false) {
                $modelData[$column] = $data[$column];
            }
        }

        foreach ($unique as $column) {
            if (isset($data[$column]) == true && \in_array($column,$skipColumns) == false) {
                $modelData[$column] = $data[$column];
            }
        }

        $uniqueQuery = $this->createSearchValues($unique,$modelData); 
         
        if ($model->where($uniqueQuery)->exists() == true) {
            if ($update == true) {
                $model = $model->where($uniqueQuery)->first();
                $result = $model->update($modelData);
                if ($result === false) {
                    $this->error('Error update model');
                } else {
                    $this->result('message','Updated model: ' . $model->uuid);
                }
            } else {
                $model = $model->where($uniqueQuery)->first();
                $this->error('Model exist' . $model->uuid);
            }
          
        } else {            
            $new = $model->create($modelData);
            if ($new === null) {
                $this->error('Error import model');
            } else {
                $this->result('message','Created model: ' . $new->uuid);
            }
        }
    
        return ($this->hasError() == false);
    }

    /**
    * Init descriptor properties 
    *
    * @return void
    */
    protected function initDescriptor(): void
    {
    }

    /**
     * Create search item values
     *
     * @param array $keys
     * @param array $item
     * @return array
     */
    protected function createSearchValues(array $keys, array $items): array
    {
        $search = [];
        foreach ($keys as $key) {
            $search[$key] = $items[$key] ?? null;
        }

        return $search;
    }
}
