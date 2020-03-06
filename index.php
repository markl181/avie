<?php
$pageTitle = 'Avie - Home';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__),4);

Bootstrap4::heading("Latest good things",2);
Bootstrap4::heading("Mar 2020",4);
Bootstrap4::list_group(["Added recipe counts on every page","Added food counts on the food pages"
]);
Bootstrap4::linebreak(2);
Bootstrap4::heading("Feb 2020",4);
Bootstrap4::list_group(["Fixed a few eensy bugs"
]);
Bootstrap4::linebreak(2);

Bootstrap4::heading("Jan 2020",4);
Bootstrap4::list_group(['Added an almost-auto-update to pick up new recipes somewhat frequently'
    ,'Added recipe search','Added ingredient colour codes'
    ,'Added option to find recipes by min green or max red ingredients'
    ,'Added a column for important tags'
    ,'Added some filters by course'
]);
Bootstrap4::linebreak(2);
Bootstrap4::heading("Dec 2019",4);
Bootstrap4::list_group(['Added an auto-update so that new foods will be searchable within 15 minutes'
,'Added a multi-ingredient search feature'
]);
Bootstrap4::linebreak(2);


include 'footer.php';

?>
