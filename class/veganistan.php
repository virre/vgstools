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


  Class Veganistan {
    
    // All Stokholm post towns except thoose in Norrtälje and Nykvarn because thoose would not
    // be included in thinking of Stockholm. Just add them to the array if you disagree..
    // Because this information cost places to get from most places
    // or APIs I used the list at post24.se/postort-kommun-lan-bokstavsordning.
    public $stockholm_towns = array(
      'Adelsö','Arlandastad','Bagarmossen', 'Bandhagen', 'Brandbergen', 'Bro', 'Bromma',
      'Brottby', 'Dalarö', 'Danderyd', 'Djurhamn', 'Djursholm', 'Drottningholm', 'Edsbro',
      'Ekerö', 'Enebyberg', 'Enhörna', 'Enskede', 'Enskede_Gård', 'Enskededalen', 'Farsta',
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
    );

    /**
     * Gets the address data from the longer description and returns it as pure text. 
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
     * Gets the description data from the longer description and returns it as pure text. 
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
  }

?>