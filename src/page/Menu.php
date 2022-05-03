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
            <a href="/press.php"><p><?= Label::get('press.title') ?></p></a>
            <div class="social">
                <a href="mailto:contact@ridewithukraine.eu?subject=RideWithUkraine: "
                ><img src="/img/social/socialmedia-contact-icons00005.svg"></a>
                <a href="https://www.instagram.com/ridewithukraine/" target="_blank"
                ><img src="/img/social/socialmedia-contact-icons00001.svg"></a>
                <a href="https://www.twitter.com/ridewithukraine" target="_blank"
                ><img src="/img/social/socialmedia-contact-icons00002.svg"></a>
            </div>
        </div>
        <?php
    }

}
