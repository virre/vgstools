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

  require_once("class/news.php");
  $news = new News();
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