<?php
defined('_JEXEC') OR die('Restricted access');

class SteemitModelSteemit extends JModelItem
{
protected $message;

public function getMsg()
{
	if(!isset($this->message))
	{
	   $this->message='Steemit Feeds!';
	}
	return $this->message;
}

}