<?php

defined('_JEXEC') or die();

class JElementJLMLItemid extends JElement
{

	var	$_name = 'jlmlitemid';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = JFactory::getDbo();

		$options 	= array(JHtml::_('select.option', '', '- '.JText::_('Select Item').' -'));

		$query = "SELECT menutype, title FROM #__menu_types ORDER BY title";
		$db->setQuery( $query );
		$mtypes = $db->loadObjectList();

		$menu =& JApplication::getMenu('site', $options);
		$mitems = $menu->getMenu();		
		foreach ($mitems as &$item) {
		 	
			if ($item->component == "com_joomleague"){
				$item->name = "--&gt; ".$item->name." &lt;--";
			}
			unset($item);
		 } 

		$childs = array();

		if ($mitems)
		{

			foreach ($mitems as $val)
			{
				$parent 	= $val->parent;
				$list 	= @$childs[$parent] ? $childs[$parent] : array();
				array_push( $list, $val );
				$childs[$parent] = $list;
			}
		}

		$list = JHtml::_('menu.treerecurse', 0, '', array(), $childs, true, 0, 0 );

		$cnt = count( $list );
		$olist = array();
		foreach ($list as $k => $v) {
			$olist[$v->menutype][] = &$list[$k];
		}

		foreach ($mtypes as $type)
		{
			$options[]	= JHtml::_('select.option',  $type->menutype, $type->title , 'value', 'text', true );
			if (isset( $olist[$type->menutype] ))
			{
				$cnt = count( $olist[$type->menutype] );
				for ($x=0; $x < $cnt; $x++)
				{
					$item = &$olist[$type->menutype][$x];
					
					//If menutype is changed but item is not saved yet, use the new type in the list
					if ( JRequest::getString('option', '', 'get') == 'com_menus' ) {
						$currentItemArray = JRequest::getVar('cid', array(0), '', 'array');
						$currentItemId = (int) $currentItemArray[0];
						$currentItemType = JRequest::getString('type', $item->type, 'get');
						if ( $currentItemId == $item->id && $currentItemType != $item->type) {
							$item->type = $currentItemType;
						}
					}
					
					$disable = strpos($node->attributes('disable'), $item->type) !== false ? true : false;
					$options[] = JHtml::_('select.option',  $item->id, $item->id.'&nbsp;&nbsp;&nbsp;' .$item->treename, 'value', 'text', $disable );

				}
			}
		}

		return JHtml::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
	
	function fetchElementOLD($name, $value, &$node, $control_name)
	{
		$options = array();
		$menu =& JApplication::getMenu('site', $options);
		$items = $menu->getMenu();		
		
		$items2 = $menu->getItems("component","com_joomleague");
		JArrayHelper::sortObjects($items2,"menutype");
		foreach ($items2 as &$item) {
			$item->title = $item->name. " (".$item->menutype." - ".$item->component.")";
			unset($item);
		}
		
		JArrayHelper::sortObjects($items,"menutype");
		foreach ($items as &$item) {
			if ($item->component!="com_joomleague"){
				$item->title = $item->name. " (".$item->menutype." - ".$item->component.")";
				$items2[] = $item;
				unset($item);
			}
		}

		return JHtml::_('select.genericlist',  $items2, ''.$control_name.'['.$name.']', '', 'id', 'title', $value, $control_name.$name );
	}
}