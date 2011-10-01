<?php
/**
 *   File functions:
 *   Loots market
 *
 *   @name                 : lmarket.php                            
 *   @copyright            : (C) 2011 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@tuxfamily.org>
 *   @version              : 1.4
 *   @since                : 29.09.2011
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

$title = "Rynek z łupami";
require_once("includes/head.php");

if ($player->location != 'Altara' && $player->location != 'Ardulith') 
{
    error ('Nie znajdujesz się w mieście.<a href');
}

/**
* Assign variables to template
*/
$smarty -> assign("Message", '');

/**
* Main menu
*/
if (!isset($_GET['view']) && !isset($_GET['buy']) && !isset($_GET['wyc']))
{
    $smarty -> assign(array("Minfo" => "Tutaj jest rynek łupów z potworów. Masz parę opcji",
                            "Aview" => "Zobacz oferty",
                            "Aadd" => "Dodaj ofertę",
                            "Adelete" => "Skasuj wszystkie swoje oferty",
                            "Alist" => "Spis wszystkich ofert na rynku",
                            "Aback2" => "Wróć na rynek"));
}

/**
* Show oferts in market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'market') 
{
    if (empty($_POST['szukany']) && !isset($_POST['szukany1'])) 
      {
        $msel = $db -> Execute("SELECT count(`id`) FROM `equipment` WHERE `status`='R' AND `type`='O'");
      }
    elseif (isset($_POST['szukany1']))
      {
	$_POST['szukany1'] = strip_tags($_POST['szukany1']);
        $strSearch = $db -> qstr($_POST['szukany1'], get_magic_quotes_gpc());
	$msel = $db -> Execute("SELECT count(`id`) FROM `equipment` WHERE `status`='R' AND `type`='O' AND name=".$strSearch);
      }
    else 
      {
        $_POST['szukany'] = strip_tags($_POST['szukany']);
        $strSearch = $db -> qstr("*".$_POST['szukany']."*", get_magic_quotes_gpc());
        $msel = $db -> Execute("SELECT count(`id`) FROM `equipment` WHERE `status`='R' AND `type`='O' AND MATCH(`name`) AGAINST (".$strSearch." IN BOOLEAN MODE)") or die($db -> ErrorMsg());
      }
    $przed = $msel -> fields['count(`id`)'];
    $msel -> Close();
    if ($przed == 0) 
    {
        error ("Nie ma ofert na rynku.");
    }
    $pages = ceil($przed / 30);
    if (isset($_GET['page']))
      {
	checkvalue($_GET['page']);
	$page = $_GET['page'];
      }
    else
      {
	$page = 1;
      }
    if ($page > $pages)
      {
	$page = $pages;
      }
    $smarty -> assign(array("Tname" => "Nazwa",
                            "Tlevel" => "Poziom",
                            "Tcost" => "Cena szt / wszystko",
                            "Tseller" => "Sprzedający",
                            "Tamount" => "Ilość",
                            "Viewinfo" => "Zobacz oferty lub",
			    "Asearch" => "Szukaj",
                            "Toptions" => "Opcje"));
    if (!in_array($_GET['lista'], array('id', 'name', 'minlev',  'amount', 'cost', 'owner'))) 
      {
	error("Zapomnij o tym!"); 
      }
    if ($_GET['lista'] == 'zr')
      {
	$strOrder = ' ASC';
      }
    else
      {
	$strOrder = ' DESC';
      }
    if (empty($_POST['szukany']) && !isset($_POST['szukany1'])) 
      {
	$pm = $db -> SelectLimit("SELECT * FROM `equipment` WHERE `status`='R' AND `type`='O' ORDER BY ".$_GET['lista'].$strOrder, 30, (30 * ($page - 1)));
      }
    elseif (isset($_POST['szukany1']))
      {
	$pm = $db -> Execute("SELECT * FROM `equipment` WHERE `status`='R' AND `type`='O' AND name=".$strSearch);
      }
    else 
      {
	$pm = $db -> SelectLimit("SELECT * FROM `equipment` WHERE `status`='R' AND `type`='O' AND MATCH(`name`) AGAINST (".$strSearch." IN BOOLEAN MODE) ORDER BY ".$_GET['lista'].$strOrder, 30, (30 * ($page - 1)));
      }
    $arrname = array();
    $arrcost = array();
    $arrowner = array();
    $arraction = array();
    $arramount = array();
    $arrlevel = array();
    $arrseller = array();
    $arrId = array();
    $arrFcost = array();
    while (!$pm -> EOF) 
      {
	$arrname[] = $pm -> fields['name'];
	$arrcost[] = $pm -> fields['cost'];
	$arrFcost[] = $pm->fields['cost'] * $pm->fields['amount'];
	$arrowner[] = $pm -> fields['owner'];
	$arramount[] = $pm -> fields['amount'];
	$arrlevel[] = $pm -> fields['minlev'];
	$seller = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$pm -> fields['owner']);
	$arrseller[] = $seller -> fields['user'];
	$seller -> Close();
	$arrId[] = $pm->fields['id'];
	$pm -> MoveNext();
      }
    $pm -> Close();
    $smarty -> assign(array("Name" => $arrname, 
			    "Cost" => $arrcost, 
			    "Owner" => $arrowner, 
			    "Action" => $arraction,  
			    "Amount" => $arramount, 
			    "Minlev" => $arrlevel,
			    "Iid" => $arrId,
			    "Pid" => $player->id,
			    "Tpages" => $pages,
			    "Tpage" => $page,
			    "Fcost" => $arrFcost,
			    "Fpage" => "Idź do strony:",
			    "Mlist" => $_GET['lista'],
			    "Abuy" => 'Kup',
			    "Aadd" => 'Dodaj',
			    "Adelete" => 'Wycofaj',
			    "Achange" => 'Zmień cenę',
			    "Seller" => $arrseller));
    if (!isset($_POST['szukany'])) 
        {
	  $_POST['szukany'] = '';
        }
}

/**
* Add oferts to market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'add') 
{
    $rzecz = $db -> Execute("SELECT `id`, `name`, `amount` FROM `equipment` WHERE `status`='U' AND `type`='O' AND `owner`=".$player -> id);
    $arrname = array();
    $arrid = array();
    $arramount = array();
    while (!$rzecz -> EOF) 
    {
        $arrname[] = $rzecz -> fields['name'];
        $arrid[] = $rzecz -> fields['id'];
        $arramount[] = $rzecz -> fields['amount'];
        $rzecz -> MoveNext();
    }
    $rzecz -> Close();
    if (count($arrid) == 0)
    {
        error("Nie masz przedmiotów na sprzedaż!");
    }
    $smarty -> assign (array("Name" => $arrname, 
                             "Itemid" => $arrid, 
                             "Amount" => $arramount,
                             "Addinfo" => "Dodaj ofertę na rynku lub",
                             "Item" => "Przedmiot",
                             "Aadd" => "Dodaj",
                             "Iamount" => "ilość",
                             "Iamount2" => "Ilość",
			     "Addall" => "wszystkie posiadane",
                             "Icost" => "Cena za sztukę"));
    if (isset ($_GET['step']) && $_GET['step'] == 'add') 
    {
        if (!isset($_POST['cost'])) 
        {
            error("Podaj cenę przedmiotu!");
        }
	checkvalue($_POST['cost']);
	checkvalue($_POST['przedmiot']);
        $item = $db -> Execute("SELECT * FROM `equipment` WHERE `id`=".$_POST['przedmiot']." AND `status`='U' AND `type`='O' AND `owner`=".$player -> id);
	if (!$item->fields['id'])
	  {
	    error("Nie ma takiego przedmiotu!");
	  }
	if (!isset($_POST['addall']))
	  {
	    checkvalue($_POST['amount']);
	  }
	else
	  {
	    $_POST['amount'] = $item->fields['amount'];
	  }
        if ($item -> fields['amount'] < $_POST['amount']) 
        {
            error ("Nie masz takiej ilości ".$item -> fields['name']);
        }
        if ($item -> fields['type'] != 'O')
        {
            error("To nie jest łup z potwora.");
        }
        $amount = $item -> fields['amount'] - $_POST['amount'];
        if ($amount > 0) 
        {
            $db -> Execute("UPDATE `equipment` SET `amount`=".$amount." WHERE `id`=".$item -> fields['id']);
        } 
            else
        {
            $db -> Execute("DELETE FROM `equipment` WHERE `id`=".$item -> fields['id']);
        }
        $test = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".$item -> fields['name']."' AND `type`='".$item -> fields['type']."' AND `status`='R' AND `owner`=".$player -> id." AND `power`=".$item -> fields['power']." AND `minlev`=".$item -> fields['minlev']);
        if (!$test -> fields['id']) 
        {
            $db -> Execute("INSERT INTO `equipment` (`owner`, `name`, `power`, `type`, `cost`, `minlev`, `status`, `amount`) VALUES(".$player -> id.", '".$item -> fields['name']."', ".$item -> fields['power'].", '".$item -> fields['type']."', ".$_POST['cost'].", ".$item -> fields['minlev'].", 'R', ".$_POST['amount'].")");
            $smarty -> assign("Message", "Dodałeś <b>".$_POST['amount']." sztuk ".$item -> fields['name']."</b> na rynku za <b>".$_POST['cost']."</b> sztuk złota. <a href=\"lmarket.php?view=add\">Odśwież</a>");
        } 
            else 
        {
            $db -> Execute("UPDATE `equipment` SET `amount`=`amount`+".$_POST['amount']." WHERE `id`=".$test -> fields['id']);
            $smarty -> assign("Message", "Dodałeś <b>".$_POST['amount']." sztuk ".$item -> fields['name']."</b>. <a href=\"lmarket.php?view=add\">Odśwież</a>");
        }
        $test -> Close();
    }
}

/**
* Delete selected ofert from market
*/
if (isset($_GET['wyc'])) 
{
    checkvalue($_GET['wyc']);
    $dwyc = $db -> Execute("SELECT * FROM `equipment` WHERE `id`=".$_GET['wyc']);
    if ($dwyc -> fields['owner'] != $player -> id) 
    {
        error ("Nie możesz wycofać cudzych ofert!");
    }
    require_once('includes/marketdel.php');
    deleteitem($dwyc, $player -> id);
    $smarty -> assign("Message", "Usunąłeś swoją ofertę i twój przedmiot wrócił do ciebie. (<a href=\"lmarket.php\">wróć</a>)");
}

