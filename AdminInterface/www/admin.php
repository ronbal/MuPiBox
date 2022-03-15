<?php
        $onlinejson = file_get_contents('https://raw.githubusercontent.com/splitti/MuPiBox/main/version.json');
        $dataonline = json_decode($onlinejson, true);
        include ('includes/header.php');
        $change=0;
        $CHANGE_TXT="<div id='lbinfo'><ul id='lbinfo'>";

        if( $_POST['restart_kiosk'] )
                {
                $command = "sudo -i -u dietpi /usr/local/bin/mupibox/./restart_kiosk.sh";
                exec($command, $output, $result );
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Chromium KIOSK restarted</li>";
                }
        if( $_POST['mupibox_update'] )
                {
                $command = "cd; curl https://raw.githubusercontent.com/splitti/MuPiBox/main/update/start_mupibox_update.sh | sudo bash";
                exec($command, $output, $result );
                $string = file_get_contents('/etc/mupibox/mupiboxconfig.json', true);
                $data = json_decode($string, true);
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Update complete to version ".$data["mupibox"]["version"]."</li>";
                }
        if( $_POST['os_update'] )
                {
                $command = "sudo apt-get update -y && sudo apt-get update -y && echo 'Operating System updated!'";
                exec($command, $output, $result );
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>OS is up to date</li>";
                }
        if( $_POST['m3u'] )
                {
                $command = "sudo /usr/local/bin/mupibox/./m3u_generator.sh";
                exec($command, $output, $result );
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Playlists generated</li>";
                }
        if( $_POST['shutdown'] )
                {
                $command = 'bash -c "exec nohup setsid /usr/local/bin/mupibox/./shutdown.sh > /dev/null 2>&1 &"';
                exec($command);
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Shutdown initiated</li>";
                }
        if( $_POST['reboot'] )
                {
                $command = 'bash -c "exec nohup setsid /usr/local/bin/mupibox/./restart.sh > /dev/null 2>&1 &"';
                exec($command);
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Restart initiated</li>";
                }
        if( $_POST['update'] )
                {
                $command = "sudo /usr/local/bin/mupibox/./setting_update.sh";
                exec($command, $output, $result );
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Update complete</li>";
                }
        if( $_POST['spotify_restart'] )
                {
                $command = "sudo /usr/local/bin/mupibox/./spotify_restart.sh";
                exec($command, $output, $result );
                $change=1;
                $CHANGE_TXT=$CHANGE_TXT."<li>Spotify Services are restarted</li>";
                }

        $rc = $output[count($output)-1];

        $CHANGE_TXT=$CHANGE_TXT."</ul>";
?>

                <form class="appnitro" method="post" action="admin.php" id="form">
                                        <div class="description">
                        <h2>MupiBox Administration</h2>
                        <p>Please be sure what you do...</p>
                </div>
                        <ul ><li id="li_1" >
                                        
                                                                <li class="li_1"><h2>MuPiBox Update</h2>
                                                                <p>
                                                                        <table>
                                                                                <tr><td>Current Version:</td>
                                                                                        <td><?php print $data["mupibox"]["version"]; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td>Latest Version:</td><td><?php print $dataonline["version"]; ?></td>
                                                                                </tr>
                                                                        </table>
                                                                        Please notice: The update procedure takes a long time. Don't close the browser and wait for the status message!
                                                                </p>
                                                                <input id="saveForm" class="button_text" type="submit" name="os_update" value="Update OS" />
                                                                <input id="saveForm" class="button_text" type="submit" name="mupibox_update" value="Update MuPiBox" /></li>

                                                                <li class="li_1"><h2>Generate Playlists</h2>
                                                                <p>The Job for generating Playlists runs every <?php print $data["mupibox"]["mediaCheckTimer"];  ?> seconds. If you need the data as soon as possible, start this job...</p>
                                                                <input id="saveForm" class="button_text" type="submit" name="m3u" value="Generate Playlists" /></li>

                                                                <li class="li_1"><h2>Update MuPiBox settings</h2>
                                                                <p>The box only updates some settings after a reboot. Some of these settings can be activated with this operation without reboot. </p>
                                                                <input id="saveForm" class="button_text" type="submit" name="update" value="Update settings" />
                                                                <input id="saveForm" class="button_text" type="submit" name="spotify_restart" value="Restart services" />
                                                                <input id="saveForm" class="button_text" type="submit" name="restart_kiosk" value="Restart Chromium-Kiosk" /></li>
                                
                                                                <li class="li_1"><h2>Control MuPiBox</h2>
                                                                <p>Restart or shutdown the boxs...</p>
                                                                <input id="saveForm" class="button_text" type="submit" name="reboot" value="Reboot MuPiBox" />
                                                                <input id="saveForm" class="button_text" type="submit" name="shutdown" value="Shutdown MuPiBox" /></li>

                                                                <li class="li_1"><h2>Backup and restore MuPiBox-settings</h2>
                                                                <p>Coming soon...</p></li>
                                
                                                                <li class="li_1">                                               <?php
                                                        if( $change )
                                                                {
                                                                print "<div id='savestategood'><p>" . $rc . "</p></div>";
                                                                }
                                                ?>
                </li>


                        </ul>
                </form>
<?php
        include ('includes/footer.php');
?>
