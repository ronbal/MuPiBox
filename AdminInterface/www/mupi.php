<?php
 $change=0;
 $CHANGE_TXT="<div id='lbinfo'><ul id='lbinfo'>";
 include ('includes/header.php');

	if($_POST['stop_sleeptimer'])
		{
		$command = "sudo pkill -f \"sleep_timer.sh\"";
		exec($command);
		$command = "sudo rm /tmp/.time2sleep";
		exec($command);
		$change=3;
		$CHANGE_TXT=$CHANGE_TXT."<li>Sleeptimer stopped</li>";
		}

	if($_POST['powerofftimer'])
		{
		$timerSleepingTime=$_POST['powerofftimer']*60;
		$command = "sudo nohup /usr/local/bin/mupibox/./sleep_timer.sh ".$timerSleepingTime."  > /dev/null 2>&1 &";
		exec($command);
		$change=3;
		$CHANGE_TXT=$CHANGE_TXT."<li>".$_POST['powerofftimer']." minutes sleeptimer started</li>";
		//sudo pkill -f "sleep_timer.sh"
		}

	if( $_POST['change_netboot'] == "activate for next boot" )
		{
		$command = "sudo /boot/dietpi/func/dietpi-set_software boot_wait_for_network 1";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Wait for Network on boot is enabled</li>";
		}
	else if( $_POST['change_netboot'] == "disable" )
		{
		$command = "sudo /boot/dietpi/func/dietpi-set_software boot_wait_for_network 0";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Wait for Network on boot is disabled</li>";
		}

	if( $_POST['change_warnings'] == "disable" )
		{
		$command = "sudo sed -i -e 's/avoid_warnings=1//g' /boot/config.txt && sudo head -n -1 /boot/config.txt > /tmp/config.txt && sudo mv /tmp/config.txt /boot/config.txt";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Warning Icons disabled [restart necessary]</li>";
		}
	else if( $_POST['change_warnings'] == "enable" )
		{
		$command = "echo 'avoid_warnings=1' | sudo tee -a /boot/config.txt";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Warning Icons enabled [restart necessary]</li>";
		}

	if( $_POST['change_turbo'] == "disable" )
		{
		$command = "sudo su - dietpi -c \". /boot/dietpi/func/dietpi-globals && G_SUDO G_CONFIG_INJECT 'initial_turbo' 'initial_turbo=0' /boot/config.txt\"";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Inital Turbo disabled</li>";
		}
	else if( $_POST['change_turbo'] == "enable" )
		{
		$command = "sudo su - dietpi -c \". /boot/dietpi/func/dietpi-globals && G_SUDO G_CONFIG_INJECT 'initial_turbo' 'initial_turbo=30' /boot/config.txt\"";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>Inital Turbo enabled</li>";
		}

	if( $_POST['change_swap'] == "disable" )
		{
		$command = "sudo /boot/dietpi/func/dietpi-set_swapfile 0";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>SWAP disabled</li>";
		}
	else if( $_POST['change_swap'] == "enable" )
		{
		$command = "sudo /boot/dietpi/func/dietpi-set_swapfile 1";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>SWAP enabled</li>";
		}

	if ($_POST['change_cpug'])
		{
		$command = "sudo su - dietpi -c \". /boot/dietpi/func/dietpi-globals && G_SUDO G_CONFIG_INJECT 'CONFIG_CPU_GOVERNOR=' 'CONFIG_CPU_GOVERNOR=".$_POST['cpugovernor']."' /boot/dietpi.txt\"";
		$test=exec($command, $output, $result );
		$command = "sudo /boot/dietpi/func/dietpi-set_cpu";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>CPU Governor changet to  ".$_POST['cpugovernor']."</li>";
		}

	if( $_POST['change_sd'] == "activate for next boot" )
		{
		$command = "echo 'dtoverlay=sdtweak,overclock_50=100' | sudo tee -a /boot/config.txt";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>SD Overclocking activated [restart necessary]</li>";
		}
	else if( $_POST['change_sd'] == "disable" )
		{
		$command = "sudo sed -i -e 's/dtoverlay=sdtweak,overclock_50=100//g' /boot/config.txt && sudo head -n -1 /boot/config.txt > /tmp/config.txt && sudo mv /tmp/config.txt /boot/config.txt";
		exec($command, $output, $result );
		$change=1;
		$CHANGE_TXT=$CHANGE_TXT."<li>SD Overclocking disabled [restart necessary]</li>";
		}

	$command = "sudo bash -c \"[[ ! -f '/etc/systemd/system/dietpi-postboot.service.d/dietpi.conf' ]] || echo 1\"";
	exec($command, $netbootoutput, $netbootresult );

	if( $netbootoutput[0] )
		{
		$netboot_state = "active";
		$change_netboot = "disable";
		}
	else
		{
		$netboot_state = "disabled";
		$change_netboot = "activate for next boot";
		}

	$command = "sudo /usr/bin/cat /boot/config.txt | /usr/bin/grep 'dtoverlay=sdtweak,overclock_50=100'";
	exec($command, $sdoutput, $sdresult );

	if( $sdoutput[0] )
		{
		$sd_state = "active";
		$change_sd = "disable";
		}
	else
		{
		$sd_state = "disabled";
		$change_sd = "activate for next boot";
		}
		
 if( isset($_POST['thisvolume']) )
  {
	$tvcommand = "sudo su dietpi -c '/usr/bin/amixer sget Master | grep \"Right:\" | cut -d\" \" -f7 | sed \"s/\\[//g\" | sed \"s/\\]//g\" | sed \"s/\%//g\"'";
	$tvresult = exec($tvcommand, $tvoutput);
	if($_POST['thisvolume'] != $tvoutput[0])
		{ 
		$command="sudo su dietpi -c '/usr/bin/amixer sset Master " . $_POST['thisvolume'] . "%'";
		$set_volume = exec($command, $output );
		$CHANGE_TXT=$CHANGE_TXT."<li>Volume: " . $_POST['thisvolume'] . "%</li>";
		$change=2;
		}
  }
 if( isset($_POST['newbrightness']) )
  {
	$command = "cat /sys/class/backlight/rpi_backlight/brightness";
	$thisbrightness = exec($command, $tboutput);

	if( $_POST['newbrightness'] != $tboutput[0] )
		{
		$brcommand="sudo su - -c 'echo " . $_POST['newbrightness'] . " > /sys/class/backlight/rpi_backlight/brightness'";
		$set_brightness = exec($brcommand, $broutput );
		$CHANGE_TXT=$CHANGE_TXT."<li>Brightness: " . $_POST['newbrightness'] . "</li>";
		$change=2;
		}
  }
 if( $data["mupibox"]["physicalDevice"]!=$_POST['audio'] && $_POST['audio'])
  {
  $data["mupibox"]["physicalDevice"]=$_POST['audio'];
  $command = "sudo /boot/dietpi/func/dietpi-set_hardware soundcard '" . $_POST['audio'] . "'";
  $change_soundcard = exec($command, $output, $change_soundcard );
  $CHANGE_TXT=$CHANGE_TXT."<li>Soundcard changed to  ".$data["mupibox"]["physicalDevice"]." [reboot is necessary]</li>";
  $change=2;
  }
 if( $data["mupibox"]["host"]!=$_POST['hostname'] && $_POST['hostname'])
  {
  $data["mupibox"]["host"]=$_POST['hostname'];
  $command = "sudo /boot/dietpi/func/change_hostname " . $_POST['hostname'];
  $change_hostname = exec($command, $output, $change_hostname );
  $command = "sudo su dietpi -c '/usr/local/bin/mupibox/./set_hostname.sh'";
  exec($command);
  $CHANGE_TXT=$CHANGE_TXT."<li>Hostname changed to  ".$data["mupibox"]["host"]." [reboot is necessary]</li>";
  $change=1;
  }
 if( $_POST['theme'] != $data["mupibox"]["theme"] && $_POST['theme'] )
  {
  $data["mupibox"]["theme"]=$_POST['theme'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New Theme  ".$data["mupibox"]["theme"]."  is active</li>";
  $change=1;
  }
 if( $_POST['tts'] != $data["mupibox"]["ttsLanguage"] && $_POST['tts'] )
  {
  $data["mupibox"]["ttsLanguage"]=$_POST['tts'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New TTS Language  ".$data["mupibox"]["ttsLanguage"]." [reboot is necessary]</li>";
  $command = "sudo rm /home/dietpi/MuPiBox/tts_files/*.mp3";
  exec($command, $output, $result );
  $change=2;
  }

 if( $data["shim"]["ledBrightnessMax"]!=$_POST['ledmaxbrightness'] && $_POST['ledmaxbrightness']!= "" )
  {
  $data["shim"]["ledBrightnessMax"]=$_POST['ledmaxbrightness'];
  $CHANGE_TXT=$CHANGE_TXT."<li>LED standard brightness set to ".$data["shim"]["ledBrightnessMax"]."</li>";
  $change=2;
  }

 if( $data["shim"]["ledBrightnessMin"]!=$_POST['ledminbrightness'] && $_POST['ledminbrightness']!= "" )
  {
  $data["shim"]["ledBrightnessMin"]=$_POST['ledminbrightness'];
  $CHANGE_TXT=$CHANGE_TXT."<li>LED standard brightness set to ".$data["shim"]["ledBrightnessMin"]."</li>";
  $change=2;
  }

 if( $data["mupibox"]["maxVolume"]!=$_POST['maxVolume'] && $_POST['maxVolume'] )
  {
  $data["mupibox"]["maxVolume"]=$_POST['maxVolume'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Max Volume is set to ".$data["mupibox"]["maxVolume"]." [reboot is necessary]</li>";
  $change=2;
  }

 if( $data["mupibox"]["startVolume"]!=$_POST['volume'] && $_POST['volume'] )
  {
  $data["mupibox"]["startVolume"]=$_POST['volume'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Start Volume is set to ".$data["mupibox"]["startVolume"]."</li>";
  $change=2;
  }
 if($data["timeout"]["idlePiShutdown"]!=$_POST['idlePiShutdown'] && isset($_POST['idlePiShutdown']) && $_POST['idlePiShutdown'] >= 0)
  {
  $data["timeout"]["idlePiShutdown"]=$_POST['idlePiShutdown'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Idle Shutdown Time is set to ".$data["timeout"]["idlePiShutdown"]."</li>";
  $change=2;
  }
 if($data["timeout"]["idleDisplayOff"]!=$_POST['idleDisplayOff'] && isset($_POST['idleDisplayOff']) && $_POST['idleDisplayOff'] >= 0)
  {
  $data["timeout"]["idleDisplayOff"]=$_POST['idleDisplayOff'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Idle Time for Display is set to ".$data["timeout"]["idleDisplayOff"]."</li>";
  $change=2;
  }
 if( $data["timeout"]["pressDelay"]!=$_POST['pressDelay'] && $_POST['pressDelay'] != "" )
  {
  $data["timeout"]["pressDelay"]=$_POST['pressDelay'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Press Button delay set to ".$data["shim"]["ledPin"]. "</li>";
  $change=2;
  }
 if( $data["shim"]["ledPin"]!=$_POST['ledPin'] && $_POST['ledPin'])
  {
  $data["shim"]["ledPin"]=$_POST['ledPin'];
  $CHANGE_TXT=$CHANGE_TXT."<li>New GPIO for Power-LED set to ".$data["shim"]["ledPin"]. "  [reboot is necessary]</li>";
  $change=2;
  }
 if( $data["chromium"]["resX"]!=$_POST['resX'] && $_POST['resX'])
  {
  $data["chromium"]["resX"]=$_POST['resX'];
  $CHANGE_TXT=$CHANGE_TXT."<li>X-Resolution set to ".$data["chromium"]["resX"]."</li>";
  $change=1;
  }
 if( $data["chromium"]["resY"]!=$_POST['resY'] && $_POST['resY'])
  {
  $data["chromium"]["resY"]=$_POST['resY'];
  $CHANGE_TXT=$CHANGE_TXT."<li>Y-Resolution set to ".$data["chromium"]["resY"]."</li>";
  $change=1;
  }
  if( $data["mupibox"]["maxVolume"] < $data["mupibox"]["startVolume"] )
	{
	$data["mupibox"]["startVolume"]=$data["mupibox"]["maxVolume"];
	$CHANGE_TXT=$CHANGE_TXT."<li>Start Volume is set to ".$data["mupibox"]["maxVolume"]." because of Max Volume</li>";
	$change=2;
	}
 if( $change == 1 )
  {
   $json_object = json_encode($data);
   $save_rc = file_put_contents('/tmp/.mupiboxconfig.json', $json_object);
   exec("sudo chmod 755 /etc/mupibox/mupiboxconfig.json");
   exec("sudo mv /tmp/.mupiboxconfig.json /etc/mupibox/mupiboxconfig.json");
   exec("sudo /usr/local/bin/mupibox/./setting_update.sh");
   exec("sudo -i -u dietpi /usr/local/bin/mupibox/./restart_kiosk.sh");
  }
 if( $change == 2 )
  {
   $json_object = json_encode($data);
   $save_rc = file_put_contents('/tmp/.mupiboxconfig.json', $json_object);
   exec("sudo mv /tmp/.mupiboxconfig.json /etc/mupibox/mupiboxconfig.json");
   exec("sudo /usr/local/bin/mupibox/./setting_update.sh");
  }
$CHANGE_TXT=$CHANGE_TXT."</ul></div>";
?>

<form class="appnitro" name="mupi" method="post" action="mupi.php" id="form">
<div class="description">
<h2>MupiBox settings</h2>
<p>This is the central configuration of your MuPiBox...</p>
</div>

	<details>
		<summary><i class="fa-solid fa-clock"></i> Timer settings</summary>
		<ul>
			<li id="li_1" >
				<h2>Sleeptimer</h2>
				<?php
				if (file_exists("/tmp/.time2sleep")) {
						?>
					MuPiBox will shut down  after (hh:mm:ss):</br>
					<div id="app"></div>
				</li>
				<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text_red" type="submit" name="stop_sleeptimer" value="Stop running timer" />
			</li>

						<?php
				} else { ?>
				How many minutes to shut down MuPiBox (default 60 minutes):
				<br/>
				<div>
				<input class="element text large" name="powerofftimer" type="range" min="15" max="360" step="15.0" value="60" oninput="this.nextElementSibling.value = this.value"><output></output> minutes
				</div>
			</li>

			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Start Power-Off Timer" />
			</li>
				<?php } ?>
		</ul>
	</details>

	<details>
		<summary><i class="fa-solid fa-screwdriver-wrench"></i> System settings</summary>
		<ul>
			<li id="li_1" >
				<h2>Hostname </h2>
				<div>
				<input id="hostname" name="hostname" class="element text medium" type="text" maxlength="255" value="<?php
				print $data["mupibox"]["host"];
				?>"/>
				</div>
			</li>
			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
			</li>
			
			<li class="li_1"><h2>Overclock SD Card</h2>
				<p>
				Just for highspeed SD Cards. You can damage data or the microSD itself!
				</p>
				<p>
				<?php
				echo "Overclocking state: <b>".$sd_state."</b>";
				?>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_sd" value="<?php print $change_sd; ?>" />
			</li>

			<li class="li_1"><h2>Wait for Network on boot</h2>
				<p>
				Speeds up the boot time, but sometimes the boot process is to fast and you have to wait for the network to be ready... Try it, if disabling this option works for you!
				</p>
				<p>
				<?php
				echo "Wait for Network on boot: <b>".$netboot_state."</b>";
				?>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_netboot" value="<?php print $change_netboot; ?>" />
			</li>

			<li class="li_1"><h2>Initial Turbo</h2>
				<p>
				Initial Turbo avoids throtteling sometimes...
				</p>
				<p>
				<?php
				$command = "cat /boot/config.txt | grep initial_turbo | cut -d '=' -f 2";
				$turbo = exec($command, $output);
				echo "Turbo seconds: <b>".$turbo."</b>";
				if($turbo == 0)
					{
					$change_turbo="enable";
					}
				else
					{
					$change_turbo="disable";
					}

				?>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_turbo" value="<?php print $change_turbo; ?>" />
			</li>

			<li class="li_1"><h2>CPU Governor</h2>
				<p>
				Try powersave (Limits CPU frequency to 600 MHz - Helps to avoid throtteling).
				</p>
				<p>
				<div>
				<select id="cpugovernor" name="cpugovernor" class="element text medium">
				<?php
				$command = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_available_governors";
				$governors = exec($command, $output);
				$cpug = explode(" ", $governors);
				$command = "cat /boot/dietpi.txt | grep CONFIG_CPU_GOVERNOR | cut -d '=' -f 2";
				$current_governor = exec($command, $output);

				foreach($cpug as $key) {
				if( $key == $current_governor )
					{
					$selected = " selected=\"selected\"";
					}
				else
					{
					$selected = "";
					}
				print "<option value=\"". $key . "\"" . $selected  . ">" . $key . "</option>";
				}
				?>
				"</select>
				</div>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_cpug" value="Save CPU Governor" />
			</li>

			<li class="li_1"><h2>Disable Warnings (Throtteling Warning)</h2>
				<p>
				Enables or disables the lightning icon (warning)! In worst case, this option can cause you loose all your data.
				</p>
				<p>
				<?php
				$command = "cat /boot/config.txt | grep 'avoid_warnings=1'";
				$warnings = exec($command, $output);
				if($warnings == "")
					{
					$change_warnings="enable";
					}
				else
					{
					$change_warnings="disable";
					}
				?>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_warnings" value="<?php print $change_warnings; ?>" />
			</li>

			<li class="li_1"><h2>SWAP</h2>
				<p>
				Enables or disables SWAP!
				</p>
				<p>
				<?php
				$command = "cat /boot/dietpi.txt | grep AUTO_SETUP_SWAPFILE_SIZE= | cut -d '=' -f 2";
				$currentswapsize = exec($command, $output);
				if($currentswapsize == 0)
					{
					$change_swap="enable";
					}
				else
					{
					$change_swap="disable";
					}

				echo "SWAP Size: <b>".$currentswapsize." MB</b>";
				?>
				</p>
				<input id="saveForm" class="button_text" type="submit" name="change_swap" value="<?php print $change_swap; ?>" />
			</li>
		</ul>
	</details>

	<details>
		<summary><i class="fa-solid fa-radio"></i> MuPiBox settings</summary>
		<ul>
			<li id="li_1" >
				<h2>Theme </h2>
				<div>
				<select id="theme" name="theme" class="element text medium" onchange="switchImage();">
				<?php
				$Themes = $data["mupibox"]["installedThemes"];
				foreach($Themes as $key) {
				if( $key == $data["mupibox"]["theme"] )
				{
				$selected = " selected=\"selected\"";
				}
				else
				{
				$selected = "";
				}
				print "<option value=\"". $key . "\"" . $selected  . ">" . $key . "</option>";
				}
				?>
				"</select>
				</div>
				<div class="themePrev"><img src="images/<?php print $data["mupibox"]["theme"]; ?>_2.0.0.png" width="250" height="150" name="selectedTheme" /></div>

			</li>
			<li id="li_1" >
				<h2>TTS Language </h2>
				<div>
				<select id="tts" name="tts" class="element text medium">
				<?php
				$language = $data["mupibox"]["googlettslanguages"];
				foreach($language as $key) {
				if( $key['iso639-1'] == $data["mupibox"]["ttsLanguage"] )
				{
				$selected = " selected=\"selected\"";
				}
				else
				{
				$selected = "";
				}
				print "<option value=\"". $key['iso639-1'] . "\"" . $selected  . ">" . $key['Language'] . "</option>";
				}
				?>
				"</select>
				</div>
			</li>

			<li id="li_1" >
				<h2>Idle Time to Shutdown </h2>
				<div>
				<input id="idlePiShutdown" name="idlePiShutdown" class="element text medium" type="number" maxlength="255" value="<?php
				print $data["timeout"]["idlePiShutdown"];
				?>"/>
				</div>
			</li>


			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
			</li>
		</ul>
	</details>


	<details>
		<summary><i class="fa-solid fa-display"></i> Display settings</summary>
		<ul>
			<li id="li_1" >
				<h2>Brightness (from 0 to 255)</h2>
				<div>
				<input class="element text medium" name="newbrightness" type="range" min="0" max="255" step="51.0" value="<?php 
					$tbcommand = "cat /sys/class/backlight/rpi_backlight/brightness";
					$tbrightness = exec($tbcommand, $boutput);
					echo $boutput[0];
				?>" list="steplist2" oninput="this.nextElementSibling.value = this.value"><output></output>
			<datalist id="steplist2">
				<option>0</option>
				<option>51</option>
				<option>102</option>
				<option>153</option>
				<option>204</option>
				<option>255</option>
			</datalist><output></output>
				</div>

			</li>
			
			<li id="li_1" >
				<h2>Idle Display Off Timeout </h2>
				<div>
				<input class="element text medium" name="idleDisplayOff" type="range" min="0" max="120" step="1.0" value="<?php 
					echo $data["timeout"]["idleDisplayOff"]
				?>" oninput="this.nextElementSibling.value = this.value"><output></output>
				</div>
			</li>

			<li id="li_1" >
				<h2>Display Resolution X </h2>
				<div>
				<input id="resX" name="resX" class="element text medium" type="number" maxlength="255" value="<?php
				print $data["chromium"]["resX"];
				?>"/>
				</div>
			</li>
			<li id="li_1" >
				<h2>Display Resolution Y </h2>
				<div>
				<input id="resY" name="resY" class="element text medium" type="number" maxlength="255" value="<?php
				print $data["chromium"]["resY"];
				?>"/>
				</div>
			</li>

			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
			</li>
		</ul>
	</details>
	<details>
		<summary><i class="fa-solid fa-volume-high"></i> Audio settings</summary>
		<ul>
			<li id="li_1" >
				<h2>Audio device / Soundcard </h2>
				<div>
				<select id="audio" name="audio" class="element text medium">
				<?php
				$audio = $data["mupibox"]["AudioDevices"];
				foreach($audio as $key) {
				if( $key['tname'] == $data["mupibox"]["physicalDevice"] )
				{
				$selected = " selected=\"selected\"";
				}
				else
				{
				$selected = "";
				}
				print "<option value=\"". $key['tname'] . "\"" . $selected  . ">" . $key['ufname'] . "</option>";
				}
				?>
				"</select>
				</div>
			</li>
			<li id="li_1" >
				<h2>Volume (in 5% Steps)</h2>
				<div>
				<p><b>PLEASE NOTE:</b> If you adjust the volume here, the volume indicator on the display will not be updated!</p>
				<input class="element text medium" name="thisvolume" type="range" min="0" max="100" step="5.0" value="<?php 
					$command = "sudo su dietpi -c '/usr/bin/amixer sget Master | grep \"Right:\" | cut -d\" \" -f7 | sed \"s/\\[//g\" | sed \"s/\\]//g\" | sed \"s/\%//g\"'";
					$VolumeNow = exec($command, $voutput);
					echo $voutput[0];
				?>"list="steplist" oninput="this.nextElementSibling.value = this.value"><output></output>
			<datalist id="steplist">
				<option>0</option>
				<option>5</option>
				<option>10</option>
				<option>15</option>
				<option>20</option>
				<option>25</option>
				<option>30</option>
				<option>35</option>
				<option>40</option>
				<option>45</option>
				<option>50</option>
				<option>55</option>
				<option>60</option>
				<option>65</option>
				<option>70</option>
				<option>75</option>
				<option>80</option>
				<option>85</option>
				<option>90</option>
				<option>95</option>
				<option>100</option>
			</datalist>

				</div>

			</li>
			<li id="li_1" >
				<h2>Volume after power on </h2>
				<div>
				<select id="volume" name="volume" class="element text medium">
				<?php
				$volume = $data["mupibox"]["startVolume"];
				for($i=0; $i <= 100; $i=$i+10) {
				if( $i == $data["mupibox"]["startVolume"] )
				{
				$selected = " selected=\"selected\"";
				}
				else
				{
				$selected = "";
				}
				print "<option value=\"". $i . "\"" . $selected  . ">" . $i . "</option>";
				}
				?>
				"</select>
				</div>
			</li>

			<li id="li_1" >
				<h2>Set max volume</h2>
				<div>
				<select id="maxVolume" name="maxVolume" class="element text medium">
				<?php
				$volume = $data["mupibox"]["maxVolume"];
				for($i=0; $i <= 100; $i=$i+10) {
				if( $i == $data["mupibox"]["maxVolume"] )
				{
				$selected = " selected=\"selected\"";
				}
				else
				{
				$selected = "";
				}
				print "<option value=\"". $i . "\"" . $selected  . ">" . $i . "</option>";
				}
				?>
				"</select>
				</div>
			</li>			
			

			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
			</li>
		</ul>
	</details>
	<details>
		<summary><i class="fa-solid fa-power-off"></i> Power-on settings</summary>
		<ul>
		
			<li id="li_1" >
				<h2>Power-Off Button delay </h2>
				<div>
				<input class="element text medium" name="pressDelay" type="range" min="0" max="5" step="1.0" value="<?php 
					echo $data["timeout"]["pressDelay"]
				?>" oninput="this.nextElementSibling.value = this.value"><output></output>
				</div>
			</li>

			<li id="li_1" >
				<h2>LED GPIO OnOffShim </h2>
				<div>
				<input id="ledPin" name="ledPin" class="element text medium" type="number" maxlength="255" value="<?php
				print $data["shim"]["ledPin"];
				?>"/>
				</div>
			</li>

			<li id="li_1" >
				<h2>LED Brightness normal (from 0 to 100%)</h2>
				<div>
				<input class="element text medium" name="ledmaxbrightness" type="range" min="0" max="100" step="1.0" value="<?php 
					echo $data["shim"]["ledBrightnessMax"]
				?>" oninput="this.nextElementSibling.value = this.value"><output></output>
				</div>
			</li>

			<li id="li_1" >
				<h2>LED Brightness dimmed (from 0 to 100%)</h2>
				<div>
				<input class="element text medium" name="ledminbrightness" type="range" min="0" max="100" step="1.0" value="<?php 
					echo $data["shim"]["ledBrightnessMin"]
				?>" oninput="this.nextElementSibling.value = this.value"><output></output>
				</div>
			</li>

			<li class="buttons">
				<input type="hidden" name="form_id" value="37271" />

				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
			</li>
		</ul>
	</details>
</form><p>

<?php
        //$time2sleep=readfile("/tmp/.time2sleep");
        $time2sleep=fgets(fopen("/tmp/.time2sleep", 'r'));
?>


<script>
// Credit: Mateusz Rybczonec

const FULL_DASH_ARRAY = 283;
const WARNING_THRESHOLD = 10;
const ALERT_THRESHOLD = 5;

const COLOR_CODES = {
  info: {
    color: "green"
  },
  warning: {
    color: "orange",
    threshold: WARNING_THRESHOLD
  },
  alert: {
    color: "red",
    threshold: ALERT_THRESHOLD
  }
};

const TIME_LIMIT = <?php echo $time2sleep; ?>;
let timePassed = 0;
let timeLeft = TIME_LIMIT;
let timerInterval = null;
let remainingPathColor = COLOR_CODES.info.color;

document.getElementById("app").innerHTML = `
<div class="base-timer">
  <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <g class="base-timer__circle">
      <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
      <path
        id="base-timer-path-remaining"
        stroke-dasharray="283"
        class="base-timer__path-remaining ${remainingPathColor}"
        d="
          M 50, 50
          m -45, 0
          a 45,45 0 1,0 90,0
          a 45,45 0 1,0 -90,0
        "
      ></path>
    </g>
  </svg>
  <span id="base-timer-label" class="base-timer__label">${formatTime(
    timeLeft
  )}</span>
</div>
`;

startTimer();

function onTimesUp() {
  clearInterval(timerInterval);
}

function startTimer() {
  timerInterval = setInterval(() => {
    timePassed = timePassed += 1;
    timeLeft = TIME_LIMIT - timePassed;
    document.getElementById("base-timer-label").innerHTML = formatTime(
      timeLeft
    );
    setCircleDasharray();
    setRemainingPathColor(timeLeft);

    if (timeLeft === 0) {
      onTimesUp();
    }
  }, 1000);
}

function formatTime(time) {
  const hours = Math.floor(time / 60 / 60);
  minutes = Math.floor(time / 60 - hours * 60);
  let seconds = time % 60;

  if (seconds < 10) {
    seconds = `0${seconds}`;
  }
  if (minutes < 10) {
    minutes = `0${minutes}`;
  }


  return `${hours}:${minutes}:${seconds}`;
}

function setRemainingPathColor(timeLeft) {
  const { alert, warning, info } = COLOR_CODES;
  if (timeLeft <= alert.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(warning.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(alert.color);
  } else if (timeLeft <= warning.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(info.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(warning.color);
  }
}

function calculateTimeFraction() {
  const rawTimeFraction = timeLeft / TIME_LIMIT;
  return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
}

function setCircleDasharray() {
  const circleDasharray = `${(
    calculateTimeFraction() * FULL_DASH_ARRAY
  ).toFixed(0)} 283`;
  document
    .getElementById("base-timer-path-remaining")
    .setAttribute("stroke-dasharray", circleDasharray);
}
</script>
<?php
 include ('includes/footer.php');
?>
