<?php

  // Copyright 20015 Virre Annergård .
  /* This file is part of vgstools.

    vgstools is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    vgstools is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with vgstools.  If not, see <http://www.gnu.org/licenses/>.*/

  /**
   * @file veganistan.php
   * @brief File containing the search class for vgstools.
   * @author Virre Annergård <virre.annergard@gmail.com>
   **/


  /**
   * @class Veganistan.
   * @brief This is the main class that holds the general functions and other classes inherits from.
   **/

  Class Veganistan {
    

    protected $site_uri = "http://www.veganistan.se"; //!< The general URI to site. 
    public $townArrays = array("stockholm"); //!< Array contains name of town arrays. 

    // All Stokholm post towns except thoose in Norrtälje and Nykvarn because thoose would not
    // be included in thinking of Stockholm. Just add them to the array if you disagree..
    // Because this information cost places to get from most places
    // or APIs I used the list at post24.se/postort-kommun-lan-bokstavsordning.
    public $stockholm_towns = array(
      'Adelsö','Arlandastad','Bagarmossen', 'Bandhagen', 'Brandbergen', 'Bro', 'Bromma',
      'Brottby', 'Dalarö', 'Danderyd', 'Djurhamn', 'Djursholm', 'Drottningholm', 'Edsbro',
      'Ekerö', 'Enebyberg', 'Enhörna', 'Enskede', 'Enskede Gård', 'Enskededalen', 'Farsta',
      'Färentuna', 'Furusund', 'Grinda', 'Gränö', 'Grödinge', 'Gustavsberg', 'Gålö', 'Gällnöby',
      'Handen', 'Haninge', 'Harö', 'Husarö', 'Hårsfjärden', 'Hägersten', 'Hässelby', 'Hölö',
      'Ingarö', 'Ingmarsö', 'Järfälla', 'Järna', 'Johanneshov', 'Jordbro', 'Kista',
      'Kungens Kurva', 'Kungsängen', 'Lidingö', 'Ljusterö', 'Märsta', 'Märsta Arlanda',
      'Möja', 'Mölnbo', 'Mörkö', 'Munsö', 'Muskö', 'Nämdö', 'Nacka', 'Nacka Strand',
      'Norra Sorunda', 'Norrby', 'Norrtälje', 'Norsborg', 'Nynäshamn', 'Ornö',  'Rosersberg',
      'Runmarö', 'Rönninge', 'Saltsjö-Boo', 'Saltsjö-Duvnäs', 'Saltsjöbaden', 'Sandhamn',
      'Segeltorp', 'Segersäng', 'Sigtuna', 'Skå', 'Skälvik', 'Skärholmen', 'Sköndal',
      'Skarpnäck', 'Skogås', 'Sollenkroka Ö', 'Sollentuna', 'Solna', 'Sorunda', 'Spånga',
      'Stavnäs', 'Stavsudda','Stenhamra', 'Steningehöjden', 'Stockholm', 'Stockholm-Arlanda',
      'Stockholm-Globen', 'Stocksund', 'Stora Vika', 'Svartsjö', 'Söderby', 'Södertälje',
      'Sundbyberg', 'Trångsund', 'Tomteboda', 'Tullinge', 'Tumba', 'Tungelsta', 'Tyresö',
      'Täby', 'Upplands Väsby', 'Uttran', 'Utö', 'Vallentuna', 'Vaxholm', 'Vega', 'Vendelsö',
      'Vårby', 'Väddö', 'Vällingby', 'Värmdö', 'Västerhaninge', 'Vätö', 'Åkersberga', 'Årsta',
      'Årsta Havsbad', 'Älvsjö', 'Älta', 'Ösmo', 'Österhaninge', 'Österskär', 
    ); //!< Array containg all Post-towns in a wide definition of Stockholm. 

    /**
     * \brief Gets the address data from the longer description and returns it as pure text. 
     *
     * @param string $string.
     *  The HTML string for object to look for address in. 
     *
     * @return string $string.
     *  Return pure text string of address. 
     **/
    public function getAddressFromDescription($string) {
      $outputstring = "";
      $dom = new DOMDocument;
      @$dom->loadHTML(utf8_decode($string));
      foreach($dom->getElementsByTagName('div') as $div) {
          $classes = $div->getAttribute('class');
          if (preg_match("/street-block/", $classes)) {
            $outputstring .= "$div->textContent\n";
          }
          if (preg_match("/locality-block/", $classes)) {
            $outputstring .= "\t$div->textContent\n";
          }
      }
      return $outputstring;
    }

    /**
     * \brief Gets the description data from the longer description and returns it as pure text. 
     *
     * @param string $string.
     *  The HTML string for object to look for description in. 
     *
     * @return string $string.
     *  Return pure text string of description. 
     **/
    public function getDescriptionFromDescription($string) {
      $outputstring = "";
      $dom = new DOMDocument;
      @$dom->loadHTML(utf8_decode($string));
      foreach ($dom->getElementsByTagName('div') as $div) {
        $classes = $div->getAttribute('class');
        if (preg_match("/field-type-text-with-summary/", $classes)) {
          $outputstring .= "$div->textContent\n";
        } 
      }
      return $outputstring;
    }

    /**
     * \brief Town exists in array. 
     *
     * @return bool. 
     *  Returns true if we have an array of towns for this town, False otherwise. 
     **/ 
    protected function existsTownArray() {
      if (in_array(strtolower($this->town), $this->townArrays)) {
        return TRUE;
      }
      return FALSE;
    }

    /**
     * \brief Returns the array of post-towns for the general town you search for.
     *
     * @return mixed.
     *  Returns the town array if exists, otherwise returns FALSE. 
     **/
    protected function getTownArray() {
      if (in_array(strtolower($this->town), $this->townArrays)) {
          $array_name = strtolower($this->town) . "_towns";
          return $this->$array_name;
      }
      return FALSE;
    }

    /** 
     * \brief Check if a link contains town (Could also be used) for other matching.
     *
     * @param $link string Contains the link to check in.
     * @param $town string Contains the string to check with.
     * @return Bool Returns TRUE if matching otherwise FALSE. 
     **/
    protected function matchLinkToTown($link, $town) {
      $towns = $this->getTownArray($town);
      if (empty($towns)) {
        if (preg_match("/$town/", $link)) {
          return TRUE; 
        }
      }
      elseif (!empty($towns)) {
        foreach ($towns as $town) {
          $town = $this->makeWebPattern($town);
          if (preg_match("/$town/", $link)) {
            return TRUE; 
          }
        }
      }
    }

    /**
     * \brief Make string lovwercase and in otherway match the pages URI structure. 
     *
     * @param $string Original string. 
     * @return String $string that been lovercased and had local characthers removed. 
     */
    protected function makeWebPattern($string) {

      // It feels like I forgot a better way to make this.
      $string = mb_strtolower($string);
      $string = str_replace("å", "a", $string);
      $string = str_replace("ä", "a", $string);
      return str_replace("ö", "o", $string);
    }

    /**
     * \brief Get the name of resturant by regexping for the page title id in html-string. 
     *
     * @param $string String Contains the html element to search through.
     * @return String of page title if found otherwise boolean False. 
     **/
    protected function getNameFromPage($string) {
      $outputstring = "";
      $dom = new DOMDocument;
      @$dom->loadHTML(utf8_decode($string));
      foreach ($dom->getElementsByTagName('h1') as $h1) {
        $id = $h1->getAttribute('id');
        if (preg_match("/page-title/", $id)) {
          return trim($h1->textContent);
        } 
      }
      return FALSE;
    }

    /**
     * \brief Get the opening times of resturant by regexping for the "oppettider-fritext" class in html-string. 
     *
     * @param $string String Contains the html element to search through.
     * @return String of opening times if found otherwise boolean False. 
     **/
    protected function getOpeningTimesFromPage($string) {
      $outputstring = "";
      $dom = new DOMDocument;
      @$dom->loadHTML(utf8_decode($string));
      foreach ($dom->getElementsByTagName('section') as $section) {
        $classes = $section->getAttribute('class');
        if (preg_match("/field-name-field-oppettider-fritext/", $classes)) {
          var_dump($section);
        } 
      }
      return FALSE;
    }

    /**
     * \brief Get the length of both arrays and arrays inside of objects. 
     *     Usefull for when we use both splFixedArray and arrays inside another array.  
     *
     * @param $arr Array contains arrays and links. 
     * @return Integer Number of content. 
     */
    protected function mixedCount($arr) {
      if (!is_array($arr) && !is_object($arr)) {
        return 0;
      }
      $count = 0;
      foreach ($arr as $index) {
        if (is_array($index)) {
          foreach($index as $index2) {
            $count++;
          }
        } elseif (!is_array($index)) {
          $count++;
        }
      }
      return $count; 
    }

  }

?>