<?php
require "include/bittorrent.php";
dbconn();
  
stdhead(PROJECTNAME);
print ("<h1>".PROJECTNAME."</h1>");
begin_main_frame();
$smarty->display(MTPTTEMPLATES."/aboutmtpt.html");
end_main_frame();
stdfoot();  
?>
