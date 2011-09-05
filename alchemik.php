<?php
/**
 *   File functions:
 *   Alchemy mill - making potions
 *
 *   @name                 : alchemik.php                            
 *   @copyright            : (C) 2004,2005,2006,2007,2011 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@tuxfamily.org>
 *   @version              : 1.4
 *   @since                : 12.08.2011
 *
 */

//
//
//       This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation; either version 2 of the License, or
//   (at your option) any later version.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

$title = "Pracownia alchemiczna";
require_once("includes/head.php");
require_once("includes/checkexp.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/alchemik.php");

if ($player -> location != 'Altara' && $player -> location != 'Ardulith') 
{
    error (ERROR);
}

/**
* Get amount of herbs from database
*/
$herb = $db -> Execute("SELECT `illani`, `illanias`, `nutari`, `dynallca` FROM `herbs` WHERE `gracz`=".$player -> id);

/**
* Assign variables to template
*/
if (!isset($_GET['alchemik']))
{
    $smarty -> assign(array("Awelcome" => WELCOME,
                            "Arecipes" => A_RECIPES,
                            "Amake" => A_MAKE,
                            "Aastral" => A_ASTRAL));
    $objAstral = $db -> SelectLimit("SELECT `amount` FROM `astral_plans` WHERE `owner`=".$player -> id." AND `name` LIKE 'R%' AND `location`='V'", 1);
    if ($objAstral -> fields['amount'])
    {
        $smarty -> assign("Astral", 'Y');
    }
        else
    {
        $smarty -> assign("Astral", '');
    }
    $objAstral -> Close();
}

/**
* Buy receptures
*/
if (isset ($_GET['alchemik']) && $_GET['alchemik'] == 'przepisy') 
{
    $plany = $db -> Execute("SELECT * FROM `alchemy_mill` WHERE `status`='S' AND `owner`=0 AND `lang`='".$player -> lang."' ORDER BY `cost` ASC");
    $arrname = array();
    $arrcost = array();
    $arrlevel = array();
    $arrid = array();
    $i = 0;
    while (!$plany -> EOF) 
    {
        $arrname[$i] = $plany -> fields['name'];
        $arrcost[$i] = $plany -> fields['cost'];
        $arrlevel[$i] = $plany -> fields['level'];
        $arrid[$i] = $plany -> fields['id'];
        $i = $i + 1;
        $plany -> MoveNext();
    }
    $plany -> Close();
    $smarty -> assign (array("Name" => $arrname,
        "Recipesinfo" => RECIPES_INFO,
        "Rname" => R_NAME,
        "Rcost" => R_COST,
        "Rlevel" => R_LEVEL,
        "Roption" => R_OPTION,
        "Abuy" => A_BUY,
        "Cost" => $arrcost, 
        "Level" => $arrlevel, 
        "Id" => $arrid));
    if (isset($_GET['buy'])) 
    {
	checkvalue($_GET['buy']);
        $plany = $db -> Execute("SELECT * FROM `alchemy_mill` WHERE `id`=".$_GET['buy']);
        $test = $db -> Execute("SELECT `id` FROM `alchemy_mill` WHERE `owner`=".$player -> id." AND `name`='".$plany -> fields['name']."'");
        if ($test -> fields['id'] != 0) 
        {
            error (P_YOU_HAVE);
        }
        $test -> Close();
        if ($plany -> fields['id'] == 0) 
        {
            error (NO_RECIPE);
        }
        if ($plany -> fields['status'] != 'S') 
        {
            error (BAD_TYPE);
        }
        if ($plany -> fields['cost'] > $player -> credits) 
        {
            error (NO_MONEY);
        }
        $db -> Execute("INSERT INTO `alchemy_mill` (`owner`, `name`, `cost`, `status`, `level`, `illani`, `illanias`, `nutari`, `dynallca`) VALUES(".$player -> id.",'".$plany -> fields['name']."',".$plany -> fields['cost'].",'N',".$plany -> fields['level'].",".$plany -> fields['illani'].",".$plany -> fields['illanias'].",".$plany -> fields['nutari'].",".$plany -> fields['dynallca'].")") or error(E_DB);
        $db -> Execute("UPDATE `players` SET `credits`=`credits`-".$plany -> fields['cost']." WHERE `id`=".$player -> id);
        $smarty -> assign (array ("Cost1" => $plany -> fields['cost'],
            "Youpay" => YOU_PAY,
            "Andbuy" => AND_BUY,
            "Name1" => $plany -> fields['name']));
        $plany -> Close();
    }
}

