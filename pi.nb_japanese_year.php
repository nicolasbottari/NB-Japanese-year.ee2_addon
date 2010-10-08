<?php

// NB Japanese Date v.2.0
// For ExpressionEngine 2
// Copyright Nicolas Bottari
// -----------------------------------------------------
//
// Description
// -----------
// Converts date into Japanese format
//
// More info: http://nicolasbottari.com/expressionengine_cms/nb_japanese_year/
//

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'NB Japanese Year',
  'pi_version' =>'2.0',
  'pi_author' =>'Nicolas Bottari',
  'pi_author_url' => 'http://nicolasbottari.com/expressionengine_cms/nb_japanese_year',
  'pi_description' => 'Displays date using Japanese era years',
  'pi_usage' => Nb_japanese_year::usage()
  );


class Nb_japanese_year
{

var $return_data = "";
var $heisei_start = 1989;
var $showa_start = 1926;
var $taisho_start = 1912;
var $meiji_start = 1868;
var $eras = array(
			"Heisei" => "&#24179;&#25104;",
			"Showa" => "&#26157;&#21644;",
			"Taisho" => "&#22823;&#27491;",
			"Meiji" => "&#26126;&#27835;",
			);
// Era entities
//&#24179;&#25104;
//&#26157;&#21644;
//&#22823;&#27491;
//&#26126;&#27835;

  function Nb_japanese_year()
  {
	$this->EE =& get_instance();
	
	// ----------
	// Parameters
	// ----------
	// @param style: era, kanji_era, shorthand_era, number
	
	$date = ($this->EE->TMPL->fetch_param('date') !== FALSE) ? $this->EE->TMPL->fetch_param('date') : date("Y-m-d");
	$format = ($this->EE->TMPL->fetch_param('style') !== FALSE) ? $this->EE->TMPL->fetch_param('style') : "era";
	$year_only = ($this->EE->TMPL->fetch_param('year_only') !== FALSE) ? $this->EE->TMPL->fetch_param('year_only') : "no";

	// -----------
	// Return code
	// -----------
	
	if(preg_match("([0-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9])", $date) === 0)
	{
		if(strlen($date) == 4)
		{
			if(!is_numeric($date) || $date <= $this->meiji_start)
			{
			$year = date("Y");
			} else {
			$year = $date;
			}
		} else {
		$year = date("Y");
		}
	} else {
		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day = substr($date, 8, 2);
	}
	
	switch ($year) {
		case ($this->heisei_start <= $year):
			$japanese_year = $year - $this->heisei_start + 1;
			$era = $this->eras['Heisei'];
			$era_short = substr(array_search($era, $this->eras), 0 , 1);
			break;
		case ($this->showa_start <= $year && $year < $this->heisei_start):
			$japanese_year = $year - $this->showa_start + 1;
			$era = $this->eras['Showa'];
			$era_short = substr(array_search($era, $this->eras), 0 , 1);
			break;
		case ($this->taisho_start <= $year && $year < $this->showa_start):
			$japanese_year = $year - $this->taisho_start + 1;
			$era = $this->eras['Taisho'];
			$era_short = substr(array_search($era, $this->eras), 0 , 1);
			break;
		case ($this->meiji_start <= $year && $year < $this->taisho_start):
			$japanese_year = $year - $this->meiji_start + 1;
			$era = $this->eras['Meiji'];
			$era_short = substr(array_search($era, $this->eras), 0 , 1);
			break;
		}
		
	if($format == "era")
	{
		if(!isset($month))
		{
		$this->return_data = $era." ".$japanese_year;
		} else {
		$this->return_data = $era." ".$japanese_year;
		}
	}
	
		
	if($format == "shorthand_era")
	{
		if(!isset($month))
		{
		$this->return_data = $era_short.$japanese_year;
		} else {
		$this->return_data = $era_short.$japanese_year;
		}
	}	
	
	if($format == "kanji_era")
	{
		if(!isset($month))
		{
		$this->return_data = $era.$this->_kanjifier($japanese_year)."&#24180;";
		} else {
		$this->return_data = $era.$this->_kanjifier($japanese_year)."&#24180;".$this->_kanjifier($month)."&#26376;".$this->_kanjifier($day)."&#26085;";
		}
	}
	
	if($format == "year_number")
	{
		$this->return_data = $japanese_year;
	}
		
		
	
	
	return $this->return_data;
	
	}
	
	function _kanjifier($num) {
	
	if(!is_numeric($num))
	{
		return;
	}
	
	$numArray = array(
	1 => "&#19968;",
	2 => "&#20108;",
	3 => "&#19977;",
	4 => "&#22235;",
	5 => "&#20116;",
	6 => "&#20845;",
	7 => "&#19971;",
	8 => "&#20843;",
	9 => "&#20061;",
	"juu" => "&#21313;",
	"hyaku" => "&#30334;",
	"sen" => "&#21315;",
	);
	
	// Generate thousands in kanji
	$thousands_kanji = (substr($num, 3) === FALSE || substr($num, -4, 1) == 0) ? "": 
		$numArray[substr($num, 0, -3)].$numArray['sen'];
	$thousands_kanji = (substr($num, 0, -3) == 1) ? $numArray['sen'] : $thousands_kanji; // remove extra "1" when flush at 1000
	
	// Generate hundreds in kanji
	$hundreds_kanji = (substr($num, 2) === FALSE || substr($num, -3, 1) == 0) ? "": 
		$numArray[substr($num, 0, -2)].$numArray['hyaku'];
	$hundreds_kanji = (substr($num, 0, -2) == 1) ? $numArray['hyaku'] : $hundreds_kanji; // remove extra "1" when flush at 100
	
	// Generate tens in kanji
	$tens_kanji = (substr($num, 1) === FALSE || substr($num, -2, 1) == 0) ? "": 
		$numArray[substr($num, 0, -1)].$numArray['juu'];
	$tens_kanji = (substr($num, 0, -1) == 1) ? $numArray['juu'] : $tens_kanji; // remove extra "1" when flush at 10
	
	// Generate single units in kanji
	$digit_kanji = (substr($num, -1) == 0) ? "" : $numArray[substr($num, -1)];
	
	
	$num_kanji = $thousands_kanji.$hundreds_kanji.$tens_kanji.$digit_kanji;
	
	
	return $num_kanji;
	}
  
// ----------------------------------------
  //  Plugin Usage
  // ----------------------------------------

  // This function describes how the plugin is used.
  //  Make sure and use output buffering

  function usage()
  {
  ob_start(); 
  ?>
-----------
   Usage
-----------

{exp:nb_japanese_year date="1984" format="kanji_era"}

---------------------
     Parameters
---------------------

date="2009"

The year to convert to Japanese format. Full Japanese dates in kanji, including the month and day, can be used if the date is in the YYYY-MM-DD format and the style is set to "kanji_era".

style="kanji_era"

The date format. Options are (for example, "2009")

    * era: The era in alphabet and date in numebrs, eg. Heisei 21
    * kanji_era: The era in kanji and date in numbers, eg. &#24179;&#25104;21&#24180;
    * shorthand_era: The first letter of the era in alphabet and date in numbers, eg. H21
    * year_number: The date in numbers only, eg. 21

Default is "era"

  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END
  
}
?>