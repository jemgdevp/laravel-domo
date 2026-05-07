<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Components;

class ChoiceFieldComponent
{
    /**
     * @param  array<string, string>  $choices
     */
    public function __construct(protected array $choices)
    {
    }

    protected int $selected = 0;

    public function handle(object $event): ComponentState
    {
        $code = $this->extractCode($event);
        if ($code === 'up') {
            $this->selected = max(0, $this->selected - 1);

            return ComponentState::Handled;
        }

        if ($code === 'down') {
            $this->selected = min(count($this->choices) - 1, $this->selected + 1);

            return ComponentState::Handled;
        }

        if ($code === 'enter') {
            return ComponentState::Submitted;
        }

        if (isset($event->char)) {
            return ComponentState::Handled;
        }

        return ComponentState::Ignored;
    }

    public function selectedKey(): string
    {
        return array_keys($this->choices)[$this->selected] ?? array_key_first($this->choices) ?? '';
    }

    /**
     * @return array<string>
     */
    public function lines(): array
    {
        $lines = [];
        $index = 0;

        foreach ($this->choices as $label) {
            $prefix = $index === $this->selected ? '› ' : '  ';
            $lines[] = $prefix.$label;
            $index++;
        }

        return $lines;
    }

    protected function extractCode(object $event): mixed
    {
        return is_string($event->code ?? null) ? strtolower((string) $event->code) : null;
    }
}