/**
* Making potions
*/
if (isset ($_GET['alchemik']) && $_GET['alchemik'] == 'pracownia') 
{
    if (!isset($_GET['rob'])) 
    {
        $arrname = array();
        $arrlevel = array();
        $arrid = array();
        $arrillani = array();
        $arrillanias = array();
        $arrnutari = array();
        $arrdynallca = array();
        $i = 0;
        $kuznia = $db -> Execute("SELECT * FROM `alchemy_mill` WHERE `status`='N' AND `owner`=".$player -> id." ORDER BY `level` ASC");
        while (!$kuznia -> EOF) 
        {
            $arrname[$i] = $kuznia -> fields['name'];
            $arrlevel[$i] = $kuznia -> fields['level'];
            $arrid[$i] = $kuznia -> fields['id'];
            $arrillani[$i] = $kuznia -> fields['illani'];
            $arrillanias[$i] = $kuznia -> fields['illanias'];
            $arrnutari[$i] = $kuznia -> fields['nutari'];
            $arrdynallca[$i] = $kuznia -> fields['dynallca'];
            $i = $i + 1;
            $kuznia -> MoveNext();
        }
        $kuznia -> Close();
        $smarty -> assign (array("Name" => $arrname, 
                                 "Level" => $arrlevel, 
                                 "Id" => $arrid, 
                                 "Illani" => $arrillani, 
                                 "Illanias" => $arrillanias, 
                                 "Nutari" => $arrnutari, 
                                 "Dynallca" => $arrdynallca,
                                 "Alchemistinfo" => ALCHEMIST_INFO,
                                 "Rname" => R_NAME,
                                 "Rlevel" => R_LEVEL,
                                 "Rillani" => R_ILLANI,
                                 "Rillanias" => R_ILLANIAS,
                                 "Rnutari" => R_NUTARI,
                                 "Rdynallca" => R_DYNALLCA));
    }
    if (isset($_GET['dalej'])) 
    {
        if ($player -> hp == 0) 
        {
            error (DEAD_PLAYER);
        }
	checkvalue($_GET['dalej']);
        $kuznia = $db -> Execute("SELECT `name` FROM `alchemy_mill` WHERE `id`=".$_GET['dalej']);
        $smarty -> assign (array ("Name1" => $kuznia -> fields['name'], 
                                  "Id1" => $_GET['dalej'],
                                  "Pstart" => P_START,
                                  "Pamount" => P_AMOUNT,
                                  "Amake" => A_MAKE));
        $kuznia -> Close();
    }
    if (isset($_GET['rob'])) 
    {
        if (!isset($_POST['razy'])) 
        {
            error (ERROR);
        }
	checkvalue($_GET['rob']);
	checkvalue($_POST['razy']);
        $kuznia = $db -> Execute("SELECT * FROM `alchemy_mill` WHERE `id`=".$_GET['rob']);
        $rillani = ($_POST['razy'] * $kuznia -> fields['illani']);
        $rillanias = ($_POST['razy'] * $kuznia -> fields['illanias']);
        $rnutari = ($_POST['razy'] * $kuznia -> fields['nutari']);
        $rdynallca = ($_POST['razy'] * $kuznia -> fields['dynallca']);
        if ($herb -> fields['illani'] < $rillani || $herb -> fields['illanias'] < $rillanias || $herb -> fields['nutari'] < $rnutari || $herb -> fields['dynallca'] < $rdynallca) 
        {
            error (NO_HERBS);
        }
        $fltEnergy = $_POST['razy'];
        if ($kuznia -> fields['level'] > 1)
        {
            $intEnergy = $fltEnergy + ($kuznia -> fields['level'] * 0.2);
        }
        if ($player -> energy < $fltEnergy) 
        {
            error (NO_ENERGY);
        }
        if ($kuznia -> fields['owner'] != $player -> id) 
        {
            error (NO_RECIPE);
        }

        /**
         * Add bonuses to ability
         */
        require_once('includes/abilitybonus.php');
        $player -> alchemy = abilitybonus('alchemy');

        $rprzedmiot = 0;
        $rpd = 0;
        $rum = 0;
        $objItem = $db -> Execute("SELECT `efect`, `type`, `power` FROM `potions` WHERE `name`='".$kuznia -> fields['name']."' AND `owner`=0");

        /**
         * Start making potions
         */
        for ($i = 1; $i <= $_POST['razy']; $i++)
        {
            if ($objItem -> fields['type'] == 'M')
            {
                $fltStat = $player -> wisdom;
            }
            if ($objItem -> fields['type'] == 'H')
            {
                $fltStat = $player -> inteli;
            }
            if ($objItem -> fields['type'] == 'P')
            {
                $fltStat = (min($player -> wisdom, $player -> inteli) + $player -> agility) / 2;
            }
            if ($objItem -> fields['type'] == 'A')
            {
                $fltStat = (min($player -> wisdom, $player -> inteli) + $player -> speed) / 2;
            }
            $intChance = ($player -> level * 5) + ($player -> alchemy / 3) + $fltStat;
            $intRoll = rand(1, 100);
            $intTmpamount = 0;
            while ($intRoll < $intChance)
            {
                $rprzedmiot ++;
                $intTmpamount ++;
                $intChance = $intChance - 50;
            }
            if ($intTmpamount)
            {
                $intRoll2 = rand(1,100);
                $strName = $kuznia -> fields['name'];
                $intPower = $objItem -> fields['power'];
                $intMaxpower = $intPower;
                if ($player -> clas == 'Rzemieślnik' && $intRoll2 > 89 && $objItem -> fields['type'] != 'A')
                {
                    if ($objItem -> fields['type'] != 'P')
                    {
                        $intMaxpower = $objItem -> fields['power'] * 2;
                        $intPower = ceil($objItem -> fields['power'] + $player -> alchemy);
                    }
                        else
                    {
                        $intMaxpower = $kuznia -> fields['level'] * 4;
                        $intPower = ceil($player -> alchemy / 2);
                    }
                    $strName = $kuznia -> fields['name']." (S)";
                    $rpd = ($rpd + ($kuznia -> fields['level'] * 10));
                    if ($intTmpamount > 1)
                    {
                        $rpd = ($rpd + ((($kuznia -> fields['level'] * 10) / 100) * (10 * ($intTmpamount - 1))));
                    }
                }
                    else
                {
                    $rpd = ($rpd + $kuznia -> fields['level']);
                    if ($intTmpamount > 1)
                    {
                        $rpd = ($rpd + (($kuznia -> fields['level'] / 100) * (10 * ($intTmpamount - 1))));
                    }
                    if ($objItem -> fields['type'] == 'P')
                    {
                        $intMaxpower = $kuznia -> fields['level'] * 2;
                        $intPower = ceil($player -> alchemy / 2);
                    }
                }
            }
                else
            {
                $rpd ++;
                if ($objItem -> fields['type'] != 'P')
                {
                    $intMaxpower = $objItem -> fields['power'];
                    $intPower = ceil($player -> alchemy);
                }
                    else
                {
                    $intMaxpower = $kuznia -> fields['level'];
                    $intPower = ceil($player -> alchemy / 2);
                }
                $strName = $kuznia -> fields['name']." (K)";
                $intTmpamount = 1;
                $rprzedmiot ++;
            }
            if ($intPower > $intMaxpower)
            {
                $intPower = $intMaxpower;
            }
            $test = $db -> Execute("SELECT `id` FROM `potions` WHERE `name`='".$strName."' AND `owner`=".$player -> id." AND `status`='K' AND `power`=".$intPower) or die("błąd");
            if (!$test -> fields['id']) 
            {
	         if ($objItem -> fields['type'] == 'M')
		   {
		     $intCost = ($intPower * 3) / 20;
		   }
		 else
		   {
		     $intCost = ((2 * $intPower) * 3) / 20;
		   }
                $db -> Execute("INSERT INTO potions (`owner`, `name`, `efect`, `power`, `amount`, `status`, `type`, `cost`) VALUES(".$player -> id.", '".$strName."', '".$objItem -> fields['efect']."', ".$intPower.", ".$intTmpamount.", 'K', '".$objItem -> fields['type']."', ".$intCost.")");
            } 
                else 
            {
                $db -> Execute("UPDATE `potions` SET `amount`=`amount`+".$intTmpamount." WHERE `id`=".$test -> fields['id']);
            }
            $test -> Close();
            $intTmpamount = 0;
        }
        $rum = ($fltEnergy * 0.01);
        if ($player -> clas == 'Rzemieślnik') 
        {
            $rpd = $rpd * 2;
            $rum = $rum * 2;
        }
        $smarty -> assign(array ("Name" => $kuznia -> fields['name'], 
                                 "Amount" => $rprzedmiot, 
                                 "Exp" => $rpd, 
                                 "Ability" => $rum,
                                 "Youmake" => YOU_MAKE,
                                 "Pgain" => P_GAIN,
                                 "Exp_and" => EXP_AND,
                                 "Alchemylevel" => ALCHEMY_LEVEL));
        $kuznia -> Close();
        checkexp($player -> exp, $rpd, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'alchemia', $rum);
        $db -> Execute("UPDATE `herbs` SET `illani`=`illani`-".$rillani.", `illanias`=`illanias`-".$rillanias.", `nutari`=`nutari`-".$rnutari.", `dynallca`=`dynallca`-".$rdynallca." WHERE `gracz`=".$player -> id);
        $db -> Execute("UPDATE `players` SET `energy`=`energy`-".$fltEnergy." WHERE `id`=".$player -> id);
    }
}

