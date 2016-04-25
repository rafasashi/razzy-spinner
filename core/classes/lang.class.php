<?php 

/**
 *@Author : Razak Zakari (Razzbee)
 * @Email : razzsense@gmail.com
 * @Whatsapp : +233244827491
 * @Facebook : facebook.com/razzbee
 * @File : lang.class.php 
 * @Class : lang extending functions 
 * @description : Contains natural language proccessing methods and functions ...
 * @license : see license.txt
 */

	class lang extends functions{
		
	public static function query_python($libName,$paramArray=[]){
		
		$pybin = realpath(dirname(__DIR__))."/bin/python";
		
		array_walk($paramArray,function(&$item,$key){
			$item = escapeshellarg($item);
			});
		
       $param = implode(" ",$paramArray);

		
		$pyFile = "$pybin/$libName.py";
		
		//lets get cache key 
		$cacheKey = $libName.serialize($param);
		
		$fetchCache = cache::fetch($cacheKey);
		
		//if cache exists , lets send it 
		if(!empty($fetchCache)){
			return $fetchCache;
		}//send cache if it exists 
		
		
		//no cache so lets now  fetch from py file 
		$execPy = shell_exec("python $pyFile $param ");
		
		//lets now cache data 
		cache::store($cacheKey,$execPy);
		
		return $execPy;
	}
	
	
	
	//simple pos tagger 
	public static function simple_pos_tagger($str){
	
	$tagger = new pos_tagger();
	
	$tags = $tagger->tag($str);
	
	//lets return it 
	return $tags;
	}//end simple pos tagger 
	
	
	//fetch synonym 
	public static function getSynonym($str,$pos,$single=false){
		
	$str = escapeshellarg($str);
		
	$posArg = "-syns".$pos;
	
	//lets get the synonym commands 
     $synonymCmd = "wn $str -n1 $posArg";
	
	//cache key 
	$cacheKey =  $synonymCmd;
	
	$cacheData = cache::fetch($cacheKey);
	
	//if cache does not exists , lets fetch it from cli 
	if(!empty($cacheData)){
		
		$synonymsData = $cacheData;
		
	}else{
		
	//lets fetch synonym 
	 $synCallBack = shell_exec($synonymCmd);

	 //if empty return error 
	 if(empty( $synCallBack)){
	return self::get_alert("error","No results found");	 
	}//end if empty 
	 
	//we called only first sense , so lets explode it 
   $explodeSense = explode("=>", $synCallBack);
	
    $synonymsData = trim(@$explodeSense[1]);
	
	if(stristr($synonymsData,"=>")){
		return  self::get_alert("error","Malfomed words ");	 
	}
	
	//save data into cache 
	cache::store($cacheData,$synonymsData);
	
	}//eend if cache does exists 
	
	//echo "<br>";
	//if single we send the first one 
	if($single == true){
	
	//explode 
	$explodeSynonyms = explode(",",$synonymsData);
	
	//shuffle($explodeSynonyms);
	
	foreach($explodeSynonyms AS $synonymsData){

	if(stristr($synonymsData,"Synonyms/Hypernyms")==true){
	
	//delete it 
	$synonymsData = "";
	
	continue;
	 }//end id 
	 else{
		 break;
	 }
	 	
	}//end loop
	
	}
	
	$synonymsData  = trim($synonymsData );
	
	if(stristr($synonymsData,"(")){
	
	//lets clean some shit 
   $synonymsData = explode("(",$synonymsData);
   
	$synonymsData = trim($synonymsData[0]);
	}//end if 
	
	$whatIHate = ["property","properties","devices","intrument","intrumentality","instruments","urgencies","urgency","happening","happenings","activity","activities","travel","travelled","travelling","change","changed","physicist","physicists","physic","abstraction","abstract","artifact","artifacts"];
	
	if(in_array($synonymsData,$whatIHate)){
	return self::get_alert("error","");	
	}
	
	return self::get_alert("success","",["synonymData"=>$synonymsData]);	 
	}//end getSynonym 
	
	
    ///wordnet lemmatize \
	public static function wn_lemma($word,$pos){
    

    $command = "wn $word -syns$pos";	
		
	$fetchLemmaCallback = shell_exec($command);
	
    if(empty($fetchLemmaCallback)){
		return $word;
	}

	//split data 
	$splitCallback = explode(PHP_EOL,$fetchLemmaCallback);
	
	//loop to get data 
	foreach($splitCallback AS $lineData){
	
	if(empty($lineData)){
		continue;
	}	
	
	//if we have sense of lemmatized name , then thats what we wants
	if(stristr($lineData," senses of ") || stristr($lineData," sense of ")){
	    $lemmaPhrash = $lineData;
		break;
	}//end if 
	
	}//end if 
	
	$lemmaPhrash = trim($lemmaPhrash);
	
	///lematized word 
	$lemmatisedWordLine = explode(" ",$lemmaPhrash);
	
	//get the last word 
	 $lemmatizedWord = end($lemmatisedWordLine);
	 
	$lemmatizedWord = trim($lemmatizedWord);
	 
	 if(empty($lemmatizedWord)){
		 return $word;
	 }
	 
    return  $lemmatizedWord;

	}//end wn lemma 
	
	//razzy therausus 
	public static function razzy_therausus($word,$pos=""){
	
    $word = strtolower($word);
		
	//lets lemmatize the word 
     $lemmatizeWord = self::wn_lemma($word,$pos);

		//lets check if we had what we want 
		if(!empty($lemmatizeWord)){
			$word = $lemmatizeWord;
		}
		
		//synonym of our  word 
		$synoWord = "";
		
		$db = realpath(dirname(__DIR__))."/libs/razzy/therausus.php";
		
		$db_data = include $db;
		
		$word = strtolower($word);
	
	  ///lets filter 
	  $filteredSynWordsArray = array_filter($db_data, function($synonymsArray) use($word) {
		    
			//filter if the word is in the array 
		     if(in_array($word,$synonymsArray)){
				 return  $synonymsArray;
			 }//end if 
			 
	  });//end array filter 
	     
	  $filteredArraySize = sizeof($filteredSynWordsArray);
	  
	 //if the returned array is empty, we return error 
	if($filteredArraySize==0){
	return  functions::get_alert("error","synonym word not found");	
	}//end if 
	  
	  //lets reindex the array data 
	  $reindexedFilteredArray = array_values($filteredSynWordsArray);
	  
	  //now select the first array data 
	  $synonymArrayData =  $reindexedFilteredArray[0];
	  
	     //lets remove the word from the array cos we dont want it to show 
		$word_key = array_search($word,$synonymArrayData);
				
		//remove the word 
		unset($synonymArrayData[$word_key]);	
	  
	  //reindex the array data 
	  $synonymArrayData = array_values($synonymArrayData);
	  
	  if(sizeof($synonymArrayData) > 1){
	 //shuffle the array  to send a random word 
	 shuffle( $synonymArrayData);
      }//end if 
	  
	 //select first index 
	 $synWord =  $synonymArrayData[0];

	 return  functions::get_alert("success","word_found",["synonymData"=>$synWord]);	
	}//end 
	
	
	//convert tense 
	public static function convert_tense($str,$to){
     
    $convertedStr = self::query_python("convert_tense",[$str,$to]);
	
	return $convertedStr;
	}//end 
	
	
	/*
		//convert tense 
	public static function convert_tense($str,$to){
     echo $str; echo "<br>";

	 $str = strtolower($str);
	 
	 //last three letters 
	 $last3LettersArray = str_split(substr($str,-3),1);
	 
	 //letters from 3 three  letters
	 $firstLetter =  $last3LettersArray[0];
	 $secondLetter =  $last3LettersArray[1];
	 $lastLetter =  $last3LettersArray[2];
	 
	 
	 //vowels 
	 $vowels =  ["a","e","i","o","u"];
	 
	 //output array 
	 $outputDataArray = [];
	 
	 //start match on words ending with ee , ye , and oe 
	 if(preg_match("/(ee|ye|oe)$/i",$str)){
		 
		//past tense 
        $outputDataArray["past"] = $str."d";
		
		//present participle 
		 $outputDataArray["present_particle"] = $str."ing";
		 
		 //present is same as word 
		  $outputDataArray["present"] = $str;
	 }//end match on words ending with ee , ye , and oe 
	 
	 
	 //a word who's last but one letter is a vowel and ends with l ,note array starts counting form 0
	elseif(in_array($secondLetter,$vowels) && $lastLetter == "l"){
		
		//past tense 
        $outputDataArray["past"] = $str."led";
		
		//present participle 
		 $outputDataArray["present_particle"] = $str."ling";
		 
		 //present is same as word 
		  $outputDataArray["present"] = $str;
	 }//end if a word last but 1 letter is a vowel and last is l 
	 
	 
	 //if the last but 3 character is not a vowel ,the last but 2 character is a vowel and the last character is a consonant 
	 //again last letter must not be l 
	 elseif((in_array( $firstLetter,$vowels)==false && in_array($secondLetter,$vowels)==true) && (in_array($lastLetter,$vowels)==false && $lastLetter != "l")){
		 
		 //past tense 
        $outputDataArray["past"] = $str.$lastLetter."ed";
		
		//present participle 
		 $outputDataArray["present_particle"] = $str.$lastLetter."ing";
		 
		 //present is same as word 
		  $outputDataArray["present"] = $str;
		  
	 }//end second character is a vowel and the last character is a consonant 
	 
	 var_dump( $outputDataArray);
	 
	 
	 die();
	return $convertedStr;
	}//end 
	*/
	
	public static function get_pos_tags($str){
	  
	$tags = self::query_python("pos_tagger",[$str]);
	
	if(empty($tags)){
	return false;	
	}
	
	$explodeTags = explode("/",$tags);
	
	return trim($explodeTags[1]);
	}//end 
	
	
	//numeric to spoken 
	public static function numericToSpoken($num){
		
		  
	$spokenNumeric = self::query_python("numeric_to_spoken",[$num]);
	
	if(empty($tags)){
	return self::system_alert("error","numeric to spoken error");	
	}
	
	return self::system_alert("success","",["synonymData"=>$spokenNumeric]);	
	}//end numeric to spoken
	
	
	
	//adjective to adverb 
	public static function adjectiveToAdverb($str){
	
	$str = strtolower($str);
	
	$exception = ["early","fast","hard","high","late","near","straight","wrong"];
	
	if(in_array($str,$exception)){
		return $str;
	}//end if its an exception 
	
	//lets detect the ending of the words 
    if(preg_match("/(y)$/i",$str)){
	$adverb = preg_match("/(y)$/i","ily",$str);
	}
	
	elseif(preg_match("/(able|ible|le)$/i",$str)){
	$adverb = preg_match("/(e)$/i","y",$str);
	}//end 
	
	elseif(preg_match("/(ic)$/i",$str) && $str !="public"){
	$adverb = preg_match("/(e)$/i","ally",$str);
	}
	
	elseif($str =="public"){
	$adverb = "publicly";
	}
	
	else{
	$adverb = $str;
	}
	
	return $adverb;
	
	}//end method 
	
	
	//keywords extractor 
	public static function extract_keywords($str,$optionsArray=null){
		
	//convert to lower case 
	$str = strtolower($str);
	
	//lets remove tags and slashes 
	 $str = strip_tags($str);
		
	//strip slashes
	$str = stripslashes($str);
	
	//min keyword length 
	$keywordMinLen = !empty($optionsArray["min_length"])?$optionsArray["min_length"] : 3;
	
	//lets now replace stop words 
	 //lets get the list of stop words 
	  $stop_words = require realpath(dirname(__DIR__))."/snippets/stopwords.php";
	 
	  //pattern  [,\.\s]+, we will remove numbers too 
	  $pattern = "\b(".implode("|",$stop_words).")\b";
	  
	  //replace stop words 
	  $str = preg_replace("/$pattern/i"," ",$str);

	  //let replace numbers 
	 $str = preg_replace("/\b([^a-zA-Z]+)\b/i"," ",$str);
    
	$str = trim($str);
	
	//explode 
	$strArrayData = explode(" ",$str);
	
	//lets avoid duplicates 
	$strArrayData = array_unique($strArrayData);
	
	//new strDataArray
	$filteredStrDataArray = [];
	
	///finally , lets loop to get some filter out 
	foreach($strArrayData AS $strData){
		
	//now filter 
     if(strlen($strData) < $keywordMinLen){
		continue; 
	 }//end if 
	 
	 //repack 
	 $filteredStrDataArray[] = $strData;
	}//end loop 
	
	return $filteredStrDataArray;
	}//end method 
	
	
	//grammer fixture 
	////////////////Fixing Gramaphemes
	public static function fix_grammer($str){
		
	//lets fetch all phrashes 
  $totalMatched = preg_match_all("/\ban?\b\s+\b\w+\b/i",$str, $matchedPhrases);
	
	//so if the matched word is bigger, then lets rock 
	if($totalMatched == 0){
		return $str;
	}//end if 
	
	//var_dump($matchedPhrases);
	
	//list of vowels 
	$vowelsArrayData = ["a","e","i","o","u"];
	

	//exception
	$exceptions = [
	                  "a"=> [],
					  "an" => ["honour","honor","unfortunate","unfortunatly"],
	                     ];
						 
	
	
	//we need the first set of matched phrases 
	$phrases = $matchedPhrases[0];
	
	//init str to replace 
	$str_to_replace = [];
	
	//loop to get the data 
	foreach($phrases AS $phrase){
	

    //lets now detect if it starts with a or an 
    $explode_phrase = explode(" ",$phrase);
	
	//the a or an before the space and word 
	$phrase_prefix =  $explode_phrase[0];
	
	$word =  strtolower($explode_phrase[1]);
	
	
	 $word_first_char = strtolower(trim(substr($word,0,1)));
	//echo "--";

      //lets check if the word is in the array 
	  if(in_array("$word_first_char",$vowelsArrayData)){
		 $phrase_prefix = "an";
	  }else{
		  $phrase_prefix = "a";
	  }
	  
	 // echo " $phrase_prefix $word <br>";
	  
	//lets fix exceptions 
	if(in_array($word,$exceptions["an"])){
		 $phrase_prefix = "an";
	}elseif(in_array($word,$exceptions["a"])){
		 $phrase_prefix = "a";
	}///end fix exceptions 
	
	//other exceptions )
	//if a word starts with uni, then it must be a
	if(preg_match("/^(uni).+/i",$word)){
		 $phrase_prefix = "a";
	}//end if 
	
	
	$fixed_phrase = "$phrase_prefix $word";
	
	$str_to_replace[$phrase] = $fixed_phrase;
	}//end phrase loop 
	
	//now lets replace them 
	$new_str = str_replace(array_keys($str_to_replace),array_values($str_to_replace),$str);
	
	return $new_str;
	}//end fix grammer 

	
	}//end class