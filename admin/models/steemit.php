<?php



defined('_JEXEC') or die;



jimport('joomla.application.component.modellist');



class SteemitModelSteemit extends JModelList 
{

public function __construct($config = array()) {

        if (empty($config['filter_fields'])) {

            $config['filter_fields'] = array(

                

            );

        }



        parent::__construct($config);

    }
    public function getconfig()

	{  

	    $db = JFactory::getDBO();
		$query = "SELECT * FROM #__steemit_config WHERE id = 1";
		$db->setQuery($query);
		$result = $db->LoadObject();

		$res = json_decode($result->config);


		if(!isset($res->downloadid))
		{
			 $res->downloadid = "";	
		}
		
		
		return $res;

	}
}