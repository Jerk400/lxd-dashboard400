<?php

if (!empty($_SERVER['PHP_AUTH_USER'])) {
  
  //Instantiate the GET variables
  if (isset($_GET['remote']))
    $remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
  if (isset($_GET['operation']))  
    $operation = filter_var(urldecode($_GET['operation']), FILTER_SANITIZE_STRING);
  if (isset($_GET['project']))
    $project = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
  if (isset($_GET['action']))
    $action = filter_var(urldecode($_GET['action']), FILTER_SANITIZE_STRING);

  //Instantiate the POST variable
  if (isset($_POST['json']))  
    $json = $_POST['json'];

  //Set the curl variables
  $cert = "/var/lxdware/data/lxd/client.crt";
  $key = "/var/lxdware/data/lxd/client.key";

  //Query DB to find remote
  $db = new SQLite3('/var/lxdware/data/sqlite/lxdware.sqlite');
  $db_results = $db->query("SELECT * FROM lxd_hosts WHERE id = $remote LIMIT 1");

  while($res = $db_results->fetchArray()){
    $url = "https://" . $res['host'] . ":" . $res['port'];

    //Run the matching action
    switch ($action) {
      case "deleteOperation":
        $url = $url . "/1.0/operations/" . $operation;
        $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X DELETE $url");
        break;
      case "loadOperation":
        $url = $url . "/1.0/operations/" . $operation;
        $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X GET $url");
      break;
    }
  }

  echo $results;

}
else {
  echo '{"error": "not authenticated", "error_code": "401", "metadata": {"err": "not authenticated", "status_code": "401"}}';
}

?>