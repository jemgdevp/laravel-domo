<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
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

        return view('domo::dashboard.analyze', compact('tables'));
    }

    /**
     * Analyze schema with AI.
     */
    public function analyze(Request $request, AiDriverInterface $ai): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:schema,models,relationships',
            'target' => 'nullable|string',
        ]);

        $type = $validated['type'];
        $target = $validated['target'] ?? null;

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
