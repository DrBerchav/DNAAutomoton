<?php
include("helper.php");
/**************************************************************************
Title:  Simulating DNA Using Cellular Automaton
Author: Dr. Tedey Berchav
Date:   August 20, 2022
**************************************************************************/
// This program is in conjuction with related published paper of same name
/***********************************************************************/

//**********
//VARIABLES
//***********/
//DEBUG TO ALLOW USER TO SEE OUTPUT OF EACH ITERATION
$DEBUG=1;

//Next we define the number of generations that the competition will run
$MaxGenerations=5; //ie: 'Z' in the paper

//The size of the grid, here we use a 25x25 battlefield size grid
//Grids in Conways game of life are always 'square'

$BoardSize=25; 

//The representation of a protien marker
$ProtienMarker="*"; 


function loadprotien($filename,&$Board,$Overlay=false)
{
//load the DNA binding protien into the "double helix" battlefield grid.
global $DEBUG;
global $ProtienMarker;

loadtable("N/A",$filename,$TheLines);

 if ($DEBUG == 1)
 {
 clearscr();
 showtable($TheLines);
 echo "\n\nLOAD PROTIEN: " . $filename . " OVERLAY: $Overlay";
 sleep(5);
 }

//Note: The file format is 25 character string of '1' (bind) and '0' non-bind
//seperated by commas. Similar to '1' (ALIVE) and '0' (DEAD) in game of life.
//The optional overlay determines if the grid is being cleared by the load
//or if we are apending one protien binding sequence on top of another for
//competition 

$Row=0;
for ($i=0;$i<count($TheLines);$i++)
 {
 $Cells=explode(",",$TheLines[$i]);
 for ($d=0;$d<count($Cells);$d++)
   {
    if ($Cells[$d] == 1) $Board[$Row][$d]=$ProtienMarker;
     else
	if ($Overlay) $Board[$Row][$d] = " ";
   }
    $Row++;
	 }
}

function MakeChoiceN($Board,$Choices,$N)
{
//Walk around the board and eliminate the choices that are empty
//Return the choice with the highest count
global $BoardSize;
global $ProtienMarker;
global $DEBUG;
$Counter=0;
for ($i=0;$i<count($Choices);$i++)
   {
   $Counts[$i]=0;
   }
for ($i=0;$i<$BoardSize;$i++)
 {
 for ($j=0;$j< $BoardSize;$j++)
  {
   $NextChoice=$Choices[$Counter];
   $Counter++;
   if ($Counter >= count($Choices)) $Counter=0;
   if ($Board[$i][$j] == $ProtienMarker)
    {
   if ($NextChoice != "") $Counts[$Counter]++ ;
    }
  }
 }
if ($DEBUG == 1)
{
echo "\n";
print_r($Counts);
echo "Counts  " . count($Counts) . "\n";
echo "Choices  " . count($Choices) . "\n";
}
$newarray=bubblesort($Counts,$Choices);
if ($DEBUG == 1) 
{
echo "\n: OVERLAYED ALLELE(S)\n";
print_r($newarray);
sleep(5);

}
for ($i=0;$i<$N;$i++)
 $retval[$i]=$newarray[$i];
return $retval;
}

function generate(&$Board)
{
//Game of life
//One generation, compare neighbours and (re)bind as per Conways game of life.
global $DEBUG;
global $BoardSize;
global $ProtienMarker;

for ($i=0;$i<$BoardSize;$i++)
 {
  for ($j=0;$j<$BoardSize;$j++)
  {
   $Neighbours=countneighbours($Board,$i,$j);
   $Cell=$Board[$i][$j];
   if ($Cell == $ProtienMarker)
    {
     //RULES:
     if ($Neighbours < 2) $Board[$i][$j]=" "; // ANY BOUND MARKER WITH < 2 NEIGHBOURS LOOSES BINDING
     if ($Neighbours == 2) $Board[$i][$j]=$ProtienMarker; // ANY BOUND MARKER WITH 2 NEIGHBOURS STAYS BOUND
     if ($Neighbours == 3) $Board[$i][$j]=$ProtienMarker; // ANY BOUND MARKER WITH 3 NEIGHBOURS STAYS BOUND
     if ($Neighbours > 3) $Board[$i][$j]=" "; //ANY UNBOUND MARKER WITH 3 BOUND NEIGHBOURS BINDS
    }
   if ($Cell == " " )
   {
    if ($Neighbours == 3) $Board[$i][$j]=$ProtienMarker;  //ANY UNMOUNBD MARKER WITH 3 BOUND NEIGHBOURS BINDS
   }
  }
 }
if ($DEBUG == 1)
{
clearscr();
showtable2($Board);
usleep(150000);
}
return $Board;
}

