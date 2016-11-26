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

    /**
     * @file search.php
     * @brief File containing the search class for vgstools.
     * @author Virre Annergård <virre.annergard@gmail.com>
     */


    // Load class to inherit from.
	require_once("veganistan.php");

    /**
     * @class Search.
     * @brief This is the class that holds the search functions.
     **/
	class Search extends Veganistan {

		protected $search_uri = "http://www.veganistan.se/?field_adress_locality="; //!< Search URI. 
		public $strict = FALSE; //!< Defines if search should be strict or not. 
		public $town = "";  //!< The town to work with. 
		protected $searchTown = ""; //!< The current town being searched when generous search is used. 

		/**
		 * \brief Do the acctual search based on if the strict property is TRUE OR FALSE.
		 *
		 * @return Array Returns an array with the result. 
		 **/
		public function _search() {
			if ($this->strict) {
				return $this->exactSearch();
			} 
			elseif (!$this->strict) {
				return $this->generousSearch();
			}
		}

		/**
		 * \brief Run an search for jsut the current town.
		 * 
		 * @return Array containing the result of search. 
		 **/
		private function exactSearch() {
			$this->searchTown = $this->town; 
			return $this->makeQuery();
		}

		/**
		 * \brief Search for places in a district.
		 *
		 * @param string $district.
		 *   Array key district name for district to search for.
		 *
		 * @return splFixedArray containing the places in district. 
		 **/
		public function districtSearch($district) {
			$town_results = $this->exactSearch();
			$count = count($town_results);
			$i = 0;
			$found = array();
			while ($i < $count) {
				$place = $town_results[$i];
				$zip_start = $this->getZipStart($place[1]);
				if ($this->isZipInDistrict($zip_start, $district)) {
					$found[] = $i;
				}
				$i++;
			}
			$results = new splFixedArray(count($found));
			$i = 0;
			foreach($found as $place_id) {
				$results[$i] = $town_results[$place_id];
				$i++;
			}
			return $results;
		}

		/**
		 * \brief Make an generous search by going through all towns in town array and querying them.
		 *
		 * @return Array with data found in search or FALSE if nothing is found.
		 */
		private function generousSearch() {
			$towns = $this->getTownArray();
			if (is_array($towns)) {
				$output = array();
				$i = 0;
				foreach($towns as $town) {
					$this->searchTown = $town;
					if ($data = $this->makeQuery()) {
						$output[$i] = $data;
						$i++;
					}
				}
				return $output;
			} elseif (!is_array($towns)) {
				return $this->exactSearch();
			}
			return FALSE;
		}

		/**
		 * \brief Make the acctual query to the homepage.
		 *
		 * @todo: Make sure to retry untill not 500 on webbrequests as load errors happens.
		 * 
		 * @return splFixedArray for each place found containg an SplFixedArray of three rows with Name,
		 *         address and description as there content or bool FALSE if something fails. 
		 **/
		private function makeQuery() {
			if (empty($this->town)) {
				die("No town have been set");
			}
			if (!empty($this->getLinks())) {
				$links = $this->getLinks();
				$results = new splFixedArray($this->mixedCount($links));
				$found = array();
				// Content array will be 0 = Name, 1 = Adress 2 = Opening times 3 = Description.
				$content = new splFixedArray(3);
				$num = 0;
				foreach ($links as $link) {
					if (is_array($link)) {
						foreach ($link as $page) {
							if ($found[$page]) {
								continue;
							}
							$uri = "http://www.veganistan.se" . $page;
							$data = file_get_contents($uri);

							// TODO: check for error.
							$content[0] = $this->getNameFromPage($data);
							$content[1] = $this->getAddressFromDescription($data);
							$content[2] = $this->getDescriptionFromDescription($data);
							$results[$num] = $content;
							$num++;
							unset($content);
							$found[$page] = TRUE;
						}
					}
					elseif (!is_array($link)) {
						if ($found[$link]) {
							continue;
						}
						$uri = "http://www.veganistan.se" . $link;
						$data = file_get_contents($uri);

						//TODO: Check for error.
						$content[0] = $this->getNameFromPage($data);
						$content[1] = $this->getAddressFromDescription($data);
						$content[2] = $this->getDescriptionFromDescription($data);
						$results[$num] = $content;
						unset($content);
						$found[$page] = TRUE;
						$num++;
					}
				}
				$results->setSize(count($found)+1);
				return $results;
			} else {
				$links = NULL;
				unset($links);
				return FALSE;
			}
			$links = NULL;
			unset($links);
		}

		/**
		 * \brief Get all links for a search.
		 *
		 * @return Returns an splFixedArray of all resturant urls on multiple pages if the search resulted in an multipage
		 *         answer or an array of urls if not multipage and FALSE on error. 
		 **/
		private function getLinks() {
			if (!empty($this->searchTown)) {
				$town = $this->searchTown;
			} elseif (empty($this->searchTown)) {
				$town = $this->town;				
			}
			$content = file_get_contents($this->search_uri . $town);
			if (!$this->isMultiPage($content)) {
				if ($data = $this->getLinksFromPage($content)) {
					return $data;
				} elseif (!$data) {
					return FALSE;
				}
			}
			elseif ($this->isMultiPage($content)) {
				$count = $this->amountPages($content);
				$rest = new splFixedArray($count); 
				if (!$count) {
					die("No multipages exists, this should not happen");
				} elseif ($count) {
					$rest[0] = $this->getLinksFromPage($content);
					$i = 1;

					// Yes the page=1 is the second page...
					for ($i; $i < $count; $i++) {
						$uri = $this->search_uri . $town . "&page=$i";
						$links = $this->fetchLinksFromPage($uri);
						$rest[$i] = $links;
					}

					// Clean up the variables we don't need more.
					// Because trusting php garbage cleaner sounds horrible.
					$uri = ""; 
					$content = "";
					unset($uri);
					unset($content);
					return $rest;
				}
				return FALSE; 
			}
			return FALSE;
		}

		/** 
		 * \brief Fetch the acctual links to resturants from a string (mostly the content result of a search).
		 *
		 * @param $content String that contains the HTML result of a search. 
		 * @return Either an array of links to resturants or FALSE if none is found or
		 *         something goes wrong. 
		 **/
		private function getLinksFromPage($content) {
			$dom = new DOMDocument;
			@$dom->loadHTML(utf8_decode($content));
			$out_links = array();
			$links = $dom->getElementsByTagName('a');
			if (!empty($this->searchTown)) {
				$town = $this->makeWebPattern($this->searchTown);
			} elseif (empty($this->searchTown)) {
				$town = $this->makeWebPattern($this->town);
			}
			foreach($links as $link) {
				$goal = $link->getAttribute('href');
				$classes = $link->getAttribute('class');
				if (empty($classes)) {
					if (preg_match("/user|nyheter|https/", $goal)) {
						continue;
					}
					if (preg_match("/\/.+\/.+/", $goal)) {
						$pattern = "/\/$town\/.+/";
						if (preg_match($pattern, $goal)) {
							$out_links[] = $goal;
						}
					} 
				} 
			}
			if (!empty($out_links)) {
				return $out_links;
			}
			return FALSE;
		}

		/**
		 * \brief Fetch the content of a page and then send it to getLinksFromPage method to extrapolate links
		 *
		 * @param $uri String The URI string to load the content off. 
		 * @return Array containing the links found on the $uri page or FALSE if none is found or anything goes wrong.
		 **/
		private function fetchLinksFromPage($uri) {
			$content = file_get_contents($uri);
			if ($links = $this->getLinksFromPage($content)) {
				return $links;
			}
			return FALSE;
		}

		/**
		 * \brief Look at a string and see if it is multipage html-page by looking for pager class. 
		 *
		 * @param $content String Contains the HTML code of an URI to check if it is a multipage reuslt.
		 *
		 * @return Bool True if it is multipage otherwise FALSE. 
		 **/
		private function isMultiPage($content) {
			 $dom = new DOMDocument;
			 @$dom->loadHTML(utf8_decode($content));
			 foreach($dom->getElementsByTagName('ul') as $ul) {
			 	$classes = $ul->getAttribute('class');
			 	if (preg_match("/pager/", $classes)) {
			 		return TRUE;
			   	}
			}
			return FALSE;
		}

		/**
		 * \brief Fetch the content and count if it is multipage by looking for the last number in the description of page length.
		 *
		 * @param $content String Contains the HTML code of an URI to check how many pages it is.
	     *
		 * @return Integer Number of pages in pagination. 
		 **/
		private function amountPages($content) {
			 $dom = new DOMDocument;
			 @$dom->loadHTML(utf8_decode($content));
			 foreach($dom->getElementsByTagName('li') as $li) {
			 	$classes = $li->getAttribute('class');
			 	if (preg_match("/pager-current/", $classes)) {
			 		return (int)strrchr($li->textContent, " ");
			   	}
			}
			return FALSE;
		}

	}




?>