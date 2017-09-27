<!doctype html><html><head>
	<?php include'imports.php'?>
	<title>Ecoinvent</title>
	<script>
		function init(){
			//init
		}
	</script>

	<!--styles-->
	<style>
		/*general*/
		body{
			font-family:sans-serif;
			margin:0;
		}
		h1{
			margin-top:5px;
			font-weight:normal;
		}
		h2{
			font-weight:normal;
		}
		table th{
			border-bottom:1px solid #ccc;
		}

		/*lists*/
		ol{	
			counter-reset:item;
		}
		ol li{
			display:block;
		}
		ol li:before{
			content:counters(item,".") ". "; 
			counter-increment:item;
			font-family:monospace;
		}
		#root {
			margin:5px;
		}
		#root > li {
			margin-top:0.5em;
		}
		#root > li > b{
			font-size:17px;
		}

		.description {
			display:inline-block;
			width:350px;
			font-size:12px;
		}

		/*folds*/
		.foldable.folded > ol {
			display:none;
		}
		.foldable b {
			cursor:pointer;
			font-size:14px;
		}
		.foldable b:hover {
			text-decoration:underline;
		}
	</style>
</head><body onload=init()>

<div id=navbar>
	Ecoinvent wastewater web tool (in development)
	<style>
		#navbar {
			background:linear-gradient(to top, #e01a1a, #eb6666);
			padding:0.5em;
			font-size:22px;
		}
	</style>
</div>

<div id=root>

<p style=max-width:50em>
	<p style=max-width:50em>
		This is a placeholder for drafting the interface 
		that will request the user's inputs
		(not yet a draft for the design, this goes last).
		The goal is to have a very clear user interface for the inputs 
		before moving forward to create the backend (models).
		<hr>
	</p>

	<div>
		<p>Web parts:</p>
		<ul>
			<li><a href=general      >1. General configuration    </a>
			<li><a href=wastewater   >2. Wastewater configuration </a>
			<li><a href=wwtp         >3. WWTP configuration       </a>
			<li><a href=construction >4. Construction materials   </a>
			<li>5. Chemicals consumed
			<li>6. Energy consumed
			<li>7. Generate ecoSpold (connection with python code)
			<li><a href=views>Views architecture</a>
			<li>Appendix: <a href=terms.php>Terms</a>
		</ul>
		<hr>
	</div>

	<div>
		<p>Metcalf &amp; Eddy, Wastewater Engineering, 5th ed., 2014, implementations:</p>
		<table>
			<tr><th>Technology<th>Coding status
			<tr>
				<td><a href="bod_removal_only.php">0. BOD<sub>5</sub> removal [example]</a>
				<td>Done
			<tr>
				<td><a href="bod_removal_with_nitrification.php">1. BOD<sub>5</sub> removal w/ &amp; w/o nitrification</a>
				<td>Done
			<tr>
				<td><a href="N_removal.php">2. N removal</a>
				<td>Done
			<tr>
				<td><a href="chem_P_removal.php">3. P removal (chemically)</a>
				<td>Done
			<tr>
				<td><a href="bio_P_removal.php">4. P removal (biologically)</a>
				<td>Done
			<tr>
				<td><a href="ekama_sizing.php">5. Reactor sizing (optim. cost) [G. Ekama]</a>
				<td>Done
			<tr>
				<td><a>6. Elementary flows factors + simpleTreat</a>
				<td>(in progress at <?php echo date("Y-m-d")?>)
			</tr>
		</table>
	</div><hr>

	<div>
		<p>Other things:</p>
		<ul>
			<li><a href=docs>Documents</a>
			<li>
				<a target=_blank href="https://docs.google.com/spreadsheets/d/1DiBhDCjxGyw2-umImIfHiZOzY5LJF_psGiD4fEf7Wgk/edit?usp=sharing">
					Google Drive document 
				</a>
			</li>
			<li>This web source code: <a href=//github.com/holalluis/ecoinvent>github.com/holalluis/ecoinvent</a>
			<li>Guillaume's github (for ecoSpold): 
				<a href="//github.com/ecoinvent/wastewater_treatment_tool">github.com/ecoinvent/wastewater_treatment_tool</a>
			</li>
		</ul>
	</div>

	<hr>

	<div>
		ICRA people:
		<ul>
			<li><a target=_blank href=mailto:lbosch@icra.cat>lbosch@icra.cat</a> (Lluís Bosch, ICRA software developer)
			<li><a target=_blank href=mailto:lcorominas@icra.cat>lcorominas@icra.cat</a> (Lluís Corominas, ICRA coordinator)
		</ul>
	</div>
</p>