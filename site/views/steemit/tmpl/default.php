
 <?php
defined('_JEXEC') or die('Restricted access');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Steemit Feeds</title>
       	<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive();
$itemId = $menu->id;
$params = $menu->getParams($itemId);

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_steemit/assets/css/style.css');
if ($params->get('load_fontawesome', 1)) 
{
	$document->addStyleSheet('https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css');
}
?>
<?php
$permlink = false;
     if (!$params->get('feed_author', ''))
		{
			$html = '<div class="mn_steem_feed_error"><p>'.JText::_('Steemit author not found fill the parameters in configuration').'</p></div>';
			return $html;
		}
		$datenow = JFactory::getDate()->toISO8601();
		$datenow = substr($datenow, 0, strpos($datenow, '+'));
		$mn_sf_author = trim($params->get('feed_author', ''));
		$mn_sf_datenow = $datenow;
		$last_post_permlink = '';
		$posts_per_page = 4;
		$included_tags = array();
		if ($params->get('feed_tags_include', '') && $params->get('feed_tags_include', ''))
		{
			$included_tags = array_map( 'trim', explode( ',', $params->get('feed_tags_include', '') ) );
		}
		$excluded_tags = array();
		if ($params->get('feed_tags_exclude', '') && $params->get('feed_tags_exclude', ''))
		{
			$excluded_tags = array_map( 'trim', explode( ',', $params->get('feed_tags_exclude', '') ) );
		}
		$excluded_posts = array();
		if ($params->get('feed_posts_exclude', '') && $params->get('feed_posts_exclude', ''))
		{
			$excluded_posts = array_map( 'trim', explode( PHP_EOL, $params->get('feed_posts_exclude', '') ) );
		}
		$posts = array();
		$previous_batch_permlink = '';
		$c = 0;
		
		while ($c < $posts_per_page)
		{
		 	$raw_url = 'https://api.steemjs.com/get_discussions_by_author_before_date?author='.$mn_sf_author.'&startPermlink='.$last_post_permlink.'&beforeDate='.$mn_sf_datenow.            '&limit='.$posts_per_page;
			$temp = file_get_contents($raw_url);
			json_decode($temp);
			$isjson = (json_last_error() == JSON_ERROR_NONE);
			if ($isjson)
			{
		    	$batch = json_decode($temp, false);
				$batch_count = count($batch);
				$last_post = $batch[$batch_count - 1];
				$last_post_permlink = $last_post->permlink;
				if ($last_post_permlink === $previous_batch_permlink) break;
				$previous_batch_permlink = $last_post->permlink;
				foreach ($batch as $item)
				{
					if ($permlink && $permlink === $item->permlink)
					{
						continue;
					}
					if (in_array($item->permlink, $excluded_posts))
					{
						continue;
					}
					
					if (count($posts) >= $posts_per_page)
					{
						break;	
					}
				
					$metadata = json_decode($item->json_metadata, false);
					if (!in_array($item, $posts))
					{
						if (empty($included_tags) && empty($excluded_tags))
						{
							$posts[] = $item;
						}
						else
						{
							if (empty($included_tags) && !empty($excluded_tags))
							{
								if (empty(array_intersect($metadata->tags, $excluded_tags)))
								{
									$posts[] = $item;
								}
							}
							else if (!empty($included_tags) && empty($excluded_tags))
							{
								if (!empty(array_intersect($metadata->tags, $included_tags)))
								{
									$posts[] = $item;
								}
							}
							else if (!empty($included_tags) && !empty($excluded_tags))
							{
								if (!empty(array_intersect($metadata->tags, $included_tags)) && empty(array_intersect($metadata->tags, $excluded_tags)))
								{
									$posts[] = $item;
								}
							}
						}
					}
				}
			}
			$c = count($posts);
			if ($c === 0 && $posts_per_page === 1)
			{
				$posts_per_page = 2;
			}
			 $app = JFactory::getApplication();
		$jinput = $app->input;  
		$safe_ajax = $jinput->get('ajax', false, 'INT');
		    $ajax = false;	
			$page = false;
		$referral_code = $params->get('feed_referral', '') ? '?r='.$params->get('feed_referral') : '';
		//$feed_show_images = true;
		$feed_show_images = $params->get('feed_show_image', true);
		$feed_image_size = '0x0';
		//$feed_fallback_image = '';	
		//$detailBoxTitle = true;
		$feed_show_title = $params->get('feed_show_title', true);
		$feed_title_limit = 20;
		//$detailBoxIntrotext = true;
		$feed_show_body = $params->get('feed_show_body', true);
		//$feed_introtext_limit = 40;
		$feed_body_limit=$params->get('feed_body_limit', 40);
		$detailBoxDate = true;
		$detailBoxCategory = true;
		$detailBoxTags = true;
		$detailBoxAuthor = true;
		$detailBoxAuthorRep = true;
		$detailBoxReward = true;
		$detailBoxVotes = true;
		$detailBoxComments = true;
		$items=array();
		foreach ($posts as $key => $item) 
		{
			$item->title = trim(stripslashes( $item->title ));
			$item->body  = trim(stripslashes( $item->body ));
			$item->short_title = (int)$feed_title_limit;		   
		//Formatted the $item->created data
			$date = strtotime($item->created);
			$now = JFactory::getDate()->format("Y-m-d H:i:s");
			$now = strtotime($now);
			$since = $now - $date;
			$chunks = array(
			array(60 * 60 * 24 * 365 , JText::_('year ago'), JText::_('years ago')),
			array(60 * 60 * 24 * 30 , JText::_('month ago'), JText::_('months ago')),
			array(60 * 60 * 24 * 7, JText::_('week ago'), JText::_('weeks ago')),
			array(60 * 60 * 24 , JText::_('day ago'), JText::_('days ago')),
			array(60 * 60 , JText::_('hour ago'), JText::_('hours ago')),
			array(60 , JText::_('minute ago'), JText::_('minutes ago')),
			array(1 , JText::_('second ago'), JText::_('seconds ago'))
		);
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name_1 = $chunks[$i][1];
			$name_n = $chunks[$i][2];
			if (($count = floor($since / $seconds)) != 0) {
				break;
			}
		}
		$print = ($count == 1) ? '1 '.$name_1 : "$count {$name_n}";
		$item->formatted_date = $print;
		//Formatted the $item->author_reputation data 
		$reputation=$item->author_reputation;
		if ($reputation == null) return $reputation;
		$is_neg = $reputation < 0 ? true : false;
		$rep = $is_neg ? abs($reputation) : $reputation;
		$str = $rep;
		$leadingDigits = (int)substr($str, 0, 4);
		$log = log($leadingDigits) / log(10);
		$n = strlen((string)$str) - 1;
		$out = $n + ($log - (int)$log);
		if (!($out)) $out = 0;
		$out = max($out - 9, 0);
		$out = ($is_neg ? -1 : 1) * $out;
		$out = $out * 9 + 25;
		$out = (int)$out;
		$item->author_reputation = $out;
		//Formatted the $item->body data 
	     $text = $item->body;
	     $num_words=$feed_body_limit;
	     $more=null;
	     if ( null === $more ) 
		{
			$more = '&hellip;';
		}
		$text = strip_tags( $text );
		$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
		$original_text = $text;
		$sep = ' ';
		if ( count( $words_array ) > $num_words ) 
		{
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} 
		else 
		{
			$text = implode( $sep, $words_array );
		}
 		$item->short_body = $text;

		//Formatted the $item->total_reward data 
			$total_payout_value = round((float)$item->total_payout_value, 2);
			$curator_payout_value = round((float)$item->curator_payout_value, 2);
			$pending_payout_value = round((float)$item->pending_payout_value, 2);
			$total_pending_payout_value = round((float)$item->total_pending_payout_value, 2);
			$item->total_reward = number_format(round(($total_payout_value + $curator_payout_value + $pending_payout_value + $total_pending_payout_value), 2), 2);
		//Formatted the $item->net_votes data 
			$item->votes = $item->net_votes;
		//Formatted the $item->replies_count data 
			$item->replies_count = 0;
			$author=$item->author;
			$replies = file_get_contents('https://api.steemjs.com/getContentReplies?parent='.$author.'&parentPermlink='.$permlink);
		
		$isjson = json_decode($replies);
		
		if ($isjson)
		{
			$replies = json_decode($replies, false);
			$childrenCount = 0;
			foreach ($replies as $reply)
			{
				$childrenCount += $reply->children;
			}
			
			$repliesCount = count($replies) + $childrenCount;
			
			$item->replies_count=$repliesCount;	
		}
		
	
			$metadata = json_decode($item->json_metadata, false);
			
			$item->tags = $metadata->tags;
			array_shift($item->tags);
			
			if (isset($metadata->image))
			{
				$raw_image = $metadata->image;
				if (array_key_exists('0', $raw_image))
				{
					$item->image = 'https://steemitimages.com/'.$feed_image_size.'/'.$raw_image[0];
				}
			}
			else
			{
				if ($feed_fallback_image)
				{
					$item->image = $feed_fallback_image;
				}
			}
												
			$items[] = $item;
		}
	}
	
