<?php

$thePage = file_get_contents('https://linezeta.wordpress.com/2-zm/zm-no-061-no-080/no-'.$_GET['pageNum'] .'/');

$thePage = explode("table",$thePage);
$thePage = "<table" . $thePage[2];

$trArr = explode("<tr>", $thePage);

$name = explode("<strong>",$trArr[1]);
$name = substr($name[2], 0, strpos($name[2], "<"));

$rarity = explode("<strong>",$trArr[2]);
$rarity = substr($rarity[1], 0, strpos($rarity[1], "<"));
$rarity = preg_replace("/★/","*",$rarity);

$number = explode("<strong>",$trArr[3]);
$number = substr($number[2], 0, strpos($number[2], "<"));

$type = explode("<strong>",$trArr[4]);
$type = substr($type[2], 0, strpos($type[2], "<"));
$type = preg_replace("/\./","",$type);

$maxLevel = explode("<strong>",$trArr[5]);
$maxLevel = substr($maxLevel[2], 0, strpos($maxLevel[2], "<"));

$cost = explode("<strong>",$trArr[6]);
$cost = substr($cost[2], 0, strpos($cost[2], "<"));

$HP = explode("<strong>",$trArr[8]);
$minHP = substr($HP[2], 0, strpos($HP[2], "<"));
$maxHP = substr($HP[4], 0, strpos($HP[4], "<"));

$ATK = explode("<strong>",$trArr[9]);
$minATK = substr($ATK[2], 0, strpos($ATK[2], "<"));
$maxATK = substr($ATK[4], 0, strpos($ATK[4], "<"));

$REC = explode("<strong>",$trArr[10]);
$minREC = substr($REC[2], 0, strpos($REC[2], "<"));
$maxREC = substr($REC[4], 0, strpos($REC[4], "<"));

$ASkill = explode("<strong>",$trArr[11]);
$ASkill = substr($ASkill[1], strpos($ASkill[1], " : ") + 3);
$ASkill = substr($ASkill, 0, strpos($ASkill, "<"));
$ASkill = preg_replace("/–/","\-",$ASkill);
if( $ASkill == "None" ) { $ASkill = ""; }
else { $ASkill = "|activeSkillName = " . $ASkill; }

$LSkill = explode("<strong>",$trArr[13]);
$LSkill = substr($LSkill[1], strpos($LSkill[1], " : ") + 3);
$LSkill = substr($LSkill, 0, strpos($LSkill, "<"));
$LSkill = preg_replace("/–/","\-",$LSkill);
if( $LSkill == "None" ) { $LSkill = ""; }
else { $LSkill = "|leaderSkillName = " . $LSkill; }

$evoList = explode("<td ",$trArr[17]);
if( strpos($evoList[1], "icon") > -1){
	$evoFrom = substr($evoList[1], strpos($evoList[1], "icon") + 4);
	$evoFrom = substr($evoFrom, 0, strpos($evoFrom, "."));
	$evoFrom = str_pad($evoFrom, 3, "0", STR_PAD_LEFT);
	$evoFrom = "|evoFrom = " . $evoFrom;
} else {
	$evoFrom = "";
}

if( strpos($evoList[2], "icon") > -1){
	$evoTo = substr($evoList[2], strpos($evoList[2], "icon") + 4);
	$evoTo = substr($evoTo, 0, strpos($evoTo, "."));
	$evoTo = str_pad($evoTo, 3, "0", STR_PAD_LEFT);
	$evoTo = "|evoTo = " . $evoTo;
} else {
	$evoTo = "";
}

$evo12 = explode("<strong>",$trArr[20]);
$evo1 = substr($evo12[1], 0, strpos($evo12[1], "<"));
$evo1 = substr($evo1, strpos($evo1, " ") + 1);
$evo2 = substr($evo12[2], 0, strpos($evo12[2], "<"));
$evo2 = substr($evo2, strpos($evo2, " ") + 1);

$evo34 = explode("<strong>",$trArr[21]);
$evo3 = substr($evo34[1], 0, strpos($evo34[1], "<"));
$evo3 = substr($evo3, strpos($evo3, " ") + 1);
$evo4 = substr($evo34[2], 0, strpos($evo34[2], "<"));
$evo4 = substr($evo4, strpos($evo4, " ") + 1);

$evo5 = explode("<strong>",$trArr[22]);
$evo5 = substr($evo5[1], 0, strpos($evo5[1], "<"));
$evo5 = substr($evo5, strpos($evo5, " ") + 1);

echo "{{Infobox ZM|num = " . $number . "|name = " . $name . "|rarity = " . $rarity . "|type = " . $type . "<br />|color = |cost = " . $cost . "}}";
echo "<br />";
echo "{{ZM|maxlv = " . $maxLevel . "|minhp = " . $minHP . "|maxhp = " . $maxHP . "|minatk = " . $minATK . "|maxatk = " . $maxATK . "|minrec = " . $minREC . "|maxrec = " . $maxREC . $ASkill . $LSkill . $evoFrom . $evoTo . "|evoMat1 = " . $evo1 . "|evoMat2 = " . $evo2 . "|evoMat3 = " . $evo3 . "|evoMat4 = " . $evo4 . "|evoMat5 = " . $evo4 . "}}";
echo "<br />";
echo "[[Category:" . $rarity . "]]<br />";
echo "[[Category:]]<br />";
echo "[[Category:" . $type . "]]";
?>