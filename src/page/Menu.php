<?php
namespace page;

use core\language\Label;

class Menu {

    static function render() {
        ?>
        <div class="menu">
            <a href="/legal-notice.php"><p><?= Label::get('legalNotice.title') ?></p></a>
            <a href="/privacy-policy.php"><p><?= Label::get('privacyPolicy.title') ?></p></a>
            <a href="mailto:contact@ridewithukraine.eu"><p><?= Label::get('contact.title') ?></p></a>
            <a href="/faq.php"><p><?= Label::get('faq.title') ?></p></a>
            <a href="/press.php"><p><?= Label::get('press.title') ?></p></a>
        </div>
        <?php
    }

}
