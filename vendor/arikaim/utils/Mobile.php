<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

/**
 * Detect browser type (mobile or desktop)
 */
class Mobile
{
    /**
     * User agent
     *
     * @var string|null
     */
    protected $userAgent;

    /**
     * Request headers
     *
     * @var array
     */
    protected $headers;

    /**
     * Rules
     *
     * @var array
     */
    protected $rules;

    /**
     * Mobile headers
     *
     * @var array
     */
    protected $mobileHeaders = [
            'HTTP_ACCEPT' => ['matches' => [
                                    'application/x-obml2d',
                                    'application/vnd.rim.html',
                                    'text/vnd.wap.wml',
                                    'application/vnd.wap.xhtml+xml'
                                ]
                            ],
            'HTTP_X_WAP_PROFILE'           => null,
            'HTTP_X_WAP_CLIENTID'          => null,
            'HTTP_WAP_CONNECTION'          => null,
            'HTTP_PROFILE'                 => null,
            'HTTP_X_OPERAMINI_PHONE_UA'    => null,
            'HTTP_X_NOKIA_GATEWAY_ID'      => null,
            'HTTP_X_ORANGE_ID'             => null,
            'HTTP_X_VODAFONE_3GPDPCONTEXT' => null,
            'HTTP_X_HUAWEI_USERID'         => null,
            'HTTP_UA_OS'                   => null,
            'HTTP_X_MOBILE_GATEWAY'        => null,
            'HTTP_X_ATT_DEVICEID'          => null,
            'HTTP_UA_CPU'                  => ['matches' => ['ARM']],
    ];

    /**
     * Os
     *
     * @var array
     */
    protected $os = [
        'AndroidOS'         => 'Android',
        'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
        'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
        'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
        'WindowsPhoneOS'    => 'Windows Phone 10.0|Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;',
        'iOS'               => '\biPhone.*Mobile|\biPod|\biPad|AppleCoreMedia',
        'MeeGoOS'           => 'MeeGo',
        'MaemoOS'           => 'Maemo',
        'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b',
        'webOS'             => 'webOS|hpwOS',
        'badaOS'            => '\bBada\b',
        'BREWOS'            => 'BREW',
    ];

    /**
     * Browsers
     *
     * @var array
     */
    protected $browsers = [
        'Chrome'          => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
        'Dolfin'          => '\bDolfin\b',
        'Opera'           => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
        'Skyfire'         => 'Skyfire',
        'Edge'            => 'Mobile Safari/[.0-9]* Edge',
        'IE'              => 'IEMobile|MSIEMobile',
        'Firefox'         => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
        'Bolt'            => 'bolt',
        'TeaShark'        => 'teashark',
        'Blazer'          => 'Blazer',
        'Safari'          => 'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
        'UCBrowser'       => 'UC.*Browser|UCWEB',
        'baiduboxapp'     => 'baiduboxapp',
        'baidubrowser'    => 'baidubrowser',
        'DiigoBrowser'    => 'DiigoBrowser',
        'Puffin'          => 'Puffin',
        'Mercury'         => '\bMercury\b',
        'ObigoBrowser'    => 'Obigo',
        'NetFront'        => 'NF-Browser',
        'GenericBrowser'  => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
        'PaleMoon'        => 'Android.*PaleMoon|Mobile.*PaleMoon',
    ];

    /**
     * Utilities
     *
     * @var array
     */
    protected $utilities = [
        'Bot'         => 'Googlebot|facebookexternalhit|AdsBot-Google|Google Keyword Suggestion|Facebot|YandexBot|YandexMobileBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot',
        'MobileBot'   => 'Googlebot-Mobile|AdsBot-Google-Mobile|YahooSeeker/M1A1-R2D2',
        'DesktopMode' => 'WPDesktop',
        'TV'          => 'SonyDTV|HbbTV',
        'WebKit'      => '(webkit)[ /]([\w.]+)',
        'Console'     => '\b(Nintendo|Nintendo WiiU|Nintendo 3DS|PLAYSTATION|Xbox)\b',
        'Watch'       => 'SM-V700',
    ];

    /**
     * User agent headers
     *
     * @var array
     */
    protected $userAgentHeaders = [
        'HTTP_USER_AGENT',
        'HTTP_X_OPERAMINI_PHONE_UA',
        'HTTP_X_DEVICE_USER_AGENT',
        'HTTP_X_ORIGINAL_USER_AGENT',
        'HTTP_X_SKYFIRE_PHONE',
        'HTTP_X_BOLT_PHONE_UA',
        'HTTP_DEVICE_STOCK_UA',
        'HTTP_X_UCBROWSER_DEVICE_UA'
    ];

    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->initHeaders();
        $this->initUserAgent();

        $this->rules = array_merge(
            $this->os,
            $this->browsers,
            $this->utilities
        );
    }

    /**
     * Init headers
     *
     * @return void
     */
    public function initHeaders()
    {
        $headers = $_SERVER;
        $this->headers = [];
    
        foreach ($headers as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $this->headers[$key] = $value;
            }
        }
    }

    /**
     * Init user agent
     *
     * @return void
     */
    public function initUserAgent()
    {
        $this->userAgent = null;
        foreach ($this->userAgentHeaders as $altHeader) {
            if (empty($this->headers[$altHeader]) == false) {
                $this->userAgent .= $this->headers[$altHeader] . " ";
            }
        }

        if (empty($this->userAgent) == false) {
            $this->userAgent = substr(trim($this->userAgent),0,500);
            return;
        }
        $this->userAgent = null;
    }

    /**
     * Check for mobile headers 
     *
     * @return bool
     */
    public function checkHeadersForMobile()
    {
        foreach ($this->mobileHeaders as $header => $matchType) {
            if (isset($this->headers[$header]) == true) {
                if (is_array($matchType['matches']) == true) {
                    foreach ($matchType['matches'] as $match) {
                        if (strpos($this->headers[$header],$match) !== false) {
                            return true;
                        }
                    }
                    return false;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Check user agent
     *
     * @return bool
     */
    protected function matchUserAgent()
    {
        foreach ($this->rules as $regex) {
            if (empty($regex) == true) {
                continue;
            }

            if ($this->match($regex) == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return true if page is open from mobile browser
     *
     * @return bool
     */
    public static function mobile()
    {
        $obj = new Mobile();

        return $obj->isMobile();
    }

    /**
     * Return true if page is open from mobile browser
     *
     * @return boolean
     */
    public function isMobile()
    {
        if ($this->checkHeadersForMobile() == true) {
            return true;
        }

        return $this->matchUserAgent();
    }

    /**
     * Match helper
     *
     * @param string $regex
     * @return bool
     */
    protected function match($regex)
    {
       return (bool)preg_match(sprintf('#%s#is', $regex),$this->userAgent,$matches);
    }
}
