<?php
$date = date('d-m-Y_His');
system("mysqldump --host=clrhidffqeadmin.mysql.db --user=clrhidffqeadmin --password=Clrhidf91 clrhidffqeadmin > clrh_idf_".$date.".sql");