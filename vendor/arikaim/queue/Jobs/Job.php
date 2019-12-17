<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue\Jobs;

use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Base class for all jobs
 */
abstract class Job implements JobInterface
{
    /**
     * Unique job id 
     *
     * @var string|integer
     */
    protected $id;

    /**
     * Job name
     *
     * @var string|null
     */
    protected $name;

    /**
     * Priority
     *
     * @var integer
     */
    protected $priority;

    /**
     * Extension name
     *
     * @var string
     */
    protected $extension;
  
    /**
     * Job code
     *
     * @return void
     */
    abstract public function execute();

    /**
     * Constructor
     *
     * @param string|null $extension
     * @param string|null $name
     */
    public function __construct($extension = null, $name = null)
    {
        $this->setExtensionName($extension);
        $this->setName($name);
        $this->setPriority(0);
        $this->id = null;
    }

    /**
     * Set
     *
     * @param string $name
     * @param mxied $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Set id
     *
     * @param string|integer $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string|integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get extension name
     *
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extension;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return void
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Set extension name
     *
     * @param string $name
     * @return void
     */
    public function setExtensionName($name)
    {
        return $this->extension = $name;
    }
}
