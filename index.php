<?php require_once('conf/config.php') ?>
<?
/*** EVERYBODY FUNCTIONS ***/

// Curl helper function
function curl_get($url)
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}

/*** VIMEO ***/
if (isset($accounts['vimeo']['username']) && $accounts['vimeo']['username'] != '')
{
	$video_bubble = true;
	$vimeo_on = true;
	$api_endpoint = 'http://www.vimeo.com/api/v2/'.$accounts['vimeo']['username'];
	$vimeo_user = simplexml_load_string(curl_get($api_endpoint.'/info.xml'));
	$vimeo_videos = simplexml_load_string(curl_get($api_endpoint.'/videos.xml'));
}

/*** YOUTUBE ***/
if (isset($accounts['youtube']['username']) && $accounts['youtube']['username'] != '')
{
	$video_bubble = true;
	$youtube_on = true;
	$youtube_rss_feed = 'http://gdata.youtube.com/feeds/api/users/'.$accounts['youtube']['username'].'/uploads?v=2';
	$youtube_simple_xml = simplexml_load_file($youtube_rss_feed);
}

/*** TWITTER ***/
if (isset($accounts['twitter']['username']) && $accounts['twitter']['username'] != '')
{
	$twitter_on = true;
	$twitter_xml_feed = 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name='.$accounts['twitter']['username'];
	$twitter_simple_xml = simplexml_load_file($twitter_xml_feed);
	$twitter_status_feed = $twitter_simple_xml->status;
}

/*** FACEBOOK ***/

$page = array_keys($_GET);
//echo $page[0];
?>
<!DOCTYPE html>
<html>
<head>
	<title><? if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name']);} ?> <? if (isset($general['last_name']) && $general['last_name'] != '') {echo strtolower($general['last_name']);} ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="css/splash.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico"> 
	<link rel="SHORTCUT ICON" href="favicon.ico">
	<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
	
	<? if (isset($accounts['vimeo']['username']) || isset($accounts['youtube']['username']))
	{
	?>
	<script type="text/javascript">
		var elementsArray = new Array();
		var nav_items;
		var i = 0;
		$(document).ready(function(){
			elementsArray = $('.content_bubble');
			nav_items = $('#elements ol li').length;
			// You can now specify a page in the uri to go to it first - pceasies.me/?photos
			page = location.href.split('?')[1];
			if(page && page.length > 3) {
				switchto(page)
			};
			$('#pictures').css('display', 'none');
			$("#photos a[rel^='prettyPhoto']").prettyPhoto({theme: 'light_rounded',slideshow:5000, autoplay_slideshow:true});
			$("#videos a[rel^='prettyPhoto']").prettyPhoto({theme: 'light_rounded',slideshow:5000, autoplay_slideshow:false});
			$('#nav li a').click(function() {
				switchto( $(this).text() );	
				return false;
			});
		});
		$(window).load(function() {
			// Waits until the pictures are fully loaded, then removes loader.gif and displays them.
			$('#loading-pics').fadeOut('fast', function() {
				$('#pictures').fadeIn(2000);
			}).html('');
		});
		function switchto( elem ){
			$(elementsArray).css('display', 'none');
			$('#'+elem).fadeIn('slow');
			// This finds the index of the nav li relative to ol and uses that to determine where the slider should be moved to
			// 42px is the base width to align to 'about' 114px is the amount of space to get to the next item
			amount = (42 + (parseInt( $('li').index($('#nav_'+elem)) )*114));
			$('#triangle').stop().animate({marginRight: amount}, 1000);
		}
	</script>
	<script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css">
	<? } ?>
	<style>
		body
		{
			
			<? if(isset($visual_style['background_image']) && $visual_style['background_image'] != '') {echo 'background-image: url('.$visual_style['background_image'].');';} ?>
			
		}
		div#nav, div#nav a
		{
			
			<? if(isset($visual_style['navigation_color']) && $visual_style['navigation_color'] != '') {
				echo('color: '.$visual_style['navigation_color'].';');
				} ?>
		
			<? if(isset($visual_style['navigation_shadows']) && $visual_style['navigation_shadows'] != '') {
				echo 'text-shadow:0px 0px 6px #666;';
				} ?>
			
		}
		<?
		if(!isset($page[0])) {
			echo '#about { display: inline; }';
		}
		?>
	</style>
	<noscript>
		<style>
			#<? if(isset($page[0])) {echo $page[0];}else{echo 'about';}?> {
				display: inline;
			}
			#nav_<? if(isset($page[0])) {echo $page[0];}else{echo 'about';}?> {
				border-bottom: 4px solid white;
			}
			div#triangle {
				display:none;
			}
			.clear {
				clear:both;
			}
		</style>
	</noscript>
