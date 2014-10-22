<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Regulator Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2012 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 *
 */

class Vz_regulator_ft extends EE_Fieldtype {

    public $info = array(
        'name'    => 'VZ Regulator',
        'version' => '1.1.0'
    );

    var $debug = TRUE;


    // --------------------------------------------------------------------


    /**
     * Fieldtype Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Load the language file
        ee()->lang->loadfile('vz_regulator');
    }

    /*
     * Register acceptable content types
     */
    public function accepts_content_type($name)
    {
        return ($name == 'channel' || $name == 'grid');
    }


    // --------------------------------------------------------------------


    /**
     * Include the JS and CSS files, but only the first time
     */
    private function _include_js_css($content_type='field')
    {
        if ( ! ee()->session->cache(__CLASS__, 'js_css'))
        {
            // Output stylesheet
            $css = file_get_contents(PATH_THIRD . '/vz_regulator/assets/styles' . ($this->debug ? '' : '.min') . '.css');
            ee()->cp->add_to_head('<style type="text/css">' . $css . '</style>');

            $scripts = file_get_contents(PATH_THIRD . '/vz_regulator/assets/scripts' . ($this->debug ? '' : '.min') . '.js');
            ee()->javascript->output($scripts);

            // Make sure we only load them once
            ee()->session->set_cache(__CLASS__, 'js_css', TRUE);
        }
    }


    // --------------------------------------------------------------------


    /**
     * Settings UI
     */
    private function _settings_ui($settings, $is_cell=FALSE)
    {
        $pattern = isset($settings['vz_regulator_pattern']) ? $settings['vz_regulator_pattern'] : '';
        $hint = isset($settings['vz_regulator_hint']) ? $settings['vz_regulator_hint'] : '';

        $settings_ui = array(
            array(
                lang('pattern_label') .
                ($is_cell ? '' : '<br/>' . lang('pattern_sublabel')),
                form_input(array(
                    'name' =>  'vz_regulator_pattern',
                    'value' => $pattern,
                    'class' => 'matrix-textarea',
                ))
            ),
            array(
                lang('hint_label') .
                ($is_cell ? '' : '<br/>' . lang('hint_sublabel')),
                form_input(array(
                    'name' =>  'vz_regulator_hint',
                    'value' => $hint,
                    'class' => 'matrix-textarea',
                ))

            )
        );

        return $settings_ui;
    }

    /**
     * Display Field Settings
     */
    public function display_settings($settings)
    {
        foreach ($this->_settings_ui($settings) as $row)
        {
            ee()->table->add_row($row);
        }
    }

    /**
     * Display Grid Cell Settings
     */
    public function grid_display_settings($settings)
    {
        $grid_settings = array();
        foreach ($this->_settings_ui($settings, true) as $row)
        {
            $grid_settings[] = $this->grid_settings_row($row[0], $row[1]);
        }

        return $grid_settings;
    }

    /**
     * Display Matrix Cell Settings
     */
    public function display_cell_settings($settings)
    {
        return $this->_settings_ui($settings, TRUE);
    }

    /**
     * Display Low Variable Settings
     */
    public function display_var_settings($settings)
    {
        return $this->_settings_ui($settings);
    }


    // --------------------------------------------------------------------


    /**
     * Save Field Settings
     */
    function save_settings($settings)
    {
        return array(
            'vz_regulator_pattern' => $settings['vz_regulator_pattern'],
            'vz_regulator_hint'    => $settings['vz_regulator_hint'],
        );
    }

    /**
     * Save Matrix Cell Settings
     */
    function save_cell_settings($settings)
    {
        return array_merge(array(
            'vz_regulator_pattern' => '',
            'vz_regulator_hint'    => ''
        ), $settings);
    }

    /**
     * Save Low Variables Settings
     */
    public function save_var_settings()
    {
        return $this->save_settings();
    }


    // --------------------------------------------------------------------


    /**
     * Display Field on Publish
     */
    function display_field($data, $name=FALSE)
    {
        $this->_include_js_css();

        $name = $name ? $name : $this->field_name;

        $pattern = isset($this->settings['vz_regulator_pattern']) ? $this->settings['vz_regulator_pattern'] : '';
        $hint = isset($this->settings['vz_regulator_hint']) ? $this->settings['vz_regulator_hint'] : '';

        // Safecracker escapes the pattern, breaking it
        if (REQ == 'PAGE')
        {
            $pattern = str_replace('\\', '\\\\', $pattern);
        }

        $output = '<div class="vz_regulator_container">';
        $output .= form_input(array(
            'name' => $name,
            'value' => $data,
            'class' => 'vz_regulator_field matrix-textarea',
            'pattern' => $pattern,
            'title' => $hint,
        ));
        $output .= $hint ? '<div class="vz_regulator_hint">' . $hint . '</div>' : '';
        $output .= '</div>';

        return $output;
    }

    /**
     * Display Cell
     */
    function display_cell($data)
    {
        return $this->display_field($data, $this->cell_name);
    }

    /**
     * Display Low Variable
     */
    function display_var_field($data)
    {
        return $this->display_field($data);
    }


    // --------------------------------------------------------------------


    /**
     * Validation to prevent saving entries that don't match
     */
    function validate($data)
    {
        if ($data == '')
        {
            if ( ! empty($this->settings['field_required']) || ! empty($this->settings['col_required']) )
            {
                return lang('required');
            }
            else
            {
                return TRUE;
            }
        }

        $pattern = isset($this->settings['vz_regulator_pattern']) ? '#' . str_replace('#', '\#', $this->settings['vz_regulator_pattern']) . '#' : '';
        $hint = isset($this->settings['vz_regulator_hint']) ? $this->settings['vz_regulator_hint'] : '';

        if ($pattern == '' || preg_match($pattern, $data) > 0)
        {
            return TRUE;
        }
        else
        {
            return $hint;
        }
    }

    /**
     * Validate Matrix Cell
     */
    public function validate_cell($data)
    {
        return $this->validate($data);
    }

}

/* End of file ft.vz_regulator.php */