<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\AI\AiDriverFactory;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected SchemaAnalyzerInterface $analyzer
    ) {}

    /**
     * Display dashboard.
     *
     * @return View
     */
    public function index()
    {
        $tables = $this->analyzer->getTables();
        $models = $this->analyzer->getModels();

        return view('domo::dashboard.index', compact('tables', 'models'));
    }

    /**
     * Display schema overview.
     *
     * @return View
     */
    public function schema()
    {
        $tableNames = $this->analyzer->getTables();

        $schemas = [];
        foreach ($tableNames as $table) {
            $tableName = $this->normalizeTableName($table);
            $schemas[$tableName] = $this->analyzer->getTableSchema($tableName);
        }

        return view('domo::dashboard.schema', compact('schemas'));
    }

    /**
     * Display models.
     *
     * @return View
     */
    public function models()
    {
        $models = $this->analyzer->getModels();
        $relationships = [];

        foreach ($models as $model) {
            $relationships[$model] = $this->analyzer->analyzeModelRelationships($model);
        }

        return view('domo::dashboard.models', compact('models', 'relationships'));
    }

    /**
     * Display AI analysis page.
     *
     * @return View
     */
    public function analyzePage()
    {
        $tables = $this->analyzer->getTables();
        $providers = array_keys((array) config('domo.providers', []));
        $activeProvider = (string) config('domo.ai_driver', 'openai');

        return view('domo::dashboard.analyze', compact('tables', 'providers', 'activeProvider'));
    }

    /**
     * Analyze schema with AI.
     *
     * The provider/model may be chosen per request from the dashboard; when
     * omitted the application's default-bound driver is used.
     */
    public function analyze(Request $request, AiDriverFactory $factory): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:schema,models,relationships',
            'target' => 'nullable|string',
            'provider' => ['nullable', 'string', Rule::in($factory->availableProviders())],
            'model' => 'nullable|string',
        ]);

        $type = $validated['type'];
        $target = $validated['target'] ?? null;
        $provider = $validated['provider'] ?? null;
        $model = $validated['model'] ?? null;

        // No explicit selection → use the container-bound default driver
        // (keeps existing bindings/mocks in play); otherwise build the choice.
        $ai = ($provider === null && $model === null)
            ? app(AiDriverInterface::class)
            : $factory->make($provider, $model);

        try {
            $result = $this->runAnalysis($ai, $type, $target);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'type' => $type,
            'target' => $target,
            'provider' => $provider ?? (string) config('domo.ai_driver', 'openai'),
            'model' => $model,
            'result' => $result,
        ]);
    }

    /**
     * Gather the relevant schema data and delegate to the AI driver.
     *
     * @return array<string, mixed>
     */
    protected function runAnalysis(AiDriverInterface $ai, string $type, ?string $target): array
    {
        if ($type === 'relationships') {
            $models = $this->collectModels($target);

            return $ai->suggestRelationships($models);
        }

        $schema = $type === 'models'
            ? $this->collectModelSchema($target)
            : $this->collectTableSchema($target);

        return $ai->analyzeSchema($schema);
    }

    /**
     * Build the schema payload for one or all tables.
     *
     * @return array<string, mixed>
     */
    protected function collectTableSchema(?string $target): array
    {
        if ($target !== null && $target !== '') {
            $tableName = $this->normalizeTableName($target);

            return [$tableName => $this->analyzer->getTableSchema($tableName)];
        }

        $schema = [];
        foreach ($this->analyzer->getTables() as $table) {
            $tableName = $this->normalizeTableName($table);
            $schema[$tableName] = $this->analyzer->getTableSchema($tableName);
        }

        return $schema;
    }

    /**
     * Build the relationship payload for one or all models.
     *
     * @return array<string, mixed>
     */
    protected function collectModelSchema(?string $target): array
    {
        $schema = [];
        foreach ($this->resolveModels($target) as $model) {
            $schema[$model] = $this->analyzer->analyzeModelRelationships($model);
        }

        return $schema;
    }

    /**
     * Collect model relationship data for the AI driver.
     *
     * @return array<string, mixed>
     */
    protected function collectModels(?string $target): array
    {
        $models = [];
        foreach ($this->resolveModels($target) as $model) {
            $models[$model] = $this->analyzer->analyzeModelRelationships($model);
        }

        return $models;
    }

    /**
     * Resolve the list of models to analyze.
     *
     * @return array<int, string>
     */
    protected function resolveModels(?string $target): array
    {
        if ($target !== null && $target !== '') {
            return [$target];
        }

        return array_map(
            static fn (mixed $model): string => (string) $model,
            $this->analyzer->getModels()
        );
    }

    /**
     * Normalize table names from driver results.
     */
    protected function normalizeTableName(mixed $table): string
    {
        if (is_array($table)) {
            return (string) reset($table);
        }

        if (is_object($table)) {
            $vars = get_object_vars($table);

            return (string) reset($vars);
        }

        return (string) $table;
    }
}
