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
 * Links content db model class
 */
class LinksContent extends Model implements ContentProviderInterface
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
    protected $table = 'links_content';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'url',
        'target',
        'options',
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
    protected $supportedContentTypes = ['link'];

    /**
     * Provider name
     *
     * @var string
     */
    protected $contentProviderName  = 'link.content';
    
    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle  = 'Links';

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
            'url'     => $model->url,
            'target'  => $model->target,
            'options' => [],
            'title'   => $model->title
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
        $url = $data['url'] ?? null;
        if (empty($url) == true) {
            return null;
        }

        $model = $this->create([
            'url'     => $url,
            'target'  => $data['target'] ?? null,
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
        if ($model == null) {
            return false;
        }
        $url = $data['url'] ?? null;
        if (empty($url) == true) {
            return false;
        }

        return (bool)$model->update([
            'url'     => $url,
            'target'  => $data['target'] ?? null,         
            'title'   => $data['title'] ?? null
        ]);       
    }
}
