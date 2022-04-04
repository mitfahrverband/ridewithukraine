<?php
namespace page;

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
        $date = isset($item->departDate) ? (new \DateTime($item->departDate))->format('d.m.') : 'DATE';
        ?>
        <a class="item" href="<?= $item->deeplink ?? '' ?>" target='_blank'>
            <div>
                <p><?= $date ?></p>
                <p><?= $item->departTime ?? 'TIME' ?></p>
            </div>
            <ul>
                <?php
                foreach ($item->stops ?? [] as $stop) {
                    ?>
                    <li><?= $stop->address ?? '' ?></li>
                    <?php
                }
                ?>
            </ul>
            <img class="logo" src="<?= self::getLogoUrl($item) ?>">
        </a>
        <?php
    }

    static function getLogoUrl(object $item) {
        if (str_contains($item->deeplink ?? '', 'mifaz'))
            return "https://ride2go.com/img/mifaz_logo.png";
        return "https://ride2go.com/img/r2g_favicon.png";
    }

}
