<?php

function getEnabledServers(){
  $servers = array(); 
  
  foreach(scandir("/etc/systemd/system/multi-user.target.wants/") as $file){
    if(preg_match("/minecraftd@(.*?)\.service/", $file, $matches) == 1){
      $servers[]  = $matches[1];
    }
  }
  
  return $servers;
}

function getPort($srvname){
  if(!file_exists("/srv/minecraft/$srvname/server.properties"))
    return "<!-- NO PROPERTIES FILE -->";

  $config = file_get_contents("/srv/minecraft/$srvname/server.properties");
  if(!preg_match("/server-port=(\d*)/", $config, $matches))
    return "<!-- NO MATCH -->";

  return $matches[1];
}

function isOnline($srvname){
  return file_exists("/srv/minecraft/$srvname/server.log.lck");

  $output = exec("systemctl status minecraftd@" . $srvname . ".service");
  echo "<!-- systemctl status minecraftd@" . $srvname . ".service = $output -->";
  return stristr($output, "inactive") === false;
}

?>

<html>
   <head>
     <title>Minecraft server status</title>
   </head>
   <body>
    <h1>Status report</h1>
    <table cellspacing="5">
      <tr>
        <th>Server name</th>
        <th>Address</th>
        <th>Port</th>
        <th>Status</th>
      <tr>

<?php

foreach(getEnabledServers() as $server){
  echo "<tr>";
  echo "<td>$server</td>";
  echo "<td>nyclon.crabdance.com (" . $_SERVER["SERVER_ADDR"] . ")</td>";
  echo "<td>" . getPort($server) . "</td>";
  echo "<td><font color=\"" . (isOnline($server) ? "green\">ONLINE" : "red\">OFFLINE") . "</font></td>";
  echo "</tr>";
}

?>

    </table>
  </body>
</html>
