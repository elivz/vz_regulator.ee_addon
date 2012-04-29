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
			$this->EE->cp->add_to_head('<style type="text/javascript">
    .vz_regulator_field {}
    .vz_regulator_field:invalid, .vz_regulator_field_invalid {}
</style>');
			$this->EE->cp->load_package_js('vz_regulator');
			
			$this->cache['jscss'] = TRUE;
		}
	}
	
	// --------------------------------------------------------------------
    
    /**
     * Settings UI
     */
    private function _settings_ui($settings)
    {
        $this->EE->lang->loadfile('vz_regulator');
        
        $pattern = isset($settings['vz_regulator_pattern']) ? $settings['vz_regulator_pattern'] : '';
        
        $settings_ui = array(
            lang('vz_regulator_pattern_label', 'vz_regulator_pattern'),
            form_input(array(
                'name' =>  'vz_regulator_pattern',
                'value' => $pattern,
            ))
        );
        
        return $settings_ui;
    }
    
    /**
     * Display Field Settings
     */
    function display_settings($settings)
    {
        $this->EE->load->library('table');

		$settings_ui = $this->_settings_ui($settings);
        $this->EE->table->add_row($settings_ui);
    }
    
	/**
	 * Display Cell Settings
	 */
    function display_cell_settings($settings)
    {
        $this->EE->lang->loadfile('vz_regulator');
        
        $pattern = isset($settings['vz_regulator_pattern']) ? $settings['vz_regulator_pattern'] : '';
        
        $settings_ui = array(
            lang('vz_regulator_pattern_cell_label', 'vz_regulator_pattern'),
            form_input(array(
                'name' =>  'vz_regulator_pattern',
                'value' => $pattern,
                'class' => 'matrix-textarea',
            ))
        );

        return array($settings_ui);
    }
	
    /**
     * Save Field Settings
     */
    function save_settings()
    {
        return array('vz_regulator_pattern' => $this->EE->input->post('vz_regulator_pattern'));
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
        
        return form_input(array(
            'name' => $name,
            'value' => $data,
            'class' => 'vz_regulator_field',
            'pattern' => $pattern
        ));
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

}

/* End of file ft.vz_regulator.php */