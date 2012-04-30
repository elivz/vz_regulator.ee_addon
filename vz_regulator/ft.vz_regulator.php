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
			$this->EE->cp->add_to_head('<style type="text/css">
    .vz_regulator_container { position: relative; }
    .vz_regulator_field:invalid, .vz_regulator_field:focus:invalid, .vz_regulator_field.invalid, .vz_regulator_field.invalid:focus { color: #c11; border-color: #a66;}
    .vz_regulator_hint { opacity: 0; position: absolute; z-index: 100; left: 5px; top: 24px; max-width: 90%; padding: 4px 6px; pointer-events: none; color: #E1E8ED; font-size: 11px; #000; background: #3E4C54; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; -webkit-box-shadow: 0 1px 5px rgba(0,0,0,0.1); -moz-box-shadow: 0 1px 5px rgba(0,0,0,0.1); box-shadow: 0 1px 5px rgba(0,0,0,0.1); -webkit-transition: all 0.2s ease-in-out; -moz-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; }
    .vz_regulator_field:invalid + .vz_regulator_hint, .vz_regulator_field.invalid + .vz_regulator_hint { top: 26px; }
    .vz_regulator_field:focus:invalid + .vz_regulator_hint, .vz_regulator_field.invalid:focus + .vz_regulator_hint { opacity: 1; }
    .vz_regulator_hint:before { content: ""; position: absolute; left: 10px; top: -6px; width: 0; height: 0; border: 6px solid transparent; border-top: 0; border-bottom: 6px solid #3E4C54; }
</style>');
			$this->EE->cp->load_package_js('vz_regulator');
			
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