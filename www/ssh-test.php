<?php


if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect("192.168.1.5", 22))){
    echo "fail: unable to establish connection\n";
} else {
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, "test", "test")) {
        echo "fail: unable to authenticate\n";
    } else {
        // allright, we're in!
        echo "okay: logged in...\n";
 
        // execute a command
        if (!($stream = ssh2_exec($con, "ls -al /" ))) {
            echo "fail: unable to execute command\n";
        } else {
            // collect returning data from command
            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream,4096)) {
                $data .= $buf;
            }
            fclose($stream);
            echo print_r($data);
        }
    }
}

