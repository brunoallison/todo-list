<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /** @var TaskRepository  */
    protected $taskRepository;

    /** @var CategoryRepository  */
    protected $categoryRepository;

    /**
     * TaskController constructor.
     * @param TaskRepository $taskRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(TaskRepository $taskRepository, CategoryRepository $categoryRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tasks = $this->taskRepository->all();
        $categories = $this->categoryRepository->getArrayCategoriesOrderlyByName();

        return view('task.index', compact('tasks', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryRepository->getArrayCategoriesOrderlyByName();

        return view('task.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->request->add(['user_id' => auth()->user()->id]);
        $this->taskRepository->create($request->all());

        flash('Tarefa adicionada com sucesso.')->success();
        return redirect()->back();
    }

    public function show(Request $request)
    {
        try {
            $task = $request->id;
            $shown = $this->taskRepository->findWhere(['id' => $task])->first();

            if (!empty($shown->title)){
                return response()->json($shown);
            }

            return response()->json(false);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function update(Request $request)
    {
        try {
            $status = $request->status;
            $updated = $this->taskRepository->update(['status' => $status], $request->id);

            if (!empty($updated->updated_at)){
                return response()->json(true);
            }

            return response()->json($updated);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function destroy(Request $request)
    {
        try {
            $removed = $this->taskRepository->delete($request->id);

            return response()->json($removed);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}