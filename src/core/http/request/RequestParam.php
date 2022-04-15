<?php
namespace core\http\request;

class RequestParam {

    public mixed $original;
    public array $errors;

    function __construct(
        public string $name,
        public mixed  $value = null,
        public bool   $throw = false,
    ) {
        $this->original = $value;
    }

    function required() {
        if (!isset($this->value)) return $this->error('required');
        return $this;
    }

    function int(bool $nullTo0 = false) {
        if ($nullTo0) $this->value ??= 0;
        if (!is_numeric($this->value)) return $this->error('int');
        $this->value = (int)$this->value;
        return $this;
    }

    function float(bool $nullTo0 = false) {
        if ($nullTo0) $this->value ??= 0.;
        if (!is_numeric($this->value)) return $this->error('float');
        $this->value = (float)$this->value;
        return $this;
    }

    function clamp(int|float $min, int|float $max) {
        if (!is_int($this->value) && !is_float($this->value)) return $this->error('clamp');
        $this->value = max($min, min($max, $this->value));
        return $this;
    }

    function string() {
        if (!is_string($this->value)) return $this->error('string');
        return $this;
    }

    function throw() {
        $this->throw = true;
        if ($this->errors) {
            $msg = $this->errors[array_key_last($this->errors)];
            throw new \RuntimeException("$this->name.error.$msg");
        }
        return $this;
    }

    protected function error(string $msg) {
        $this->errors[] = $msg;
        $this->value = null;
        if ($this->throw) $this->throw();
        return $this;
    }

}
