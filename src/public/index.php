<?php
use page\Page;

require_once "../core/Autoload.php";

Page::render(function () {
    ?>
    <div class="a col-span-2">Welcome</div>
    <div class="row-span-2 md:space-y-3">
        <div class="a">Search</div>
        <div class="a hidden md:block">Routes</div>
    </div>
    <div class="a col-span-2">
        <?php renderIframe(); ?>
    </div>
    <?php
});

function renderIframe() {
    ?>
    <iframe class="w-full h-0" src="./iframe.php" onload="resizeTripIframe(this)"></iframe>
    <script>
      function resizeTripIframe(iframe) {
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
      }
    </script>
    <?php
}

?>
