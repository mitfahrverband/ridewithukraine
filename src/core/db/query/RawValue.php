<?php
namespace core\db\query;

interface RawValue {

    function getSql(): string;

}
