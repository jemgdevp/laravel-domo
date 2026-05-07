<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Quit;
use Jemgdevp\Domo\Services\TUI\Contracts\Screen;
use Jemgdevp\Domo\Services\TUI\Widgets\BannerWidgetRenderer;
use Jemgdevp\Domo\Services\TUI\Widgets\KeyHintsWidgetRenderer;
use Jemgdevp\Domo\Services\TUI\Widgets\SchemaTableWidgetRenderer;
use PhpTui\Term\Actions;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Bridge\PhpTerm\PhpTermBackend;
use PhpTui\Tui\DisplayBuilder;

class DomoTuiApp
{
    /**
     * @param  array<class-string, Screen>  $screens
     */
    public function __construct(
        protected array $screens,
        protected string $initialScreen
    ) {
        $this->activeScreen = $this->screens[$initialScreen];
    }

    protected Screen $activeScreen;

    protected bool $colors = true;

    protected bool $simple = false;

    public function withOptions(bool $colors, bool $simple): self
    {
        $this->colors = $colors;
        $this->simple = $simple;

        return $this;
    }

    public function run(): void
    {
        $terminal = Terminal::new();
        $backend = PhpTermBackend::new($terminal);
        $display = DisplayBuilder::default($backend)
            ->addWidgetRenderer(new KeyHintsWidgetRenderer())
            ->addWidgetRenderer(new BannerWidgetRenderer($this->colors))
            ->addWidgetRenderer(new SchemaTableWidgetRenderer($this->simple))
            ->fullscreen()
            ->build();

        $terminal->execute(Actions::cursorHide());
        $terminal->execute(Actions::alternateScreenEnable());
        $terminal->enableRawMode();

        try {
            while (true) {
                while (null !== $event = $terminal->events()->next()) {
                    $action = $this->dispatch($event);
                    if ($action instanceof Quit) {
                        return;
                    }
                }

                $display->draw($this->activeScreen->build());
                usleep(50_000);
            }
        } finally {
            $terminal->disableRawMode();
            $terminal->execute(Actions::alternateScreenDisable());
            $terminal->execute(Actions::cursorShow());
        }
    }

    public function dispatch(object $event): object
    {
        $action = $this->activeScreen->handle($event);

        if ($action instanceof Navigate && isset($this->screens[$action->screen])) {
            $this->activeScreen = $this->screens[$action->screen];
        }

        return $action;
    }
}

