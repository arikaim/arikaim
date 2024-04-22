<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Controllers\Traits;

/**
 * View svg trait
*/
trait ViewSvg
{
    /**
     * View svg icon
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function viewSvg($request, $response, $data)
    {
        $name = $data->get('name','icons~image');
        $width = $data->get('width','256px');
        $height = $data->get('height','256px');

        return $this->renderSvg($response,$name,[          
            'attr'  => [            
                'height' => $width,
                'width'  => $height
            ]
        ]);  
    }

    /**
     * Render svg icon
     *
     * @param ResponseInterface $response
     * @param string            $componentName
     * @param array             $params
     * @param array|null        $allowedLibs
     * @return ResponseInterface
     */
    public function renderSvg($response, string $componentName, array $params = [], ?array $allowedLibs = null)
    {
        if ($allowedLibs == null) {
            $allowedLibs = [
                'icons',
                'material-icons'
            ];
        }
        
        $component = $this->get('view')->renderComponent($componentName,'en',$params,'svg');
     
        if (\in_array($component->getTemplateName(),$allowedLibs) == false) {
            $response->withHeader('Content-Type','text/html');
            $response->getBody()->write('Not allowed components library: ' . $component->getTemplateName());
            return $response;
        }
        
        return $this->viewImageHeaders($response,'image/svg+xml',$component->getHtmlCode());
    }
}
