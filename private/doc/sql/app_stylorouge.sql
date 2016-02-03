<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Export: app_stylorouge - Adminer</title>
<link rel="stylesheet" type="text/css" href="adminer-4.2.3-en.php?file=default.css&amp;version=4.2.3">
<script type="text/javascript" src="adminer-4.2.3-en.php?file=functions.js&amp;version=4.2.3"></script>
<link rel="shortcut icon" type="image/x-icon" href="adminer-4.2.3-en.php?file=favicon.ico&amp;version=4.2.3">
<link rel="apple-touch-icon" href="adminer-4.2.3-en.php?file=favicon.ico&amp;version=4.2.3">

<body class="ltr nojs" onkeydown="bodyKeydown(event);" onclick="bodyClick(event);">
<script type="text/javascript">
document.body.className = document.body.className.replace(/ nojs/, ' js');
var offlineMessage = 'You are offline.';
</script>

<div id="help" class="jush-sql jsonly hidden" onmouseover="helpOpen = 1;" onmouseout="helpMouseout(this, event);"></div>

<div id="content">
<p id="breadcrumb"><a href="adminer-4.2.3-en.php">MySQL</a> &raquo; <a href='adminer-4.2.3-en.php?username=root' accesskey='1' title='Alt+Shift+1'>Server</a> &raquo; <a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge">app_stylorouge</a> &raquo; Export
<h2>Export: app_stylorouge</h2>
<div id='ajaxstatus' class='jsonly hidden'></div>

<form action="" method="post">
<table cellspacing="0">
<tr><th>Output<td><label><input type='radio' name='output' value='text' checked>open</label><label><input type='radio' name='output' value='file'>save</label><label><input type='radio' name='output' value='gz'>gzip</label>
<tr><th>Format<td><label><input type='radio' name='format' value='sql' checked>SQL</label><label><input type='radio' name='format' value='csv'>CSV,</label><label><input type='radio' name='format' value='csv;'>CSV;</label><label><input type='radio' name='format' value='tsv'>TSV</label>
<tr><th>Database<td><select name='db_style'><option><option>USE<option selected>DROP+CREATE<option>CREATE</select><label><input type='checkbox' name='routines' value='1'>Routines</label><label><input type='checkbox' name='events' value='1'>Events</label><tr><th>Tables<td><select name='table_style'><option><option selected>DROP+CREATE<option>CREATE</select><label><input type='checkbox' name='auto_increment' value='1' checked>Auto Increment</label><label><input type='checkbox' name='triggers' value='1' checked>Triggers</label><tr><th>Data<td><select name='data_style'><option><option>TRUNCATE+INSERT<option selected>INSERT<option>INSERT+UPDATE</select></table>
<p><input type="submit" value="Export">
<input type="hidden" name="token" value="886884:910004">

<table cellspacing="0">
<thead><tr><th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables' checked onclick='formCheck(this, /^tables\[/);'>Tables</label><th style='text-align: right;'><label class='block'>Data<input type='checkbox' id='check-data' checked onclick='formCheck(this, /^data\[/);'></label></thead>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='task' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">task</label><td align='right'><label class='block'><span id='Rows-task'></span><input type='checkbox' name='data[]' value='task' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='user' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">user</label><td align='right'><label class='block'><span id='Rows-user'></span><input type='checkbox' name='data[]' value='user' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='user_task' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">user_task</label><td align='right'><label class='block'><span id='Rows-user_task'></span><input type='checkbox' name='data[]' value='user_task' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<script type='text/javascript'>ajaxSetHtml('adminer-4.2.3-en.php?username=root&db=app_stylorouge&script=db');</script>
</table>
</form>
<p><a href='adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;dump=user%25'>user</a></div>

<form action="" method="post">
<p class="logout">
<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="886884:910004">
</p>
</form>
<div id="menu">
<h1>
<a href='https://www.adminer.org/' target='_blank' id='h1'>Adminer</a> <span class="version">4.2.3</span>
<a href="https://www.adminer.org/#download" target="_blank" id="version"></a>
</h1>
<script type="text/javascript" src="adminer-4.2.3-en.php?file=jush.js&amp;version=4.2.3"></script>
<script type="text/javascript">
var jushLinks = { sql: [ 'adminer-4.2.3-en.php?username=root&db=app_stylorouge&table=$&', /\b(task|user|user_task)\b/g ] };
jushLinks.bac = jushLinks.sql;
jushLinks.bra = jushLinks.sql;
jushLinks.sqlite_quo = jushLinks.sql;
jushLinks.mssql_bra = jushLinks.sql;
bodyLoad('5.5');
</script>
<form action="">
<p id="dbs">
<input type="hidden" name="username" value="root"><span title='database'>DB</span>: <select name='db' onmousedown='dbMouseDown(event, this);' onchange='dbChange(this);'><option value=""><option selected>app_stylorouge<option>information_schema<option>mysql<option>performance_schema<option>phpmyadmin<option>test</select><input type='submit' value='Use' class='hidden'>
<input type="hidden" name="dump" value=""></p></form>
<p class='links'><a href='adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;sql='>SQL command</a>
<a href='adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;import='>Import</a>
<a href='adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;dump=' id='dump' class='active '>Export</a>
<a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;create=">Create table</a>
<p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>
<a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;select=task" class='select'>select</a> <a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;table=task" title='Show structure'>task</a><br>
<a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;select=user" class='select'>select</a> <a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;table=user" title='Show structure'>user</a><br>
<a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;select=user_task" class='select'>select</a> <a href="adminer-4.2.3-en.php?username=root&amp;db=app_stylorouge&amp;table=user_task" title='Show structure'>user_task</a><br>
</div>
<script type="text/javascript">setupSubmitHighlight(document);</script>
