<?php

date_default_timezone_set('Europe/Stockholm');

class ConfigParser {
  function __construct($configPath){
    $this->settings = array();
    if(file_exists($configPath)){
      foreach(file($configPath) as $line){
	$parts = array_map("trim", explode("=", $line));
	if(count($parts) == 2){
	  $this->settings[ $parts[0] ] = str_replace('"', "", $parts[1]);
	}
      }
    }else{
      $this->settings = null;
    }
  }

  function isValidConfig(){
    return $this->settings != null;
  }

  function get($key){
    if($this->settings == null)
      return "";

    if(array_key_exists($key, $this->settings))
      return $this->settings[$key];
    else
      return "";
  }

}

function getEnabledServers(){
  $servers = array(); 
  
  foreach(scandir("/etc/systemd/system/multi-user.target.wants/") as $file){
    if(preg_match("/minecraftd@(.*?)\.service/", $file, $matches) == 1){
      $servers[]  = $matches[1];
    }
  }
  
  return $servers;
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
<script type="text/javascript">
function toggle(name){
  elem = document.getElementById(name);
  if(elem.style.display == "none"){
    elem.style.display = "block";
  }else{
    elem.style.display = "none";
  }
}
</script>
   </head>
   <body>
    <h1>Status report</h1>
    <table cellspacing="5">
      <tr>
        <th></th>
        <th>Server name</th>
        <th>Address</th>
        <th>Port</th>
        <th>Status</th>
      <tr>

<?php

foreach(getEnabledServers() as $server){
  $config = new ConfigParser("/srv/minecraft/$server/server.properties");

  $extra = null;
  if(isOnline($server) && $config->get("enable-query") == "true" && $config->get("query.port") != ""){
    include_once "PHP-Minecraft-Query/MinecraftQuery.class.php";
    
    $Query = new MinecraftQuery( );

    try
    {
      $Query->Connect( 'localhost', intval($config->get("query.port")) );

      $info = $Query->GetInfo();
      $extra .= "<b>Query data</b><br />";
      $extra .= "Description: " . $info["HostName"] . "<br />";
      $extra .= "Server software: " . $info["Software"] . " " . $info["Version"] . "<br />";
      $extra .= "Map name: " . $info["Map"] . "<br />";
      $extra .= "Game type: " . $info["GameType"] . "<br />";
      $extra .= "Player count: " . $info["Players"] . "/" . $info["MaxPlayers"] . "<br />";

      $players = $Query->GetPlayers();
      if(!empty($players))
	$extra .= "<br />Players: ". implode(",", $players) . "<br/>";
    }
    catch( MinecraftQueryException $e )
    {
        $extra .= "<br/>" . $e->getMessage( ) . "<br/>";
    }
  }

  if(file_exists("/srv/minecraft/rotateinfo-$server")){
    $info = file("/srv/minecraft/rotateinfo-$server");

    $last = intval((time() - intval($info[2])) / 60);
    
    preg_match("/([\d.]*)(\w?)/", $info[3], $parts);
    $keys = array("h" => 3600, "m" => 60, "s" => 1);
    $tmp = intval($info[2]) + ($parts[1] * $keys[$parts[2]]);

    $next = intval(($tmp - time()) / 60);

    if($next >= 0){

      $extra .= "<br/><b>Map rotation is used</b><br/>";
      $extra .= "Switched to " . $info[1] . " from " . $info[0] . " $last minutes ago <br/>";
      $extra .= "Will switch to next one in $next minutes <br/>";
    
      if(file_exists("/srv/minecraft/$server/worlds.conf")){
	$extra .= "Worlds: " . implode(", ", file("/srv/minecraft/$server/worlds.conf")) . "<br />";
      }
    }else{
      $extra .= "<br/><b>Warning: Change time is in the past! Check world changing script!!!</b><br/>";
    }
  }

  if($config->get("white-list") == "true"){
    $extra .= "<br /><b>White listing is in effect</b>  ";
    
    $wplayers = @file("/srv/minecraft/$server/white-list.txt");
    if(!empty($wplayers))
      $extra .= "<br /><i>Allowed players: </i>" . implode(", ", $wplayers);
    $extra .= "<br />";
  }

  if(file_exists("overview/". $server . "-" . $config->get("level-name"))){
    $finfo = stat("overview/". $server . "-" . $config->get("level-name"));
    $rendertime =  date("Y-m-d H:i:s", $finfo["mtime"]);
    $extra .= "<br /><a href=\"overview/\" target=\"_blank\"><b>World overview</b></a> (Rendered $rendertime)<br />";
  }

  echo "<tr>";
  echo "<td>";
  
  if($extra){
    echo "<a href=\"#\" onClick=\"toggle('row-$server');\"><b>+</b></a>";
  }

  echo "</td>";
  echo "<td>$server</td>";
  echo "<td>nyclon.crabdance.com (" . $_SERVER["SERVER_ADDR"] . ")</td>";
  echo "<td>" . $config->get("server-port") . "</td>";
  echo "<td><font color=\"" . (isOnline($server) ? "green\">ONLINE" : "red\">OFFLINE") . "</font></td>";
  echo "</tr>";

  if($extra){
    echo "<tr><td></td><td colspan=\"4\"> <div id=\"row-$server\" style=\"display:none;\">";
    if($extra)
      echo $extra;

    echo "<br /></div></td></tr>";
  }
}

?>

    </table>
  </body>
</html>
