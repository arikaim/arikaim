<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View\Html;

use Arikaim\Core\Collection\Interfaces\CollectionInterface;
use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Utils\Text;

/**
 * Page head class
 */
class PageHead extends Collection implements CollectionInterface, \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     *  Added to each html tag
     */
    const END_OF_LINE = "\n\t\t";

    /**
     * Property params
     *
     * @var array
     */
    protected $params;

    /**
     * Page head html code
     *
     * @var string
     */
    protected $htmlCode;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = []) 
    {  
        parent::__construct($data);

        $this->data['og'] = [];
        $this->data['twitter'] = [];
        $this->params = [];
        $this->htmlCode = '';
    }

    /**
     * Return page head as array 
     *
     * @return array
     */
    public function toArray(): array
    {
        $this->data['html_code'] = $this->htmlCode;
        
        return parent::toArray();
    }

    /**
     * Set property value param
     *
     * @param string $name
     * @param mixed $value
     * @return PageHead
     */
    public function param(string $name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Add or set property
     *
     * @param string $name
     * @param array $arguments
     * @return PageHead
     */
    public function __call($name, $arguments)
    {       
        $value = \trim($arguments[0] ?? '');
        $options = (isset($arguments[1]) == true) ? $arguments[1] : [];

        if (\substr($name,0,2) == 'og') {
            $name = \strtolower(\substr($name,2));
            return $this->og($name,$value,$options);          
        }
        if (\substr($name,0,7) == 'twitter') {
            $name = \strtolower(\substr($name,7));
            return $this->twitter($name,$value);
        }

        return $this->set($name,$value);      
    }

    /**
     * Set meta title, description and keywords
     *
     * @param array $data
     * @return PageHead
     */
    public function setMetaTags(array $data): object
    {
        $this->set('title',$this->getString('title'));
        $this->set('description',$this->getString('description'));
        $this->set('keywords',$this->getString('keywords'));
        
        return $this;
    }

    /**
     * Apply meta tags if values are empty
     *
     * @param array $data
     * @return PageHead
     */
    public function applyDefaultMetaTags(array $data): object
    {
        $this->applyDefault('title',$data);
        $this->applyDefault('description',$data);
        $this->applyDefault('keywords',$data);    
        
        return $this;
    }

    /**
     * Apply item value if is empty in collection
     *
     * @param string $key
     * @param array $data
     * @return PageHead
     */
    public function applyDefault(string $key, array $data): object
    {
        if (empty($this->get($key)) == true) {          
            $this->set($key,$data[$key] ?? null);
        }

        return $this;
    }

    /**
     * Apply og property if value is not empty
     *
     * @param string $key
     * @param string $default
     * @return PageHead
     */
    public function applyOgProperty(string $key, $default = ''): object
    {
        $value = $this->get($key,$default);
        if (empty($value) == false) {
            $this->og($key,$value);
        }

        return $this;
    }

    /**
     * Apply twitter property if value is not empty
     *
     * @param string $key
     * @param string $default
     * @return PageHead
     */
    public function applyTwitterProperty(string $key, $default = ''): object
    {
        $value = $this->get($key,$default);
        if (empty($value) == false) {
            $this->twitter($key,$value);
        }

        return $this;
    }

    /**
     * Set keywords metatag
     *
     * @param string|array ...$keywords
     * @return PageHead
     */
    public function keywords(...$keywords)
    {      
        $keywords = $this->createKeywords(...$keywords);
        
        return $this->set('keywords',$keywords);     
    }

    /**
     * Create keywords
     *
     * @param mixed ...$keywords
     * @return string
     */
    public function createKeywords(...$keywords): string
    {
        $words = [];
        foreach ($keywords as $text) {          
            $text = Text::tokenize($text,' ',Text::LOWER_CASE,true);          
            $words = \array_merge($words,$text);
        }

        return Arrays::toString($words,',');
    }   

    /**
     * Set keywords if field is empty
     *
     * @param array ...$keywords
     * @return PageHead
     */
    public function applyDefaultKeywors(...$keywords): object
    {
        if (empty($this->get('keywords')) == true) {
            $this->keywords(...$keywords);
        }

        return $this;
    }

    /**
     * Set Open Graph property
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return PageHead
     */
    public function og(string $name, $value, array $options = []): object
    {      
        return $this->addProperty('og',$name,$value,$options);
    }

    /**
     * Set Open Graph title property
     *
     * @param string|null $title
     * @return PageHead
     */
    public function ogTitle(?string $title = null): object
    {
        return $this->og('title',$this->get('title',$title));
    }
    
    /**
     * Set Open Graph description property
     *
     * @param string|null $description
     * @return PageHead
     */
    public function ogDescription(?string $description = null): object
    {
        return $this->og('description',$this->get('description',$description));
    }

    /**
     * Set twitter property
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return PageHead
     */
    public function twitter(string $name, $value, array $options = []): object
    {
        return $this->addProperty('twitter',$name,$value,$options);      
    }

    /**
     * Set twitter title property
     *
     * @param string|null $title
     * @return PageHead
     */
    public function twitterTitle(?string $title = null): object
    {
        return $this->twitter('title',$this->get('title',$title));
    }

    /**
     * Set twitter description property
     *
     * @param string|null $description
     * @return PageHead
     */
    public function twitterDescription(?string $description = null): object
    {
        return $this->twitter('description',$this->get('description',$description));
    }

    /**
     * Add property
     *
     * @param string $key
     * @param string $name
     * @param string $value
     * @param array $options
     * @return PageHead
     */
    protected function addProperty(string $key, string $name, $value, array $options = []): object
    {      
        $this->data[$key][$name] = $this->createProperty($name,$value,$options);

        return $this;
    }

    /**
     * Create property array
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return array
     */
    protected function createProperty(string $name, string $value, array $options = []): array
    {        
        return [            
            'name'      => \strtolower($name),
            'value'     => Text::render($value,$this->getParams()),
            'options'   => $options
        ];
    }

    /**
     * Resolve properties
     *   
     * @param string $key
     * @return boolean
     */
    public function resolveProperties(string $key): bool
    {
        $items = $this->data[$key] ?? null;
        if (\is_array($items) == false) {
            return false;
        }

        $properties = [];
        foreach ($items as $name => $value) {
            $property = (\is_array($value) == false) ? $this->createProperty($name,$value,[]) : $value;  
            $properties[] = $property;           
        }
        $this->data[$key] = $properties;

        return true;
    }


    /**
     * Add comoponent instance code
     *
     * @param array $item
     * @return void
     */
    public function addComponentInstanceCode(array $item): void 
    {
        $code = '<meta class="component-instance" component-name="' . $item['name'] . '" component-type="' . $item['type'] . '" component-id="' . $item['id'] . '" />' . Self::END_OF_LINE;
        
        $this->htmlCode .= $code;
    }

    /**
     * Add library include code
     *
     * @param array $file
     * @return void
     */
    public function addLibraryIncludeCode(array $file): void
    {
        if ($file['type'] == 'js') {                
            $attr = ($file['params_text'] ?? '') . 
                (($file['async'] == true) ? ' async' : '') .
                (empty($file['crossorigin']) ? '' : ' crossorigin');

            $this->addScriptCode($file['file'],'','library_' . $file['library'],$attr);
        }
        if ($file['type'] == 'css') { 
            if (($file['async'] ?? false) == true) {
                $this->addLinkCode($file['file'],'','preload','all',"this.onload=null;this.rel='stylesheet'");
            } else {
                $this->addLinkCode($file['file'],'text/css','stylesheet');
            }               
        }      
    }

    /**
     * Add component file code
     *
     * @param array $file
     * @return void
     */
    public function addComponentFileCode(array $file): void
    {
        $crossorigin = (\in_array('crossorigin',$file['params'] ?? []) ==true )? ' crossorigin="anonymous"' : '';
                    
        $attr = 'async class="component-file" ' . $crossorigin . '
            component-type="'. $file['component_type'] . '"
            component-name="'. $file['component_name'] . '"
            component-id="'. $file['component_id'] . '"';

        $this->addScriptCode($file['url'],'','',$attr);         
    }

    /**
     * Add metatag code
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addMetaTag(string $name, string $value): void
    {
        $this->htmlCode .= $this->getMetaCode($name,$value);
    }

    /**
     * Add meta code fro items
     *
     * @param array $items
     * @return void
     */
    public function addMetaTagCodeForItems(array $items): void
    {
        foreach ($items as $name) { 
            $this->addMetaTag($name,$this->getString($name));
        }
    }

    /**
     * Add script code
     *
     * @param string $src
     * @param string $type
     * @param string $id
     * @param string $attr
     * @return void
     */
    public function addScriptCode(string $src, string $type = '', string $id = '', string $attr = '')
    {
        $this->htmlCode .= $this->getScriptCode($src,$type,$id,$attr);
    }

    /**
     * Add inline script code
     *
     * @param string $content
     * @param string $type
     * @param string $attr
     * @return void
     */
    public function addInlineScriptCode(string $content, string $type = 'text/javascript', string $attr = ''): void
    {
        $this->htmlCode .= $this->createInlineScriptCode($content,$type,$attr);
    }

    /**
     * Add link code
     *
     * @param string $href
     * @param string $type
     * @param string $rel
     * @param string $media
     * @param string $onload
     * @param string $attr
     * @return void
     */
    public function addLinkCode(string $href, string $type, string $rel = '', string $media = 'all', string $onload = '', string $attr = ''): void
    {
        $this->htmlCode .= $this->getLinkCode($href,$type,$rel,$media,$onload,$attr);
    }

    /**
     * Add ccomment code
     *
     * @param string $comment
     * @return void
     */
    public function addCommentCode(string $comment)
    {
        $this->htmlCode .= '<!-- ' . $comment . " -->" . Self::END_OF_LINE;
    }

    /**
     * Get html code
     *
     * @return string
     */
    public function getHtmlCode(): string
    {
        return $this->htmlCode;
    }

    /**
     * Add head html code
     *
     * @param string $code
     * @return void
     */
    public function addHtmlCode(string $code): void
    {
        $this->htmlCode .= $code;
    }

    /**
     * Get metatag html code
     *
     * @param string $name
     * @param string $value
     * @param string $keyName
     * @return string
     */
    public function getMetaCode(string $name, string $value, string $keyName = 'name'): string
    {
        return '<meta ' . $keyName . '="' . $name . '" content="' . $value . '"/>' . Self::END_OF_LINE;
    }

    /**
     * Create script code
     *
     * @param string $src
     * @param string $type
     * @param string $id
     * @param string $attr
     * @return string
     */
    public function getScriptCode(string $src, string $type = '', string $id = '', string $attr = ''): string
    {
        return '<script src="' . $src . '" ' . $attr . (empty($id) ? '' : ' id="' . $id . '"') . ' type="' . (empty($type) ? 'text/javascript' : $type) . '"></script>' . Self::END_OF_LINE;       
    }

    /**
     * Create inline script code
     *
     * @param string $content
     * @param string $type
     * @param string $attr
     * @return string
     */
    public function createInlineScriptCode(string $content, string $type = 'text/javascript', string $attr = ''): string
    {
        return '<script type="' . $type . '" ' . $attr . ">\n" . $content ."\n</script>\n";
    }

    /**
     * Create link code
     *
     * @param string $href
     * @param string $type
     * @param string $rel
     * @param string $media
     * @param string $onload
     * @param string $attr
     * @return string
     */
    public function getLinkCode(string $href, string $type, string $rel = '', string $media = 'all', string $onload = '', string $attr = ''): string 
    {
        return '<link media="' . $media . '" href="' . $href . '" '  . 
            (empty($type) ? '' : ' type="' . $type . '"') . 
            (empty($onload) ? '' : ' onload="' . $onload . '"') . 
            (empty($rel) ? '' : ' rel="' . $rel . '" ') . $attr . ' />' . Self::END_OF_LINE;       
    }
}