function initializeboard($Board)
{
//Set initial board state (empty)
for ($i=0;$i<$BoardSize;$i++)
 {
  for ($j=0;$j<$BoardSize;$j++) $Board[$i][$j]= " ";
 }
return $Board;
}


function boardempty($Board)
{
//detect an empty board (troubleshooting)
global $BoardSize;
global $ProtienMarker;
for ($i=0;$i<$BoardSize;$i++)
 {
  for ($j=0;$j<$BoardSize;$j++)
  {
   if ($Board[$i][$j] == $ProtienMarker) return false;
   }
 }
return true;
}

function countneighbours($Board,$i,$j)
{
//Helper function to count the neighbours to a cell
global $ProtienMarker;
$count=0;
$Above=$i+1;
$Below=$i-1;
$LeftLoc=$j-1;
$RightLoc=$j+1;
for ($c=0;$c<8;$c++)
 {
 $C[$c]=0;
 }
try
 {
 $C[0]=$Board[$Above][$LeftLoc];
 $C[1]=$Board[$Above][$RightLoc];
 $C[2]=$Board[$Below][$RightLoc];
 $C[3]=$Board[$Below][$LeftLoc];
 $C[4]=$Board[$i][$LeftLoc];
 $C[5]=$Board[$i][$LeftLoc];
 $C[7]=$Board[$Above][$j];
 $C[8]=$Board[$Below][$j];
 }
catch (Exceptin $e) { }
for ($c=0;$c<8;$c++)
{
if ($C[$c]==$ProtienMarker) { $count++; }
}
return $count;
}





function SetTrait($TheMatrix,$TraitName,$ChooseFrom,&$ResultDictionary)
{
//overlay the ALLELE(S) over each row in the matrix, and determine fitness
global $DEBUG;
$TheChoice=makechoice($TheMatrix,$ChooseFrom);
if ($DEBUG == 1) 
{
echo "EXPRESSED TRAIT: " . $TheChoice . "\n";
sleep(5);
}
$ResultDictionary[$TraitName]=$TheChoice;
}

function decodedna($dnafile,&$Traits)
{
//Read the dna file and evaulate it to determine traits expressed
/*
File Format:

TraitName | Protien  Structure 1 ... ; Protien Struture N .... | Trait Arragement | Trait Array

Eg:

Gender|LoadProtien("./y-protien.dna",$TheMatrix,true);::LoadProtien("./x-protien.dna",$TheMatrix,false);|SetTrait($TheMatrix,"gender",Array("male","male","female"),$Traits);
---
Gender is expressed by 2 DNA binding protiens referred to as the "X" and "Y" chromosones

Gender | LoadProtien("./y-protien.dna",$TheMatrix,true) ; LoadProtien("./x-protien.dna",$TheMatrix,true)

We choose to align to the grid in the following order male, male, female

SetTrait($TheMatrix,"gender",Array("male","male","female")

The exposed trait is then stored in the array 'Traits'
*/

global $MaxGenerations;
global $DEBUG;
loadtable("n/a",$dnafile,$Dictionary);
for ($zt=0;$zt<count($Dictionary);$zt++)
  {
  $TheMatrix=initializeboard($TheMatrix);
  $TestLine=strpos($Dictionary[$zt],"|");
  if ($TestLine !== false)
	{
	$Qarray=explode("|",$Dictionary[$zt]);
	$Descr=$Qarray[0];
	$GenoType=$Qarray[1];
	$TraitFunction=$Qarray[2];
	$Alleles=explode("::",$GenoType);
        if ($DEBUG == 1)
        {
        clearscr();
        echo "Testing Trait: $Descr\n";
        sleep (5);
        }
	for ($protien=0;$protien<count($Alleles);$protien++)
	{
	eval($Alleles[$protien]);
        for ($p=0;$p<$MaxGenerations;$p++) 
       {
       $TheMatrix=generate($TheMatrix);
        if ($DEBUG == 1) 
            {
            echo "\n\nTESTING TRAIT: $Descr\n";
           echo "\nGENERATION: $p";
           sleep(1);
           }
       }
	if (BoardEmpty($TheMatrix)) echo "ERROR: BOARD IS EMPTY!!\n";
	if (BoardEmpty($TheMatrix) && $Protien > 0)
  	eval($Alleles[$protien-1]);
	}
	}
 eval($TraitFunction);
  }
}

/*** MAIN PROGRAM ******/
if ($DEBUG == 1) error_reporting(E_ERROR | E_WARNING | E_PARSE);

$Traits=Array();
decodedna("./dna.dat",$Traits);
if ($DEBUG == 1)
{
echo "\n FINAL EXPRESSED TRAITS: \n";
echo "\n===========================\n";
print_r($Traits);
}
?>
