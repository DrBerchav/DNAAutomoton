<?php
// HELPER FUNCTIONS 

function MakeChoice($Board,$Choices)
{
$r=MakeChoiceN($Board,$Choices,1);
return $r[0];
}


function  loadtable($TableName,$ReadFrom,&$Dictionary)
{
//Simple helper function to read the file $ReadFrom and populate
//the dictionary object from the file.

global $DEBUG;
$checkfile=file_exists($ReadFrom);
if ($checkfile == true)
 {
 $Dictionary=file($ReadFrom);
 }
return true;
}

//For "nice" output display

function clearscr()
{
echo "\033[2J" . "\033[H";
}


function showtable($Dictionary)
{
for ($i=0;$i<count($Dictionary);$i++)
{
 $Cells=explode(",",$Dictionary[$i]);
for ($j=0;$j<count($Cells);$j++)
 {
 if ($Cells[$j] == 1) echo "*";
 else echo " ";
 }
echo "\n";
}
}

function showtable2($Dictionary)
{
for ($i=0;$i<count($Dictionary);$i++)
{
 $Cells=$Dictionary[$i];
for ($j=0;$j<count($Cells);$j++)
 {
 echo $Cells[$j];
 }
echo "\n";
}
}
function BubbleSort($InputArray,$SortArray)
{
$arr=$InputArray;
//print_r($arr);
$newarray=$SortArray;
if (count($arr) <> count($newarray))
 {
 echo "Problem Here!";
 return;
 }
for ($i=0;$i<count($arr);$i++)
 {
 for ($j=0;$j<count($arr)-1;$j++)
  {
  if ($arr[$j] < $arr[$j+1])
    {
//	echo "swapping!\n";
	$tempval=$arr[$j+1];
	$arr[$j+1]=$arr[$j];
	$arr[$j]=$tempval;
	$tmpv=$newarray[$j+1];
	$newarray[$j+1]=$newarray[$j];
	$newarray[$j]=$tmpv;
	
    }
  }
 }
//print_r($arr);
return $newarray;
}

?>
