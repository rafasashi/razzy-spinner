import sys;
import en;
import json;

str = sys.argv[1];

tags = en.sentence.tag(str);

print tags;
