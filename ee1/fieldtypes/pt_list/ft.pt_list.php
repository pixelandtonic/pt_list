<?php if (! defined('EXT')) exit('Invalid file request');


/**
 * P&T List Fieldtype Class for EE1
 *
 * @package   P&T List
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_list extends Fieldframe_Fieldtype {

	var $info = array(
		'name'             => 'P&amp;T List',
		'version'          => '1.0',
		'versions_xml_url' => 'http://pixelandtonic.com/ee/versions.xml',
		'no_lang'          => TRUE
	);

	// --------------------------------------------------------------------

	/**
	 * Theme URL
	 */
	private function _theme_url()
	{
		if (! isset($this->_theme_url))
		{
			global $PREFS;
			$theme_folder_url = $PREFS->ini('theme_folder_url', 1);
			$this->_theme_url = $theme_folder_url.'third_party/pt_list/';
		}

		return $this->_theme_url;
	}

	/**
	 * Include Theme CSS
	 */
	private function _include_theme_css($file)
	{
		$this->insert('head', '<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'" />');
	}

	/**
	 * Include Theme JS
	 */
	private function _include_theme_js($file)
	{
		$this->insert('body', '<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function display_field($field_name, $data, $settings, $cell = FALSE)
	{
		$this->_include_theme_css('styles/pt_list.css');
		$this->_include_theme_js('scripts/pt_list.js');

		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);

		if (! $cell)
		{
			$this->insert_js('new ptList(jQuery("#'.$field_id.'"));');
		}

		$r = '<ul id="'.$field_id.'" class="pt-list ee1">';

		if ($data)
		{
			$list = explode("\n", $data);

			foreach($list as $li)
			{
				$r .= '<li><span>'.$li.'</span>'
				    .   '<input type="hidden" name="'.$field_name.'[]" value="'.str_replace(array('"', '&'), array('&quot;', '&amp;'), $li).'" />'
				    . '</li>';
			}
		}

		$r .=   '<li class="input last"><input type="text" name="'.$field_name.'[]" /></li>'
		    . '</ul>';

		return $r;
	}

	/**
	 * Display Cell
	 */
	function display_cell($cell_name, $data, $settings)
	{
		$this->_include_theme_js('scripts/matrix2.js');

		return $this->display_field($cell_name, $data, $settings, TRUE);
	}

	/**
	 * Display LV field
	 */
	function display_var_field($cell_name, $data, $settings)
	{
		return $this->display_field($cell_name, $data, $settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field
	 */
	function save_field($data, $settings)
	{
		// flatten list into one string
		$data = implode("\n", array_filter($data));

		// use real quotes
		$data = str_replace('&quot;', '"', $data);

		return $data;
	}

	/**
	 * Save Cell
	 */
	function save_cell($data, $settings)
	{
		return $this->save_field($data, $settings);
	}

	/**
	 * Save Var
	 */
	function save_var_field($data, $settings)
	{
		return $this->save_field($data, $settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Tag
	 */
	function display_tag($params, $tagdata, $data, $settings)
	{
		global $FNS;

		// ignore if empty
		if (! $data) return '';

		if (! $tagdata)
		{
			return $this->ul($params, $tagdata, $data, $settings);
		}

		$r = '';

		$data = explode("\n", $data);

		foreach ($data as $item)
		{
			$item_tagdata = $tagdata;

			$vars = array('item' => $item);

			$item_tagdata = $FNS->prep_conditionals($item_tagdata, $vars);
			$item_tagdata = $FNS->var_swap($item_tagdata, $vars);

			$r .= $item_tagdata;
		}

		if (isset($params['backspace']) && $params['backspace'])
		{
			$r = substr($r, 0, -$params['backspace']);
		}

		return $r;
	}

	/**
	 * UL
	 */
	function ul($params, $tagdata, $data, $settings)
	{
		return '<ul>'."\n"
		     .   $this->display_tag($params, '<li>'.LD.'item'.RD.'</li>'."\n", $data, $settings)
		     . '</ul>';
	}

	/**
	 * OL
	 */
	function ol($params, $tagdata, $data, $settings)
	{
		return '<ol>'."\n"
		     .   $this->display_tag($params, '<li>'.LD.'item'.RD.'</li>'."\n", $data, $settings)
		     . '</ol>';
	}

	/**
	 * Display Variable tag
	 */
	function display_var_tag($params, $tagdata, $data, $settings)
	{
		return $this->display_tag($params, $tagdata, $data, $settings);
	}

}
