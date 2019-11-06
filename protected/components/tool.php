<?php

namespace Components;

class Tool
{
    protected $_basePath;

    public function __construct($_basePath = null)
    {
        $this->_basePath = $_basePath;
    }
    
    public function get_css($data, $eregs = null)
    {
        if (!file_exists($this->_basePath . $data['path']))
            return false;
        $result = file_get_contents($this->_basePath . $data['path']);
        if ($result) {
            if ($eregs) {
                if (!is_array($eregs['patern'])) {
                    $pattern = $eregs['patern'];
                    $patterns = "/" . preg_replace(['/\//'], ['\/'], $pattern) . "/";
                    $replacements = $eregs['replacement'];
                    $result = preg_replace([$patterns], [$replacements], $result);
                } else {
                    $patterns = [];
                    foreach ($eregs['patern'] as $i => $pat) {
                        $new_pat = "/" . preg_replace(['/\//'], ['\/'], $pat) . "/";
                        $patterns[$i] = $new_pat;
                    }
                    $result = preg_replace($patterns, $eregs['replacement'], $result);
                }
            }
            
            return '<style>' . $result . '</style>';
        } else {
            return false;
        }
    }

    public function get_js($data)
    {
        if (!file_exists($this->_basePath . $data['path']))
            return false;
        $result = file_get_contents($this->_basePath . $data['path']);
        if ($result) {
            return '<script type="text/javascript">' . $result . '</script>';
        } else {
            return false;
        }

    }

    public function url_origin( $use_forwarded_host = false )
    {
        $s = $_SERVER;
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    public function get_sitemaps() {
        $results = [];
        $omodel = new \Model\OptionsModel();
        $pages = array();
        foreach (glob($_SERVER['DOCUMENT_ROOT'].'/themes/'.$omodel->getOptions()['theme'].'/views/*.phtml') as $filename) {
            $page = basename($filename, '.phtml');
            $excludes = ['post', 'sitemap.xml', '404'];
            if (file_exists($filename) && !in_array($page, $excludes) && strpos($page, "_") == false) {
                $loc = self::url_origin().'/'.$page;
                if ($page == 'index') {
                    $loc = self::url_origin().'/';
                }
                $pages[] = [
                    'loc' => $loc,
                    'lastmod' => date ("c", filemtime($filename)),
                    'priority' => ($page == 'index')? 1.0 : 0.5
                ];
            }
        }
        $results = array_merge($results, $pages);

        $exts = $omodel->getInstalledExtensions();

        if (array_key_exists("blog", $exts)) {
            $pmodel = new \ExtensionsModel\PostModel();
            $posts = $pmodel->getSitemaps();
            $results = array_merge($results, $posts);
        }

        return $results;
    }

    public function rpHash($value) {
        $hash = 5381;
        $value = strtoupper($value);
        for($i = 0; $i < strlen($value); $i++) {
            $hash = ($this->leftShift32($hash, 5) + $hash) + ord(substr($value, $i));
        }
        return $hash;
    }

    private function leftShift32($number, $steps) {
        // convert to binary (string)
        $binary = decbin($number);
        // left-pad with 0's if necessary
        $binary = str_pad($binary, 32, "0", STR_PAD_LEFT);
        // left shift manually
        $binary = $binary.str_repeat("0", $steps);
        // get the last 32 bits
        $binary = substr($binary, strlen($binary) - 32);
        // if it's a positive number return it
        // otherwise return the 2's complement
        return ($binary{0} == "0" ? bindec($binary) :
            -(pow(2, 31) - bindec(substr($binary, 1))));
    }

    public function getCustomMenu() {
        $omodel = new \Model\OptionsModel();
        $exts = $omodel->getInstalledExtensions();

        $menus = [];
        foreach ($exts as $ext_name => $ext_data) {
            if (array_key_exists('menu', $ext_data)) {
                $menus[$ext_name] = $ext_data['menu'];
            }
        }
        return $menus;
    }

    public function createSlug($str)
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        $str = trim($str, '-');
        return $str;
    }
}