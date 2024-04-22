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
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Actions\Action;
use Exception;

/**
* Model export to Json action
*/
class ModelExport extends Action 
{
    /**
     *  Base export path relative to storage path
     */   
    const EXPORT_PATH = 'files/json/';

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
     * @return void
     * @throws Exception
     */
    public function run(...$params)
    {
        $modelClass = $this->getOption('model_class');
        $schemaClass = $this->getOption('schema_class',$modelClass);
        $extension = $this->getOption('extension');
        $itemId = $this->getOption('uuid');
      
        $model = Model::create($modelClass,$extension);
        if ($model == null) {
            throw new Exception("Not valid model class or extension name!",1);
        }

        $schema = Factory::createSchema($schemaClass,$extension);
        if ($schema == null) {
            throw new Exception("Not valid schema class or extension name!",1);
        }

        $model = $model->findById($itemId);
        if ($model == null) {
            throw new Exception("Not valid model uuid!",1);
        }

        $fileName = $this->getFileName($model);
        $data = $model->toArray();
        $relations = $model->relationsToArray();
        $relationNames = $this->getRelationNames($model);
      
        // remove relations data 
        foreach ($data as $key => $value) {
            if (\in_array($key,$relationNames) == true) {
                unset($data[$key]);
            }
        }

        $content = [
            'date_exported' => DateTime::toString(DateTime::ISO8601_FORMAT),
            'model_class'   => $modelClass,
            'schema_class'  => $schemaClass,
            'extension'     => $extension,
            'uuid'          => $model->uuid,
            'unique'        => ['uuid'],
            'data'          => $data,
            'relations'     => $relations
        ];

        $result = $this->saveToFile($fileName,$content);
        if ($result !== false) {
            $this->result('file_name',$fileName);
        } else {
            $this->error('Error exporting model');
        }

        return $result;
    }

    /**
     * Get relations names
     *
     * @param object $model
     * @return array
     */
    public function getRelationNames($model): array
    {
        return \array_keys($model->getRelations());            
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
     * Save file
     *
     * @param string $fileName
     * @param mixed $data
     * @return boolean
     */
    public function saveToFile(string $fileName, $data): bool
    {
        global $arikaim;

        if (\is_array($data) == true) {
            $data = \json_encode($data,JSON_PRETTY_PRINT);
        }

        if (\is_object($data) == true) {
            $data = \serialize($data);
        }

        return $arikaim->get('storage')->write($fileName,$data);
    }

    /**
     * Get file name
     *
     * @param object $model
     * @return string
     */
    protected function getFileName(object $model): string
    {        
        $fileName = $this->getOption('file_name');
        if (empty($fileName) == true) {
            $class = Utils::getBaseClassName($model);
            $fileName = \strtolower($class) . '-' . $model->uuid . '.json';
        }

        return Self::EXPORT_PATH . $fileName;
    }
}
