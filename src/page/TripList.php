<?php
namespace page;

use core\http\Url;
use core\language\Language;

class TripList {

    static function render(array $items) {
        ?>
        <div class="trip-list">
            <?php
            foreach ($items as $item) self::renderItem($item);
            ?>
        </div>
        <?php
    }

    static function renderItem(object $item) {
        if (isset($item->depart)) {
            $date = $item->depart;
            $time = $date?->format('H:i');
        } else {
            $date = isset($item->departDate) ? (new \DateTime($item->departDate)) : null;
            $time = $item->departTime ?? null;
        }
        $date = $date?->format('d.m.');
        $url = $item->url ?? $item->deeplink ?? '';
        $url = Url::addParams($url, lang: Language::get());
        ?>
        <a class="item" href="<?= $url ?>" target='_blank'>
            <div>
                <p><?= $date ?? 'DATE' ?></p>
                <p><?= $time ?? 'TIME' ?></p>
            </div>
            <ul>
                <?php
                foreach ($item->stops ?? [] as $stop) {
                    ?>
                    <li><?= $stop->city ?? $stop->address ?? '' ?></li>
                    <?php
                }
                ?>
            </ul>
            <img class="logo" src="<?= self::getLogoUrl($item) ?>">
        </a>
        <?php
    }

    static function getLogoUrl(object $item): string {
        $url = $item->url ?? $item->deeplink ?? null;
        if (!$url) return '';
        if (str_contains($url, 'mifaz'))
            return "/img/mifaz.png";
        if (str_contains($url, 'besser'))
            return "/img/bessermitfahren_logo_sm.png";
        return "/img/icon_ride2go_green_small.jpg";
    }

}
