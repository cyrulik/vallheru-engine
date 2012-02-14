<?php
/**
 *   File functions:
 *   Show text in chat
 *
 *   @name                 : chatmsg.php                            
 *   @copyright            : (C) 2004,2005,2006,2011,2012 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@vallheru.net>
 *   @version              : 1.5
 *   @since                : 14.02.2012
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
/**
* GZIP compression
*/
$compress = FALSE;
if ($compress)
  {
    if (!ob_start("ob_gzhandler"))
      {
	ob_start();
      }
  }

require_once('includes/config.php');
require_once('includes/sessions.php');
require_once('libs/Smarty.class.php');

$smarty = new Smarty;
$smarty->compile_check = true;

$stat = $db -> Execute("SELECT `id`, `rank`, `style`, `graphic` FROM `players` WHERE `email`='".$_SESSION['email']."'");

/**
* Get the localization for game
*/
require_once("languages/pl/chatmsg.php");

/**
* Select style for chat
*/
if ($stat -> fields['graphic']) 
{
    $smarty -> template_dir = "./templates/".$stat -> fields['graphic'];
    $smarty -> compile_dir = "./templates_c/".$stat -> fields['graphic'];
}   
    else
{
    $smarty -> template_dir = './templates';
    $smarty -> compile_dir = './templates_c';
}

$chat = $db -> SelectLimit("SELECT * FROM `chat` WHERE `ownerid`=0 OR `ownerid`=".$stat -> fields['id']." OR `senderid`=".$stat -> fields['id']." ORDER BY `id` DESC", 25);
$pl = $db -> Execute("SELECT `rank`, `id`, `lpv`, `user` FROM `players` WHERE `page`='Chat'");
$arrtext = array();
$arrauthor = array();
$arrsenderid = array();
$arrSdate = array();
if ($stat -> fields['rank'] == 'Admin' || $stat -> fields['rank'] == 'Staff' || $stat -> fields['rank'] == 'Karczmarka') 
{
    $smarty -> assign ("Showid", 1);
}
else
  {
    $smarty->assign("Showid", '');
  }
while (!$chat -> EOF) 
  {
    if (strpos($chat->fields['chat'], "<a href=") === FALSE)
      {
	$text = wordwrap($chat -> fields['chat'], 60, " ", 1);
      }
    else
      {
	$text = $chat->fields['chat'];
      }
    $arrtext[] = $text;
    $arrauthor[] = $chat -> fields['user'];
    $arrsenderid[] = $chat -> fields['senderid'];
    $arrSdate[] = $chat->fields['sdate'];
    $chat -> MoveNext();
  }
$chat -> Close();
$arrtext = array_reverse($arrtext);
$arrauthor = array_reverse($arrauthor);
$arrsenderid = array_reverse($arrsenderid);
$arrSdate = array_reverse($arrSdate);


$ctime = time();
$on = '';
$numon = 0;
while (!$pl -> EOF) 
{
    $span = ($ctime - $pl -> fields['lpv']);
    if ($span <= 180) 
    {
        $on = $on." [<A href=view.php?view=".$pl -> fields['id']." target=_parent>".$pl -> fields['user']."</a> (".$pl -> fields['id'].")] ";
        $numon = ($numon + 1);
    }
    $pl -> MoveNext();
}
$pl -> Close();
$query = $db -> Execute("SELECT count(`id`) FROM `chat`");
$numchat = $query -> fields['count(`id`)'];
$query -> Close();

$smarty->assign(array("Player" => $on, 
		      "Text1" => $numchat, 
		      "Online" => $numon, 
		      "Author" => $arrauthor, 
		      "Text" => $arrtext, 
		      "Senderid" => $arrsenderid,
		      "Sdate" => $arrSdate,
		      "Thereis" => THERE_IS,
		      "Texts" => TEXTS,
		      "Cplayers" => C_PLAYERS,
		      "Cid" => C_ID));
$smarty -> display ('chatmsgs.tpl');
if ($compress)
  {
    ob_end_flush();
  }
?>
