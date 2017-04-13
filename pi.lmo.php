<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lmo
{

    public function __construct()
    {

    }

    public function criticalcss()
    {

        if (env('CRITICAL_CSS') === 'off') {
            return;
        }
        
        $base = $_SERVER['DOCUMENT_ROOT'] . '/assets/css/critical/';

        if (env('CRITICAL_CSS_BASE')) {
            $base = env('CRITICAL_CSS_BASE');
        }

        $name = ee()->TMPL->fetch_param('name');

        $file = $base . $name .'.css';
        
        if (ENV !== 'local') {
            $file = $base . $name .'.min.css';
        }
        
        // var_dump($file, $name);exit;

        if (file_exists($file)) {
            $css = file_get_contents($file);
            return "<!-- {$name} --><style media='screen'>.forPrint{display:none;}{$css}</style>";
        }

        return;
    }

}