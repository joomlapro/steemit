<?php


defined('_JEXEC') or die('Restricted access');

class SteemitController extends JControllerLegacy
{
	
public function saveconfig()
{
		$input = JFactory::getApplication()->input;
		$post = $input->post->getArray(); 
		$params = $post['params'];

		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__steemit_config WHERE  id = 1";
		$db->setQuery($query);
		$result = $db->LoadObjectList();
		if(empty($result))
		{
		  $db = JFactory::getDBO();
		  $query = "INSERT INTO #__steemit_config  (id, config) VALUES (1, '{}')";
		  $db->setQuery($query);
		  $db->query();
		}
		
		$db = JFactory::getDBO();
		$parameter =  $db->escape(json_encode($params));
		
		$query = "UPDATE #__steemit_config SET config='".$parameter."' WHERE id = 1";
		$db->setQuery($query);
		$db->query();	

		$app = JFactory::getApplication();
		$message = "Settings Successfully Saved!";
		JFactory::getApplication()->enqueueMessage($message);
		$app->redirect("index.php?option=com_steemit", "");


   
}


}