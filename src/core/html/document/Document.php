<?php
namespace core\html\document;

use core\language\{Label, Language};

class Document {

    public static bool $webAppCapable = true;
    protected static ?string $name = null;
    protected static ?string $title = null;
    protected static ?string $description = null;
    protected static ?string $keywords = null;
    protected static array $head = [];

    static function setName(string $name) {
        self::$name = $name;
    }

    static function getTitle(): string {
        if (isset(self::$title)) return self::$title;
        if (isset(self::$name)) $title = Label::getOrDefault(self::$name . '.title', null);
        return $title ?? Label::getOrDefault('document.title', '');
    }

    static function setTitle(string $title) {
        self::$title = $title;
    }

    static function getDescription(): string {
        if (isset(self::$description)) return self::$description;
        if (isset(self::$name)) $description = Label::getOrDefault(self::$name . '.description', null);
        return $description ?? Label::getOrDefault('document.description', '');
    }

    static function setDescription(string $description) {
        self::$description = $description;
    }

    static function getKeywords(): string {
        if (isset(self::$keywords)) return self::$keywords;
        if (isset(self::$name)) $keywords = Label::getOrDefault(self::$name . '.keywords', null);
        return $keywords ?? Label::getOrDefault('document.keywords', '');
    }

    static function setKeywords(string $keywords) {
        self::$keywords = $keywords;
    }

    static function add(DocumentFragment $documentFragment) {
        self::addHead(fn() => $documentFragment->render(), $documentFragment->getOrder());
    }

    static function addPreload(string $href, string $as = null, int $order = 10) {
        // $as = font, script, style
        if (!$as) {
            if (preg_match('/(\.ttf|\.woff2?)$/', $href)) {
                $as = 'font';
            } elseif (str_ends_with('.css', $href)) {
                $as = 'script';
            } elseif (str_ends_with('.js', $href)) {
                $as = 'script';
            }
        }
        self::addHead("<link rel=\"preload\" href=\"$href\" as=\"$as\" crossorigin=\"anonymous\">", $order);
    }

    static function addMeta(string $name, string $content, $order = 20) {
        self::addHead("<meta name=\"$name\" content=\"$content\"/>", $order);
    }

    static function addIcon($type, $href, $order = 50) {
        self::addHead("<link rel=\"icon\" type=\"$type\" href=\"$href\">", $order);
    }

    static function addStyle(string|callable $style, $order = 60) {
        $head = function () use ($style) {
            echo '<style>';
            echo is_string($style) ? $style : $style();
            echo '</style>';
        };
        self::addHead($head, $order);
    }

    static function addStylesheet($href, $order = 60) {
        self::addHead("<link rel=\"stylesheet\" href=\"$href\"/>", $order);
    }

    static function addScript(string $script, bool $module = false, int $order = 80) {
        $module = $module ? 'type="module"' : '';
        self::addHead("<script $module>$script</script>", $order);
    }

    static function addScriptFile(string $src, bool $defer = false, $order = 80) {
        $defer = $defer ? ' defer' : '';
        self::addHead("<script$defer src=\"$src\"></script>", $order);
    }

    static function addHead($head, $priority = 100) {
        self::$head[$priority][] = $head;
    }

    static function render(callable $renderFunction) {
        ob_start();
        $renderFunction();
        $body = ob_get_clean();
        $description = static::getDescription();
        $keywords = static::getKeywords();
        ?>
        <!DOCTYPE html>
        <html lang="<?= Language::get() ?>">
        <head>
            <title><?= self::getTitle() ?></title>
            <meta charset="utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1"/>
            <?= static::$webAppCapable ? '<meta name="mobile-web-app-capable" content="yes">' : '' ?>
            <?= $description ? "<meta name='description' content='$description'>" : '' ?>
            <?= $keywords ? "<meta name='keywords' content='$keywords'>" : '' ?>
            <?php
            ksort(self::$head);
            foreach (self::$head as $head) {
                foreach ($head as $element) {
                    echo is_string($element) ? $element : $element();
                }
            }
            ?>
        </head>
        <body>
        <?= $body ?>
        </body>
        </html>
        <?php
    }

}
