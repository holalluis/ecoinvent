<!doctype html><html>
	<head><?php include'imports.php'?>
	<title>Elementary Flows</title>
</head><body onload="init()">
<?php include'navbar.php'?>
<div id=root>

<h1>Generate ecoSpold file (XML)</h1>

<script src="createEcospold.js"></script>

<p>(Note: an empty ecoSpold file is generated now, under development)</p>

<button style=font-size:20px onclick="createEcospold()">
	Generate
</button>
