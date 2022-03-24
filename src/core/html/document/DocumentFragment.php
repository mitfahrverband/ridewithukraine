<?php
namespace core\html\document;

interface DocumentFragment {

    function getOrder(): int;

    function render();

}
