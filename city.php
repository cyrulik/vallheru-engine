<?php
/**
 *   File functions:
 *   City menu and resets without Cron
 *
 *   @name                 : city.php                            
 *   @copyright            : (C) 2004,2005,2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 12.10.2006
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
// $Id: city.php 704 2006-10-12 16:21:03Z thindil $

$title = "Altara"; 
require_once("includes/head.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/city.php");

if($player -> location != 'Altara' && $player -> location != 'Ardulith') 
{
    error (NO_CITY);
}

/**
* Resets without Cron

$objReset = $db -> Execute("SELECT value FROM settings WHERE setting='reset'");
if ($newhour == 12 && $objReset -> fields['value'] == 'N') 
{
    $db -> Execute("UPDATE settings SET value='Y' WHERE setting='reset'");
    require_once('includes/resets.php');
    mainreset();
}

if ($newhour == 13 && $objReset -> fields['value'] == 'Y') 
{
    $db -> Execute("UPDATE settings SET value='N' WHERE setting='reset'");
}

$arrreset = array(14,16,18,20,22,0);
if ($objReset -> fields['value'] == 'N') 
{
    foreach ($arrreset as $resettime) 
    {
        if ($resettime == $newhour) 
        {
            $db -> Execute("UPDATE settings SET value='Y' WHERE setting='reset'");
            require_once('includes/resets.php');
            smallreset();
            break;
        }
    }
}

$arrreset1 = array(15,17,19,21,23,1);
if ($objReset -> fields['value'] == 'Y') 
{
    foreach ($arrreset1 as $resettime) 
    {
        if ($resettime == $newhour) 
        {
            $db -> Execute("UPDATE settings SET value='N' WHERE setting='reset'");
        }
    }
}
$objReset -> Close();

* End resets without Cron
*/

