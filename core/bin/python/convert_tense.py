import sys;
import en;
import json;

str = sys.argv[1];

convert_to =  sys.argv[2];

new_word = "";

if convert_to == "past":
   new_word = en.verb.past(str);
elif convert_to == "present_participle":
   new_word = en.verb.present_participle(str)
elif  convert_to == "past_participle":
   new_word = en.verb.past_participle(str)
	
print new_word;

