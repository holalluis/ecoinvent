<?php /*
  ELEMENTARY FLOWS (SINGLE PLANT MODEL)
  -------------------------------------
  - The backend is in 'elementary.js' (data structures + technology appliying)
  - The views and frontend is implemented here
*/?>
<!doctype html><html><head>
  <?php include'imports.php'?>
  <title>Elementary Flows</title>

  <!--load backend: elementary flows and mass balances-->
  <script src="elementary.js"></script>
  <script src="mass_balances.js"></script>

  <!--init-->
  <script>
    //"init" is fired each time an input changes
    function init(){
      //1. reset all outputs to zero
      (function reset_all_outputs(){
        for(var out in Outputs){
          Outputs[out].influent=0;
          Outputs[out].effluent.water=0;
          Outputs[out].effluent.air=0;
          Outputs[out].effluent.sludge=0;
        }
      })();

      //2. make incompatible technologies inactive
      (function disable_impossible_options(){
        //if bod removal is false
        //  disable nitrification
        //  disable sst sizing
        //  disable denitri
        //  disable bioP
        //  disable chemP
        //  disable metals
        if(getInput('BOD',true).value==false){
          getInput('SST',true).value=false;
          getInput('Nit',true).value=false;
          getInput('Des',true).value=false;
          getInput('BiP',true).value=false;
          getInput('ChP',true).value=false;
          getInput('Met',true).value=false;
        }else{
          getInput('Fra',true).value=true; //if bod active, fra active
          getInput('SST',true).value=true; //if bod active, sst active
        }

        //only one of the two P removal technologies is allowed
        if(getInput('BiP',true).value){ getInput('ChP',true).value=false }
        if(getInput('ChP',true).value){ getInput('BiP',true).value=false }

        //denitri is possible only if nitri
        if(getInput('Nit',true).value==false){
          getInput('Des',true).value=false;
        }
      })();
      //3. disable technology checkboxes accordingly
      (function(){
        function set_checkbox_disabled(tech,newValue){
          var el=document.querySelector('#inputs_tech input[tech='+tech+']')
          el.disabled=newValue;
          if(newValue){
            el.checked=false;
          }
          el.parentNode.parentNode.style.color= newValue ? '#aaa' : '';
        }
        if(getInput('BOD',true).value==false) {
          set_checkbox_disabled('Nit',true);
          set_checkbox_disabled('Des',true);
          set_checkbox_disabled('BiP',true);
          set_checkbox_disabled('ChP',true);
          set_checkbox_disabled('Met',true);
        }else{
          set_checkbox_disabled('Nit',false);
          set_checkbox_disabled('BiP',false);
          set_checkbox_disabled('ChP',false);
          set_checkbox_disabled('Met',false);
        }
        if(getInput('Nit',true).value==false){
          set_checkbox_disabled('Des',true);
        }else{
          set_checkbox_disabled('Des',false);
        }
        if(getInput('BiP',true).value==true){
          set_checkbox_disabled('ChP',true);
        }
        if(getInput('ChP',true).value==true){
          set_checkbox_disabled('BiP',true);
        }
      })();

      //find current inputs from the technology combination to create the input table
      var Inputs_current_combination=[];
      (function set_current_inputs(){
        var input_codes=[];

        //technologies active array
        Technologies_selected
          .map(tec=>{return tec.id})
          .filter(tec=>{return getInput(tec,true).value})
          .forEach(tec=>{
            if(!Technologies[tec])return;
            input_codes=input_codes.concat(Technologies[tec].Inputs)
          });

        //remove duplicates
        input_codes=uniq(input_codes);

        //recalculate current inputs array
        input_codes.forEach(code=>{
          Inputs_current_combination.push(code);
        });
        //console.log(Inputs_current_combination);
      })();

      //frontend set the color of inputs (grey:not needed, black:needed);
      Inputs.forEach(i=>{
        var h=document.getElementById(i.id).parentNode.parentNode;
        if(Inputs_current_combination.indexOf(i.id)+1){
          h.style.color="";
        }else{
          h.style.color="#aaa";
        }
      });

      /* Main backend function "compute_elementary_flows" */
      //it will fill "Variables" & "Outputs" data structures
      (function(){
        var Input_set={
          //technologies activated
          is_Pri_active : getInput("Pri",true).value,
          is_BOD_active : getInput("BOD",true).value,
          is_Nit_active : getInput("Nit",true).value,
          is_Des_active : getInput("Des",true).value,
          is_BiP_active : getInput("BiP",true).value,
          is_ChP_active : getInput("ChP",true).value,
          is_Met_active : getInput("Met",true).value,

          //ww characteristics
          Q          : getInput('Q').value, //22700
          T          : getInput('T').value, //12
          COD        : getInput('COD').value, //300
          bCOD       : getInput('bCOD').value, //224
          sCOD       : getInput('sCOD').value, //132
          BOD        : getInput('BOD').value, //140
          sBOD       : getInput('sBOD').value, //70
          TSS        : getInput('TSS').value, //70
          VSS        : getInput('VSS').value, //60
          TKN        : getInput('TKN').value, //35
          Alkalinity : getInput('Alkalinity').value, //140
          TP         : getInput('TP').value, //6

          //influent metals
          Ag : getInput('Ag').value,
          Al : getInput('Al').value,
          As : getInput('As').value,
          B  : getInput('B').value,
          Ba : getInput('Ba').value,
          Be : getInput('Be').value,
          Br : getInput('Br').value,
          Ca : getInput('Ca').value,
          Cd : getInput('Cd').value,
          Cl : getInput('Cl').value,
          Co : getInput('Co').value,
          Cr : getInput('Cr').value,
          Cu : getInput('Cu').value,
          F  : getInput('F').value,
          Fe : getInput('Fe').value,
          Hg : getInput('Hg').value,
          I  : getInput('I').value,
          K  : getInput('K').value,
          Mg : getInput('Mg').value,
          Mn : getInput('Mn').value,
          Mo : getInput('Mo').value,
          Na : getInput('Na').value,
          Ni : getInput('Ni').value,
          Pb : getInput('Pb').value,
          Sb : getInput('Sb').value,
          Sc : getInput('Sc').value,
          Se : getInput('Se').value,
          Si : getInput('Si').value,
          Sn : getInput('Sn').value,
          Sr : getInput('Sr').value,
          Ti : getInput('Ti').value,
          Tl : getInput('Tl').value,
          V  : getInput('V').value,
          W  : getInput('W').value,
          Zn : getInput('Zn').value,

          //design parameters
          removal_bp           : getInput('removal_bp').value,  //%
          removal_nbp          : getInput('removal_nbp').value, //%
          removal_iss          : getInput('removal_iss').value, //%
          SRT                  : getInput('SRT').value, //5
          MLSS_X_TSS           : getInput('MLSS_X_TSS').value, //3000
          zb                   : getInput('zb').value, //500
          Pressure             : getInput('Pressure').value, //95600
          Df                   : getInput('Df').value, //4.4
          DO                   : getInput('DO').value, //2.0
          SF                   : getInput('SF').value, //1.5
          NH4_eff              : getInput('NH4_eff').value, //0.50
          sBODe                : getInput('sBODe').value, //3
          TSSe                 : getInput('TSSe').value, //10
          Anoxic_mixing_energy : getInput('Anoxic_mixing_energy').value, //5
          NO3_eff              : getInput('NO3_eff').value, //6
          SOR                  : getInput('SOR').value, //24
          X_R                  : getInput('X_R').value, //8000
          clarifiers           : getInput('clarifiers').value, //3
          TSS_removal_wo_Fe    : getInput('TSS_removal_wo_Fe').value, //60
          TSS_removal_w_Fe     : getInput('TSS_removal_w_Fe').value, //75
          C_PO4_eff            : getInput('C_PO4_eff').value, //0.1
          FeCl3_solution       : getInput('FeCl3_solution').value, //37
          FeCl3_unit_weight    : getInput('FeCl3_unit_weight').value, //1.35
          days                 : getInput('days').value, //15

          //these three inputs had an equation but they are now inputs again
          rbCOD                : getInput('rbCOD').value, //80 g/m3
          VFA                  : getInput('VFA').value, //15 g/m3
          //var C_PO4_inf      : getInput('C_PO4_inf').value, //5 g/m3
        };
        var Result=compute_elementary_flows(Input_set);

        //fill summary tables (section 3.3)
        (function fill_summary_tables(){
          //get all variable codes from summary table
          var codes=(function(){
            var codes=[];
            var spans=document.querySelectorAll('#summary span[id]');
            for(var i=0;i<spans.length;i++){
              codes.push(spans[i].id);
            }
            return codes;
          })();
          codes.forEach(id=>{ //id: DOM element id <string>
            var value = Result.summary[id] ? Result.summary[id].value : 0;
            var el=document.querySelector('#summary #'+id);

            var unit = Result.summary[id] ? Result.summary[id].unit  : "";
            el.innerHTML=format(value)+" <small>"+unit.prettifyUnit()+"</small>";

            if(!value){
              el.parentNode.style.color= isNaN(value) ? 'red' : '#aaa';
              return;
            }else el.parentNode.style.color='';

          });
        })();
      })();

      /*
       * update frontend with calculated values
       */
      (function updateViews(){
        //update number of inputs and variables
        document.querySelector('#input_amount').innerHTML=(function(){
          var a=Inputs_current_combination.length;
          var b=Inputs.length;
          return a+" of "+b;
        })();
        document.querySelector('#variable_amount').innerHTML=Variables.length;

        //create variables table
        (function(){
          var table=document.querySelector('table#variables');
          while(table.rows.length>1){table.deleteRow(-1)}
          if(Variables.length==0){
            table.insertRow(-1).insertCell(-1).outerHTML="<td colspan=4 style=text-align:center><em>~Activate some technologies first";
          }

          Variables.forEach((i,ii)=>{
            var newRow=table.insertRow(-1);

            if(ii>0 && (Variables[ii-1].tech != i.tech)){
              newRow.style.borderTop="1px solid #ccc";
            }

            //tech name
            (function(){
              var tech_name = Technologies[i.tech] ? Technologies[i.tech].Name : i.tech;
              newRow.setAttribute('tech',i.tech);
              newRow.insertCell(-1).outerHTML="<td class=help title='"+tech_name+"' style='font-family:monospace'>"+i.tech;
            })();

            //variable name and link to source code
            (function(){
              var path = Technologies[i.tech] ? "techs" : ".";
              var file = Technologies[i.tech] ? Technologies[i.tech].File : "elementary.js";
              var link="<a href='see.php?path="+path+"&file="+file+"&remark="+i.id+"' target=_blank>"+i.id+"</a>";
              newRow.insertCell(-1).outerHTML="<td class=help title='"+i.descr.replace(/_/g,' ')+"' style='font-family:monospace'>"+link;
            })();

            //color remark if value==zero
            (function(){
              var color=i.value ? "" : "style='background:linear-gradient(to left,yellow,white)'";
              newRow.insertCell(-1).outerHTML="<td class=number "+color+">"+format(i.value);
              newRow.insertCell(-1).outerHTML="<td class=unit>"+i.unit.prettifyUnit();
            })();
          });

        })();

        //deal with outputs selected unit before updating outputs (default is kg/d)
        //initially they are in g/d
        (function unit_change_outputs(){
          if(Options.currentUnit.value=="g/m3") {
            var Q=getInput('Q').value; //flowrate;
            for(var out in Outputs){
              Outputs[out].influent        /= Q;
              Outputs[out].effluent.water  /= Q;
              Outputs[out].effluent.air    /= Q;
              Outputs[out].effluent.sludge /= Q;
            }
          }else{
            for(var out in Outputs){
              Outputs[out].influent        /= 1000;
              Outputs[out].effluent.water  /= 1000;
              Outputs[out].effluent.air    /= 1000;
              Outputs[out].effluent.sludge /= 1000;
            }
          }
        })();

        //update outputs
        (function(){
          var t=document.querySelector('#outputs');
          for(var output in Outputs) {
            var tr=t.querySelector('#'+output);
            //influent
            var value = Outputs[output].influent;
            var color = value ? '':'#aaa';
            tr.querySelector('td[phase=influent]').innerHTML=format(value,false,color);
            //effluent
            ['water','air','sludge'].forEach(phase=>{
              var value = Outputs[output].effluent[phase];
              var color = value ? '':'#aaa';
              tr.querySelector('td[phase='+phase+']').innerHTML=format(value,false,color);
            });
          }
        })();
      })();

      //set "scroll to" links visibility
      (function(){
        function set_scroll_link_visibility(tec){
          var el=document.querySelector('#variable_scrolling a[tech='+tec+']')
          if(el){
            el.style.display=getInput(tec,true).value ? "":"none";
          }
        }
        Technologies_selected.forEach(t=>{
          set_scroll_link_visibility(t.id)
        });
      })();

      //MASS BALANCES (end part)
      do_mass_balances();

      //update ouputs "<span class=currentUnit>" elements that show the selected unit
      Options.currentUnit.update();
    }
  </script>

  <!--user options object-->
  <script>
    //options
    var Options={
      /*the user can select the Outputs displayed unit */
      currentUnit:{
        value:"kg/d",
        update:function(){
          var els=document.querySelectorAll('span.currentUnit');
          for(var i=0;i<els.length;i++){
            els[i].innerHTML=this.value.prettifyUnit();
          }
        }
      },
      /*further user-options here*/
    }
  </script>

  <!--CSS-->
  <style>
    #root hr{
      margin:0px auto;
    }
    #root th{
      background:#eee;
    }
    #root input[type=number]{
      text-align:right;
    }
    #root #mass_balances [phase]{
      text-align:right;
    }
    #root .help:hover{
      text-decoration:underline;
    }
    #root #inputs, #root #variables {
      font-size:smaller;
    }
    #root .circle{
      text-align:center;
      border-radius:17px;
      width:17px;
    }
    #root #summary > ul {
      font-size:smaller;
    }
    #root #summary ul ul{
      padding-left:20px;
    }
    #root table#inputs_tech,
    #root table#inputs,
    #root table#variables,
    #root table#outputs,
    #root table#mass_balances {
      width:100%;
      border-collapse:collapse;
    }
  </style>
