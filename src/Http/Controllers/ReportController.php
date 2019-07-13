<?php

namespace Ibarts\Reportsystem\Http\Controllers;

use Ibarts\Reportsystem\Models\Report;
use App\Http\Controllers\Member\MemberBaseController;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Requests\ProjectMembers\StoreProjectMembers;
use App\Http\Requests\Tasks\StoreTask;
use App\Http\Requests\User\UpdateProfile;
use App\Issue;
use App\ModuleSetting;
use App\Notifications\NewTask;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\LanguageSetting;
use App\Project;
use App\ProjectActivity;
use App\ProjectMember;
use App\ProjectTimeLog;
use App\SubTask;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\Traits\ProjectProgress;
use App\User;
use App\Role;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;


class ReportController extends MemberBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.tasks');
        $this->pageIcon = 'ti-layout-list-thumb';
        
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            $this->modules = $this->user->modules;

            $userRole = $this->user->role; // Getting users all roles

            if(count($userRole) > 1){ $roleId = $userRole[1]->role_id; } // if single role assign getting role ID
            else{ $roleId = $userRole[0]->role_id; } // if multiple role assign getting role ID

            // Getting role detail by ID that got above according single or multiple roles assigned.
            $this->userRole = Role::where('id', $roleId)->first();

            
            return $next($request);
        });
        

    }
    
    public function index()
    {
        $projects = User::all();
        return view('reportsystem::index',  $this->data);
    }

    public function create()
    {
        $reports = Report::all();
        $edata = new \stdClass();
        $edata->cdate = Carbon::now();
        $submit = 'Add';
        $this->tasks = Task::where('user_id', Auth::user()->id)->get();
        
        return view('reportsystem::create', ['data' => $this->data, 'edata' => $edata, 'tasks' => $this->tasks]);
    }

    public function store()
    {
        $input = Request::all();
        Report::create($input);
        return redirect()->route('report.create');
    }

    public function edit($id)
    {
        $reports = Report::all();
        $report = $reports->find($id);
        $submit = 'Update';
        return view('reportsystem::list', compact('reports', 'report', 'submit'));
    }

    public function update($id)
    {
        $input = Request::all();
        $report = Report::findOrFail($id);
        $report->update($input);
        return redirect()->route('report.create');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->route('report.create');
    }
    
    public function data(Request $request) {
        $reports = Report::join('users', 'reports.userid', '=', 'users.id')
            ->select('reports.id', 'users.name', 'reports.userid', 'reports.report_date', 'reports.status','reports.percent_complete','reports.percent_extra','reports.created_at');

        $reports = $reports->get();

        return DataTables::of($reports)
            ->addColumn('action', function ($row) {
                $action = '';
                    $action.= ' <a href="' . route('member.employees.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                      
                      
                $action = '-';

                return $action;

            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn('name', function ($row) {
                return '<a href="' . route('member.report.show', $row->id) . '">' . ucwords($row->name) . '</a>';
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }
    
    public function show($id) {
        if(!$this->user->can('view_employees')){
            abort(403);
        }
        $taskBoardColumn = TaskboardColumn::where('slug', 'completed')->first();

        $this->employeeDocs = EmployeeDocs::where('user_id', '=', $id)->get();
        $this->employee = User::withoutGlobalScope('active')->findOrFail($id);
        $this->taskCompleted = Task::where('user_id', $id)->where('tasks.board_column_id', $taskBoardColumn->id)->count();
        $this->hoursLogged = ProjectTimeLog::where('user_id', $id)->sum('total_hours');
        $this->activities = UserActivity::where('user_id', $id)->orderBy('id', 'desc')->get();
        $this->projects = Project::select('projects.id', 'projects.project_name', 'projects.deadline', 'projects.completion_percent')
            ->join('project_members', 'project_members.project_id', '=', 'projects.id')
            ->where('project_members.user_id', '=', $id)
            ->get();
        return view('member.employees.show', $this->data);
    }

}