if ($player -> location == 'Altara')
{
    $objItem = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".ITEM."' AND `owner`=".$player -> id);
    if (!$objItem -> fields['id'])
    {
        $intItem = 0;
        $objPoll = $db -> Execute("SELECT `value` FROM `settings` WHERE `setting`='poll'");
        if ($objPoll -> fields['value'] == 'Y' && $player -> poll == 'N')
        {
            $strInfo = "<b>N</b> ";
        }
            else
        {
            $strInfo = '';
        }
        $objPoll -> Close();
        $arrTitles = array(BATTLE_FIELD, COMMUNITY, VILLAGE, WEST_SIDE, HOUSES_SIDE, CASTLE, JOBS, SOUTH_SIDE);
        $arrFiles = array(array('battle.php', 'armor.php', 'weapons.php', 'bows.php', 'outposts.php'),
                          array('news.php', 'forums.php?view=categories', 'chat.php', 'mail.php', 'tribes.php', 'newspaper.php'),
                          array('train.php', 'mines.php', 'farm.php', 'core.php'),
                          array('grid.php', 'wieza.php', 'temple.php', 'msklep.php', 'jewellershop.php'),
                          array('house.php', 'memberlist.php?limit=0&amp;lista=id', 'monuments.php', 'hof.php', 'library.php'),
                          array('updates.php', 'tower.php', 'referrals.php', 'jail.php', 'court.php', 'polls.php', 'alley.php', 'stafflist.php'),
                          array('landfill.php', 'smelter.php', 'kowal.php', 'alchemik.php'),
                          array('market.php', 'warehouse.php', 'travel.php'));
        $arrNames = array(array(BATTLE_ARENA, ARMOR_SHOP, WEAPON_SHOP, BOWS_SHOP, OUTPOSTS),
                          array(NEWS, FORUMS, INN, PRIV_M, CLANS, PAPER),
                          array(SCHOOL, MINES, FARMS, CORES),
                          array(LABYRYNTH, MAGIC_TOWER, TEMPLE, ALCHEMY_SHOP, JEWELLER_SHOP),
                          array(HOUSES, PLAYERS_L, MONUMENTS, HERO_VALL, LIBRARY),
                          array(UPDATES, TIMER, REFERR, JAIL2, COURT, $strInfo.POLLS, WELLEARNED, STAFF_LIST),
                          array(CLEAN_CITY, SMELTER, BLACKSMITH, ALCHEMY_MILL),
                          array(MARKET, WAREHOUSE, TRAVEL));
        $smarty -> assign(array("Titles" => $arrTitles,
                                "Files" => $arrFiles,
                                "Names" => $arrNames,
                                "Cityinfo" => CITY_INFO));
    }
        else
    {
        $intItem = 1;
        if (!isset($_GET['step']))
        {
            $smarty -> assign(array("Staffquest" => STAFF_QUEST,
                                    "Sqbox1" => SQ_BOX1,
                                    "Sqbox2" => SQ_BOX2));
        }
            else
        {
            if ($_GET['step'] == 'give')
            {
                $smarty -> assign(array("Staffquest" => STAFF_QUEST1,
                                        "Temple" => TEMPLE));
                $db -> Execute("DELETE FROM `equipment` WHERE `id`=".$objItem -> fields['id']);
                $db -> Execute("UPDATE `players` SET `credits`=`credits`+100000 WHERE `id`=".$player -> id);
                require_once("includes/checkexp.php");
                checkexp($player -> exp, 10000, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, '', 0);
            }
                elseif ($_GET['step'] == 'take')
            {
                $smarty -> assign("Staffquest", STAFF_QUEST2);
                $db -> Execute("DELETE FROM `equipment` WHERE `id`=".$objItem -> fields['id']);
                $db -> Execute("UPDATE `players` SET `credits`=`credits`+10000 WHERE `id`=".$player -> id);
            }
        }
    }
    $objItem -> Close();
}
    else
{
    $intItem = 0;
    if (!isset($_GET['step']))
    {
        $objPoll = $db -> Execute("SELECT `value` FROM `settings` WHERE `setting`='poll'");
        if ($objPoll -> fields['value'] == 'Y' && $player -> poll == 'N')
        {
            $strInfo = "<b>N</b> ";
        }
            else
        {
            $strInfo = '';
        }
        $objPoll -> Close();
        $arrTitles = array(BATTLE_FIELD, COMMUNITY, VILLAGE, WEST_SIDE, HOUSES_SIDE, CASTLE, JOBS, SOUTH_SIDE);
        $arrFiles = array(array('temple.php', 'library.php', 'jeweller.php'),
                          array('bows.php', 'msklep.php', 'wieza.php', 'forums.php?view=categories', 'chat.php'),
                          array('jail.php', 'maze.php', 'mail.php', 'tribes.php'),
                          array('alchemik.php', 'lumbermill.php', 'train.php', 'jewellershop.php'),
                          array('landfill.php', 'warehouse.php', 'market.php', 'battle.php', 'core.php', 'polls.php'),
                          array('updates.php', 'tower.php', 'news.php', 'newspaper.php', 'alley.php', 'stafflist.php', 'court.php'),
                          array('house.php', 'memberlist.php?limit=0&amp;lista=id', 'monuments.php', 'outposts.php', 'farm.php'),
                          array('travel.php', 'city.php?step=forest'));
        $arrNames = array(array(TEMPLE, LIBRARY, JEWELLER),
                          array(BOWS_SHOP, ALCHEMY_SHOP, MAGIC_TOWER, FORUMS, INN),
                          array(JAIL2, LABYRYNTH, PRIV_M, CLANS),
                          array(ALCHEMY_MILL, LUMBER_MILL, SCHOOL, JEWELLER_SHOP),
                          array(CLEAN_CITY, WAREHOUSE, MARKET, BATTLE_ARENA, CORES, $strInfo.POLLS),
                          array(UPDATES, TIMER, NEWS, PAPER, WELLEARNED, STAFF_LIST, COURT),
                          array(HOUSES, PLAYERS_L, MONUMENTS, OUTPOSTS, FARMS),
                          array(TRAVEL, FOREST2));
        $smarty -> assign(array("Titles" => $arrTitles,
                                "Files" => $arrFiles,
                                "Names" => $arrNames,
                                "Cityinfo" => CITY_INFO));
    }
        else
    {
        $db -> Execute("UPDATE `players` SET `miejsce`='Las' WHERE `id`=".$player -> id);
        $smarty -> assign("Message", GO_FOREST);
    }
}

/**
* Initialization of variable
*/
if (!isset($_GET['step']))
{
    $_GET['step'] = '';
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array("Item" => $intItem,
                        "Step" => $_GET['step'],
                        "Location" => $player -> location)); 
$smarty -> display ('city.tpl');

require_once("includes/foot.php"); 
?>
