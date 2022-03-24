<?php
namespace core\html\components;

use core\html\document\Document;

class Menu {

    protected array $menu = [];

    function __construct(
        protected ?string $class = null,
        protected ?string $itemClass = null,
    ) {
    }

    function add(string|array $text, string $url = null, Menu $subMenu = null, string $class = null) {
        $menu = &$this->menu;
        if (is_array($text)) {
            foreach ($text as $i => $name) {
                if ($i === array_key_last($text)) {
                    $text = $name;
                    break;
                }
                $entry = &$menu[$name];
                if (!$entry) {
                    $entry = (object)[
                        'subMenu' => new Menu(null, $this->itemClass),
                    ];
                }
                $menu = &$entry->subMenu->menu;
            }

        }
        $menu[$text] = (object)[
            'url' => $url,
            'subMenu' => $subMenu,
            'class' => $class,
        ];
        return $this;
    }

    function render(bool $active = true) {
        $active = $active ? 'active' : '';
        ?>
        <ul class="core-menu <?= $active ?> <?= $this->class ?>">
            <?php
            foreach ($this->menu as $text => $o) {
                $class = join(' ', [$this->itemClass, $o->class ?? '']);
                $c = $class ? "class='$class'" : '';
                if ($o->subMenu) {
                    ?>
                    <li class="core-submenu <?= $class ?>"><?= $text ?></li>
                    <?php
                    $o->subMenu->render(false);
                } elseif ($o->url) {
                    ?>
                    <li><a <?= $c ?> href="<?= $o->url ?>"><?= $text ?></a></li>
                    <?php
                } else {
                    ?>
                    <li <?= $c ?>><?= $text ?></li>
                    <?php
                }
            }
            ?>
        </ul>
        <?php
    }

}

Document::addStyle('
    .core-submenu:not(.active) + ul {
        display: none;
    }
');

Document::addScript('
  document.querySelectorAll(".core-menu").forEach((menu) => {
    let submenus = [];
    menu.querySelectorAll(":scope > .core-submenu").forEach((item) => {
      submenus.push(item);
      item.addEventListener("click", (e) => {
        e.stopPropagation();
        let isOpen = item.classList.contains("active");
        submenus.forEach((item) => item.classList.remove("active"));
        if (!isOpen) {
          item.classList.add("active");              
        }
      });
    })
  });
', module: true);
