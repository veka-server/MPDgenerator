<?php 
	/**
	*	Script de génération de mpd pour mysql.
	*	@auteur: Veka (dl16-213)
	*/

	include "config.php";
	include "php/app.php";

	$app = new App(TYPEBASE);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>GenMPD</title>

	<link rel="stylesheet" href="css/defaut.css">
	<link rel="stylesheet" href="css/jquery_ui.css">
</head>
<body>

	<h1>Générateur de MPD</h1>

	<p class="warning">
		Ce générateur ne fournis aucune garantie.<br/>
		En aucun cas, je ne serai responssable d'une erreur ou tout autre problème survenant avec ce script.
	</p>	

	<div id="wrapper">

		<ul class="resum">
			<li>Type : <?php echo TYPEBASE; ?></li>
			<li>User : <?php echo USER; ?></li>
			<li>Host : <?php echo HOST; ?></li>
			<?php if (TYPEBASE == "mysql"): ?>
				<li>Database : <?php echo DATABASE; ?></li>			
			<?php endif ?>
			<?php if (TYPEBASE == "oracle"): ?>
				<li>SID : <?php echo SID; ?></li>
			<?php endif ?>
		</ul>

		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Schema MPD</a></li>
				<li><a href="#tabs-2">Text MPD</a></li>
				<li><a href="#tabs-3">Dico données</a></li>
				<li><a href="#tabs-4">yUML demo</a></li>
			</ul>
			<div id="tabs-1">
				<button id="draw">générer</button>
				<div id="schema">
					<p>
						<a id="savebutton" href="javascript:initImageDownloadLink()" download="uml.png" title="Télécharger le diagramme">
							Télécharger le fichier image
						</a>
					</p>
					<p class='legend'><b>Legend</b><br/>PK = primary key<br/>FK = foreign key</p>
					<canvas id="canvas"></canvas>
					<div id="data"><?php echo htmlentities($app->dataG.$app->dataL );?></div>
				</div>
			</div>
			<div id="tabs-2">
				<div id='mpdText'><?php echo $app->data; ?></div>
			</div>
			<div id="tabs-3">
				<table class="dico">
					<tr>
						<td>Nom</td>
						<td>Type</td>
						<td>Default</td>
						<td>Contrainte</td>
					</tr>

					<?php foreach ($app->dataDico as $key => $data) { ?>
					<tr>
						<td><?php echo $data['nom'] ?></td>
						<td><?php echo $data['type'] ?></td>
						<td><?php echo $data['default'] ?></td>
						<td><?php echo $data['contrainte'] ?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<div id="tabs-4">
				<textarea id="yuml"><?php echo htmlentities($app->dataG.$app->dataLy );?></textarea>
				<br>
				Utilisez le code ci-dessus sur le site suivant : <a href="http://yuml.me/diagram/class/draw">Demo de yUML</a>
			</div>
		</div>


		<!-- sources : http://www.nomnoml.com/ -->
		<script src="lib/jquery1.10.2.js"></script>
		<script src="lib/underscore.js"></script>
		<script src="lib/underscore.skanaar.js"></script>
		<script src="lib/dagre.min.js"></script>
		<script src="lib/skanaar.canvas.js"></script>
		<script src="lib/vector.js"></script>
		<script src="lib/nomnoml.jison.js"></script>
		<script src="lib/nomnoml.parser.js"></script>
		<script src="lib/nomnoml.layouter.js"></script>
		<script src="lib/nomnoml.renderer.js"></script>
		<script src="lib/nomnoml.app.js"></script>

		<script src="lib/jquery-ui.js"></script>
		<script>
			$(function() {
				$( "#tabs" ).tabs();
			});
		</script>


	</body>
	</html>
