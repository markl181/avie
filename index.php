<?php
$pageTitle = 'Avie - Home';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__),4);

$today = get_date('Y-m-d');


Bootstrap4::heading("Latest good things",2);

Bootstrap4::heading("Added an auto-update so that new foods will be searchable within an hour",4);
Bootstrap4::heading("Added an almost-auto-update to pick up new recipes somewhat frequently...",4);


include 'footer.php';

?>
