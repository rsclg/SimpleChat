<?php

  /*
   * ---------------------------------------------------------------------------
   * Exanto Webchat - Simple Ajax Chat
   * ---------------------------------------------------------------------------
   * Copyright (c) 2007 - Exanto Internet Solutions - http://www.exanto.de/
   * ---------------------------------------------------------------------------
   * This program is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation; either version 2 of the License, or
   * (at your option) any later version.
   *
   * This program is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   *
   * You should have received a copy of the GNU General Public License
   * along with this program; if not, write to the Free Software
   * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
  session_start();  
  
  // config
  define("CHAT_TITLE", "Webchat");
  define("PASSWORD", "ttlgwebchat");
  define("CHAT_FILE", "msgs.txt");
  define("USER_FILE", "user.txt");
  // define icon set directory
  //define("ICON_SET", "smiley_fuge_icons");
  //define("ICON_SET", "smiley_onigiri_riceballs");
  define("ICON_SET", "smiley_the_glassy");
  
	$icons = array(
	":-)" => array("file" => "smiley.png", "tooltip" => "Lächeln"),
	":)" => array("file" => "smiley.png", "tooltip" => "Lächeln"),
	";-)" => array("file" => "smiley-wink.png", "tooltip" => "Zwinkern"),
	";)" => array("file" => "smiley-wink.png", "tooltip" => "Zwinkern"),
	":-D" => array("file" => "smiley-grin.png", "tooltip" => "Grinsen"),
	":D" => array("file" => "smiley-grin.png", "tooltip" => "Grinsen"),
	":-(" => array("file" => "smiley-sad.png", "tooltip" => "Traurig"),
	":(" => array("file" => "smiley-sad.png", "tooltip" => "Traurig"),
	";-(" => array("file" => "smiley-cry.png", "tooltip" => "Weinen"),
	";(" => array("file" => "smiley-cry.png", "tooltip" => "Weinen"),
	":-O" => array("file" => "smiley-yell.png", "tooltip" => "Schreien"),
	":O" => array("file" => "smiley-yell.png", "tooltip" => "Schreien"),
	"&gt;-(" => array("file" => "smiley-mad.png", "tooltip" => "Sauer"), // '>' will be masked with '&gt;'
	"&gt;(" => array("file" => "smiley-mad.png", "tooltip" => "Sauer"), // '>' will be masked with '&gt;'
	":-@" => array("file" => "smiley-evil.png", "tooltip" => "Wütend"),
	":@" => array("file" => "smiley-evil.png", "tooltip" => "Wütend"),
	"&gt;-@" => array("file" => "smiley-twist.png", "tooltip" => "Zornig"), // '>' will be masked with '&gt;'
	"&gt;@" => array("file" => "smiley-twist.png", "tooltip" => "Zornig"), // '>' will be masked with '&gt;'
	":-o" => array("file" => "smiley-frighten.png", "tooltip" => "Erschrocken"),
	":o" => array("file" => "smiley-frighten.png", "tooltip" => "Erschrocken"),
	":-P" => array("file" => "smiley-razz.png", "tooltip" => "Zunge raustrecken"),
	":P" => array("file" => "smiley-razz.png", "tooltip" => "Zunge raustrecken"),
	":-X" => array("file" => "smiley-kiss.png", "tooltip" => "Küssen"),
	":X" => array("file" => "smiley-kiss.png", "tooltip" => "Küssen"),
	"8-|" => array("file" => "smiley-roll.png", "tooltip" => "Ironisch"),
	"8|" => array("file" => "smiley-roll.png", "tooltip" => "Ironisch"),
	"B-)" => array("file" => "smiley-cool.png", "tooltip" => "Cool"),
	"B)" => array("file" => "smiley-cool.png", "tooltip" => "Cool"),
	":-#" => array("file" => "smiley-zipper.png", "tooltip" => "Schweigen"),
	":#" => array("file" => "smiley-zipper.png", "tooltip" => "Schweigen"),
	":-§" => array("file" => "smiley-red.png", "tooltip" => "Verlegen"),
	":§" => array("file" => "smiley-red.png", "tooltip" => "Verlegen"),
	"O:)" => array("file" => "smiley-angel.png", "tooltip" => "Unschuldig"),
	"O)" => array("file" => "smiley-angel.png", "tooltip" => "Unschuldig"),
	":-Z" => array("file" => "smiley-sleep.png", "tooltip" => "Schlafen"),
	":Z" => array("file" => "smiley-sleep.png", "tooltip" => "Schlafen"),
	":-/" => array("file" => "smiley-confuse.png", "tooltip" => "Verwirrt"),
	":/" => array("file" => "smiley-confuse.png", "tooltip" => "Verwirrt"),
	"$-)" => array("file" => "smiley-money.png", "tooltip" => "Geldgierig"),
	"$)" => array("file" => "smiley-money.png", "tooltip" => "Geldgierig"),
	"ccd" => array("file" => "thumb-up.png", "tooltip" => "Daumen hoch"),
	"cd" => array("file" => "thumb-up.png", "tooltip" => "Daumen hoch"),
	"ccq" => array("file" => "thumb-down.png", "tooltip" => "Daumen runter"),
	"cq" => array("file" => "thumb-down.png", "tooltip" => "Daumen runter")
	);
  
  // perform as requested by GET
  if ($_GET['action'] && $_SESSION['auth'] == true) {
    switch ($_GET['action']) {
    case 'logout':
      remove_user($_SESSION['name']);
      session_destroy();
      header("Location: " . $_SERVER['PHP_SELF']);
      break;
    case 'get_messages':
      echo get_messages($_SESSION['last_action']);
      die;
      break;
    /*case 'get_userlist':
      echo get_userlist();
      die;
      break;*/
    }
  }
  
  // message was sent, write to file
  if ($_POST['msg'] && $_SESSION['auth'] == true) {
    write_message(microtime_float(), $_SESSION['name'], htmlspecialchars($_POST['msg']));
    die;
  }
  
  // check if login is valid
  if ($_GET['name'] && $_GET['password'] && $_SESSION['auth'] != true) {
    $_SESSION['name'] = $_GET['name'];
    if ($_GET['password'] == PASSWORD) {
      $_SESSION['auth'] = true;
      $_SESSION['last_action'] = microtime_float();
      add_user($_SESSION['last_action'], $_SESSION['name']);
    } else {
      $msg = "<h3 class='error'>Fehler bei der Anmeldung!</h3>";
    }
  }
  
  /* 
   * functions
   */
  // gets messages from file which are older than $timestamp
  function get_messages($timestamp) {
	global $icons;

    if (!file_exists(CHAT_FILE)) { die; }
    $data = file(CHAT_FILE);
	$html .= '<table width="100%" cellpadding="3" cellspacing="0" width="100%">';
    /*foreach ($data as $line) {
      $chunks = explode("\t", $line);
	  $html .= '<tr>';
	  //$html .= '<p><span class="chatDate">' . $chunks[1] . '</span> - ' . $chunks[2] . '</p>';
	  $html .= '<td class="chatDate">' . $chunks[1] . '</td>';
	  $html .= '<td class="chatUsername">' . $chunks[2] . '</td>';
	  $html .= '<td class="chatMsg">' . $chunks[3] . '</td>';
	  $html .= '</tr>';
      //only fetch messages that were written after last user action
      //if ($chunks[0] > $timestamp) { $new_msgs[] = $chunks[1]; }
    }*/
	
	$startValue = 0;
	$maxLines = 100;
	if (count($data) > $maxLines){
		$startValue = count($data) - $maxLines;
	}
	
    for ($i = $startValue; $i < count($data); $i++) {
		$chunks = explode("\t", $data[$i]);
		$html .= '<tr>';
		$html .= '<td class="chatDate">' . $chunks[1] . '</td>';
//		$html .= '<td class="chatUsername">' . $chunks[2] . '</td>';
		$html .= '<td class="chatUsername"><a href="javascript:void(window.open(\'http://www.razyboard.com/system/writepm.php?id=ttlg&to=' . $chunks[2] .  '&statistik=1\', \'blank\', \'width=680, height=350, scrollbars=yes\'));">' . $chunks[2] . '</a></td>';
		  
		// convert links
		$content_array = explode(" ", $chunks[3]);
		$output = '';

		foreach($content_array as $content)
		{
			$content_trimmed = trim($content);
			
			if(substr($content, 0, 7) == "http://" || substr($content, 0, 8) == "https://"){
				$output .= ' <a href="' . $content . '" target="_blank">' . $content . '</a> ';
			}
			else if(substr($content, 0, 4) == "www.") {
				$output .= ' <a href="http://' . $content . '" target="_blank">' . $content . '</a> ';
			}
			else if(substr($content, 0, 1) == "@") {
				$output .= ' <b><i>' . $content . '</i></b> ';
			}
			else if((substr($content_trimmed, 0, 1) == "*") && (substr($content_trimmed, strlen($content_trimmed) - 1, strlen($content_trimmed)) == "*")) {
				$output .= ' <b>' . substr($content_trimmed, 1, strlen($content_trimmed) - 2) . '</b> ';
			}
			else if ($icons[$content_trimmed] != null) {
				$output .= ' <img src="icons/' . ICON_SET . "/" . $icons[$content_trimmed]["file"].'" title="'.$icons[$content_trimmed]["tooltip"].'" /> ';
			}
			else {
				$output .= " " . $content . " ";
			}
		}
		$output = trim($output);

		$html .= '<td class="chatMsg">' . $output . '</td>';
		$html .= '</tr>';
		  //only fetch messages that were written after last user action
		  //if ($chunks[0] > $timestamp) { $new_msgs[] = $chunks[1]; }
    }
	$html .= "</table>";
    /*if (!is_array($new_msgs)) { $new_msgs = array(); }
    //update last_action of user
    update_last_action(microtime_float(), $_SESSION['name']);
    //return nicely formatted html
    $html = false;
    foreach ($new_msgs as $msg) {
      $html .= '<p>' . $msg . '</p>';
    }*/
    return $html;
  }
  
  // writes timestamp to user-file and given user
  /*function update_last_action($timestamp, $name) {
    $users = get_user_array();
    if (!$handle = fopen(USER_FILE, "w+")) { die("Konnte User-Datei nicht anlegen!"); }
    if (!chmod(USER_FILE, 0700)) { die("Konnte User-Datei nicht schützen!"); }
    foreach($users as $user) {
      $chunks = explode("\t", $user);
      if (trim($chunks[1]) == trim($name)) {
        //he is the one... update him
        fwrite($handle, $timestamp . "\t" . $name . "\n");
        $_SESSION['last_action'] = $timestamp;
      } else {
        //other user... write back to file or delete if idled out
        if (microtime_float() - $chunks[0] > 20.0) {
          remove_user($chunks[1]);
        } else {
          //write existing users back to file
          fwrite($handle, $user);
        }        
      }
    }
  }*/
  
  // writes message to file
  function write_message($timestamp, $name, $msg) {
    //check if file exists, else create
    if (!$handle = fopen(CHAT_FILE, "a")) { die("Konnte Chat-Datei nicht anlegen!"); }
    if (!chmod(CHAT_FILE, 0700)) { die("Konnte Chat-Datei nicht schützen!"); }
    fwrite($handle, $timestamp . "\t" . date("d.m.Y-H:i:s", time()) . "\t" . $name . "\t" . $msg . "\n");
    fclose($handle);
  }
  
  // fetches the userlist from file as html
  /*function get_userlist() {
    $data = get_user_array();
    $list = "";
    foreach($data as $entry) {
      $chunks = explode("\t", $entry);
      $list  .= $chunks[1];
    }
    return "<p>" . nl2br($list) . "</p>";
  }*/
  
  // fetches the userlist from file as array
  function get_user_array() {
    if (!file_exists(USER_FILE)) { return array(); }
    $data = file(USER_FILE);
    if (!is_array($data)) { $data = array(); }
    return $data;
  }
  
  // adds username to active user file
  function add_user($timestamp, $user) {
    $users = get_user_array();
    if (!$handle = fopen(USER_FILE, "w+")) { die("Konnte User-Datei nicht anlegen!"); }
    if (!chmod(USER_FILE, 0700)) { die("Konnte User-Datei nicht schützen!"); }
    //check if user exists
    foreach ($users as $name) {
      $chunks = explode("\t", $name);
      if (trim($chunks[1]) == trim($user)) {
        $msg = "<h3 class='error'>Benutzer ist bereits eingeloggt!</h3>";
        $_SESSION['auth'] = false;
      } else {
        //remove idled out users
        if (microtime_float() - $chunks[0] > 20.0) {
          remove_user($chunks[1]);
        } else {
          //write existing users back to file
          fwrite($handle, $name);
        }
      }
    }
    if ($_SESSION['auth'] == true) {
      // if he is (still) authenticated, add him to user file
      fwrite($handle, $timestamp . "\t" . $user . "\n");
      //write_message($timestamp, "-----", "Teilnehmer $user hat den Chat betreten\n");
    }
    fclose($handle);
  }
  
  // removes username from active user file
  function remove_user($user) {
    $users = get_user_array();    
    //remove user and write back to file
    if (!$handle = fopen(USER_FILE, "w+")) { die("Konnte User-Datei nicht aktualisieren!"); }
    foreach ($users as $name) {
      // strip timestamp
      $chunks = explode("\t", $name);
      if (trim($chunks[1]) != trim($user)) {
        fwrite($handle, $name);
      }
    }
    //write_message(microtime_float(), "-----", "Teilnehmer $user hat den Chat verlassen\n");
    fclose($handle);
    $users = get_user_array();
    if (count($users) == 0) {
      //no users, delete the message file
      //unlink(CHAT_FILE);
    }
  }
  
  //returns microtime as float value
  function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
 <head>
   <title><?php echo CHAT_TITLE; ?></title>
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
   <script type="text/javascript" src="prototype.js"></script>
   <script type="text/javascript">
   <!--
   // sends the message to SELF which writes it to file
   function send_message(msg) {
      $('msginput').clear();
      new Ajax.Request('<?php echo $_SERVER['PHP_SELF']; ?>', {
          parameters: { msg: msg },
          method: 'post'
      });
   }
   // scroll to bottom in chat div
   var timer;
   var px;
   function scrollDown() {
     px = 100;
     clearTimeout(timer);
     d = $('messages');
     y = d.scrollTop;
     y += px;
     if (y <= d.scrollHeight - d.offsetHeight + px && y >= 0 - px) {
       d.scrollTop = y;
       timer = setTimeout('scrollDown()', 50);
     }
     if (y < 0 || y > d.scrollHeight - d.offsetHeight) {
       clearTimeout(timer);
     }
   }
   //-->
   </script>
   <link rel="stylesheet" href="http://www.rsc-lueneburg.de/css/layout.css" type="text/css" />
   <style type="text/css">
   <!--
   body, h1, h3, .error, .success, p.link { text-align: center; margin: 0px; font-family: arial; font-size: 0.9em;}
   p { margin: 0px; padding: 0px; }
   .error { color: red; }
   .success { color: green; }
   p.link a { color: #999; }
   label.small img {padding-top: 5px; height: 16px; width: 16px;}
   div#login, div#chat { 
     margin: 0 auto; width: 100%; text-align: center;
   }
   div#chat { width: 100%; text-align: left; background-color: #DBDBDB;}
   div#messages, div#userlist { 
     width: 100%; height: 172px; background: white; border: 1px solid #eee; 
     text-align: left; padding: 0px; margin: 0px; overflow: auto;
   }
   input#msginput { 
     background: white; border: 1px solid #eee; width: 98%;
     margin: 0px; padding: 3px; 
   }
   select#updater, select#icons {
		width: auto; font-size: 0.8em;
   }
   input#msginput:focus { border: 1px solid #aaa; }
   .chatDate {font-size: 0.8em; width:1%; vertical-align: top; white-space: nowrap;}
   .chatUsername {font-size: 0.8em; width:1%; font-weight: bold; vertical-align: top;}
   .chatMsg {vertical-align: top;}
   form { padding: 0px; margin: 0px; }
   div#chat table td {
		padding-right: 3px;
   }
   div#help {
		width: 90%;
		height: 80%;
		border: 1px solid #000000;
		background: #ffffff;
		position: absolute;
		z-index: 10;
		right: 16px;
		bottom: 16px;
		display: none;
		font-size: 0.9em;
		overflow-y: auto;
		padding: 3px;
   }
   label#helpLabel:hover div#help {
		display: block;
   }
   div#help ul {
		margin-top: 5px;
   }
   div#help ul li {
		padding-left: 10px;
   }
   div#help span.return {
		font-family: monospace;
		font-size: 1.5em;
   }
   select {
	height: 20px;
   }
   #icons option {
	background-repeat: no-repeat;
	background-position: center center;
	width: <?php $size = getimagesize('icons/' . ICON_SET . '/control_emoticons.png'); echo $size[0] - 8; ?>px; /*icon width - 8*/
	height: <?php $size = getimagesize('icons/' . ICON_SET . '/control_emoticons.png'); echo $size[1] +2; ?>px;/*icon height + 2*/
	padding-top: 2px;
   }
   #icons option:before
   {
   margin-left: -3px;
   padding-top: 2px;
   }
   option[value=":-)"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley.png');}
   option[value=";-)"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-wink.png'); }
   option[value=":-D"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-grin.png'); }
   option[value=":-("]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-sad.png'); }
   option[value=";-("]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-cry.png'); }
   option[value=":-O"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-yell.png'); }
   option[value=">-("]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-mad.png'); }
   option[value=":-@"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-evil.png'); }
   option[value=">-@"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-twist.png'); }
   option[value=":-o"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-frighten.png'); }
   option[value=":-P"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-razz.png'); }
   option[value=":-X"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-kiss.png'); }
   option[value="8-|"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-roll.png'); }
   option[value="B-)"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-cool.png'); }
   option[value=":-#"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-zipper.png'); }
   option[value=":-§"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-red.png'); }
   option[value="O:)"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-angel.png'); }
   option[value=":-Z"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-sleep.png'); }
   option[value=":-/"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-confuse.png'); }
   option[value="$-)"]:before { content: url('icons/<?php echo ICON_SET; ?>/smiley-money.png'); }
   option[value="ccd"]:before { content: url('icons/<?php echo ICON_SET; ?>/thumb-up.png'); }
   option[value="ccq"]:before { content: url('icons/<?php echo ICON_SET; ?>/thumb-down.png'); }
   -->
   </style>
 </head> 
 <body>
  
  <?php
  if ($_SESSION['auth'] != true) {
    // show login
  ?>

   <h1>.: <?php echo $_GET['name']; ?> :.</h1>
   <h3>- Bitte anmelden -</h3>
   
  
   <form name="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <div id="login">
    <table>
    <tr><td>
      <label for="name">Name:</label>
    </td><td>
      <input type="text" size="25" name="name" value="<?php echo $_SESSION['name']; ?>"/>
    </td></tr>
  
    <tr><td>
      <label for="password">Passwort:</label>
    </td><td>
      <input type="password" size="25" name="password" />
    </td></tr>
    </table>
    <input type="submit" value="Anmelden" />
    </div>
   </form>
 
  <?php
    } else {
    // display chat
  ?>
  
    <!--h1>.: <?php echo CHAT_TITLE; ?> :.</h1-->
    <!--h3>- Angemeldet als <?php echo $_SESSION['name']; ?> -</h3-->
    <div id="chat">
      <form name="msg-form" action="javascript:send_message($F('msginput'));">
		  <div id="messages" style="padding-bottom: 25px;"><!-- --></div>
		  <table width="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="100%">
					<input autocomplete="off" id="msginput" type="text" name="msginput" title="Chatnachricht eingeben"/>
				</td>
				<td>
					<label class="small" for="updater" title="Automatischen Aktualisierung">
						<img src="icons/control_updater.png"/>
					</label>
				</td>
				<td>
					<select id="updater" title="Auswahl des Zeitintervall der automatischen Aktualisierung">
						<option value="0">aus</option>
						<option value="5">5 sek</option>
						<option value="10">10 sek</option>
						<option value="30">30 sek</option>
						<option value="60">1 min</option>
						<option value="120">2 min</option>
						<option value="300">5 min</option>
					</select>
				</td>
				<td>
					<label class="small" for="autoscroll" title="Automatisches Scrollen">
						<img src="icons/control_autoscroll.png"/>
					</label>
				</td>
				<td>
					<input type="checkbox" id="autoscroll" title="Auswahl ob automatisch zur letzten Nachricht gescrollt werden soll"/>
				</td>
				<td>
					<label class="small" for="icons" title="Auswahl von Emoticon">
						<img src="icons/<?php echo ICON_SET; ?>/control_emoticons.png"/>
					</label>
				</td>
				<td>
					<select id="icons" title="Auswahl eines Emoticon das im Chat erscheinen soll">
						<option value=""> - </option>
						<option value=":-)" title="Lächeln:    :-)    oder    :)">:-)</option>
						<option value=";-)" title="Zwinkern:    ;-)    oder    ;)">;-)</option>
						<option value=":-D" title="Grinsen:    :-D    oder    :D">:-D</option>
						<option value=":-(" title="Traurig:    :-(    oder    :(">:-(</option>
						<option value=";-(" title="Weinen:    ;-(    oder    ;(">;-(</option>
						<option value=":-O" title="Schreien:    :-O    oder    :O">:-O</option>
						<option value=">-(" title="Sauer:    >-(    oder    >(">>-(</option>
						<option value=":-@" title="Wütend:    :-@    oder    :@">:-@</option>
						<option value=">-@" title="Zornig:    >-@    oder    >@">>-@</option>
						<option value=":-o" title="Erschrocken:    :-o    oder    :o">:-o</option>
						<option value=":-P" title="Zunge raustrecken:    :-P    oder    :P">:-P</option>
						<option value=":-X" title="Küssen:    :-X    oder    :X">:-X</option>
						<option value="8-|" title="Ironisch:    8-|    oder    8|">8-|</option>
						<option value="B-)" title="Cool:    B-)    oder    B)">B-)</option>
						<option value=":-#" title="Schweigen:    :-#    oder    :#">:-#</option>
						<option value=":-§" title="Verlegen:    :-§    oder    :§">:-§</option>
						<option value="O:)" title="Unschuldig:    O:)    oder    O)">:-§</option>
						<option value=":-Z" title="Schlafen:    :-Z    oder    :Z">:-Z</option>
						<option value=":-/" title="Verwirrt:    :-/    oder    :/">:-/</option>
						<option value="$-)" title="Geldgierig:    $-)    oder    $)">$-)</option>
						<option value="ccd" title="Daumen hoch:    ccd    oder    ccd">:-d</option>
						<option value="ccq" title="Daumen runter:    ccq    oder    cq">:-q</option>
					</select>
				</td>
				<td>
					<label id="helpLabel" class="small" for="help" title="Hilfe">
						<img src="icons/control_help.png"/>
						<div id="help">
							<u><b>Chat-Tags</b></u>
							<ul>
								<li><b>Hyperlink</b> - http://<i>&lt;URL&gt;</i> | https://<i>&lt;URL&gt;</i> | www.<i>&lt;URL&gt;</i> - Generiert einen Link zu <i>&lt;URL&gt;</i><br/>Ergebnis: <a>http://www.url.de</a></li>
								<li><b>Nachricht</b> - @<i>&lt;USER&gt;</i> - Nachricht an <i>&lt;USER&gt;</i><br/>Ergebnis: <b><i>@User</i></b></li>
								<li><b>Hervorhebung</b> - *<i>&lt;TEXT&gt;</i>* - Hebt <i>&lt;TEXT&gt;</i> hervor (fett, <i>&lt;TEXT&gt;</i> darf <u>keine</u> Leerzeichen enthalten)<br/>Ergebnis: <b>Text</b></li>
							</ul>
							<u><b>Controls</b></u>
							<ul>
								<li><b>Texteingabe</b> - zur Eingabe des Chatnachricht, mit <span class="return">[RETURN]</span> wird die Nachricht abgesendet</li>
								<li><b>Automatische Aktualisierung</b> - Auswahl eines Zeitintervalls zur automatischen Aktualisierung des Chats</li>
								<li><b>Automatisches Scrollen</b> - bei markierter Checkbox wird immer automatisch zum Ende des Chats gescrollt</b></li>
								<li><b>Emoticon</b> - Auswahl an Emoticons zur Darstellung im Chat, kann auch per Tastatur eingegeben werden (siehe Tooltip welche Kombinationen möglich sind)</li>
								<li><b>Hilfe</b> - zeigt diesen Hilfetext</b></li>
							</ul>
						</div>
					</label>
				</td>
				
			  </tr>
		  </table>
      </form>
    </div>
    <!--p class="link"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=logout" title="Abmelden">Abmelden</a></p-->

    <script type="text/javascript">
    <!--
	// read update interval from cookie
	var updateInterval = 60;
	var updateIntervalCookie = document.cookie.match(/update_interval=\d+/);
	if (updateIntervalCookie != null && updateIntervalCookie.length > 0)
	{
		updateIntervalCookie = String(updateIntervalCookie);
		updateInterval = parseInt(updateIntervalCookie.substring("update_interval=".length, updateIntervalCookie.length));
	}
	for (var i = 0; i < document.getElementById("updater").options.length; i++) {
		if (updateInterval == document.getElementById("updater").options[i].value) {
			document.getElementById("updater").options[i].selected = true;
		}
	}
    // fetches messages from file
	var updater = new Ajax.PeriodicalUpdater('messages', '<?php echo $_SERVER['PHP_SELF']; ?>',
    {
      parameters: { action: 'get_messages' },
      method: 'get',
	  frequency: updateInterval
    });
	switchUpdaterFrequency();
    // fetches userlist from file every 10 seconds
    /*new Ajax.PeriodicalUpdater('userlist', '<?php echo $_SERVER['PHP_SELF']; ?>',
    {
      parameters: { action: 'get_userlist' },
      method: 'get',
      frequency: 10
    }); */   
    // sets timer for scrolling of message-window and restarts itself
    var autoscroll = true;
	var autoscrollCookie = document.cookie.match(/autoscroll=\d/);
	if (autoscrollCookie != null && autoscrollCookie.length > 0)
	{
		autoscrollCookie = String(autoscrollCookie);
		autoscroll = parseInt(autoscrollCookie.substring("autoscroll=".length, autoscrollCookie.length)) == 1 ? true : false;
	}
    var autotimer = null;
    function timedScroll() {
      scrollDown();
      autotimer = setTimeout("timedScroll()", 1000);
    }
	if (autoscroll) {
		timedScroll();
		document.getElementById("autoscroll").checked = true;
	}
    // switches autoscrolling on/off, called by observing checkbox "autoscroll"
    function switchTimedScroll() {
		if (autoscroll == true) {
			// stop autoscroll
			clearTimeout(autotimer);
			autoscroll = false;
		} else {
			// start autoscroll
			timedScroll();
			autoscroll = true;
		}
		var date = new Date();
		date = new Date(date.getTime() +1000*60*60*24*365);
		document.cookie = 'autoscroll=' + (autoscroll ? 1 : 0) + '; expires='+date.toGMTString()+';'; 
    }
	function switchUpdaterFrequency() {
		if (document.getElementById("updater").options.selectedIndex == 0) {
			updater.stop();
		}
		else {
			updater.frequency = document.getElementById("updater").options[document.getElementById("updater").options.selectedIndex].value;
			updater.start();
		}
		
		var date = new Date();
		date = new Date(date.getTime() +1000*60*60*24*365);
		document.cookie = 'update_interval=' + document.getElementById("updater").options[document.getElementById("updater").options.selectedIndex].value + '; expires='+date.toGMTString()+';'; 
	}
	function addIcon() {
		if (document.getElementById("icons").options.selectedIndex > 0) {
			var cursorPos = document.getElementById("msginput").selectionStart;
			
			var insertValue = " " + document.getElementById("icons").options[document.getElementById("icons").options.selectedIndex].value + " ";
			
			document.getElementById("msginput").value = document.getElementById("msginput").value.substr(0, cursorPos)
												 + insertValue
												 + document.getElementById("msginput").value.substr(cursorPos, 
																						document.getElementById("msginput").value.length);
			
			document.getElementById("msginput").focus();
			document.getElementById("msginput").selectionStart = cursorPos + insertValue.length;
			document.getElementById("msginput").selectionEnd = cursorPos + insertValue.length;
		
			document.getElementById("icons").options[0].selected = true;
		}
	}
    Event.observe('updater', 'change', switchUpdaterFrequency);
    Event.observe('autoscroll', 'click', switchTimedScroll);
    Event.observe('icons', 'change', addIcon);
    //-->
    </script>
    
  <?php
    }
    echo $msg;
  ?>

  </body>
</html>
