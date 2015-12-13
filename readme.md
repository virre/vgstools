## VGS Tools 

  ### Introduction
   Veganistan is an excellent resource for Vegans in Stockholm and to some extent the rest of Sweden. However the search function is weak as it is based on the idea that post-towns are the local seperator which is not a well working idea as just Stockholm municipiality have a very high load of them. This in combination with my preference for CLI tools made me start writeing this tool libary. I am now realesing the first version which is an alpha and only contains the possiblity to fetch new data from the 
   provided xml files. This should not create to high a load on the server when done but still show a start of how to work aroudn the post-town/postal-code problems. There is a lot of things still todo. 

 ### Install
   To use this toolset you will need to include the relevant class into your own code. At this town the relevant part is news.php as it contain the working code. For an example of how you can do this examples.php is provided. 

 ### Restrictions
   I only gone through the post-towns for Stockholm, and I have not made a function to select out which one to use and etc. I wanted to share the code quickly instead of when all functionalities where done. This is for the same reason this is on GitHub and in GPLv3. Also please make sure to limit your requests to not crash the servers of veganistan as they repeatdly have proven to not handle to much automated traffic. If you can, cache your calls. 

 ### Contact
    I am Virre at virre.annergard@gmail.com, e-mail is probably the best way to reach me. I can from time to time be seen on IRC: freenode as linwendil, but this is rather uncommon. 

 ### License
    This software is Free software GPLv3, see gpl.txt. The content of Veganistan is however covered by veganistans copyright restrictions. This software only fetches data from it and have no intention to break any copyright infringement on veganistans content. 