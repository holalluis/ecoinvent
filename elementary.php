<!doctype html><html><head>
	<?php include'imports.php'?>
	<title>Elementary Flows</title>
	<script src="techs/constants.js"></script>
</head><body><h1>Elementary Flows</h1>

<p>Implementation in progress as <?php echo date("M-d-Y") ?>

<script>
	var Inputs = [
		//table 1 & 2
		{id:"Q",           value:22700,  unit:"m3/d" },
		{id:"T",           value:12,     unit:"ºC"   },
		{id:"SRT",         value:5,      unit:"d"    },
		{id:"COD",         value:300,    unit:"g/m3" },
		{id:"sCOD",        value:132,    unit:"g/m3" },
		{id:"BOD",         value:140,    unit:"g/m3" },
		{id:"sBOD",        value:70,     unit:"g/m3" },
		{id:"MLSS_X_TSS",  value:3000,   unit:"g/m3" },
		{id:"VSS",         value:60,     unit:"g/m3" },
		{id:"TSS",         value:70,     unit:"g/m3" },
		{id:"TSSe",        value:1,      unit:"g/m3" },
		{id:"TSS_was",     value:1,      unit:"g/m3" },
		//table 3
		{id:"TKN",         value:35,     unit:"g/m3" },
		{id:"Ne",          value:0.50,   unit:"g/m3" },
		{id:"NOx_e",       value:1,      unit:"g/m3" },
		{id:"PO4_e",       value:1,      unit:"g/m3"},
		//table 4
		{id:"TP",          value:6,      unit:"g/m3" },
	];

	var Technologies = [
		//booleans
		{id:"raw_wastewater", value:true},
		{id:"nitrification", value:true},
		{id:"denitrification", value:true},
		{id:"BiP", value:true},
		{id:"ChP", value:true},
	];

	var Outputs={
		"COD":{water:0, air:0, sludge:0},
		"CO2":{water:0, air:0, sludge:0},
		"CH4":{water:0, air:0, sludge:0},
		"TKN":{water:0, air:0, sludge:0},
		"NOx":{water:0, air:0, sludge:0},
		"N2" :{water:0, air:0, sludge:0},
		"N2O":{water:0, air:0, sludge:0},
		"TP" :{water:0, air:0, sludge:0},
		"TS" :{water:0, air:0, sludge:0},
	};
</script>

<!--table 2-->
<script>
	//inputs
	var Q = 22700
	var T = 12
	var SRT = 5
	var COD = 300
	var sCOD = 132
	var BOD = 140
	var sBOD = 70
	var MLSS_X_TSS = 3000;
	var VSS = 60
	var TSS = 70
	var TSSe = 1;
	var TSS_was = 2;

	//technologies active
	var raw_wastewater = true
	var nitrification = false;
	var denitrification = false;
	var BiP = false;
	var ChP = false;

	//primary treatment boolean
	var primary_effluent_wastewater = !raw_wastewater;

	//equations
	var bCOD = 1.6*BOD;
	var nbCOD = COD - bCOD;
	var nbsCODe = sCOD - 1.6*sBOD
	var nbpCOD = COD - bCOD - nbsCODe;
	var VSS_COD = (COD-sCOD)/VSS;
	var nbVSS = nbpCOD/VSS_COD;
	if(raw_wastewater){
		var rbCOD = 0.2*bCOD;
	}else if(primary_effluent_wastewater){
		var rbCOD = 0.32*bCOD;
	}
	var VFA = 0.15*rbCOD;
	var iTSS = TSS - VSS;
	var S0 = bCOD;
	var mu_mT = mu_m * Math.pow(1.07, T - 20);
	var bHT = bH * Math.pow(1.04, T - 20); 
	var S = Ks*(1+bHT*SRT)/(SRT*(mu_mT-bHT)-1);
	var P_X_bio = (Q*YH*(S0 - S) / (1 + bHT*SRT) + (fd*bHT*Q*YH*(S0 - S)*SRT) / (1 + bHT*SRT))/1000;
	var P_X_VSS = P_X_bio + Q*nbVSS/1000;
	var P_X_TSS = P_X_bio/0.85 + Q*nbVSS/1000 + Q*(TSS-VSS)/1000;
	var X_VSS_V = P_X_VSS*SRT;
	var X_TSS_V = P_X_TSS*SRT;
	var V_reactor = X_TSS_V*1000/MLSS_X_TSS;
	var tau = V_reactor*24/Q;
	var MLVSS = X_VSS_V/X_TSS_V*MLSS_X_TSS;
	var VSSe = TSSe*0.85
	var Qwas = (V_reactor*MLSS_X_TSS/SRT - TSSe*Q)/(-TSSe + TSS_was);
</script>

