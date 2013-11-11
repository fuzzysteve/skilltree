<?php
$expires = 3599;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
$memcache = new Memcache;
$memcache->connect('localhost', 11211) or die ("Could not connect");

require_once('db.inc.php');
?>
<html>
<head>
<title>Skill unlock information</title>
  <link href="style.css" rel="stylesheet" type="text/css"/>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <link href="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="/blueprints/dataTables.currencySort.js"></script>

<script>
</script>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/header.php'); ?>
</head>
<body>
<?php include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php'); ?>
<div class="container">

<p>What does a Skill unlock at its various levels?</p>

<?php


if (is_numeric($_GET['skillid']))
{
$skillid=$_GET['skillid'];
echo "<table>";
$skillname="select typename from invTypes join invGroups on (invGroups.groupid=invTypes.groupid) where typeid=? and categoryid=16";

$skillnamestmt=$dbh->prepare($skillname);
$skillnamestmt->execute(array($skillid));

if ($row=$skillnamestmt->fetchObject())
{
echo "<h2>".$row->typename."</h2>";
}
else
{exit;}



$sql='select typeid,attributeid from dgmTypeAttributes where coalesce(valueint,valuefloat)=? and attributeid in (182,183,184,1289,1285,1290)';

$stmt = $dbh->prepare($sql);




$skill1=array(0);
$skill2=array(0);
$skill3=array(0);
$skill4=array(0);
$skill5=array(0);
$skill6=array(0);


$stmt->execute(array($skillid));
while ($row = $stmt->fetchObject()){

    switch ($row->attributeid) {
         case 182:
             $skill1[]=$row->typeid;
             break;
         case 183:
             $skill2[]=$row->typeid;
             break;
         case 184:
             $skill3[]=$row->typeid;
             break;
         case 1285:
             $skill4[]=$row->typeid;
             break;
         case 1289:
             $skill5[]=$row->typeid;
             break;
         case 1290:
             $skill6[]=$row->typeid;
             break;
    }
}


$breakdownsql="select innert.typeid,level,typename,categoryname,invGroups.categoryid from (
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=277 and typeid in (".join(",",$skill1).")
union
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=278 and typeid in (".join(",",$skill2).")
union
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=279 and typeid in (".join(",",$skill3).")
union
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=1286 and typeid in (".join(",",$skill4).")
union
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=1287 and typeid in (".join(",",$skill5).")
union
select typeid,coalesce(valueint,valuefloat) level from dgmTypeAttributes where attributeid=1290 and typeid in (".join(",",$skill6)."))
 innert join invTypes on (innert.typeid=invTypes.typeid) join invGroups on (invTypes.groupid=invGroups.groupid) join invCategories on (invGroups.categoryid=invCategories.categoryid) order by level,invGroups.categoryid";


$items=$dbh->prepare($breakdownsql);

$items->execute();
$level=0;
while ($row = $items->fetchObject()){
if ($level != $row->level)
{
if ($level!=0)
{
echo "</tgroup>";
}
echo "<tgroup><tr><th colspan-2>Level ".$row->level."</th></tr>";
$level=$row->level;
}
if ($row->categoryid==7 or $row->categoryid==6)
{
echo "<tr><td><a href='https://zkillboard.com/item/".$row->typeid."' target='_BLANK'>".$row->typename."</td><td>".$row->categoryname."</td></tr>";
}
if ($row->categoryid==16)
{
echo "<tr><td><a href='/skills/?skillid=".$row->typeid."'>".$row->typename."</td><td>".$row->categoryname."</td></tr>";
}



}

?>
</table>
<?
}
else
{
?>
<form action="/skills/index.php" method="GET">
<select name="skillid">
<?php

$sql="select typename,typeid from invTypes join invGroups on (invTypes.groupid=invGroups.groupid) where categoryid=16 order by typename asc";

$stmt=$dbh->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetchObject()){
echo "<option value=".$row->typeid.">".$row->typename."</option>\n";
}
?>
</select>
<input type="submit" value="Select Skill"></form>
<?php


}

?>

</div>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/footer.php'); ?>
</body>
</html>
