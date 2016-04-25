<?php 

/**
 *@Author : Razak Zakari (Razzbee)
 * @Email : razzsense@gmail.com
 * @Whatsapp : +233244827491
 * @Facebook : facebook.com/razzbee
 * @File : proccess_spin.php 
 * @Class : 
 * @description : Proccessor for the frontend 
 * @license : see license.txt
 */

ob_start();
include_once realpath(dirname(__DIR__))."/autoload.php";



$article_data = trim(htmlentities($_POST["article"]));

if(empty($article_data)){
	functions::ajax_alert("error","The Article field cannot be empty");
}


//lets get the ignore words 
$ignore_words = htmlentities(@$_POST["ignore_words"]);

//ignore quoted words 
$ignore_quoted_words = ((int) @$_POST["ignore_quoted_words"] == 1)? true : false;


//ignore ignore_capilised_words
$ignore_capilised_words = ((int) @$_POST["ignore_capilised_words"] == 1)? true : false;

//ignore_braced_words
$ignore_braced_words = ((int) @$_POST["ignore_braced_words"] == 1)? true : false;


$spinner = new spinner();

$options = ["ignore_quoted_strings"=>$ignore_quoted_words,"ignore_cap_words"=>$ignore_capilised_words,"ignore_braced_words"=>$ignore_braced_words,"ignore_words"=>$ignore_words];

//spin the word 
$spinArticle = $spinner->spin($article_data,$options);


functions::ajax_alert("success","",["spinned_article"=>$spinArticle]);
?>