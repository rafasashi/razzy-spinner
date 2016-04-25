import nltk_core
import sys
import json
from nltk.stem.wordnet import WordNetLemmatizer
import pprint

#Init NLTK
nltk_core.init_nltk()

#Get the str we want to lemmatize 
str_to_lemma = sys.argv[1];

#pos  - part of speech 
str_pos = sys.argv[2];

wn_lemma  = WordNetLemmatizer();

new_word = wn_lemma.lemmatize(str_to_lemma,str_pos);

if(len(new_word) == 0):
    alert_data = nltk_core.send_alert("erro","word could not be lemmatized")
else:
    utf8_word = new_word.encode("utf8")
    alert_data = nltk_core.send_alert("success","",{"lemmatize_word":utf8_word})

#Return Json results 
print json.dumps(alert_data)