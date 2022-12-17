<?php
   /**
   * SMS Manager - Online portal to check received SMS messages from Twilio
   * 
   * @author Rudy G. (a.k.a. R.Beat)
   * @copyright 2022 R.Beat (https://rbeat.gq)
   */
   date_default_timezone_set("UTC");
   ini_set('error_reporting', E_ALL);
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   ini_set('log_errors', 'On');
   ini_set('error_log', 'logs/php_errors.log');

   $conf = include_once('config.php');
   $db = mysqli_connect($conf['DB_host'],$conf['DB_log'],$conf['DB_pass'],$conf['DB_name']) or die('Error accessing DB.');
   $data = file_get_contents('php://input');
   
   // DEBUG
   file_put_contents("logs/cache.log", $data);
   if(isset($data))
   {
     try{
       parse_str($data, $d);
       if($d['SmsStatus'] === "received"){
         $sender   = mysqli_escape_string($db, str_replace("+", "", $d['From']));
         $receiver = mysqli_escape_string($db, str_replace("+", "", $d['To']));
         $text     = mysqli_escape_string($db, $d['Body']);
         $date     = time();
         $sql      = "INSERT INTO `msg` (`sender`, `receiver`, `date`, `text`) VALUES ($sender, $receiver, $date, '" . $text . "');";
            $us       = mysqli_query($db, $sql);
            var_dump(json_encode($us));
            die();
         }
      } catch(exception $e) {
         var_dump(json_encode($e));
         file_put_contents("logs/error.log", json_encode($e) . PHP_EOL . "-----------------" . PHP_EOL, FILE_APPEND);
         die();
      }
   }
?>