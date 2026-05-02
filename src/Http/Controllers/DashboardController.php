<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;

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
            $tableName = is_array($table) ? reset($table) : $table;
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
     *
     * @return JsonResponse
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:schema,models,relationships',
            'target' => 'nullable|string',
        ]);

        // TODO: Implement AI analysis

        return response()->json([
            'success' => true,
            'message' => 'Analysis complete',
        ]);
    }
}
