<?php header('X-XSS-Protection:0')?>
<!doctype html>
<html>
  <head>
    <title>4/4 Generate ecoSpold files</title>
  </head>

<body onload="init()">
<style>
  ul {
    margin:0;
  }
</style>

<script>
  function init(){
    document.getElementById('please_wait').style.display='none';
  }
</script>

<!doctype html>
<a href="..">Home</a>
<h1>
  4. Generating ecoSpold files
  <small>(step 4 of 4)</small>
</h1>

<small id=please_wait>Please wait...</small>

<?php
  //receive OS command from POST[input] to invoke python
  $cmd=isset($_POST['input']) ? $_POST['input'] : './test.py a b c d';
?>

<!--shell prompt -->
  <form method=POST style=display:none>$
  <input name=input id=input value="<?php echo $cmd?>"
    placeholder="write command here" style="width:50%">
  </form>

<!--show cmd
<?php
  $formatted_cmd = strlen($cmd)>140 ? substr($cmd,0,140)."..." : $cmd;
  echo "<b><code>&gt; $formatted_cmd</code></b>";
?>
-->

<!--cmd output generated by shell-->
<pre style="background:#eee;padding:8px"><code><?php
  //var_dump(shell_exec($cmd." 2>&1"));
  $exportPath='export PATH=/home/clients/4dd5f0e34c312c0032584b62dbe7ba8e/opt/anaconda3/bin:/usr/local/bin:$PATH';
  $result=shell_exec("$exportPath; ".$cmd.' 2>&1'); //need to add the path of "python3"
  echo $result;
?>
</code></pre>

<!--focus on cmd prompt-->
<script>document.querySelector('#input').select();</script>

<!--pascal text-->
<div>
  The generated datasets are in the ecoSpold2 format (.xml). This type of files can
  be either opened in the ecoEditor (freeware published by ecoinvent), imported 
  into MS Excel, imported into LCA software tools which support import of ecoSpold2
  format, opened using XML editor or processed using different types of programming
  languages (e.g. Python).
</div>
