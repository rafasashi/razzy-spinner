<?php
/**
 *@Author : Razak Zakari (Razzbee)
 * @Email : razzsense@gmail.com
 * @Whatsapp : +233244827491
 * @Facebook : facebook.com/razzbee
 * @File : spinner.class.php 
 * @Class : spinner  
 * @description : The Core class for rewriting the articles  
 * @license : see license.txt
 */
 
	use \ICanBoogie\Inflector;

	class spinner extends lang{


	//split sentence
	/**
	Accepts an array as param ($options)
	returns an array of splitted
	**/
	public static function spin($str,$options=[]){

	//if str is empty
	if(empty($str)){
	return self::get_alert("error","string cannot be empty");
	}//end if str is empty


	//original string
	$original_str = $str;

	//words to ignore
	$defaultWordsToIgnore = "drone,stimulator,computer,forces,system,systems,bats,bat,sports,sport,shoot,bow,champion,knockout,pendulum,block,form,years,tables,table,macau,performance,right,microcosm,mouth,tail,high,space,fly,flying,pick,wheel,grow,seat,fishing,set,clear,sound,race,races,part,manoeuvre,cause,speed,speeding,come,go,entertainment,shot,official,desert,deserts,winter,gains,traffic,traffics,operate,direct,arm,mod,flight,present,don't,don’t,darkness,notice,place,technologies,technology,record,records,display,displays,sensitive,analog,fresh,moon,skull,nature,good,rest";

	/***
	$defautOptions
	****/
	$defaultOptions = [
	"ignore_quoted_strings" => true,
	"ignore_stop_words" => true,
	"ignore_cap_words"=>true,
	"ignore_braced_words" => true,
	"ignore_words" => "",
	"highlight_changes" => true
	];

	$str = strtolower($str);

	//options array
	//override default options
	$optionsArray = array_merge($defaultOptions,$options);

	//we dont need tags
	 $str  = strip_tags( $str );

	 //we dont need slashes eithe
	 	//we dont need tags
	 $str  = stripslashes( $str );

	 //remove urls
	  $str = preg_replace('|https?://www\.[a-z\.0-9]+|i', ' ',  $str);

	  //remove emails
	   $str = preg_replace('/([a-z0-9\.\_\-]+\@[a-z0-9\.\_\-]+\.[a-z0-9\.]+)/i', ' ',  $str);

	 //echo "<br><br>";
	 //remove words with 't 's 'n example , was'nt,don't ......
 $str = preg_replace("/\b(\w+[’`\"']+[a-z]+)\b/i", " ",$str);

	// echo "<br><br>";

	//remove all non english characters
	$str = preg_replace('/[^\00-\255]+/u', ' ', $str);



	//first of all lets now listen to the options
	if($optionsArray["ignore_quoted_strings"] == true){

	//lets make sure we clean str
	$str = str_replace(["\" ","' "],["\"","'"],$str);

	//lets remove all quoted strings
	$str = preg_replace('/"([^"]+)"/i', " ", $str);

	//again lets replace quotes with single quotes
	 $str = preg_replace("/'([^']+)'/", " ", $str);

	}//end remove quoted words

	//ignore capitalised words
	if($optionsArray["ignore_cap_words"] == true){
	 $str = preg_replace("/\b([A-Z]+)\b/", " ", $str);
	}//end ignore cap words


	//lets remove hyphenated words
	$str = preg_replace("/\b([a-zA-Z0-9\-]+\-{1}[a-zA-Z0-9\-]+)\b/i"," ",$str);



     //lets get the list of stop words
	  $stop_words = require realpath(dirname(__DIR__))."/snippets/stopwords.php";


	//if ignore  stop words is true
	if($optionsArray["ignore_stop_words"] == true){

	  //pattern  [,\.\s]+
	  $pattern = "\b(".implode("|",$stop_words).")\b";

	  //replace stop words
	  $str = preg_replace("/$pattern/i"," ",$str);
	}//end if ignore stop words is true

	//ignore words or words to ignore
	$ignore_words = $optionsArray["ignore_words"].",".$defaultWordsToIgnore;

	//lets remove words to remove
	if(!empty($ignore_words)){
	   $ignore_words = trim($ignore_words);

	   $ignore_words = str_replace([","," ,"],"|",$ignore_words );

	   //now replace the data
	   $ignore_words_pattern = "/\b($ignore_words)\b/i";

	   //remove words
	 $str = preg_replace($ignore_words_pattern," ",$str);

	}//end if we have words to remove


	//remove all symbols
   $str = preg_replace("/\b([^a-zA-Z0-9\-]+)\b/i"," ",$str);


	//lets get the part of speech of words
	$posTagsArray = self::simple_pos_tagger($str);

	//var_dump($posTagsArray);

   // var_dump($posTagsArray); die();
  //The kind of part of speech we want to work with
	$required_pos_tags = ["noun"
	                                                 =>["NN","NNS","NN$","NNS$","NP","NP$","PN","NR"],

								      "adjective" => ["JJ","JJR","JJS","JJT"],

									  "adverb" => ["RB","RN"],

									  "verb" => ["VB","VBD","VBG","VBN"]
									];

	//ican boogie php lang stuff
	$inflector = Inflector::get('en');


	$strReplacementData = [];

	//lets loop and remove some data
	foreach($posTagsArray AS $posDataArray){

		 $strData =  trim($posDataArray["token"]);


		if(in_array($strData,$stop_words) || is_numeric($strData) || $strData == null || strlen($strData) < 3){
		continue;
		}

		//echo "--";
		$pos_tag =  trim($posDataArray["tag"]);

		//echo "<br>";

			//detect if its a plural
			$isPlural = false;

		     //lets get
			$tense  = null;

			//adjective type
			$adjective_type = null;

		if(in_array($pos_tag,$required_pos_tags["noun"])){

			$tag_key = "n";

			$pos = "noun";
	   //echo $strData; echo "- <br>";

		if($pos_tag=="NNS" || $pos_tag == "NNS$"){
				$isPlural = true;
			}//end if its plural

		}//end noun

		 //adjective
		 elseif(in_array($pos_tag,$required_pos_tags["adjective"])){

		 	$tag_key = "a";

			$pos = "adjective";

			//lets get adjective types
			if($pos_tag == "JJR"){

			}//end if

		 }//end if adjective

		 	 elseif(in_array($pos_tag,$required_pos_tags["adverb"])){

		 	$tag_key = "r";

			$pos = "adverb";

		 }//end if adverb

		  //adjective
		 elseif(in_array($pos_tag,$required_pos_tags["verb"])){

		    $tag_key = "v";

			$pos = "verb";

			if($pos_tag == "VBD"){
				$tense = "past";
			}
		   elseif($pos_tag ==  "VBG"){
			   $tense = "present_participle";
		    }//end verb
		   elseif($pos_tag ==  "VBN"){
		    $tense = "past_participle";
		    }//end tense detect

		//skip word if no pos tag found or not supported
		 } else{
			 //echo "$strData<br>";
			 continue;
		}//end


	 //echo "$strData - $pos - Is plural : $isPlural - Is past tense : $tense ";
		//echo "<br>";
	//first try from razzy thes
	 $synCallback = lang::razzy_therausus($strData,$tag_key);


	//if not error , we get synData  from razzyTherausus
	if($synCallback["alert_type"] != "success"){
	//get synonym
	$synCallback = self::getSynonym($strData, $tag_key,true,$pos_tag);

	//check if error or contains spaces , then skip
	if($synCallback["alert_type"] =="error" || stristr(@$synCallback["synonymData"], " ")){
	continue;
	}//end if

	}//end if empty from razzyTherausus use wordnet


	//synonym data
	$synonymData = trim(@$synCallback["synonymData"]);
		// stristr($synonymData," ")==true ||  && stristr($synonymData,",")  == false

	if(empty($synonymData) || stristr($synonymData,"digit")){
	continue;
	}

	//if the original word was an adverb ,lets check and replace it, maybe an adjective was returned
	if($pos == "adverb"){

		$newSynonymData = self::adjectiveToAdverb($synonymData);

		//if the same adjective is same as the adverb, we will check for the ending
		if(($newSynonymData == $synonymData) && preg_match("/(ly)$/i",$strData)){
			$newSynonymData =  $newSynonymData."ly";
		  }

		 $synonymData =  $newSynonymData;
		}//end if


	if($pos == "noun"){
	//if its plural
	if($isPlural == 1){
	  $synonymData = $inflector->pluralize( $synonymData);
	}else{
	  $synonymData = $inflector->singularize($synonymData);
	}
	}//end if noun


		//$pos_tagger = self::simple_pos_tagger($synonymData);

		//var_dump

		// echo "$strData - $pos - $synonymData";
		//echo "<br>";
		if($pos == "verb"){

		if($tense != null){

		$newSynonymData = lang::convert_tense($synonymData,$tense);

		//echo $tense." - $synonymData - $newSynonymData";

		if(!empty($newSynonymData)){
			$synonymData = $newSynonymData;
			}//end if

		}//end if past tense
		}//end if verb work on tense

				// echo "$strData - $pos - $synonymData <br>";
	    //detect if we need highlighting
		//if($optionsArray["highlight_changes"]==true){
		//$wordToReplace = "<span style='background:yellow;'>$synonymData</span>";
		//}else{
			$wordToReplace =  $synonymData;
		//}//end high light detect

		$strReplacementData["/\b($strData)\b/i"] = $wordToReplace;
   }//en dloop

	//var_dump($strReplacementData);

	$spinnedWord = preg_replace(array_keys($strReplacementData),array_values($strReplacementData),$original_str);

  //  echo "\r\n\r\n\r\n Spinned Word : \r\n\r\n";
    $spinnedWord = lang::fix_grammer($spinnedWord);
	// echo "\r\n\r\n\r\n ";

	return $spinnedWord;
	}//end l


	}//end class
