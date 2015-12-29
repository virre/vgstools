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

  // Example file of how you can use the avalible tools

  require_once("class/search.php");

  // Create a search object.
  $search = new Search();

  // Set a town to search. 
  $search->town = "Stockholm";

  // Make a generous search and show the amount of restuants. 
  print "Amount of results with with generous search for $search->town " . count($search->_search());

  // Set to strict search
  $search->strict = TRUE;

  // Make a strict search and show the amount of results. 
  print "Amount of results with with strict search for $search->town " . count($search->_search());


  // Create a news Object
  $news = new News();

  // Lets try a Strict search for a town that have no local XML. 
  $news->town = "Solna";
  $all_town_places = $news->getNewsXML(TRUE);
  foreach ($all_town_places as $place) {
    print "Title: $place->title\n";
    print "Adress: $place->adress\n";
    print "Link: $place->link\n";
    print "Description: $place->description";
    print " -----\n";
  }

  // Strict search for Stockholm that do have a local xml. 
  $news->town = "Stockholm";
  $all_town_places = $news->getNewsXML(TRUE);
  foreach ($all_town_places as $place) {
    print "Title: $place->title\n";
    print "Adress: $place->adress\n";
    print "Link: $place->link\n";
    print "Description: $place->description";
    print " -----\n";
  }

  // None-strict search for Stockholm so we will use the array of posttowns in Stockholm. 
  $news->town = "Stockholm";
  $all_town_places = $news->getNewsXML();
  foreach ($all_town_places as $place) {
    print "Title: $place->title\n";
    print "Adress: $place->adress\n";
    print "Link: $place->link\n";
    print "Description: $place->description";
    print " -----\n";
  }
?>