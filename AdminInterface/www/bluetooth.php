<?php
        /*
        'https://gist.github.com/yejun/2c1a070a839b3a7b146ede8a998b5495    !!!!!
        discoverable on
        pairable on
        agent on
        default-agent
        scan on
        */

        $change=0;
        include ('includes/header.php');
		
        if( $_POST['pair_selected'] )
			{
			$command = "sudo /usr/local/bin/mupibox/./pair_bt.sh ".$_POST['bt_device'];
			exec($command, $output, $result );
			$change=1;			
			}
		

        if( $_POST['scan_new'] )
                {
                /*$command = "sudo hcitool scan > /tmp/bt_scan";*/
                $command = "sudo -u dietpi /usr/local/bin/mupibox/./scan_bt.sh";
                exec($command, $output, $result );
                $change=1;
                }

        if( $_POST['change_bt'] == "turn on" )
                {
                $command = "sudo -u dietpi /usr/local/bin/mupibox/./start_bt.sh";
                exec($command, $output, $result );
                $change=1;
                }
        if( $_POST['change_bt'] == "turn off" )
                {
                $command = "sudo -u dietpi /usr/local/bin/mupibox/./stop_bt.sh";
                exec($command, $output, $result );
                $change=1;
                }

        $command = "sudo -u dietpi bluetoothctl show | grep 'Powered: yes'";
        exec($command, $btoutput, $btresult );
        if( $btoutput[0] )
                {
                $bt_state = "ON";
                $change_bt = "turn off";
				
				$command = "sudo -u dietpi bluetoothctl devices";
				exec($command, $devoutput, $devresult );
                }
        else
                {
                $bt_state = "OFF";
                $change_bt = "turn on";
                }
?>

                <form class="appnitro"  method="post" action="bluetooth.php">
                                        <div class="description">
                        <h2>MupiBox bluetooth settings</h2>
                        <p> Set up bluetooth connections...</p>
                </div>
                        <ul ><li id="li_1" >
                                        
                                                                <li class="li_1"><h2>Bluetooth power state</h2>
                                                                <p>
                                                                <?php 
                                                                echo "Bluetooth power state: <b>".$bt_state."</b>".$smboutput[0];
                                                                ?>
                                                                </p>
                                                                <input id="saveForm" class="button_text" type="submit" name="change_bt" value="<?php print $change_bt; ?>" /></li>



                 <li id="li_1" >
                <div>
                     <input id="saveForm" class="button_text" type="submit" name="scan_new" value="Scan for devices" /><br/>
                        <select id="bt_device" name="bt_device" class="element text medium">
<?php
        if( $_POST['scan_new'] )
        {
                                                $string = fopen('/tmp/bt_scan','r' );
                                                $bt=1;
                                                while (($line = fgetcsv($string, 0, "\t")) !== false) {
                                                        if($bt > 1)
                                                                {
                                                                print "<option value='".$line[1]."'>".$line[2]."</option>";
                                                                }
                                                        $bt++;
                                                }
        }
?>
</select>
               
                                <input id="saveForm" class="button_text" type="submit" name="pair_selected" value="Pair selected device" />
</div>
                </li>
				<li>
				<div class="description">
                        <h2>Paired Devices</h2>
                        <p><?php 
				foreach($devoutput as $device)
				{
					print $device."<br />";
				}
				?></p>
                </div>
				</li>
                        </ul>
                </form>
        </div>
<?php
        include ('includes/footer.html');
?>
