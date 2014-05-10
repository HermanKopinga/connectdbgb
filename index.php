<?php
/*
 * Connectdbgb
 * Herman Kopinga 2014 herman@kopinga.nl
 * 
 * Column editor in a webpage. Basically a multiple textfile editor.
 * Warning: Non-secure, localhost only, simple, optimistic coding.
 * 
 * I use this for project lists in my workshop.
 *
 * Funny how little code this takes. The structure is in the textfile naming.
 * 11Name.txt is first row, first column.
 * 12Anothername.txt is first row, second column.
 * 13Nextone.txt is first row, third column.
 * 21Textfile.txt is second row, first column.
 * etc...
 * etc...
 *
 * Designed to work with Fluid (makes an app from a webpage on OSX).
 * With the global hotkey F6 I get my project overview.
 * 
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="nl"> 
<head>
<title>Connectdbgb</title>
  <meta name="generator" content="Dusty old hand coding"/> 
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta name="Description" content="" /> 
  <meta name="Keywords" content="" />
  <style type="text/css">

   html {
     background:           url(kleedje.jpg) no-repeat center center fixed;
     -webkit-background-size: cover;
     -moz-background-size: cover;
     -o-background-size:   cover;
     background-size: 	   cover;
     background-color:     #ededed;
   }  
   
   body {
      font-size:            100%;
      color:                #141607;
      margin:               20;
    }
   </style>
</head>
<body>
<form action="" method="post">

<?php 
error_reporting(E_STRICT);
ini_set('display_errors', '1');
ini_set('date.timezone', "Europe/Amsterdam");
// Config, not really nessesary but nice to have some sense of future expanding :)
$datadir = "hermanmaakt";
$basepath = "/Users/hermankopinga/Sites/connectdbgb/data/";
$path = $basepath . $datadir;
// Little feature to activate demo contents if datadir isn't found.
if (!is_dir($path)) {
  $path = $basepath . "demo";
}
$currentrow = 1;

// There was a change to one of the columns, update them.
if(isset($_POST["update"])) {        
    // Go through all the textareas (named files in the html).
    foreach($_POST["files"] as $filename => $lines) {
        // Read and sanitize the textarea and file contents.
        $postcontents = trim(substr($lines, strpos ($lines, '--------------------')+20));
        $postcontents = str_replace("\r", "", $postcontents);
        $filecontents = trim(file_get_contents($path . "/" . $filename));
        // Compare the two, if there are changes we need to write them to disk.
        if ($filecontents != $postcontents) {
            // Content changed.
            // Make backup in archive folder with timestamp.
            // Write new contents to new file.
            // Success is not checked on these, optimistic programming ftw :)
            echo $filename . "<br />";
            $newfilename = str_replace(".txt", "", $filename) . date("YmdHms") . ".txt";
            file_put_contents($path . "/archive/" . $newfilename, $filecontents);
            file_put_contents($path . "/" . $filename, $postcontents);
        }
    }
}

// Show all textareas with the textfiles in them.
if ($handle = opendir($path)) {
    // This might be robust reading of a directory.
    while (false !== ($entry = readdir($handle))) {
    	// Read only text files.
        if (substr($entry, -4) == '.txt') {
        	// First character is the row number (>9 is non supported/undocumented).
            $row = substr($entry,0,1);
            // Ah! A new row, simply done by adding a linebreak to the HTML.
            if ($row > $currentrow) {
               echo "<br />\n\n";
               $currentrow = $row;
            }
            // Print the textarea for this textfile.
            // Note the name, this is a php 'trick',
            // name form elements as if they are part of an array 
            // and php will parse it for you :)
            echo "<textarea cols=20 rows=20 name=\"files[$entry]\">\n";
            // Only print the name part of the filename in the textarea.
            echo str_replace(".txt", "", substr($entry,2));
            echo "\n--------------------\n";
            // This can probably be even shorter.
            $fullpath = $path . '/' . $entry;
            $lines = file($fullpath);
            foreach ($lines as $line) {
                echo "$line";
            }
            echo "\n</textarea>\n\n";
        }
    }
    closedir($handle);
}

// Finish up the form and HTML, accesskey S is used for save. One less mouseclick :)

?>
<br />
<input type="hidden" name="update">
<input type="submit" accesskey="s" value="Save">
</form>
</body>
</html>