<!--table 3-->
<script>
	//inputs
	var TKN = 35
	var Ne = 0.50
	var NOx_e = 1 //TODO

	//equations
	var nbpON = 0.064*nbVSS;
	var nbsON = 0.3;
	var TKN_N2O = 0.001*TKN;
	var bTKN = TKN - nbpON - nbsON - TKN_N2O;
	var NOx = bTKN - Ne - 0.12*P_X_bio;
</script>

<!--table 4-->
<script>
	var TP = 6

	//equations
	var nbpP = 0.025*nbVSS;
	var nbsP = 0;
	var aP = TP - nbpP - nbsP;
	var aPchem = aP - 0.015*P_X_bio/Q;
</script>

<!--OUTPUTS table 5 elementary flows-->
<script>
	//COD
	var sCODe = Q*nbsCODe + Q*Ks*(1+fd*SRT)/(SRT*(YH-bHT)-1)
	var biomass_CODe = Q*VSSe*1.42;
	var COD_water = sCODe + biomass_CODe;
	Outputs.COD.water=COD_water;

	var COD_air = 0;
	var COD_sludge = (function(){
		var A = (Q*YH*(S0-S)/(1+bHT*SRT) + fd*bHT*Q*YH*(S0-S)*SRT/(1+bHT*SRT))/1000;
		var B = Qwas * sCODe;
		return A+B;
	})();
	Outputs.COD.sludge=COD_sludge;

	//CO2
	var CO2_water=0;
	var CO2_air=0;
	var CO2_sludge=0;

	//CH4
	var CH4_water=0;
	var CH4_air=bCOD*Q*0.95;
	var CH4_sludge=0;

	//TKN
	var TKN_water=0;
	var sTKNe = Ne*Q + nbsON*Q;
	var TKN_air = (function(){
		if(nitrification){
			return sTKNe + Q*VSSe*0.12;
		}
		else{
			return Q*TKN - 0.12*P_X_bio;
		}
	})();
	var TKN_sludge = 0.12*P_X_bio + Qwas*sTKNe;

	//NOx
	var NOx_water=(function(){
		if(denitrification){
			return Q*NOx_e;
		}else{
			return Q*(bTKN - Ne) - 0.12*P_X_bio;
		}
	})();
	var NOx_air=0;
	var NOx_sludge=Qwas*NOx_e;

	//N2
	var N2_water=0;
	var N2_air = 0.22 * (NOx - NOx_e);
	var N2_sludge=0;

	//N2O
	var N2O_water=0;
	var N2O_air=Q*TKN_N2O;
	var N2O_sludge=0;

	//TP
	var TP_sludge=(function(){
		if(BiP || ChP){
			return 0;//TODO	
		}else{
			return TP;
		}
	})();
	var TP_air=0;
	var TP_sludge=0;

	//TS
	var TS_water=0;
	var TS_air=0;
	var TS_sludge=0;
</script>

<!--tables-->
<div style="display:flex;flex-wrap:wrap">
	<!--table inputs-->
	<div>
		1. Inputs
		<table id=inputs border=1>
			<tr><th>Input<th>Value<th>Unit
		</table>
		<script>
			var t=document.querySelector('table#inputs');
			Inputs.forEach(i=>{
				var newRow=t.insertRow(-1);
				newRow.insertCell(-1).innerHTML=i.id;
				newRow.insertCell(-1).innerHTML="<input id='"+i.id+"' value='"+i.value+"' type=number>"
				newRow.insertCell(-1).innerHTML=i.unit.replace('m3','m<sup>3</sup>');
			});
		</script>
	</div>

	<div>&emsp;</div>

	<!--outputs-->
	<div>
		2. Outputs
		<table id=outputs border=1 cellpadding=2>
			<tr>
				<th rowspan=2>Compound
				<th colspan=3>Effluent (g/d)
			</tr>
			<tr>
				<th>Water<th>Air<th>Sludge
		</table>
		<script>
			var table=document.querySelector('table#outputs');
			for(var output in Outputs) {
				var newRow=table.insertRow(-1);
				newRow.insertCell(-1).innerHTML=output
					.replace('O2','O<sub>2</sub>')
					.replace('N2','N<sub>2</sub>')
					.replace('CH4','CH<sub>4</sub>');

				newRow.insertCell(-1).innerHTML=format(Outputs[output].water);
				newRow.insertCell(-1).innerHTML=format(Outputs[output].air);
				newRow.insertCell(-1).innerHTML=format(Outputs[output].sludge);
			}
		</script>
	</div>

	<div>&emsp;</div>

	<!--mass balances-->
	<div>
		3. Mass balances
		<table border=1>
			<tr><th>Element<th>Effluent<th>Air<th>Sludge<th>Difference
			<tr><td>C<td>-<td>-<td>-<td>-
			<tr><td>N<td>-<td>-<td>-<td>-
			<tr><td>P<td>-<td>-<td>-<td>-
			<tr><td>C<td>-<td>-<td>-<td>-
		</table>
	</div>
</div>

<hr>

<a href=inputs.php>Back</a>

