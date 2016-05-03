# razzy-spinner
Article Rewriter or Spinner Written in PHP &amp; Python 

Requirements:
WordNet 
Python

To install wordnet on centos os :
yum install wordnet 

To install wordnet on ubuntu :
sudo apt-get install wordnet

#Usage:
first include the autoloader 

<?php 
include_once "autoload.php";

$spinner = new spinner();

$article_data = "Hello , I am home , lets start now.";

$spinned_article = $spinner->spin($article_data);

echo $spinned_article;

?>

#See Demos for more options and info 
sponsored by https://playslack.com
