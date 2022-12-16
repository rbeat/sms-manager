<!DOCTYPE html>
<?php
   /**
   * SMS Manager - Online portal to check received SMS messages from Twilio
   * 
   * @author Rudy G. (a.k.a. R.Beat)
   * @copyright 2022 R.Beat (https://rbeat.gq)
   */
   date_default_timezone_set("UTC");
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
?>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>SMS Manager</title>
      <link href="favicon.ico" rel="icon" type="image/x-icon" />
      <style>
         .t{
         text-align: center !important;
         }
         input, select {
         margin-bottom: 5px;
         }
         a {
         text-decoration: none;
         }
         .t {
         font-family: arial, sans-serif;
         border-collapse: collapse;
         width: 100%;
         }
         .entity {
         border: 1px solid #dddddd;
         text-align: center;
         padding: 8px;
         }
         .tr:nth-child(even) {
         background-color: #dddddd;
         }
         kbd {
         font-style: normal;
         background-color: #eee;
         border-radius: 3px;
         border: 1px solid #b4b4b4;
         box-shadow: 0 1px 1px rgba(0, 0, 0, .2), 0 2px 0 0 rgba(255, 255, 255, .7) inset;
         color: #333;
         display: inline-block;
         font-size: .85em;
         font-weight: 700;
         line-height: 1;
         padding: 2px 4px;
         white-space: nowrap;
         transition: 0.3s;
         white-space: pre-wrap;       /* Since CSS 2.1 */
         white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
         white-space: -pre-wrap;      /* Opera 4-6 */
         white-space: -o-pre-wrap;    /* Opera 7 */
         word-wrap: break-word;     /* Internet Explorer 5.5+ */
         }
      </style>
   </head>
   <body>
      <h1>SMS Manager</h1>
      <h4>(c) <?php echo date("Y"); ?> Rudy G. (a.k.a. R.Beat) | 🇺🇦 Слава Україні!</h4>
      <p style="color:grey">
         Server time: <?php echo date(DATE_RFC822); ?>
      </p>
      <hr/>
      <?php
         $conf = include_once('config.php');
         $db = mysqli_connect($conf['DB_host'],$conf['DB_log'],$conf['DB_pass'],$conf['DB_name']) or die('Error accessing DB.');
         
         if(!empty($_POST['type']) && $_POST['type'] === "check_sms"){
            if(!empty($_POST['number']) && !empty($_POST['pass'])){
               $number  = mysqli_escape_string($db, $_POST['number']);
               $pass    = hash('sha256', mysqli_escape_string($db, $_POST['pass']));
               $sql     = "SELECT * FROM `numbers` WHERE `number` = $number AND `pass` = '" . $pass . "';";
               $us      = mysqli_query($db, $sql);
               if (mysqli_num_rows($us) <= 0) {
                  die("<h3>Incorrect phone number and/or password. Please, try again.</h3><br><a href='.'>< Back</a>");
               }else{
                  $sql = "SELECT * FROM `msg` WHERE `receiver` = $number;";
                  $us = mysqli_query($db, $sql);
                  ?>
                     <table>
                        <h3>SMS history for <?=$number ?></h3>
                        <tr>
                           <td><button onclick="window.location.href = '.'">&larr; Back</button> | </td>
                           <td><button onclick="location.reload()">↻ Refresh</button></td>
                        </tr>
                     </table>
                     <hr>
                  <?php
                  if (mysqli_num_rows($us) <= 0) {
                     echo("<h3>Empty.</h3>");
                  }else{
                     ?>
                     <table class="t">
                        <tr class='tr'>
                           <th class="entity">Sender</th>
                           <th class="entity">Date</th>
                           <th class="entity">Text</th>
                        </tr>
                     <?php
                     while ($row = mysqli_fetch_assoc($us)) {
                        echo "<tr class='tr'>";
                        echo "<td class='entity'><kbd>" . $row['sender'] . "</kbd></td>";
                        echo "<td class='entity'>" . date(DATE_RFC822, $row['date']) . "</td>";
                        echo "<td class='entity'><pre>" . $row['text'] . "</pre></td>";
                        echo "</tr>";
                     }
                     ?>
                     </table>
                     <?php
                  }
               }
            }
            die();
         }
         
      ?>
      <form action="." method="post">
         <h2>Check current SMS messages</h2>
         <input type="hidden" name="type" value="check_sms"/>
         <table>
            <tr>
               <td><label>Phone number w/o +<sup style="color: red">*</sup>:</label></td>
               <td class="tdi"><input type="number" name="number" onkeydown="isNumberKey(this)" required /></td>
            </tr>
            <tr>
               <td><label>Password<sup style="color: red">*</sup>:</label></td>
               <td class="tdi"><input type="password" name="pass" required/></td>
            </tr>
         </table>
         <br>
         <input type="submit" />
      </form>
      <script>
         function isNumberKey(evt) {
             var charCode = evt.which ? evt.which : evt.keyCode;
             if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
             return true;
         }
      </script>
   </body>
</html>