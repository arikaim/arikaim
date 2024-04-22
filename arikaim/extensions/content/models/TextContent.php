<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Content\Traits\ContentProvider;

use Arikaim\Core\Interfaces\Content\ContentProviderInterface;

/**
 * Text content db model class
 */
class TextContent extends Model implements ContentProviderInterface
{
    use Uuid,     
        Find, 
        DateCreated,  
        ContentProvider,      
        UserRelation;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'text_content';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'text',
        'user_id',  
        'date_created'         
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
   
    /**
     * Content provider content types
     *
     * @var array
     */
    protected $supportedContentTypes = ['text'];

    /**
     * Provider name
     *
     * @var string
     */
    protected $contentProviderName  = 'text.content';
    
    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle  = 'Text';

    /**
     * Get content
     *
     * @param string|int|array $key  Id, Uuid or content name slug
     * @param string|null $contentType  Content type name
     * @param string|array|null $keyFields
     * @return array|null
     */
    public function getContent($key, ?string $contentType = null, $keyFields = null): ?array
    {
        $model = $this->findById($key);
       
        return ($model == null) ? null : [
            'text'  => $model->text,
            'title' => $model->title
        ];
    }

    /**
     * Get total data items
     *
     * @return integer|null
     */
    public function getItemsCount(): ?int
    {
        return $this->all()->count();
    }

    /**
     * Create item
     *
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return array|null
     */
    public function createItem(array $data, ?string $contentType = null): ?array
    {
        $model = $this->create([
            'text'    => $data['text'] ?? '',
            'user_id' => $data['user_id'] ?? null,
            'title'   => $data['title'] ?? null
        ]);

        return ($model == null) ? null : [$model->uuid,$this->getProviderName()];
    }

    /**
     * Save content item
     *
     * @param string|int $key
     * @param array $data
     * @param string|null $contentType  Content type name
     * @return boolean
     */
    public function saveItem($key, array $data, ?string $contentType = null): bool
    {
        $model = $this->findById($key);
      
        return ($model == null) ? false : (bool)$model->update([
            'text'  => $data['text'] ?? null,
            'title' => $data['title'] ?? null
        ]);       
    }
}
