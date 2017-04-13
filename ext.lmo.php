<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lmo_ext {

    protected $name           = HELPER_NAME;
    public $version           = HELPER_VER;
    protected $description    = HELPER_DESC;
    protected $settings_exist = 'n';
    protected $docs_url       = ''; // 'https://ellislab.com/expressionengine/user-guide/';

    protected $settings       = array();

    /**
     * Constructor
     *
     * @param   mixed   Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }

    /**
     * Activate Extension
     *
     * @return void
     */
    public function activate_extension()
    {
        $this->settings = array();

        $data = array(
            'class'     => __CLASS__,
            'method'    => 'cp_js_end_hook',
            'hook'      => 'cp_js_end',
            'settings'  => serialize($this->settings),
            'priority'  => 10,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);
    }

    /**
     * Update Extension
     * 
     * @return  mixed   void on update / false if none
     */
    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        if ($current < '1.0')
        {
            // Update to version 1.0
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
                    'extensions',
                    array('version' => $this->version)
        );
    }

    /**
     * Disable Extension
     *
     * @return void
     */
    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    // --------------------------------
    //  Settings
    // --------------------------------

    public function settings()
    {   
        return array();
    }

    public function cp_js_end_hook()
    {
        $last = ee()->extensions->last_call;

        $js = '';

        $ver = App\Version::current();
        
        if (strpos($ver, '-dev') !== false && ENV !== 'local') {
            $ver .= ' (' . substr(getRevision(), 0, 6) . ')';
        }
        
        $bg = ee()->config->item('environment_color') ? ee()->config->item('environment_color') : '#1f2b33';
        
        $server = '';//' | ' . $_SERVER['SERVER_NAME'] . '@' . (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');
        
        $other = ''; //critical_css: ' . (env('CRITICAL_CSS') === 'off' ? 'off' : 'on');

        $other .= ' | release: <b>' . $ver . '</b>';

        $env = "<b>" . ENV . "</b>";

        $msg = $env . $server . $other;
        $js = '';
        
            $js = '(function () {

                var $header = $(".nav-global");
                           
                var $data = $("<div />", {
                    html: "' . $msg .'",
                    style: "color: orange"
                });

                var $div = $("<div />", {
                    "class": "nav-global-site environment-label " + "env-' . ENV . '".toLowerCase(),
                    html: $data,
                    style: "margin: 10px;"
                });

                $header.append($div);

            })();';
            
            $css = '<style type="text/css" media="screen">
                        .environment-label {
                            font-size: 150%;
                            height: 30px;
                            position: relative;
                            width: 100%;
                        }
                        .environment-label div {
                            background: ' . $bg . '; color: orange; position: fixed; top: 0; z-index: 100; text-align:center; width: 100%; padding :4px }
                    </style>';
            
            // ee()->cp->add_to_head($css);
        

        return $js;
    }

}
