<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content\Type;

use Arikaim\Core\Interfaces\Content\ContentItemInterface;
use Arikaim\Core\Interfaces\Content\ActionInterface;
use Exception;

/**
 *  Content type action abstract class
 */
abstract class Action implements ActionInterface 
{
    /**
     * Action name
     *
     * @var string
     */
    protected $name;

    /**
     * Action type
     *
     * @var string
     */
    protected $actionType;

    /**
     * Action title
     *
     * @var string|null
     */
    protected $title;

    /**
     * Constructor
     *
     * @param string|null $name
     * @param string|null $actionType
     * @param string|null $title
     * @throws Exception
     */
    public function __construct(?string $name = null, ?string $actionType = null, ?string $title = null)
    {      
        $this->name = $name;
        $this->actionType = $actionType;
        $this->title = $title;
        $this->init();

        if (empty($this->name) == true || empty($this->actionType) == true) {
            throw new Exception('Not valid content type action name or type.',1);            
        }
    }

    /**
     * Init action
     *
     * @return void
     */
    abstract public function init(): void;

    /**
     * Execute action
     *
     * @param ContentItemInterface $content    
     * @param array|null $options
     * @return mixed
     */
    abstract public function execute($content, ?array $options = []); 
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->actionType;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass(): string
    {
        return \get_class($this);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Set type
     *
     * @param string $actionType
     * @return void
     */
    protected function setType(string $actionType): void
    {
        $this->actionType = $actionType;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }   
}
