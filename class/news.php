<?php

  // Copyright 20015 Virre Annergård.
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
  require_once("veganistan.php");

  class News extends Veganistan {

    public $town = "";

    /**
     * Fetch the News XML feed. 
     *
     * @param bool $stric.
     *  Get strict from town if True otherwise look for posttown arrays for general area. 
     *
     * @return Object.
     *  Returns simplexml objects for either all post towns in area or just the strict search ones.
     **/
    public function getNewsXML($strict = FALSE) {
      if (!$strict) {
        $rawxml = $this->getXML();
        if (!$rawxml) {
          die("Something went wrong, please make sure you are connected to the Internet");
        }
        return $this->sortOutObjects($rawxml, $this->stockholm_towns);
      }
      elseif ($strict) {
        $rawxml = $this->getXML($this->town);
        return $rawxml;
      }

    }

    /**
     * Check so file is xml and exists.
     *
     * @param string $path.
     *  The path to check.
     *
     * $return bool.
     *  True if file exists and is XML otherwise FALSE.
     **/
    private function xml_exists($path) {
      if (strcmp(strtolower(pathinfo($path, PATHINFO_EXTENSION)), "xml") !==0 ) {
        return FALSE;
      }
      $ch = curl_init("$path");
      curl_setopt($ch, CURLOPT_NOBODY, TRUE);
      curl_exec($ch);
      if (intval(curl_getinfo($ch, CURLINFO_HTTP_CODE)) === 200) {
        return TRUE;
      }
      return FALSE;
    }

    /**
     * Get the XML Value either for all or just
     * a specific posttown.
     *
     * @todo: 
     *   Handle small town strict when file don't exist.
     *
     * @param string $value.
     *  If set contains the town to check.
     *
     * @return mixed $xml.
     *  Contains the SimpleXML object for a town
     *  or false if somethign went wrong and was not handled.
     **/
    private function getXML($value = FALSE) {
      $uri = "http://www.veganistan.se/";
      if (!$value) {
        $uri .= "nyheter.xml";
      }
      elseif (is_string($value)) {
        $uri .= strtolower($value) . ".xml";
      }
      if (!$this->xml_exists($uri)) {
        die("Something went wrong, be sure you are online and that veganistan.se is up");
      }
        $xml = simplexml_load_file($uri);
      if (is_object($xml)) {
        return $xml;
      } else {
        return FALSE;
      }
    }

    /**
     * Sort out objects that are in a posttown in the town area.
     *
     * @param Objct $xmlfile.
     *  The raw simplexml feed from xml file.
     * @param array $town_array.
     *  The acctuall array of towns to compare with. 
     *
     * @return array $output.
     *  Array containing objects of place data in pure text. 
     **/
    private function sortOutObjects($xmlfile, $town_array) {
      $output = array();
      foreach ($xmlfile as $objects) {
        foreach ($objects as $object) {
          if (empty($object)) {
            continue ;
          }
          if (!$this->isLinkTown($object->link, $town_array)) {
            continue;
          }
          $data = new stdClass();
          $data->title = (string) $object->title;
          $data->adress = $this->getAddressFromDescription((string)$object->description);
          $data->description = $this->getDescriptionFromDescription((string)$object->description);
          $data->link = (string) $object->link;
          $output[] = $data;
        }
      }
      return $output;
    }

    /**
     * Function to look at link and match to post-town ,array used for non-strict search.
     *
     * @param string $uri .
     *  The uri to get town from.
     * @param array $towns.
     *  The town array to compare with. 
     *
     * @return bool. 
     *  Return True if one of the post-towns is the town part of the link otherwise fasle.  
     **/
    private function isLinkTown($uri, $towns) {
      $towns = array_map("mb_strtolower", $towns);
      $str = str_replace("http://www.veganistan.se/", "", $uri);
      $remove_from_post = strrpos($str, '/');
      $str = substr($str, 0, $remove_from_post);
      if (in_array($str, $towns)) {
        return TRUE;
      }
      return FALSE;
    }
  }




?>