</head><body onload="init()">
<?php include'navbar.php'?>

<div id=root>
<h1>Elementary Flows (single plant model)</h1>

<!--hints-->
<p style=font-size:smaller>
  Note: mouse over inputs and variables to see a description.
  <br>
  Note: modify inputs using the <kbd>&uarr;</kbd> and <kbd>&darr;</kbd> keys.
</p>
<hr>

<!--INPUTS AND OUTPUTS VIEW SCAFFOLD-->
<div class=flex>
  <!--1. Inputs-->
  <div>
    <p><b><u>1. User Inputs</u></b></p>

    <!--load influent file component-->
    <div style=font-size:smaller>
      Load an influent file:
      <div>
        <script>
          function loadFile(evt) {
              var file=evt.target.files[0];
              var reader=new FileReader();
              reader.onload=function() {
                var saved_file;
                try{
                  saved_file=JSON.parse(reader.result);
                  (function(){
                    //technologies
                    Object.keys(saved_file.techs).forEach(key=>{
                      var newValue=saved_file.techs[key].value;
                      document.querySelector('#inputs_tech input[tech='+key+']').checked=newValue;
                      getInput(key,true).value=newValue;
                    });
                    //inputs
                    Object.keys(saved_file.inputs).forEach(key=>{
                      var newValue=saved_file.inputs[key].value;
                      document.querySelector('#inputs #'+key).value=newValue;
                      getInput(key,false).value=newValue;
                    });
                  })();
                  init();
                }catch(e){alert(e)}
              }
              try{
                reader.readAsText(file);
              }catch(e){alert(e)}

              //show "loaded successfully"
              (function(){
                var div=document.createElement('p');
                div.style.background="lightgreen";
                div.style.fontFamily="monospace";
                div.style.padding="3px 5px";
                div.innerHTML="File loaded correctly <button onclick=this.parentNode.parentNode.removeChild(this.parentNode)>ok</button>";
                document.querySelector("#loadFile").parentNode.appendChild(div);
                div.querySelector('button').focus();
                setTimeout(function(){if(div.parentNode){div.parentNode.removeChild(div)}},5000);
              })();
          }
        </script>
        <input id=loadFile type=file accept=".json" onchange="loadFile(event)"
          style="
            width:100%;
          "
        >
      </div>

      <!--save as json file component-->
      <p>
        Save an influent file:
        <button id=saveToFile onclick="saveToFile()"
          style="
            width:100%;
          "
        >Save influent as...</button>
        <script>
          /*Generate a json/text file*/
          function saveToFile() {
            var saved_file = {
              techs:{
              },
              inputs:{
              },
            }
            Technologies_selected.filter(t=>{return !t.notActivable}).forEach(t=>{
              saved_file.techs[t.id]={
                descr:t.descr,
                value:t.value,
              };
            });
            Inputs.forEach(i=>{
              saved_file.inputs[i.id]={
                descr:i.descr,
                value:i.value,
                unit:i.unit,
              };
            });
            var datestring=(new Date()).toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,13);
            //console.log(datestring);
            var link=document.createElement('a');
            link.href="data:text/json;charset=utf-8,"+JSON.stringify(saved_file,null,'  ');
            link.download="inf"+datestring+"UTC.json";
            link.click();
          }
        </script>
      </p>
    </div>

    <!--enter technologies-->
    <div>
      <p>1.1. Activate technologies of your plant</p>
      <table id=inputs_tech border=1></table>
    </div>

    <!--enter ww characteristics-->
    <div>
      <p>1.2. Enter influent inputs
        <small>(required: <span id=input_amount>0</span>)</small>
      </p>

      <!--set all inputs to zero-->
      <p style=margin-top:0>
        <button style="width:100%"
          onclick="(function(){
            var inputs=document.querySelectorAll('#inputs input');
            for(var i=0;i<inputs.length;i++){
              inputs[i].value=0;
              getInput(inputs[i].id).value=0;
            }
            init();
          })()">Set all inputs to zero</button>
      </p>

      <!--inputs table-->
      <table id=inputs border=1>
        <tr><th>Input<th>Value<th>Unit
        <style>
          #inputs input[type=number]{
            border:none;
            width:80px;
          }
        </style>
      </table>

      <!--go to top link-->
      <div style=font-size:smaller><a href=#>&uarr; top</a></div>
    </div>
  </div><hr>

  <!--2. Variables (intermediate step between inputs and outputs)-->
  <div style=width:360px>
    <p><b>
      <u>2. Variables calculated: <span id=variable_amount>0</span></u>
    </b></p>

    <!--links for scrolling variables-->
    <div id=variable_scrolling style=font-size:smaller>
      <script>
        //frontend function: scroll to variables of a technology
        function scroll2tec(tec){
          var els=document.querySelectorAll('#variables tr[tech='+tec+']');
          els[0].scrollIntoView();
          //create a mini animation of changing colors
          for(var i=0;i<els.length;i++) {
            els[i].style.transition='background 0.4s';
            els[i].style.background='lightblue';
          }
          setTimeout(function(){
            for(var i=0;i<els.length;i++) {
              els[i].style.background='white';
            }
          },800);
          //end animation
        }
      </script>
      Scroll to &rarr;
      <a          href=# onclick="scroll2tec('Pri');                    return false">Pri</a>
      <a tech=Fra href=# onclick="scroll2tec(this.getAttribute('tech'));return false">Fra</a>
      <a tech=BOD href=# onclick="scroll2tec(this.getAttribute('tech'));return false">BOD</a>
      <a tech=Nit href=# onclick="scroll2tec(this.getAttribute('tech'));return false">Nit</a>
      <a tech=SST href=# onclick="scroll2tec(this.getAttribute('tech'));return false">SST</a>
      <a tech=Des href=# onclick="scroll2tec(this.getAttribute('tech'));return false">Des</a>
      <a tech=BiP href=# onclick="scroll2tec(this.getAttribute('tech'));return false">BiP</a>
      <a tech=ChP href=# onclick="scroll2tec(this.getAttribute('tech'));return false">ChP</a>
      <a tech=Met href=# onclick="scroll2tec(this.getAttribute('tech'));return false">Met</a>
      <script>
        //add <a title=description> for the scroll links
        (function(){
          var els=document.querySelectorAll('#variable_scrolling a[tech]');
          for(var i=0;i<els.length;i++){
            els[i].title=Technologies[els[i].getAttribute('tech')].Name;
          }
        })();
      </script>
    </div>

    <!--Variables-->
    <table id=variables><tr>
      <th>Tech
      <th>Variable
      <th>Result
      <th>Unit
    </table>

    <!--link go to top--><div><small><a href='#'>&uarr; top</a></small></div>
  </div><hr>

  <!--3. Outputs-->
  <div style=width:360px>
    <p><b><u>3. Outputs</u></b></p>

    <!--menu to change output units (kg/d or g/m3)-->
    <div style=font-size:smaller>
      Select units:
      <label>
        <input type=radio name=currentUnit value="kg/d" onclick="Options.currentUnit.value=this.value;init()" checked> kg/d
      </label><label>
        <input type=radio name=currentUnit value="g/m3" onclick="Options.currentUnit.value=this.value;init()"> g/m<sup>3</sup>
      </label>
    </div>

    <!--table effluent phases-->
    <div>
      <p>3.1. Effluent</p>
      <table id=outputs border=1 style=font-size:smaller>
        <tr>
          <th rowspan=2>Compound
          <th rowspan=2>Influent <small>(<span class=currentUnit>kg/d</span>)</small>
          <th colspan=3>Effluent <small>(<span class=currentUnit>kg/d</span>)</small>
        <tr>
          <th>Water<th>Air<th>Sludge
        </tr>
      </table>
    </div>

    <!--table mass balances-->
    <div>
      <p>3.2. Mass balances <small>(<a href="see.php?path=.&file=mass_balances.js" target=_blank>equations</a>)</small></p>
      <table id=mass_balances border=1 style=font-size:smaller>
        <tr>
          <th rowspan=2>Element <th rowspan=2>Influent<br><small>(<span class=currentUnit>kg/d</span>)</small>
          <th colspan=3>Effluent <small>(<span class=currentUnit>kg/d</span>)</small>
          <th rowspan=2>|Error|<br><small>(%)</small>
        <tr>
          <th>Water<th>Air<th>Sludge
        <tr id=C><th>COD<td phase=influent><td phase=water><td phase=air><td phase=sludge><td phase=balance>
        <tr id=N><th>N  <td phase=influent><td phase=water><td phase=air><td phase=sludge><td phase=balance>
        <tr id=P><th>P  <td phase=influent><td phase=water><td phase=air><td phase=sludge><td phase=balance>
      </table>
    </div>

    <!--summary tables-->
    <div id=summary>
      <!--DESIGN SUMMARY-->
      <p>3.3. Design summary
        <em style=display:block;font-size:smaller>The following variables have been selected from table 2</em>
        <ul>
          <li>Solids Retention Time (SRT):       <span id=SRT>0</span>
          <li>Hydraulic detention time (&tau;):  <span id=tau>0</span>
          <li>MLVSS:                             <span id=MLVSS>0</span>
          <!-- <li>BOD loading:                       <span id=BOD_loading>0</span> -->
          <li>Total sludge production:           <span id=P_X_TSS>0</span>
            <ul>
              <li>f_O: <span id=sludge_f_O>0</span>
              <li>f_H: <span id=sludge_f_H>0</span>
              <li>
                <issue class=under_dev></issue>
                <issue class=help_wanted></issue>
              </li>
            </ul>
          </li>
          <li>Observed yield
            <ul>
              <li>Y_obs_TSS:                     <span id=Y_obs_TSS>0</span>
              <li>Y_obs_VSS:                     <span id=Y_obs_VSS>0</span>
            </ul>
          </li>
          <li>Aeration
            <ul>
              <li>Air flowrate:                  <span id=air_flowrate>0</span>
              <li>O<sub>2</sub> required (OTRf): <span id=OTRf>0</span>
              <li>SOTR:                          <span id=SOTR>0</span>
              <li>SDNR (denitrification):        <span id=SDNR>0</span>
            </ul>
          </li>
          <li>RAS ratio:                         <span id=RAS>0</span>
          <li>Clarifiers
            <ul>
              <li>Amount:                        <span id=clarifiers>0</span>
              <li>Diameter:                      <span id=clarifier_diameter>0</span>
            </ul>
          </li>
          <li>Reactor volume
            <ul>
              <li>V<sub>aer</sub>:               <span id=V_aer>0</span>
              <li>V<sub>nox</sub>:               <span id=V_nox>0</span>
              <li>V<sub>ana</sub>:               <span id=V_ana>0</span>
              <li>V<sub>total</sub>:             <span id=V_total>0</span>
            </ul>
          </li>
          <li>Concrete (see <a href="construction.php" target=_blank>construction</a>)
            <ul>
              <li>Reactor: <span id=concrete_reactor>0</span>
              <li>Settler: <span id=concrete_settler>0</span>
                <issue class=under_dev></issue>
                <issue class=help_wanted></issue>
            </ul>
          </li>
        </ul>
      </p>

      <!--TECH SPHERE-->
      <p>3.4. Technosphere
        <button onclick="(function(){
          //TODO
        })()">&darr;</button>
        <ul id=technosphere>
          <li>Chemicals
            <ul>
              <li>
                Dewatering
                <ul>
                  <li>Copolymer of acrylamide 0.48%:
                  <span id=Dewatering_polymer>0</span>
                </ul>
              </li>
              <li>Alkalinity to maintain pH ~ 7.0
                <ul>
                  <li>For Nitrification:       <span id=alkalinity_added>0</span>
                  <li>For Denitrification:     <span id=Mass_of_alkalinity_needed>0</span>
                </ul>
              </li>
              <li>FeCl<sub>3</sub> used for chemical P removal
                <ul>
                  <li>Volume per day:          <span id=FeCl3_volume>0</span>
                  <li>Volume storage required: <span id=storage_req_15_d>0</span>
                </ul>
              </li>
            </ul>
          </li>
          <li>Energy for:
            <ul>
              <li>Aeration:                    <span id=aeration_power>0</span>
              <li>Anoxic Mixing:               <span id=mixing_power>0</span>
              <li>Pumping
                <ul>
                  <li>External recirculation:  <span id=pumping_power_external>0</span>
                  <li>Internal recirculation:  <span id=pumping_power_internal>0</span>
                  <li>Wastage  recirculation:  <span id=pumping_power_wastage> 0</span>
                </ul>
              </li>
              <li>Dewatering:                  <span id=dewatering_power>0</span>
              <li>Other:                       <span id=other_power>0</span>
              <li>Total energy
                <ul>
                  <li>Expressed as power needed:   <span id=total_power>0</span>
                  <li>Expressed as energy per day: <span id=total_daily_energy>0</span>
                  <li>Expressed as energy per m3:  <span id=total_energy_per_m3>0</span>
                </ul>
            </ul>
          </li>
        </ul>
      </p>
    </div>
  </div>
