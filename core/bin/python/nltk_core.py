import os
import nltk
import sys
import json


def init_nltk():
    cur_dir = os.path.dirname(os.path.abspath(__file__));

    #We need data path 
    nltk_data_path = cur_dir+"/nltk_data";

    #Change the path of the nltk data 
    nltk.data.path.append(nltk_data_path);

    return nltk_data_path

def send_alert(alert_type,alert_msg,extra_data_dict):
    """
    For Sending Alerts to other modules based on fail or success 
    """
    alert_data = {"alert_type":alert_type,"alert_msg":alert_msg}
	
    if(len(extra_data_dict) > 0):
	    alert_data.update(extra_data_dict)

    #return results
    return alert_data
