<?php
defined('_JEXEC') OR die('Restricted access');

class SteemitModelSteemit1 extends JModelItem
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