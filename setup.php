<html>
	<head>
		<title>PHP OMXPlayer Control Setup</title>
		<style type="text/css">
			.error{ color:red; font-weight:bold; }
		</style>
	</head>
	<body>

		<?php
		error_reporting(E_ALL);
		define(FIFO, $_SERVER['DOCUMENT_ROOT'].'/omxplayer_fifo');
		$omxsh = "#!/bin/sh\nsudo sh -c \"./cls.sh\"\nomxplayer -p -o hdmi \"$1\" <".FIFO." >/dev/null 2>&1 &\nsleep 1\necho -n . >".FIFO."\n";


		if ( @file_get_contents('omx_php.sh') != $omxsh ) {

			$processUser = posix_getpwuid(posix_geteuid());
			if ( is_writable('/dev/vchiq') ) {
				echo '/dev/vchiq is writable - OK<br>';
			} else {
				echo '/dev/vchiq is not writable for httpd user<br>';
				echo 'you have to run shell command:<br>';
				echo 'sudo usermod -a -G video '.$processUser['name'].'<br>';
				echo 'this will allow http server user which runs omxplayer access /dev/vchiq to display video<br>';
				die();
			}

			if ( posix_mkfifo(FIFO, 0777) ) {
				echo FIFO.' is writable - OK<br>';
			} else {
				echo 'can\'t create '.FIFO.' - please fix persmissions!<br>';
				die();
			}

			if ( chmod(FIFO,0777) ) {
				echo FIFO.' permissions - OK<br>';
			} else {
				echo 'can\'t change permissions for '.FIFO.' - please fix persmissions!<br>';
				die();
			}

			unlink(FIFO);

			if ( file_put_contents('omx_php.sh', $omxsh) ) {
				chmod ('omx_php.sh', 0777);
				echo 'config saved - OK';
			}	else {
				echo 'error saving shell script - please fix permissions';
			}
			
			echo "<h1 class=\"error\">Please note - if you want to clear screen before player start please modify cls.sh to your needs and run this command from shell</h1>";
			echo "<p><b><i>sudo sh -c 'echo \"".$processUser['name']." ALL=(ALL) NOPASSWD: /bin/sh -c ./cls.sh\" >/etc/sudoers.d/".$processUser['name']." && chmod 0640 /etc/sudoers.d/".$processUser['name']."'</i></b></p>";
			echo "<p>in short this command allows cls.sh script to do necessary tasks to clear screen and it can be done only using sudo,
						and command add this cls.sh scirpt rights to do so for apache</p>";
			
		}

		//////////////////////////////////////////////////////////////////////////////////
		// Explore the files via a web interface.
		$script = basename(__FILE__); // the name of this script
		$path = !empty($_REQUEST['path']) ? $_REQUEST['path'] : dirname(__FILE__); // the path the script should access

		if ($_REQUEST['save'] == 'save') {
			if ( file_put_contents('cfg.php', "<?php\ndefine('FIFO', '".FIFO."');\ndefine('PATH', '".$path."');\n?>\n") ) {
				header("Location: omxplayer.php");
			} else {
				echo 'error saving config file - please fix permissions';
				die();
			}
		}

		echo "<h1>Please choose videos directory</h1>";
		echo "<h1><b>Browsing Location:</b></h1><input type=\"text\" name=\"path\" value=\"{$path}\" /><a href=\"{$script}?path={$path}&save=save\"><button type=\"button\">save path</button></a>";

		$directories = array();
		$files = array();

		// Check we are focused on a dir
		if (is_dir($path)) {
			chdir($path); // Focus on the dir
			if ($handle = opendir('.')) {
				while (($item = readdir($handle)) !== false) {
					// Loop through current directory and divide files and directorys
					if(is_dir($item)){
						array_push($directories, realpath($item));
					}
					else
					{
						array_push($files, ($item));
					}
				}
				closedir($handle); // Close the directory handle
			}
			else {
				echo "<p class=\"error\">Directory handle could not be obtained.</p>";
			}
		}
		else
		{
			echo "<p class=\"error\">Path is not a directory</p>";
		}
		asort($directories, SORT_NATURAL);
		asort($files, SORT_NATURAL);
		// There are now two arrays that contians the contents of the path.

		// List the directories as browsable navigation
		echo "<h2>Navigation</h2>";
		echo "<ul>";
		foreach( $directories as $directory ){
			echo ($directory != $path) ? "<li><a href=\"{$script}?path={$directory}\">{$directory}</a></li>" : "";
		}
		echo "</ul>";

		echo "<h2>Files</h2>";
		echo "<ul>";
		foreach( $files as $file ){
			// Comment the next line out if you wish see hidden files while browsing
			if(preg_match("/^\./", $file) || $file == $script): continue; endif; // This line will hide all invisible files.
			echo '<li>' . $file . '</li>';
		}
		echo "</ul>";

		?>

	</body>
</html>