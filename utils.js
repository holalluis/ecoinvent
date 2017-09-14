
function density_of_air(temperature,pressure) {
	/*
		Appendix B, B-3
		Calculate Density of air at other temperatures
		formula: d = PM/RT
		inputs:
			- T: temperature (ºC)
			- P: pressure (Pa)
	*/
	var R=8314;//universal gas constant (J/K·kmol)
	var M=28.97;//molecular weight of air (g/mol)
	var density = pressure*M/(R*(273.15+temperature));
	return density;//kg/m3
	/*tests:
		console.log(density_of_air(20,1.01325e5)) //1.2043845867047405 kg/m3
		console.log(density_of_air(12,95.6e3))    //1.1682155731228065 kg/m3
	*/
}

function air_solubility_of_oxygen(temperature,elevation){
	/*
		Appendix E, Table E-1, page 1923
		The air solubility of oxygen in mg/L as functions of temperature (ºC) and elevation (m) for 0-1800 m
		Perform linear interpolation or bilinear interpolation
	*/
	//input checks
	console.log('Calculating air solubility of oxygen (mg/L) from Table E-1 (Appendix E)');
	temperature=temperature||0;
	elevation=elevation||0;
	if(temperature<0) temperature=0;
	if(temperature>40)temperature=40;
	if(elevation<0)   elevation=0;
	if(elevation>1800)elevation=1800;

	/*
		Table: index 1 is temperature and index 2 is elevation
		Table[20][200]
	*/
	var Table=[
		{0: 14.621, 200: 14.276, 400: 13.94,  600: 13.612, 800: 13.291, 1000: 12.978, 1200: 12.672, 1400: 12.373, 1600: 12.081, 1800: 11.796},
		{0: 14.216, 200: 13.881, 400: 13.554, 600: 13.234, 800: 12.922, 1000: 12.617, 1200: 12.32,  1400: 12.029, 1600: 11.745, 1800: 11.468},
		{0: 13.829, 200: 13.503, 400: 13.185, 600: 12.874, 800: 12.57,  1000: 12.273, 1200: 11.984, 1400: 11.701, 1600: 11.425, 1800: 11.155},
		{0: 13.46,  200: 13.142, 400: 12.832, 600: 12.53,  800: 12.234, 1000: 11.945, 1200: 11.663, 1400: 11.387, 1600: 11.118, 1800: 10.856},
		{0: 13.107, 200: 12.798, 400: 12.496, 600: 12.201, 800: 11.912, 1000: 11.631, 1200: 11.356, 1400: 11.088, 1600: 10.826, 1800: 10.57},
		{0: 12.77,  200: 12.468, 400: 12.174, 600: 11.886, 800: 11.605, 1000: 11.331, 1200: 11.063, 1400: 10.801, 1600: 10.546, 1800: 10.296},
		{0: 12.447, 200: 12.174, 400: 11.866, 600: 11.585, 800: 11.311, 1000: 11.044, 1200: 10.782, 1400: 10.527, 1600: 10.278, 1800: 10.035},
		{0: 12.138, 200: 11.851, 400: 11.571, 600: 11.297, 800: 11.03,  1000: 10.769, 1200: 10.514, 1400: 10.265, 1600: 10.022, 1800: 9.784},
		{0: 11.843, 200: 11.562, 400: 11.289, 600: 11.021, 800: 10.76,  1000: 10.505, 1200: 10.256, 1400: 10.013, 1600: 9.776,  1800: 9.544},
		{0: 11.559, 200: 11.285, 400: 11.018, 600: 10.757, 800: 10.502, 1000: 10.253, 1200: 10.01,  1400: 9.772,  1600: 9.54,   1800: 9.314},
		{0: 11.288, 200: 11.02,  400: 10.759, 600: 10.504, 800: 10.254, 1000: 10.011, 1200: 9.773,  1400: 9.541,  1600: 9.315,  1800: 9.093},
		{0: 11.027, 200: 10.765, 400: 10.51,  600: 10.26,  800: 10.017, 1000: 9.779,  1200: 9.546,  1400: 9.319,  1600: 9.098,  1800: 8.881},
		{0: 10.777, 200: 10.521, 400: 10.271, 600: 10.027, 800: 9.789,  1000: 9.556,  1200: 9.329,  1400: 9.107,  1600: 8.89,   1800: 8.678},
		{0: 10.536, 200: 10.286, 400: 10.041, 600: 9.803,  800: 9.569,  1000: 9.342,  1200: 9.119,  1400: 8.902,  1600: 8.69,   1800: 8.483},
		{0: 10.306, 200: 10.06,  400: 9.821,  600: 9.587,  800: 9.359,  1000: 9.136,  1200: 8.918,  1400: 8.705,  1600: 8.498,  1800: 8.295},
		{0: 10.084, 200: 9.843,  400: 9.609,  600: 9.38,   800: 9.156,  1000: 8.938,  1200: 8.724,  1400: 8.516,  1600: 8.313,  1800: 8.114},
		{0: 9.87,   200: 9.635,  400: 9.405,  600: 9.18,   800: 8.961,  1000: 8.747,  1200: 8.538,  1400: 8.334,  1600: 8.135,  1800: 7.94},
		{0: 9.665,  200: 9.434,  400: 9.209,  600: 8.988,  800: 8.774,  1000: 8.564,  1200: 8.359,  1400: 8.159,  1600: 7.963,  1800: 7.772},
		{0: 9.467,  200: 9.24,   400: 9.019,  600: 8.804,  800: 8.593,  1000: 8.387,  1200: 8.186,  1400: 7.99,   1600: 7.798,  1800: 7.611},
		{0: 9.276,  200: 9.054,  400: 8.837,  600: 8.625,  800: 8.418,  1000: 8.216,  1200: 8.019,  1400: 7.827,  1600: 7.639,  1800: 7.455},
		{0: 9.092,  200: 8.874,  400: 8.661,  600: 8.453,  800: 8.25,   1000: 8.052,  1200: 7.858,  1400: 7.669,  1600: 7.485,  1800: 7.304},
		{0: 8.914,  200: 8.7,    400: 8.491,  600: 8.287,  800: 8.088,  1000: 7.893,  1200: 7.703,  1400: 7.518,  1600: 7.336,  1800: 7.159},
		{0: 8.743,  200: 8.533,  400: 8.328,  600: 8.127,  800: 7.931,  1000: 7.74,   1200: 7.553,  1400: 7.371,  1600: 7.193,  1800: 7.019},
		{0: 8.578,  200: 8.371,  400: 8.169,  600: 7.972,  800: 7.78,   1000: 7.592,  1200: 7.408,  1400: 7.229,  1600: 7.054,  1800: 6.883},
		{0: 8.418,  200: 8.214,  400: 8.016,  600: 7.822,  800: 7.633,  1000: 7.449,  1200: 7.268,  1400: 7.092,  1600: 6.92,   1800: 6.752},
		{0: 8.263,  200: 8.063,  400: 7.868,  600: 7.678,  800: 7.491,  1000: 7.31,   1200: 7.132,  1400: 6.959,  1600: 6.79,   1800: 6.625},
		{0: 8.113,  200: 7.917,  400: 7.725,  600: 7.537,  800: 7.354,  1000: 7.175,  1200: 7.001,  1400: 6.83,   1600: 6.664,  1800: 6.501},
		{0: 7.968,  200: 7.775,  400: 7.586,  600: 7.401,  800: 7.221,  1000: 7.045,  1200: 6.873,  1400: 6.706,  1600: 6.542,  1800: 6.382},
		{0: 7.827,  200: 7.637,  400: 7.451,  600: 7.269,  800: 7.092,  1000: 6.919,  1200: 6.75,   1400: 6.584,  1600: 6.423,  1800: 6.266},
		{0: 7.691,  200: 7.503,  400: 7.32,   600: 7.141,  800: 6.967,  1000: 6.796,  1200: 6.63,   1400: 6.467,  1600: 6.308,  1800: 6.153},
		{0: 7.559,  200: 7.374,  400: 7.193,  600: 7.017,  800: 6.845,  1000: 6.677,  1200: 6.513,  1400: 6.353,  1600: 6.196,  1800: 6.043},
		{0: 7.43,   200: 7.248,  400: 7.07,   600: 6.896,  800: 6.727,  1000: 6.561,  1200: 6.399,  1400: 6.241,  1600: 6.087,  1800: 5.937},
		{0: 7.305,  200: 7.125,  400: 6.95,   600: 6.779,  800: 6.612,  1000: 6.448,  1200: 6.289,  1400: 6.133,  1600: 5.981,  1800: 5.833},
		{0: 7.183,  200: 7.006,  400: 6.833,  600: 6.665,  800: 6.5,    1000: 6.339,  1200: 6.181,  1400: 6.028,  1600: 5.878,  1800: 5.731},
		{0: 7.065,  200: 6.89,   400: 6.72,   600: 6.553,  800: 6.39,   1000: 6.232,  1200: 6.077,  1400: 5.925,  1600: 5.777,  1800: 5.633},
		{0: 6.949,  200: 6.777,  400: 6.609,  600: 6.445,  800: 6.284,  1000: 6.127,  1200: 5.974,  1400: 5.825,  1600: 5.679,  1800: 5.536},
		{0: 6.837,  200: 6.667,  400: 6.501,  600: 6.338,  800: 6.18,   1000: 6.025,  1200: 5.874,  1400: 5.727,  1600: 5.583,  1800: 5.442},
		{0: 6.727,  200: 6.559,  400: 6.395,  600: 6.235,  800: 6.078,  1000: 5.926,  1200: 5.776,  1400: 5.631,  1600: 5.489,  1800: 5.35},
		{0: 6.62,   200: 6.454,  400: 6.292,  600: 6.134,  800: 5.979,  1000: 5.828,  1200: 5.681,  1400: 5.537,  1600: 5.396,  1800: 5.259},
		{0: 6.515,  200: 6.351,  400: 6.191,  600: 6.035,  800: 5.882,  1000: 5.733,  1200: 5.587,  1400: 5.445,  1600: 5.306,  1800: 5.171},
		{0: 6.412,  200: 6.25,   400: 6.092,  600: 5.937,  800: 5.787,  1000: 5.639,  1200: 5.495,  1400: 5.355,  1600: 5.218,  1800: 5.084},
	];

	//case 1: temp and elevation defined
	if(Table[temperature] && Table[temperature][elevation]){
		console.log('temperature and elevation defined, no interpolation needed');
		return Table[temperature][elevation];
	}

	//case 2: temp defined and elevation undefined
	if(Table[temperature] && !Table[temperature][elevation]) {
		console.log('temperature defined and elevation undefined => performing linear interpolation (axis x)');
		//find elevation above and below
		var Elevations=[0,200,400,600,800,1000,1200,1400,1600,1800];
		for(var i=1;i<Elevations.length;i++){
			if ((Elevations[i-1]<elevation) && (elevation<Elevations[i])){
				var e_below = Elevations[i-1];
				var e_above = Elevations[i];
				break;
			}
		}
		var percentage = (elevation-e_below)/(e_above-e_below);
		var s_below = Table[temperature][e_below];
		var s_above = Table[temperature][e_above];
		console.log('value between '+s_below+' and '+s_above);
		var s_range = s_above - s_below;
		var s_added = s_range*percentage;
		var s_inter = s_below + s_added;
		return s_inter;
	}

	//case 3: temp undefined and elevation defined
	if(!Table[temperature] && Table[0][elevation]) {
		console.log('temperature undefined and elevation defined => performing linear interpolation (axis y)');
		//find temperature above and below
		var t_below = Math.floor(temperature);
		var t_above = Math.ceil(temperature);
		var percentage = (temperature-t_below)/(t_above-t_below);
		var s_below = Table[t_below][elevation];
		var s_above = Table[t_above][elevation];
		console.log('value between '+s_below+' and '+s_above);
		var s_range = s_above - s_below;
		var s_added = s_range*percentage;
		var s_inter = s_below + s_added;
		return s_inter;
	}
	else{
		//case 4: bilinear interpolation
		console.log('temperature undefined and elevation undefined => performing bilinear interpolation (axis x,y)');
		//find temperature above and below (x1,x2)
		var x1 = Math.floor(temperature);
		var x2 = Math.ceil(temperature);
		//find elevation above and below (y1,y2)
		var Elevations=[0,200,400,600,800,1000,1200,1400,1600,1800];
		for(var i=1;i<Elevations.length;i++){
			if ((Elevations[i-1]<elevation) && (elevation<Elevations[i])){
				var y1 = Elevations[i-1];
				var y2 = Elevations[i];
				break;
			}
		}

		//change names to inputs for formula convenience
		var x=temperature;
		var y=elevation;

		//now we need 4 values of solubility from the table: top-left, top-right, bottom-left, bottom-right
		var f_x1_y1 = Table[x1][y1];
		var f_x1_y2 = Table[x1][y2];
		var f_x2_y1 = Table[x2][y1];
		var f_x2_y2 = Table[x2][y2];

		console.log("Bilinear interpolation f(x,y), x="+x+", y="+y+", between: ");
		console.log("f(x1,y1): "+f_x1_y1);
		console.log("f(x1,y2): "+f_x1_y2);
		console.log("f(x2,y1): "+f_x2_y1);
		console.log("f(x2,y2): "+f_x2_y2);

		//apply linear interpolation in the x direction
		var f_x_y1 = (x2-x)/(x2-x1)*f_x1_y1 + (x-x1)/(x2-x1)*f_x2_y1;
		var f_x_y2 = (x2-x)/(x2-x1)*f_x1_y2 + (x-x1)/(x2-x1)*f_x2_y2;
		//proceed to interpolate the y direction to obtain f(x,y) estimate
		var f_x_y = (y2-y)/(y2-y1)*f_x_y1 + (y-y1)/(y2-y1)*f_x_y2;
		return f_x_y;
	}
}