</div><hr>

<!--note for development-->
<p><div style=font-size:smaller>
  <?php include'btn_reset_cache.php'?>
</div></p>

<script>
  //POPULATE PAGE DEFAULT VALUES
  //this function only fires at the beggining
  (function(){
    //populate technologies table
    (function(){
      var t=document.querySelector('table#inputs_tech');
      //only technologies activable by user
      Technologies_selected
        .filter(tec=>{return !tec.notActivable})
        .forEach(tec=>{
          var newRow=t.insertRow(-1);
          //tec name
          newRow.insertCell(-1).innerHTML=tec.descr;
          //checkbox
          var checked = getInput(tec.id,true).value ? "checked" : "";
          newRow.insertCell(-1).outerHTML="<td style=text-align:center><input type=checkbox "+checked+" onchange=\"toggleTech('"+tec.id+"')\" tech='"+tec.id+"'>";
          //implementation link
          if(Technologies[tec.id]){
            newRow.insertCell(-1).innerHTML="<small><center>"+
              "<a href='see.php?path=techs&file="+Technologies[tec.id].File+"' title='see javascript implementation' target=_blank>"+
              "equations"+
              "</a></cente></small>"+
              "";
          }
      });
    })();

    //populate input table
    (function(){
      var table=document.querySelector('table#inputs');

      //add a row to table
      function process_input(i,display){
        display=display||"";
        var newRow=table.insertRow(-1);
        newRow.style.display=display;
        var advanced_indicator = i.color ? "<div class=circle style='background:"+i.color+"' title='Advanced knowledge required to modify this input'></div>" : "";
        //insert cells
        newRow.title=i.descr;
        newRow.insertCell(-1).outerHTML="<td class='flex help' style='justify-content:space-between'>"+i.id + advanced_indicator;
        newRow.insertCell(-1).innerHTML="<input id='"+i.id+"' value='"+i.value+"' type=number step=any onchange=setInput('"+i.id+"',this.value) min=0>"
        newRow.insertCell(-1).outerHTML="<td class=unit>"+i.unit.prettifyUnit();
      }

      //populate inputs (isParameter==false)
      (function(){
        var newRow=table.insertRow(-1);
        var newCell=document.createElement('th');
        newRow.appendChild(newCell);
        newCell.colSpan=3;
        newCell.style.textAlign='left';
        //add <button>+/-</button> Metals
        newCell.appendChild((function(){
          var btn=document.createElement('button');
          btn.innerHTML='↓';
          btn.addEventListener('click',function(){
            this.innerHTML=(this.innerHTML=='→')?'↓':'→';
            Inputs.filter(i=>{return !i.isParameter && !i.isMetal}).forEach(i=>{
              var h=document.querySelector('#inputs #'+i.id).parentNode.parentNode;
              h.style.display=h.style.display=='none'?'':'none';
            });
          });
          return btn;
        })());
        newCell.appendChild((function(){
          var span=document.createElement('span');
          span.innerHTML=' Wastewater characteristics';
          return span;
        })());
      })();
      Inputs.filter(i=>{return !i.isParameter && !i.isMetal}).forEach(i=>{
        process_input(i);
      });

      //populate design parameters (isParameter==true)
      (function(){
        var newRow=table.insertRow(-1);
        var newCell=document.createElement('th');
        newRow.appendChild(newCell);
        newCell.colSpan=3;
        newCell.style.textAlign='left';
        //add <button>+/-</button> Metals
        newCell.appendChild((function(){
          var btn=document.createElement('button');
          btn.innerHTML='↓';
          btn.addEventListener('click',function(){
            this.innerHTML=(this.innerHTML=='→')?'↓':'→';
            Inputs.filter(i=>{return i.isParameter}).forEach(i=>{
              var h=document.querySelector('#inputs #'+i.id).parentNode.parentNode;
              h.style.display=h.style.display=='none'?'':'none';
            });
          });
          return btn;
        })());
        newCell.appendChild((function(){
          var span=document.createElement('span');
          span.innerHTML=' Design parameters';
          return span;
        })());
      })();
      Inputs.filter(i=>{return i.isParameter}).forEach(i=>{
        process_input(i);
      });

      //populate metals (isMetal==true)
      (function(){
        var newRow=table.insertRow(-1);
        var newCell=document.createElement('th');
        newRow.appendChild(newCell);
        newCell.colSpan=3;
        newCell.style.textAlign='left';
        //add <button>+/-</button> Metals
        newCell.appendChild((function(){
          var btn=document.createElement('button');
          btn.innerHTML='→';
          btn.addEventListener('click',function(){
            this.innerHTML=(this.innerHTML=='→')?'↓':'→';
            Inputs.filter(i=>{return i.isMetal}).forEach(i=>{
              var h=document.querySelector('#inputs #'+i.id).parentNode.parentNode;
              h.style.display=h.style.display=='none'?'':'none';
            });
          });
          return btn;
        })());
        newCell.appendChild((function(){
          var span=document.createElement('span');
          span.innerHTML=' Metals';
          return span;
        })());
      })();
      Inputs.filter(i=>{return i.isMetal}).forEach(i=>{
        process_input(i,'none');
      });
    })();

    //populate outputs
    (function(){
      var table=document.querySelector('#outputs');
      function populate_output(key,display){
        display=display||"";
        var newRow=table.insertRow(-1);
        newRow.style.display=display;
        var output=Outputs[key];
        newRow.id=key;
        newRow.title=output.descr;
        //output id
        //link to source code
        var link="<a href='see.php?file=elementary.js&remark=Outputs."+key+"' target=_blank>"+key.prettifyUnit()+"</a>";
        newRow.insertCell(-1).outerHTML="<th style='font-weight:normal;'>"+link;
        //influent and effluent defaults as 0
        ['influent','water','air','sludge'].forEach(phase=>{
          newRow.insertCell(-1).outerHTML="<td phase="+phase+" class=number><span style=color:#aaa>0";
        });
      }

      //normal outputs
      (function(){
        var newRow=table.insertRow(-1);
        var newCell=document.createElement('th');
        newRow.appendChild(newCell);
        newCell.colSpan=5;
        newCell.style.textAlign='left';
        //add <button>+/-</button> Metals
        newCell.appendChild((function(){
          var btn=document.createElement('button');
          btn.innerHTML='↓';
          btn.addEventListener('click',function(){
            this.innerHTML=(this.innerHTML=='→')?'↓':'→';
            Object.keys(Outputs).filter(i=>{return !getInputById(i).isMetal}).forEach(i=>{
              var h=document.querySelector('#outputs #'+i);
              h.style.display=h.style.display=='none'?'':'none';
            });
          });
          return btn;
        })());
        newCell.appendChild((function(){
          var span=document.createElement('span');
          span.innerHTML=' Main compounds';
          return span;
        })());
      })();
      Object.keys(Outputs)
        .filter(key=>{return !getInputById(key).isMetal})
        .forEach(key=>{
          populate_output(key);
      });

      //metals
      (function(){
        var newRow=table.insertRow(-1);
        var newCell=document.createElement('th');
        newRow.appendChild(newCell);
        newCell.colSpan=5;
        newCell.style.textAlign='left';
        //add <button>+/-</button> Metals
        newCell.appendChild((function(){
          var btn=document.createElement('button');
          btn.innerHTML='→';
          btn.addEventListener('click',function(){
            this.innerHTML=(this.innerHTML=='→')?'↓':'→';
            Inputs.filter(i=>{return i.isMetal}).forEach(i=>{
              var h=document.querySelector('#outputs #'+i.id);
              h.style.display=h.style.display=='none'?'':'none';
            });
          });
          return btn;
        })());
        newCell.appendChild((function(){
          var span=document.createElement('span');
          span.innerHTML=' Metals';
          return span;
        })());
      })();
      Object.keys(Outputs)
        .filter(key=>{return getInputById(key).isMetal})
        .forEach(key=>{
          populate_output(key,'none');
      });
    })();

    //lcorominas requested hiding these inputs from frontend.
    //but these inputs should not be hidden
    /*
    [
      //'C_PO4_inf', //already calculated in elementary.js
      //'sBODe',     //used in nitrification.js
    ].forEach(id=>{
      document.querySelector('#inputs #'+id).parentNode.parentNode.style.display='none';
    });
    */
  })();
</script>
