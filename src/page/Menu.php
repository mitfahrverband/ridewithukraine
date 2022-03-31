<?php
namespace page;

use core\language\Label;

class Menu {

    static function render() {
        ?>
        <div>
            <a href="/legal-notice.php"><p><?= Label::get('legalNotice.title') ?></p></a>
            <a href="/privacy-policy.php"><p><?= Label::get('privacyPolicy.title') ?></p></a>
        </div>
        <?php
    }

}
