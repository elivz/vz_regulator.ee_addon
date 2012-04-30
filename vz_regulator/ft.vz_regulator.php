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
		'name'			=> 'VZ Regulator',
		'version'		=> '1.0.0'
	);
	
	/**
	 * Fieldtype Constructor
	 */
	function Vz_regulator_ft()
	{
		parent::EE_Fieldtype();

		if (!isset($this->EE->session->cache['vz_regulator']))
		{
			$this->EE->session->cache['vz_regulator'] = array('jscss' => FALSE);
		}
		$this->cache =& $this->EE->session->cache['vz_regulator'];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Include the JS and CSS files,
	 * but only the first time
	 */
	private function _include_jscss()
	{
		if (!$this->cache['jscss'])
		{
            $styles = '<style type="text/css">' . file_get_contents(PATH_THIRD . '/vz_regulator/assets/styles.min.css') . '</style>';
            $scripts = '<script type="text/javascript">// <![CDATA[ ' . file_get_contents(PATH_THIRD . '/vz_regulator/assets/scripts.min.js') . ' // ]]></script>';
			$this->EE->cp->add_to_head($styles . $scripts);
			
			$this->cache['jscss'] = TRUE;
		}
	}
	
	// --------------------------------------------------------------------
    
    /**
     * Settings UI
     */
    private function _settings_ui($settings, $is_cell=FALSE)
    {
        $this->EE->lang->loadfile('vz_regulator');
        
        $pattern = isset($settings['vz_regulator_pattern']) ? $settings['vz_regulator_pattern'] : '';
        $hint = isset($settings['vz_regulator_hint']) ? $settings['vz_regulator_hint'] : '';
        
        $settings_ui = array(
            array(
                '<strong>' . lang('pattern_label') .'</strong>'.
                ($is_cell ? '' : '<br/>' . lang('pattern_sublabel')),
                form_input(array(
                    'name' =>  'vz_regulator_pattern',
                    'value' => $pattern,
                    'class' => 'matrix-textarea',
                ))
            ),
            array(
                '<strong>' . lang('hint_label') .'</strong>'.
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
    function display_settings($settings)
    {
        $this->EE->load->library('table');

		foreach ($this->_settings_ui($settings) as $row)
        {
            $this->EE->table->add_row($row);
        }
    }
    
	/**
	 * Display Cell Settings
	 */
    function display_cell_settings($settings)
    {
        return $this->_settings_ui($settings, TRUE);
    }
	
    /**
     * Save Field Settings
     */
    function save_settings()
    {
        return array(
            'vz_regulator_pattern' => $this->EE->input->post('vz_regulator_pattern'),
            'vz_regulator_hint' => $this->EE->input->post('vz_regulator_hint'),
        );
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish
	 */
	function display_field($data, $name=FALSE)
	{
        $this->_include_jscss();
        
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
        if ($data == '') return TRUE;

        $pattern = isset($this->settings['vz_regulator_pattern']) ? '#' . preg_quote($this->settings['vz_regulator_pattern'], '#') . '#' : '';
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

}

/* End of file ft.vz_regulator.php */