?>
<body>
	<style type="text/css">

		.leftdiv
		{

		  width:25%;
		  float:left;	
		}
		.rightdiv
		{
			width:75%;
			float:left;
			text-align: left;
			
		}
		.item_body
			{
				width:75%;
				float:left;
				text-align: left;
				padding-left: 10px;
			}
			.item_title
			{
				width:75%;
				float:left;
				text-align: left;
				padding-left: 10px;
			}
	@media only screen and (max-width : 620px)
	 {
 			
			.data_div
			{
	          width:100%;
	          float:inherit;

			}
			
			.leftdiv
			{

			  width:100%;
			  float:left;	
			}
			.rightdiv
			{
				width:100%;
				float:left;
				text-align: left;
				
			}
			
			.currency
			{
				margin-left: inherit;

			}
			.gap-right 
			{
	  			margin-right: 10px; 
			}
			.date_author
			{
				margin-bottom: 10px;
				margin-top: 20px;
								
			}
			.img
			{
				width:100%;
				height: 100%;
			}
			.li
			{
				overflow: hidden;
			}
			.image img
			{
				width: 100%;
				max-width: 100%;
			}
			.item_body
			{
				width:100%;
				float:inherit;
				text-align: inherit;
				padding-left: 10px;
			}
		.item_title
			{
				width:100%;
				float:left;
				text-align: left;
				padding-left: 10px;
			}
			
		}
	</style>


