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
use Arikaim\Core\Db\Traits\DateUpdated;
use Arikaim\Core\Content\Traits\ContentProvider;

use Arikaim\Core\Interfaces\Content\ContentProviderInterface;

/**
 * SMS content db model class
 */
class SmsContent extends Model implements ContentProviderInterface
{
    use Uuid,     
        Find, 
        DateCreated,  
        DateUpdated,
        ContentProvider,      
        UserRelation;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'sms_content';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'phone', 
        'message',
        'user_id',  
        'date_updated',
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
    protected $supportedContentTypes = ['sms'];

    /**
     * Provider name
     *
     * @var string
     */
    protected $contentProviderName  = 'sms.content';
    
    /**
     * Content provider title
     *
     * @var string
     */
    protected $contentProviderTitle  = 'SMS message';

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
            'message' => $model->message,
            'phone'   => $model->phone
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
        $message = $data['message'] ?? '';
        $phone = $data['phone'] ?? '';
        if (empty($message) == true || empty($phone) == true) {
            return null;
        }

        $model = $this->create([
            'message' => $message,
            'user_id' => $data['user_id'] ?? null,
            'phone'   => $phone
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
        $message = $data['message'] ?? '';
        $phone = $data['phone'] ?? '';
        if (empty($message) == true || empty($phone) == true) {
            return false;
        }

        $model = $this->findById($key);
      
        return ($model == null) ? false : (bool)$model->update([
            'message' => $message,
            'phone'   => $phone
        ]);       
    }
}