/**
* Delete oferts from market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'del') 
{
    $objArm = $db -> Execute("SELECT * FROM `equipment` WHERE `owner`=".$player -> id." AND `status`='R' AND `type`='O'");
    while (!$objArm -> EOF)
    {
        $intTest = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".$objArm -> fields['name']."' AND `type`='".$objArm -> fields['type']."' AND `status`='U' AND `owner`=".$player -> id." AND `power`=".$objArm -> fields['power']." AND `cost`=1 AND `minlev`=".$objArm -> fields['minlev']);
        if (!$intTest -> fields['id']) 
        {
            $db -> Execute("UPDATE `equipment` SET `status`='U', `cost`=1 WHERE `id`=".$objArm -> fields['id']);
        } 
            else 
        {
            $db -> Execute("UPDATE `equipment` SET `amount`=`amount`+".$objArm -> fields['amount']." WHERE `id`=".$intTest -> fields['id']);
        }
        $intTest -> Close();
        $objArm -> MoveNext();
    } 
    $db -> Execute("DELETE FROM `equipment` WHERE `status`='R' AND `type`='O' AND `owner`=".$player -> id);
    $smarty -> assign("Message", "Usunąłeś wszystkie swoje oferty i twoje przedmioty wróciły do ciebie. (<a href=\"lmarket.php\">wróć</a>)");
}

/**
* Buy items from market
*/
if (isset($_GET['buy'])) 
{
    checkvalue($_GET['buy']);
    $buy = $db -> Execute("SELECT * FROM `equipment` WHERE `id`=".$_GET['buy']." AND `type`='O' AND `status`='R'");
    if (!$buy -> fields['id']) 
    {
        error ("Nie ma takiej oferty na rynku.");
    }
    if ($buy -> fields['owner'] == $player -> id) 
    {
        error ("Nie możesz kupić swoich przedmiotów!");
    }
    $seller = $db -> Execute("SELECT user FROM players WHERE id=".$buy -> fields['owner']);    
    $smarty -> assign(array("Name" => $buy -> fields['name'], 
                            "Itemid" => $buy -> fields['id'], 
                            "Amount1" => $buy -> fields['amount'], 
                            "Cost" => $buy -> fields['cost'], 
                            "Seller" => $seller -> fields['user'], 
                            "Sid" => $buy -> fields['owner'], 
                            "Type" => $buy -> fields['type'], 
                            "Item" => "Przedmiot",
                            "Buyinfo" => "Zakup przedmiot lub",
                            "Oamount" => "Ilość w ofercie",
                            "Icost" => "Cena za sztukę",
                            "Iseller" => "Sprzedający",
                            "Bamount" => "Ilość",
                            "Abuy" => "Kup"));
    $seller -> Close();
    if (isset($_GET['step']) && $_GET['step'] == 'buy') 
    {
        if (!isset($_POST['amount'])) 
        {
            error("Podaj ile przedmiotów chcesz kupić.");
        }
	checkvalue($_POST['amount']);
        if ($_POST['amount'] > $buy -> fields['amount']) 
        {
            error("Nie ma takiej ilości ".$buy -> fields['name']." na rynku!");
        }
        $price = $_POST['amount'] * $buy -> fields['cost'];
        if ($price > $player -> credits) 
        {
            error ("Nie stać cię!");
        }
        $test = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".$buy -> fields['name']."' AND `type`='".$buy -> fields['type']."' AND `status`='U' AND `owner`=".$player -> id." AND `power`=".$buy -> fields['power']." AND `cost`=1 AND `minlev`=".$buy -> fields['minlev']);
        if (!$test -> fields['id']) 
        {
            $db -> Execute("INSERT INTO `equipment` (`owner`, `name`, `power`, `type`, `cost`, `zr`, `wt`, `minlev`, `maxwt`, `amount`, `magic`, `poison`, `szyb`, `twohand`, `ptype`, `repair`) VALUES(".$player -> id.", '".$buy -> fields['name']."', ".$buy -> fields['power'].", '".$buy -> fields['type']."', 1, ".$buy -> fields['zr'].", ".$buy -> fields['wt'].", ".$buy -> fields['minlev'].", ".$buy -> fields['maxwt'].", ".$_POST['amount'].", '".$buy -> fields['magic']."', ".$buy -> fields['poison'].", ".$buy -> fields['szyb'].", '".$buy -> fields['twohand']."', '".$buy -> fields['ptype']."', ".$buy -> fields['repair'].")");
        } 
            else 
        {
            $db -> Execute("UPDATE `equipment` SET `amount`=`amount`+".$_POST['amount']." WHERE `id`=".$test -> fields['id']);
        }
        $test -> Close();
        if ($_POST['amount'] == $buy -> fields['amount']) 
        {
            $db -> Execute("DELETE FROM `equipment` WHERE `id`=".$buy -> fields['id']);
        } 
            else 
        {
            $db -> Execute("UPDATE `equipment` SET `amount`=`amount`-".$_POST['amount']." WHERE `id`=".$buy -> fields['id']);
        }
        $db -> Execute("UPDATE `players` SET `bank`=`bank`+".$price." WHERE `id`=".$buy -> fields['owner']);
        $db -> Execute("UPDATE `players` SET `credits`=`credits`-".$price." WHERE `id`=".$player -> id);
        $strDate = $db -> DBDate($newdate);
        $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`, `type`) VALUES(".$buy -> fields['owner'].", '<b><a href=view.php?view=".$player -> id.">".$player -> user."</a></b>, ID <b>".$player -> id."</b> zaakceptował Twoją ofertę za ".$_POST['amount']." sztuk ".$buy -> fields['name']."</b>. Dostałeś <b>".$price."</b> sztuk złota do banku.', ".$strDate.", 'M')");
        $smarty -> assign("Message", "<br />Kupiłeś ".$_POST['amount']." sztuk przedmiotu: ".$buy -> fields['name']."</b> za <b>".$price."</b> sztuk złota.");
    }
    $buy->Close();
}

/**
* List of all oferts on market
*/
if (isset($_GET['view']) && $_GET['view'] == 'all') 
{
    $oferts = $db -> Execute("SELECT `name` FROM `equipment` WHERE `status`='R' AND `type`='O' GROUP BY `name`");
    $arrname = array();
    $arramount = array();
    $i = 0;
    while (!$oferts -> EOF) 
    {
        $arrname[$i] = $oferts -> fields['name'];
        $arramount[$i] = 0;
        $query = $db -> Execute("SELECT count(`id`) FROM `equipment` WHERE `status`='R' AND `name`='".$arrname[$i]."'");
        $arramount[$i] = $query -> fields['count(`id`)'];
        $query -> Close();
        $oferts -> MoveNext();
        $i = $i + 1;
    }
    $oferts -> Close();
    $smarty -> assign(array("Name" => $arrname, 
                            "Amount" => $arramount, 
                            "Message" => "<br />(<a href=\"lmarket.php\">wróć</a>)",
                            "Listinfo" => "Tutaj masz spis wszystkich ofert jakie są na rynku.",
                            "Iname" => "Nazwa",
                            "Iamount" => "Ofert",
                            "Iaction" => "Akcja",
                            "Ashow" => "Pokaż"));
}

/**
* Initialization of variables
*/
if (!isset($_GET['view'])) 
{
    $_GET['view'] = '';
}
if (!isset($_GET['wyc'])) 
{
    $_GET['wyc'] = '';
}
if (!isset($_GET['buy'])) 
{
    $_GET['buy'] = '';
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array("View" => $_GET['view'], 
                        "Remowe" => $_GET['wyc'], 
                        "Buy" => $_GET['buy'],
                        "Aback" => "wróć"));
$smarty -> display('lmarket.tpl');

require_once("includes/foot.php"); 
?>
