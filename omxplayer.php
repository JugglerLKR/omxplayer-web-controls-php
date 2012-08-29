<?php
require_once 'cfg.php';
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'ru_RU.UTF-8');
?>
<html>
	<head>
		<title>PHP OMXPlayer Control</title>
		<style type="text/css">
			.error{ color:red; font-weight:bold; }
			.button{ height: 50px; width: 85px; }
		</style>
		<script src="JsHttpRequest.js"></script>
		<script>
			function omxajax(act) {

				if (act == 'play') {
					var arg=document.getElementById('selected_file').value;
					//alert (arg);
				}

				JsHttpRequest.query(
				'omx_control.php',
				{
					"act": act,
					"arg": arg
				},
				function(result, errors) {
					if (result['err']) {
						document.getElementById('err').innerHTML = result['err'];
						//alert (result['err']);
					} else {
						if (result) {
							document.getElementById('res').innerHTML = result['res'];
							document.getElementById('err').innerHTML = '&nbsp;';
						}
					}
				},
				true //disable caching
				);

			}
		</script>
	</head>
	<body>
		<center>
			<?php
			$files = glob(PATH.'/{*.[mM][kK][vV],*.[aA][vV][iI],*.[mM][pP][4]}', GLOB_BRACE | GLOB_MARK);
			//print_r($files);
			$vids = array_filter ($files, function ($file) { if (substr($file,-1) != '/') return true;} ); //filter out directories
			?>
			<select id="selected_file">
				<?php
				foreach ($vids as $key=>$val) {
					echo '<option value="'.$val.'">'.basename($val).'</option>';
				}
				?>
			</select>

			<table cellspacing="5" cellpadding="0" border="0">
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('voldown');">VOLUME -</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('play');">PLAY</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('volup');">VOLUME +</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('seek-30');">SEEK -30</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('pause');">PAUSE</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('seek30');">SEEK +30</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('seek-600');">SEEK -600</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('stop');">STOP</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('seek600');">SEEK +600</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('speedup');">SPEED +</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextchapter');">NEXT CHAPTER</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextaudio');">NEXT AUDIO</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('speeddown');">SPEED -</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('prevchapter');">PREV CHAPTER</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('prevaudio');">PREV AUDIO</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('prevsubtitles');">PREV SUBTITLES</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('togglesubtitles');">TOGGLE SUBTITLES</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextsubtitles');">NEXT SUBTITLES</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="">&nbsp;</button>
					</td>
					<td>
						<a href="setup.php?path=<?php echo PATH;?>"><button type="button" class="button" >SETUP</button></a>
					</td>
					<td>
						<button type="button" class="button" onclick="">&nbsp;</button>
					</td>
				</tr>
			</table>



		</center>
		<div id="res">&nbsp;</div><div id="err">&nbsp;</div>

	</body>
</html>

