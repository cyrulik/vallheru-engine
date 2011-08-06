<?php
/**
 *   File functions:
 *   Main file of chat - bot Innkeeper and private talk to other players
 *
 *   @name                 : chat.php                            
 *   @copyright            : (C) 2004,2005,2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @author               : eyescream <tduda@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 23.11.2006
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
// $Id: chat.php 840 2006-11-24 16:41:26Z thindil $

$title = "Karczma";
require_once("includes/head.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/chat.php");

$db -> Execute("UPDATE `players` SET `page`='Chat' WHERE `id`=".$player -> id);
if (isset ($_GET['action']) && $_GET['action'] == 'chat') 
{
    if (isset($_POST['msg']))
    {
        $_POST['msg'] = strip_tags($_POST['msg']);
    }
    if (isset($_POST['msg']) && $_POST['msg'] != '') 
    {
        if ($player -> rank == 'Admin') 
        {
            $starter = "<span style=\"color: #0066cc;\">".$player -> user."</span>";
        } 
            elseif ($player -> rank == 'Staff') 
        {
            $starter = "<span style=\"color: #00ff00;\">".$player -> user."</span>";
        } 
            else 
        {
            $starter = $player -> user;
        }
        $czat = $db -> Execute("SELECT `gracz` FROM `chat_config` WHERE `gracz`=".$player -> id);
        if ($czat -> fields['gracz'])
        {
            error (NO_PERM);
        }
        $czat -> Close();
        require_once('includes/bbcode.php');
        $_POST['msg'] = bbcodetohtml($_POST['msg']);
        if (!ereg("[[:graph:]]+", $_POST['msg']))
        {
            error(ERROR);
        }
        /**
         * Start innkeeper bot
         */
        require_once('class/bot_class.php');
        $objBot = new Bot($_POST['msg'], 'Karczmarzu');
        $blnCheckbot = $objBot -> Checkbot();
        if ($blnCheckbot)
        {
            $strAnswer = $objBot -> Botanswer();
            $arrText = explode(" ", $_POST['msg']);
            if (ereg("^[1-9][0-9]*$", end($arrText))) 
            { 
                $user = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".end($arrText));
                $id = $user -> fields['user'];
                $intValues = count($arrText) - 2;
                $strItem = ' ';
                for ($i = 0; $i < $intValues; $i++)
                {
                    $strItem = $strItem.$arrText[$i]." ";
                }
                $message = $strItem." ".FOR_A." ".$id;
                $user -> Close();
            }
                else
            {
                $message = $_POST['msg'];
            }
        }
            else
        {
            $message = $_POST['msg'];
        }
        $test1 = explode("=", $_POST['msg']);
        if (ereg("^[1-9][0-9]*$", $test1[0]) && isset($test1[1])) 
        {
            $user = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$test1[0]);
            $id = $user -> fields['user'];
            if ($id) 
            {
                $message = "<b>".$id.">>></b> ".$test1[1];
            } 
                elseif ($test1[1]) 
            {
                $message = $test1[1];
            }
            $owner = $test1[0];
        } 
            else 
        {
            $owner = 0;
            if (!isset($test1[0])) 
            {
                $evade = true;
            }
        }
        if (!isset($evade)) 
        {
            $db -> Execute("INSERT INTO `chat` (`user`, `chat`, `senderid`, `ownerid`) VALUES('".$starter."', '".$message."',".$player -> id.",".$owner.")");
        }
        $intLpv = (time() - 180);
        $objInnkeeper = $db -> Execute("SELECT `user` FROM `players` WHERE `rank`='Karczmarka' AND `page`='Chat' AND `lpv`>=".$intLpv);
        if (!$objInnkeeper -> fields['user'])
        {
            if (isset($strAnswer))
            {
                $db -> Execute("INSERT INTO `chat` (`user`, `chat`) VALUES('<i>".INNKEEPER2."</i>', '".$strAnswer."')");
            }
        }
            else
        {
            if ($blnCheckbot)
            {
                $db -> Execute("INSERT INTO `chat` (`user`, `chat`) VALUES('<i>Barnaba</i>', '".INNKEEPER_GONE.$objInnkeeper -> fields['user'].RULES."')");
            }
        }
        $objInnkeeper -> Close();
    }
}

