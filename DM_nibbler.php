<?php 
/** GetSimple CMS Nibbler Verification 
* Web site: http://www.digimute.com/
* @version  1.0
* @author   mike@digimute.com
*
* original by Stephan Gerlach, http://blog.silktide.com/2012/02/verify-your-site-in-nibbler-with-wordpress-plugin/ 
*/


# get correct id for plugin
$thisfile_nibbler = basename(__FILE__, '.php');
$DM_nibbler_config_file=GSDATAOTHERPATH .'nibbler.xml';

# add in this plugin's language file
i18n_merge($thisfile_nibbler) || i18n_merge($thisfile_nibbler, 'en_US');

# register plugin
register_plugin(
  $thisfile_nibbler,
  'Nibbler Verification',
  '1.0',
  'Mike Swan',
  'http://www.digimute.com/',
  'Adds nibbler verification code to a website',
  'plugins',
  'DM_nibbler_init'
);
    
add_action('plugins-sidebar','createSideMenu',array($thisfile_nibbler,'Nibbler Config')); 
add_action('theme-header','DM_add_Nibbler_code',array());


if (file_exists($DM_nibbler_config_file)) {
	$x = getXML($DM_nibbler_config_file);
	$nibblercode = $x->nibblercode;
} else {
	$nibblercode = '';
	$xml = @new SimpleXMLElement('<item></item>');
	$xml->addChild('nibblercode', '');
	$xml->asXML($DM_nibbler_config_file);
}

function DM_add_Nibbler_code(){
	global $nibblercode;
	if ($nibblercode!=''){
		echo "\n".'<meta name="nibbler-site-verification" content="'.$nibblercode.'" />'."\n";
	}
}

//Admin Content
function DM_nibbler_init() {
	global $nibblercode,$DM_nibbler_config_file,$thisfile_nibbler;
	$success=null;$error=null;
	if (isset($_POST['submit'])) {		
		$nibblercode = isset($_POST['nibblercode']) ? $_POST['nibblercode'] : $nibblercode;
	if (!$error) {
			$xml = @new SimpleXMLElement('<item></item>');
			$xml->addChild('nibblercode', $nibblercode);			
			if (!$xml->asXML($DM_nibbler_config_file)) {
				$error = i18n_r($thisfile_nibbler.'/NIBBLER_ERROR');
			} else {
				$x = getXML($DM_nibbler_config_file);
				$nibblercode = $x->nibblercode;
				$success = i18n_r($thisfile_nibbler.'/NIBBLER_SUCCESS');
			}
	
		}
	}

//Main Navigation For Admin Panel
?>

	<h3 class="floated"><?php echo i18n_r($thisfile_nibbler.'/NIBBLER_TITLE'); ?></h3>  <br/><br/>
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		<p><label for="nibblercode" ><?php echo i18n_r($thisfile_nibbler.'/NIBBLER_DESC'); ?></label>
			<input id="nibblercode" name="nibblercode" class="text" value="<?php echo $nibblercode; ?>" />
		</p>
		<p><input type="submit" id="submit" class="submit" value="<?php echo i18n_r($thisfile_nibbler.'/NIBBLER_SAVE'); ?>" name="submit" /></p>
	</form>



<?php	
}

?>