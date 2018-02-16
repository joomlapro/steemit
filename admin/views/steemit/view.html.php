<?php

/**

 * @version     1.0.0

 * @package     com_steemit

 * @copyright   Copyright (C) 2018. All rights reserved.

 * @license     GNU General Public License version 2 or later; see LICENSE.txt



 */

// No direct access

defined('_JEXEC') or die;



jimport('joomla.application.component.view');

class SteemitViewSteemit extends JViewLegacy
{
    function display($tpl=null)
	{
	   $this->msg="You are in default view";
	   JToolbarHelper::title(JText::_('Steemit Feed'), 'stack article');
	   JToolbarHelper::preferences('com_steemit');
	   JToolBarHelper::apply('saveconfig');
	  


	   $input = JFactory::getApplication()->input;  
		 $task = $input->get("task");

		$model = $this->getModel();


		

		$configs = $model->getconfig();
		

		$this->assignRef("configs", $configs);

 parent::display($tpl);
	}

}
