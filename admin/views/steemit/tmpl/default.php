
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Steemit</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="components/com_steemit/assets/css/vmuikit.css">
	</head>
<?php
/**
 * @version     1.0.0
 * @package     com_steemit
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * ©Copyright   2018 JoomlaPro.com
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');
JHtml::_('bootstrap.tooltip');


$downloadid = $this->configs->downloadid;
 
 
$xml = simplexml_load_file('components/com_steemit/steemit.xml');
$version = (string)$xml->version;
$creationDate = $xml->creationDate;
?>
<style type="text/css">
.noborder td
{
  border:0px none !important;
}
</style>
    <div class="">
    <div class="span6">
       <h2>Steemit Feed</h2>
<form class="uk-form" name="adminForm" id="adminForm" method="post">
  <?php
 
 if(empty($downloadid))
 {
 
   $displaystring = "Please add your Download ID for easy updates";
   $divclass = "alert alert-warning";   

 }
 if(!empty($downloadid))
 {
  
 			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
			->select(array('e.extension_id', 'e.type', 'e.name', 'e.manifest_cache', 'us.update_site_id', 'us.enabled', 'us.extra_query'))
			->from($db->quoteName('#__extensions', 'e'))
			->join('LEFT OUTER', $db->quoteName('#__update_sites_extensions', 'use') . ' ON (' . $db->quoteName('e.extension_id') . ' = ' .
				$db->quoteName('use.extension_id') . ')')
			->join('LEFT OUTER', $db->quoteName('#__update_sites', 'us') . ' ON (' . $db->quoteName('us.update_site_id') . ' = ' .
				$db->quoteName('use.update_site_id') . ')')
			->where($db->quoteName('element') . ' = ' . $db->quote('com_steemit'));
			
			

			$db->setQuery($query);
			$component = $db->LoadObject();
			
			
			$extension                 = new stdClass;
			$extension->update_site_id = $component->update_site_id;
			$extension->name           = $component->name;
			$extension->type           = 'extension';

			// Link to the PRO version updater XML:
			$extension->location = 'https://joomlapro.com/index.php?option=com_rdsubs&view=updater&cat=14&type=1&format=xml';
			$extension->enabled              = 1;
			$extension->last_check_timestamp = 0;
			$extension->extra_query          = 'key=' . $downloadid;
			if ($component->update_site_id)
			{
				JFactory::getDbo()->updateObject('#__update_sites', $extension, 'update_site_id');
			}

 
      $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://joomlapro.com/vmuikit.php?downloaid=".$downloadid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    unset($ch);
      $resposnedata = json_decode($response);
  
    if(!empty($resposnedata))
    {
      $fromdate_str = strtotime($resposnedata->fromdate);
      $todate_str = strtotime($resposnedata->todate);
      $currdate_str = time();
      if($todate_str > $currdate_str)
      {
       $displaystring =  "Subscription is active and expires : ".$resposnedata->todate;
       $divclass = "alert alert-success";
      }
      else
      {
       $displaystring =  "Subscription has expired! <a href='https://joomlapro.com/product/vmuikit'>Renew your subscription!</a>";
       $divclass = "alert alert-error"; 
      }
    }
    else
    {
     $displaystring =  "Subscription ID is Invalid. Enter Valid ID";
     $divclass = "alert alert-error"; 
    }
    
   
 }

 
?>

 <div class="block">
        <div class="block-title">Download ID - Auto Updates</div>
        <div class="block-data">
              <div class="well <?php echo $divclass; ?>" style="margin-bottom:0px;">
                 <table class="table" style="margin-bottom:0px;">
                     <tr class="row1 noborder">
                           <td><label style="width:113px; margin-top:7px" class="hasTooltip" data-original-title="<strong>Download ID</strong>"><?php echo JText::_("Download ID"); ?></label></td>
                            <td>
                            <input style="margin-bottom:0px;width: 80%" type="text" id="params[downloadid]" name="params[downloadid]" value="<?php echo $downloadid; ?>">
                            <p style="margin-bottom:0;"><?php echo $displaystring; ?></p>
                            </td>
                    </tr> 
                 </table>
                </div>
          </div>
   </div>


  <div class="span12" style="margin-left:0px;">
		<div style="clear:both;"></div><!--Closed-->
   			<div class="left" style="border:1px solid #e5e5e5; border-radius:5px; margin-top:5px;">
    		 	<div style="padding:0px 0px 10px 0px;">
        			<div class="block">
              			<div class="block-title">Message</div><!--Closed-->
              				<div class="block-data">
              		 			<div class="well <?php echo $divclass; ?>" style="margin-bottom:0px;">
              		 				<label > Please add a menu item "Steemit Feed" to your menu. There you will also see you settings. </label>	<br/>		
               					</div>
               				</div>	
              		</div>
            	</div>
			</div>
	</div>						
	
</div>
  <input type="hidden" id="params[version]" name="params[version]" value=<?php echo $version; ?>>
                
<input type="hidden" value="saveconfig" name="task" />
</form>
  
<div class="span6">

  <div class="block">
    <div class="block-title">Quick Links</div>
    <div class="block-data">
          <div class="icon_div">
                <a class="btn " href="https://joomlapro.com/product/vmuikit">
                    <span class="bigicon icon-cart"></span>
                    <span>Renew subscription</span>
                </a>
           </div>
          <div class="icon_div">
                <a class="btn " href="https://steemit.com">
                    <span class="bigicon icon-feed"></span>
                    <span>Steemit</span>
                </a>
           </div>
           <div class="icon_div">
                <a class="btn " href="https://joomlapro.com">
                    <span class="bigicon icon-joomla"></span>
                    <span>JoomlaPro</span>
                </a>
           </div>
          
     </div>
      <div style="clear:both;"></div>
  </div>
 
    <div class="block">
        <div class="block-title">Component Information</div><!--Closed-->
        <div class="block-data">
           <table class="table table-striped">
               <tr>
              	 <td>Release Date</td>
                 <td><?php echo $creationDate; ?></td>
               </tr>
               <tr>
              	 <td>Installed Version</td>
                 <td> Steemit <?php echo $version; ?></td>
               </tr>
              <tr>
              	 <td>Development</td>
                 <td><a target="_blank" href="https://joomlapro.com">JoomlaPro.com</a></td>
               </tr>
               <tr>
              	 <td>License</td>
                 <td><a href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank">GNU GPL v2</a></td>
               </tr>
           </table>
            
        </div>
    </div>
    
</div>
</div>
<div style="clear:both;"></div>
    <div class="center" style="border:1px solid #e5e5e5; border-radius:5px; margin-top:5px;">
    	<div style="padding:0px 0px 10px 0px;">
    		<a href="https://joomlapro.com"  target="_blank"><img style="width:260px;" src="http://joomlapro.com/images/logo1.png"></a>
    			<p>Developed with love and coffee by <a href="https://joomlapro.com" target="_blank">JoomlaPro.com</a></p>
    				<div id="fb-root"></div><!--Closed-->
    					<script>(function(d, s, id) {
     						var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) return;
                     		js = d.createElement(s); js.id = id;
                     		js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11';
                     		fjs.parentNode.insertBefore(js, fjs);
                     		}(document, 'script', 'facebook-jssdk'));</script>
                     		<div class="fb-like" data-href="https://www.facebook.com/joomlapro" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div><!--Closed-->
        </div>
    </div>
</div>