/**
 * Make astral potions
 */
if (isset($_GET['alchemik']) && $_GET['alchemik'] == 'astral')
{
    $objAstral = $db -> Execute("SELECT `name` FROM `astral_plans` WHERE `owner`=".$player -> id." AND `name` LIKE 'R%' AND `location`='V'") or die($db -> ErrorMsg());
    if (!$objAstral -> fields['name'])
    {
        error(NO_PLAN);
    }
    $arrHerbs = array(HERB1, HERB2, HERB3, HERB4, ENERGY_PTS);
    $arrAmount = array(array(3000, 1000, 2000, 1000, 50),
                       array(5000, 2500, 3500, 1500, 75),
                       array(7000, 3500, 5000, 2000, 100),
                       array(9000, 4500, 6500, 2500, 125),
                       array(12000, 6000, 8000, 3000, 150));
    $arrNames = array(POTION1, POTION2, POTION3, POTION4, POTION5);
    $arrAviable = array();
    $arrAmount2 = array();
    $arrNumber = array();
    $i = 0;
    while (!$objAstral -> EOF)
    {
        $intKey = str_replace("R", "", $objAstral -> fields['name']);
        $arrNumber[$i] = $intKey;
        $intKey = $intKey - 1;
        $arrAviable[$i] = $arrNames[$intKey];
        $arrAmount2[$i] = $arrAmount[$intKey];
        $i ++;
        $objAstral -> MoveNext();
    }
    $objAstral -> Close();

    $smarty -> assign(array("Awelcome" => WELCOME,
                            "Aviablecom" => $arrAviable,
                            "Mineralsname" => $arrHerbs,
                            "Minamount" => $arrAmount2,
                            "Compnumber" => $arrNumber,
                            "Abuild" => A_BUILD,
                            "Tname" => T_NAME,
                            "Message" => ''));

    /**
     * Start make potions
     */
    if (isset($_GET['potion']))
    {
        $_GET['potion'] = intval($_GET['potion']);
	if ($_GET['potion'] < 1 || $_GET['potion'] > 5)
        {
            error(ERROR);
        }
        $strName = "R".$_GET['potion'];
        $objAstral = $db -> Execute("SELECT `amount` FROM `astral_plans` WHERE `owner`=".$player -> id." AND `name`='".$strName."' AND `location`='V'");
        if (!$objAstral -> fields['amount'])
        {
            error(NO_PLAN);
        }
        $objAstral -> Close();
        $intKey = $_GET['potion'] - 1;
        $arrSqlherbs = array('illani', 'nutari', 'illanias', 'dynallca');
        for ($i = 0; $i < 4; $i++)
        {
            $strSqlname = $arrSqlherbs[$i];
            if ($herb -> fields[$strSqlname] < $arrAmount[$intKey][$i])
            {
                error(NO_AMOUNT.$arrHerbs[$i]);
            }
        }
        if ($player -> energy < $arrAmount[$intKey][4])
        {
            error(NO_ENERGY);
        }
        $arrChance = array(0.05, 0.04, 0.03, 0.02, 0.01);
        $intChance = floor($player -> alchemy * $arrChance[$intKey]);
        if ($intChance > 95)
        {
            $intChance = 95;
        }
        $intRoll = rand(1, 100);
        if ($intRoll <= $intChance)
        {
            $strCompname = "T".$intKey;
            $objTest = $db -> Execute("SELECT `amount` FROM `astral` WHERE `owner`=".$player -> id." AND `type`='".$strCompname."' AND `number`=0 AND `location`='V'");
            if (!$objTest -> fields['amount'])
            {
                $db -> Execute("INSERT INTO `astral` (`owner`, `type`, `number`, `amount`, `location`) VALUES(".$player -> id.", '".$strCompname."', 0, 1, 'V')");
            }
                else
            {
                $db -> Execute("UPDATE `astral` SET `amount`=`amount`+1 WHERE `owner`=".$player -> id." AND `type`='".$strCompname."' AND `number`=0 AND `location`='V'");
            }
            $objTest -> Close();
            $arrExp1 = array(2000, 3000, 4000, 5000, 6000);
            $arrExp2 = array(3000, 4000, 5000, 6000, 7000);
            $intGainexp = rand($arrExp1[$intKey], $arrExp2[$intKey]);
            $arrAbility = array(1, 1.5, 2, 2.5, 3);
            checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'alchemia', $arrAbility[$intKey]);
            $strMessage = YOU_MAKE.$arrNames[$intKey]."! ".YOU_GAIN11.$intGainexp.YOU_GAIN12.$arrAbility[$intKey].YOU_GAIN13.YOU_USE;
        }
            else
        {
            $intRoll2 = rand(1, 100);
            if ($player -> clas == 'Rzemieślnik')
            {
                if ($intRoll2 < 6)
                {
                    $fltBonus = 0;
                }
                if ($intRoll2 > 5 && $intRoll2 < 21)
                {
                    $fltBonus = 0.2;
                }
                if ($intRoll2 > 20 && $intRoll2 < 51)
                {
                    $fltBonus = 0.25;
                }
                if ($intRoll2 > 50)
                {
                    $fltBonus = 0.33;
                }
            }
                else
            {
                if ($intRoll2 < 6)
                {
                    $fltBonus = 0;
                }
                if ($intRoll2 > 5 && $intRoll2 < 21)
                {
                    $fltBonus = 0.4;
                }
                if ($intRoll2 > 20 && $intRoll2 < 51)
                {
                    $fltBonus = 0.5;
                }
                if ($intRoll2 > 50)
                {
                    $fltBonus = 0.66;
                }
            }
            for ($i = 0; $i < 4; $i ++)
            {
                $arrAmount[$intKey][$i] = ceil($arrAmount[$intKey][$i] * $fltBonus);
            }
            $strMessage = YOU_FAIL.$arrNames[$intKey].YOU_FAIL2.YOU_USE;
        }
        for ($i = 0; $i < 4; $i++)
        {
            $strMessage = $strMessage.$arrHerbs[$i].": ".$arrAmount[$intKey][$i]."<br />";
        }
        $smarty -> assign("Message", $strMessage);
        $db -> Execute("UPDATE `players` SET `energy`=`energy`-".$arrAmount[$intKey][4]." WHERE `id`=".$player -> id);
        $db -> Execute("UPDATE `herbs` SET `illani`=`illani`-".$arrAmount[$intKey][0].", `illanias`=`illanias`-".$arrAmount[$intKey][2].", `nutari`=`nutari`-".$arrAmount[$intKey][1].", `dynallca`=`dynallca`-".$arrAmount[$intKey][3]." WHERE `gracz`=".$player -> id);
    }
}

$herb -> Close();

/**
* Initialization of variables
*/
if (!isset($_GET['alchemik'])) 
{
    $_GET['alchemik'] = '';
}
if (!isset($_GET['buy'])) 
{
    $_GET['buy'] = '';
}
if (!isset($_GET['rob'])) 
{
    $_GET['rob'] = '';
}
if (!isset($_GET['dalej'])) 
{
    $_GET['dalej'] = '';
}

/**
* Assing variables and display page
*/
$smarty -> assign (array ("Alchemist" => $_GET['alchemik'], 
    "Buy" => $_GET['buy'], 
    "Make" => $_GET['rob'],
    "Back" => BACK,
    "Next" => $_GET['dalej']));
$smarty -> display ('alchemist.tpl');

require_once("includes/foot.php");
?>