/**
* Give items to player
*/
if (isset($_GET['step']) && $_GET['step'] == 'give')
{
    if ($player -> rank != 'Admin' && $player -> rank != 'Karczmarka')
    {
        error(ERROR);
    }
    if (!ereg("^[0-9]*$", $_POST['giveid'])) 
    {
        error(ERROR);
    }
    if ($_POST['giveid'] > 0)
    {
        $objUser = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$_POST['giveid']);
        if (!isset($_POST['innkeeper']))
        {
            $_POST['innkeeper'] = '';
        }
        $db -> Execute("INSERT INTO `chat` (`user`, `chat`) VALUES('<i>".$player -> user."</i>', '".PLEASE." ".$objUser -> fields['user']." ".HERE_IS." ".$_POST['item']." ".$_POST['innkeeper']."')");
        $objUser -> Close();
    }
        else
    {
        if (!isset($_POST['innkeeper']))
        {
            $_POST['innkeeper'] = '';
        }
        $db -> Execute("INSERT INTO `chat` (`user`, `chat`) VALUES('<i>".$player -> user."</i>', '".FOR_ALL2." ".$_POST['item'].FOR_ALL3." ".$_POST['innkeeper']."')");
    }
}

/**
* Ban/unban players in chat
*/
if (isset($_GET['step']) && $_GET['step'] == 'ban')
{
    if ($player -> rank != 'Admin' && $player -> rank != 'Karczmarka')
    {
        error(ERROR);
    }
    if (!ereg("^[1-9][0-9]*$", $_POST['banid']) || !ereg("^[1-9][0-9]*$", $_POST['duration'])) 
    {
        error(ERROR);
    }
    if ($_POST['ban'] == 'ban') 
    {
        $intTime = $_POST['duration'] * 7;
        $db -> Execute("INSERT INTO `chat_config` (`gracz`, `resets`) VALUES(".$_POST['banid'].", ".$intTime.")");
        $strDate = $db -> DBDate($newdate);
        $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$_POST['banid'].", '".YOU_BLOCK2.$_POST['duration'].T_DAYS.$_POST['verdict'].BLOCK_BY.'<b><a href="view.php?view='.$player -> id.'">'.$player -> user."</a></b>, ID <b>".$player -> id."</b>.', ".$strDate.")") or die($db -> ErrorMsg());
        error(YOU_BLOCK." ".$_POST['banid']);
    }
    if ($_POST['ban'] == 'unban') 
    {
        $db -> Execute("DELETE FROM `chat_config` WHERE `gracz`=".$_POST['banid']);
        error(YOU_UNBLOCK." ".$_POST['banid']);
    }
}

/**
* Chat prune
*/
if (isset ($_GET['step']) && $_GET['step'] == 'clearc') 
{
    if ($player -> rank != 'Admin' && $player -> rank != 'Karczmarka')
    {
        error(ERROR);
    }
    $db -> Execute("TRUNCATE TABLE `chat`");
    error(CHAT_PRUNE);
}

if ($player -> rank == 'Admin' || $player -> rank == 'Karczmarka')
{
    $arritems = array(BEER, HONEY, WINE, MUSTAK, JUICE, CUCUMBERS, TEA, MEAT, MEAT2, MEAT3, MEAT4, FOOD, FOOD2, FOOD3, EGGS, EGG, EGG2, MILK, ICE, CHICKEN, FLAPJACK, COFFE);
    $smarty -> assign(array("Aban" => A_BAN,
                            "Aunban" => A_UNBAN,
                            "Chatid" => CHAT_ID,
                            "Agive" => A_GIVE,
                            "Items" => $arritems,
                            "Withcomm" => WITH_COMM,
                            "Aprune" => A_PRUNE,
                            "Ona" => ON_A,
                            "Tdays" => T_DAYS));
}

$query = $db -> Execute("SELECT count(*) FROM `chat`");
$numchat = $query -> fields['count(*)'];
$query -> Close();
$smarty -> assign (array("Number" => $numchat,
                         "Arefresh" => A_REFRESH,
                         "Asend" => A_SEND,
                         "Inn" => INN,
                         "Innis" => INN_IS,
                         "Inntexts" => INN_TEXTS,
                         "Rank" => $player -> rank));
$smarty -> display ('chat.tpl');

require_once("includes/foot.php");
?>