</head>
<body>
	<div id="nav">
		<h1>
		<? if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name']);} ?> <? if (isset($general['last_name']) && $general['last_name'] != '') {echo strtolower($general['last_name']);} ?>
		</h1>
		<div id="elements">
			<ol>
				<li id="nav_about"><a href="?about">about</a></li>
				<? if ($images) { ?><li id="nav_photos"><a href="?photos">photos</a></li><? } ?>
				<? if ($videos) { ?><li id="nav_videos"><a href="?videos">videos</a></li><? } ?>
				<? if ($twitter) { ?><li id="nav_twitter"><a href="?twitter">twitter</a></li><? } ?>
			</ol>
			<div id="triangle">
				<img src="images/bubble_triangle_100.png" width="30" height="15">
			</div>
			<div class="clear"></div>
		</div>	
	</div>
	
	
	
	<div id="about" class="content_bubble">
		<h3>about</h3>
		<?=$general['about_me']; ?>
	</div>
	
	<? if ($images) { ?> <!-- Images true/false check added outside of div some the 'photos' line and an empty div won't show up when disabled -->
	<div id="photos" class="content_bubble">
		<h3><? if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>photos</h3>
		<div id="loading-pics"><img src="images/loading.gif"></div>
		<div id="pictures">
		<?php
		if($images) {
			if($accounts['flickr']['username'] !== '') {
				require_once('helpers/flickr.php');
				$accounts['flickr']['username'] = getID($accounts['flickr']['username']); // This turns the username into an id if it isn't one
				$flickr_images = getPhotos($accounts['flickr']['username'], 25);
				foreach($flickr_images as $item) {
					echo '<a href="'.$item['url'].'" rel="prettyPhoto[flickr]"><img src="'.$item['url'].'" class="image-thumb" id="'.$item['title'].'" alt="<a href=\''.$item['link'].'\'>'.$item['title'].'</a>"></a>';
				}
			}
			if($accounts['flickr']['username'] !== '' && $accounts['picasa']['username'] !== '') {
				echo '<hr>';
			}
			if($accounts['picasa']['username'] !== '') {
				require_once('helpers/picasa.php');
				$picasa_images = getPicasaPhotos($accounts['picasa']['username'], 25);
				foreach($picasa_images as $item) {
					echo '<a href="'.$item['url'].'" rel="prettyPhoto[picasa]"><img src="'.$item['url'].'" class="image-thumb" id="'.$item['title'].'" alt="<a href=\''.$item['link'].'\'>'.$item['title'].'</a>"></a>';
				}
			}
		}
		?>
		</div>	
		<p class="more">
		<?php
			if($accounts['flickr']['username'] !== '') echo '<a href="http://flickr.com/photos/'.$accounts["flickr"]["username"].'">Flickr...</a><br>';
			if($accounts['picasa']['username'] !== '') echo '<a href="http://picasaweb.google.com/'.$accounts["picasa"]["username"].'">Picasa...</a>';
		?>
		</p>
		
	</div>
	<? } ?>
	
	<div id="videos" class="content_bubble">
		<? if ($videos) { ?>
		<h3><? if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>videos</h3>
		<?
		if (isset($general['about_videos']) && $general['about_videos'] != '')
		{
			echo '<p>'.$general['about_videos'].'</p>';
		}
		?>
			<? if ($accounts['vimeo']['username'] !== '') { ?>
			<!-- Vimeo -->
			<div id="vimeo_videos">
				<?php foreach ($vimeo_videos->video as $video): ?>
	            <a href="<?=$video->url ?>%26width%3D640" rel="prettyPhoto" title="<?=$video->title ?>"><img src="<?=$video->thumbnail_small ?>" width="120" height="90"></a>
				<?php endforeach; ?>
			</div>
			<? } ?>
			
			<? if ($accounts['youtube']['username'] !== '') { ?>
			<!-- YouTube -->
			<div id="youtube_videos">
				<?
				// iterate over entries in feed
				foreach ($youtube_simple_xml->entry as $entry)
				{
					// Namespace info...
					$media = $entry->children('http://search.yahoo.com/mrss/');

					// Get the video URL...
					$attrs = $media->group->player->attributes();
					$video_url = $attrs['url'];
					$video_url = preg_replace( '/&/', '%26', $video_url );
					$video_title = $media->group->title; 

					// Get the video thumbnail...
					$attrs = $media->group->thumbnail[0]->attributes();
					$thumbnail = $attrs['url'];
					
					echo '<a href="'.$video_url.'%26width%3D640" rel="prettyPhoto" title="'.$video_title.'"><img src="'.$thumbnail.'" width="120" height="90"></a>';
				}
				?>
			</div>
			<? } ?>
		<? } ?>
	</div>
	
	<div id="twitter" class="content_bubble">
		<h3><? if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>tweets</h3>
			<div id ="twitter_feed">
				<? if ($twitter) { ?>
				<?
				foreach ($twitter_simple_xml->status as $tweet)
				{
					echo '<p class="tweet"><img src="'.$tweet->user->profile_image_url.'" style="float: left; margin: 0 8px 8px 0;" height="60" width="60">'.$tweet->text.'<br><span style="font-size: 10px; font-style: italic;">'.$tweet->created_at.'</span></p><hr>';
				}
				?>
				<p class="more">
					<a href="http://twitter.com/<?=$accounts['twitter']['username'] ?>">More...</a>
				</p>
				<? } ?>
			</div>
	</div>
	
	<div id="footer">
		Lifehacker.me by <a href="http://lifehacker.com">Lifehacker</a>.
	</div>
	
	<div id="spacer" style="padding-bottom: 12px; float: right; clear: both;">&nbsp;</div>
	
</body>
</html>