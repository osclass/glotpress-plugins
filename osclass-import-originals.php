<?php

    system("cd Osclass; git checkout develop; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (checkout)"; exit; };

    system("cd Osclass; git reset --hard origin/develop; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (git reset)"; exit; };

    system("cd Osclass; git pull origin develop; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (git pull)"; exit; };

//system("cd i18n-tools; php makepot.php all ../Osclass; echo $?", $rv);
//if($rv!=0) { echo "CRON FAILED (makepot)"; exit; };

    system("cd i18n-tools; php makepot.php core ../Osclass tmp/core.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (makepot)"; exit; };

    system("cd i18n-tools; php makepot.php messages ../Osclass tmp/messages.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (makepot)"; exit; };

    system("cd i18n-tools; php makepot.php theme ../Osclass/oc-content/themes/bender tmp/theme.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (makepot)"; exit; };

    system("cd i18n-tools; php makepot.php mail ../Osclass/oc-content/languages/en_US tmp/mail.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (makepot)"; exit; };

    system("php import-originals.php -p osclass/dev/core -f i18n-tools/tmp/core.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (import core)"; exit; };

    system("php import-originals.php -p osclass/dev/flash-messages -f i18n-tools/tmp/messages.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (import flash-messages)"; exit; };

    system("php import-originals.php -p osclass/dev/email-templates -f i18n-tools/tmp/mail.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (import email-templates)"; exit; };

    system("php import-originals.php -p osclass/dev/bender -f i18n-tools/tmp/theme.po; echo $?", $rv);
    if($rv!=0) { echo "CRON FAILED (import modern)"; exit; };

?>
