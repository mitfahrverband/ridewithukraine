<?php
use page\Page;

require_once "../core/Autoload.php";

Page::render(function () {
    ?>
    <div class="a col-span-2">Welcome</div>
    <div class="row-span-2 md:space-y-3">
        <div class="a hidden md:block h-48">Menu</div>
        <div class="a">Search</div>
        <div class="a hidden md:block">Routes</div>
    </div>
    <div class="a col-span-2">
        <?php
        require 'trips.php';
        ?>
    </div>
    <?php
});
?>