<?php


 foreach($items as $item)
	 {
	
$url = 'https://steemit.com'.$item->url;
$url_author = 'https://steemit.com/@'.$item->author;
	 
?>

    <div style="text-align: right;"> <!-- First div Start -->
		
		<div class="data_div"><!-- Data div Start -->

			<article class="li">
				<?php // Image
				if ($feed_show_images && isset($item->image) && $item->image)
				{ ?>
				<div class="leftdiv">
				 	<a target="_blank" href="<?php echo $url; ?>">
						<img src="<?php echo $item->image;?>" class="img"/>
					</a>

				</div>
			<?php
				}
			?>
			
				<div><!-- content div Start -->
					<?php // Image
					if ($feed_show_images && isset($item->image) && $item->image)
					{ ?>
					<div class="date_author"><!-- DATE_AUTHOR div Start -->
			    		<span class="author_name"><?php echo $item->author;?><?php echo '('.$item->author_reputation.')';?></span>
			    		<span class="author_category"><?php echo 'in '.$item->category.' .';?></span>
			    		<span class="time_post"><?php echo $item->formatted_date; ?></span>	
					</div><!-- DATE_AUTHOR div end -->
					<?php
					}
					?>
					<div class="item_title">
			  		<h3>
			  			<?php 
			  			if($feed_show_title)
			  			{			  							
			  			echo '<a target="_blank" href="'.$url.'">'.$item->title.'</a>';
			  			}
			  			?>
			  		</h3>
					
			  		<?php
			  		if($feed_show_body)
			  		{
			  		 $text = strip_tags( $item->short_body);
			  		
					$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
					// strip urls
					$str = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $text); 
					$str = preg_replace('/[!]*[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[.jpg()]/i', ' ', $str);
					$str = preg_replace('/-/',"", $str);
					$str = str_replace("![ ]","",$str );
					$str = rtrim($str,"; ");
					$str = ltrim($str,"[]");
					$str = trim($str,"]");
			  		?>
			  		<?php echo $str; ?>
			  		<div>
			  			<?php
			  				echo '<a target="_blank" href="'.$url.'">Read More &raquo;</a>';
			  			}
			  			?>
			  		</div>
			  		</div>

			  		
			  		<?php // Image
						if ($feed_show_images && isset($item->image) && $item->image)
					{
					 ?>
			  		 	<div class="currency"><!-- currency div Start -->
							<a target="_blank" href="<?php echo $url_author; ?>">
								<?php echo '$'.$item->total_reward.' |';?>
							</a>
							<i class="fa fa-chevron-up"></i>
								<?php echo $item->votes.' |';?>
							<i class="fa fa-comments"></i>
								<?php echo $item->replies_count;?>
						</div><!-- currency div end -->
						<?php
						}
						?>
			  	</div><!-- content div end -->
			</article>
		</div><!-- Data div end -->
		
     </div><!-- First div end -->
     <hr />
		        
		<?php
	 }
	 ?>
	
	</body>
	</html>
	
	
	







