<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


require_once PATH_THIRD.'pt_list/config.php';


/**
 * P&T List Fieldtype Class for EE2
 *
 * @package   P&T List
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_list_ft extends EE_Fieldtype {

	var $info = array(
		'name'    => 'P&amp;T List',
		'version' => PT_LIST_VER
	);

	var $has_array_data = TRUE;

	/**
	 * Fieldtype Constructor
	 */
	function Pt_list_ft()
	{
		parent::EE_Fieldtype();

		/** ----------------------------------------
		/**  Prepare Cache
		/** ----------------------------------------*/

		if (! isset($this->EE->session->cache['pt_list']))
		{
			$this->EE->session->cache['pt_list'] = array('includes' => array());
		}
		$this->cache =& $this->EE->session->cache['pt_list'];
	}

	// --------------------------------------------------------------------

	/**
	 * Theme URL
	 */
	private function _theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			if (substr($theme_folder_url, -1) != '/') $theme_folder_url .= '/';
			$this->cache['theme_url'] = $theme_folder_url.'third_party/pt_list/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Include Theme CSS
	 */
	private function _include_theme_css($file)
	{
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'" />');
		}
	}

	/**
	 * Include Theme JS
	 */
	private function _include_theme_js($file)
	{
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Insert JS
	 */
	private function _insert_js($js)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function display_field($data, $cell = FALSE)
	{
		$this->_include_theme_css('styles/pt_list.css');
		$this->_include_theme_js('scripts/pt_list.js');

		$field_name = $cell ? $this->cell_name : $this->field_name;
		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);

		if (! $cell)
		{
			$this->_insert_js('new ptList(jQuery("#'.$field_id.'"));');
		}

		$r = '<ul id="'.$field_id.'" class="pt-list ee2">';

		if ($data)
		{
			$list = is_array($data) ? $data : explode("\n", $data);

			foreach($list as $li)
			{
				$r .= '<li><span>'.$li.'</span>'
				    .   '<input type="hidden" name="'.$field_name.'[]" value="'.str_replace('"', '&quot;', $li).'" />'
				    . '</li>';
			}
		}

		$r .=   '<li class="input">'.form_input($field_name.'[]').'</li>'
		    . '</ul>';

		return $r;
	}

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		$this->_include_theme_js('scripts/matrix2.js');

		return $this->display_field($data, TRUE);
	}

	/**
	 * Display LV field
	 */
	function display_var_field($data)
	{
		return $this->display_field($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field
	 */
	function save($data)
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
	function save_cell($data)
	{
		return $this->save($data);
	}

	/**
	 * Save Var
	 */
	function save_var_field($data)
	{
		return $this->save($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Pre-process
	 */
	function pre_process($data)
	{
		$data = explode("\n", $data);

		foreach ($data as &$item)
		{
			$item = array('item' => $item);
		}

		return $data;
	}

	/**
	 * Replace Tag
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		// ignore if empty
		if (! $data) return '';

		if (! $tagdata)
		{
			return $this->replace_ul($data, $params);
		}

		// pre_process() fallback for Matrix
		if (is_string($data)) $data = $this->pre_process($data);

		$r = $this->EE->TMPL->parse_variables($tagdata, $data);

		if (isset($params['backspace']) && $params['backspace'])
		{
			$r = substr($r, 0, -$params['backspace']);
		}

		return $r;
	}

	/**
	 * Replace UL
	 */
	function replace_ul($data, $params = array())
	{
		return '<ul>'.NL
		     .   $this->replace_tag($data, $params, '<li>'.LD.'item'.RD.'</li>'.NL)
		     . '</ul>';
	}

	/**
	 * Replace OL
	 */
	function replace_ol($data, $params = array())
	{
		return '<ol>'.NL
		     .   $this->replace_tag($data, $params, '<li>'.LD.'item'.RD.'</li>'.NL)
		     . '</ol>';
	}

	/**
	 * Display Variable tag
	 */
	function display_var_tag($data, $params, $tagdata)
	{
		return $this->replace_tag($data, $params, $tagdata);
	}

}
