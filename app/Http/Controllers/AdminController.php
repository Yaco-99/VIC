<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Formation;
use App\Job;
use App\Housing;
use App\HousingGallery;
use App\Agenda;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    //Default view
    public function index()
    {
        return view('layouts.baseadmin');
    }

    public function agenda($page){
        $agendas = Agenda::skip((int)($page-1)*20)->take(20)->get();
        foreach($agendas as $user){
            $username = User::where('id',$user->user_id)->get();
            $user->user_id = $username[0]->first_name . " " . $username[0]->last_name;
        }
        foreach($agendas as $user){
            $username = User::where('id',$user->follower_id)->get();
            if(sizeof($username) != 0) $user->follower_id = $username[0]->first_name . " " . $username[0]->last_name;
        }
        return view('admin.agenda.agenda',compact('agendas'));
    }

    public function agendaCreateView(){
        $usersList = User::where('role', 0)->get()->toArray();
        $users = [];
        foreach($usersList as $user){
            $user = (object) array("id"=> $user['id'],"name"=>$user['first_name'].' '.$user['last_name']);
            array_push($users,$user);
        }
        return view('admin.agenda.agenda_create',compact('users'))->with('updated',false);
    }

    //Housing
    public function logement($page){
        $logements = Housing::skip((int)($page-1)*20)->take(20)->get();
        return view('admin.housing.housing',compact('logements'));
        
    }

    public function logementCreateView(){
        return view('admin.housing.housing_create')->with('updated',false);
    }

    public function logementEditView($id){
        $logement = Housing::findOrFail($id);
        $images = HousingGallery::where('housing_id',$id)->get();
        return view('admin.housing.housing_edit',compact('logement','images'))->with('updated',false);
    }

    public function logementDeleteView($id){
        $housing = Housing::findOrFail($id);
        return view('admin.housing.housing_delete_confirm',compact('housing'))->with('view',true);
    }

    public function logementDelete($id){
        $housing = Housing::find($id);
        $housingGallerys = HousingGallery::where('housing_id',$id)->get();
        foreach($housingGallerys as $housingGallery){
            Storage::delete(str_replace('storage/','',$housingGallery->img_link));
        }
        HousingGallery::where('housing_id',$id)->delete();
        $housing->delete();

        return redirect('/admin/logement/list/1');
    }

    public function logementEdit(Request $request, $id){
        $request->validate([
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validRequest = $request->validate([
            'location' => ['required', 'string', 'max:255'],
            'area' => ['nullable'],
            'type' => ['required', 'string', 'max:255'],
            'price' => ['required'],
            'availability' => ['nullable'],
            'email' => ['required', 'string', 'max:255'],
            'phone' => ['nullable'],
            'description' => ['required', 'string'],
            'proximity' => ['string','max:255']
        ]);

        

        $housing = Housing::findOrFail($id);
        $housing->location = $validRequest['location'];
        $housing->area = $validRequest['area'];
        $housing->type = $validRequest['type'];
        $housing->price = $validRequest['price'];
        $housing->availability = $validRequest['availability'];
        $housing->email = $validRequest['email'];
        $housing->phone = $validRequest['phone'];
        $housing->description = $validRequest['description'];
        $housing->proximity = $validRequest['proximity'];
        $housing->save();

        if ($request->file('file')) {
            $imagePath = $request->file('file');
            $imageName = $imagePath->getClientOriginalName();

            $path = $request->file('file')->storeAs('img/housing', $imageName, 'public');
            $path = 'storage/'.$path;
            HousingGallery::create([
                'housing_id' => $id,
                'img_link' => $path
            ]);
        }

        $logement = $housing;
        $images = HousingGallery::where('housing_id',$id)->get();
        return view('admin.housing.housing_edit',compact('logement','images'))->with('updated',true);
        
    }

    public function logementImage(Request $request,$id){
        $image = HousingGallery::find($id);
        $image->delete();
        $url = "/admin/logement/edit/".$request['id'];
        return redirect($url);
    }

    public function logementCreate(Request $request){
        

        $validRequest = $request->validate([
            'location' => ['required', 'string', 'max:255'],
            'area' => ['nullable'],
            'type' => ['required', 'string', 'max:255'],
            'price' => ['required'],
            'availability' => ['nullable'],
            'email' => ['required', 'string', 'max:255'],
            'phone' => ['nullable'],
            'description' => ['required', 'string'],
            'proximity' => ['string','max:255']
        ]);

        Housing::create([
            'location' => $validRequest['location'],
            'area' => $validRequest['area'],
            'type' => $validRequest['type'],
            'price' => $validRequest['price'],
            'availability' => $validRequest['availability'],
            'email' => $validRequest['email'],
            'phone' => $validRequest['phone'],
            'description' => $validRequest['description'],
            'proximity' => $validRequest['proximity']
        ]);

        return view('admin.housing.housing_create')->with('updated',true);
    }

    //formation
    public function formation($page){
        $formations = Formation::skip((int)($page-1)*20)->take(20)->get();
        return view('admin.formation.formation',compact('formations'));
    }

    public function formationCreateView(){
        return view('admin.formation.formation_create')->with('updated',false);
    }

    public function formationEditView($id){
        $formation = Formation::findOrFail($id);
        return view('admin.formation.formation_edit',compact('formation'))->with('updated',false);
    }

    public function formationDeleteView($id){
        $formation = Formation::findOrFail($id);
        return view('admin.formation.formation_delete_confirm',compact('formation'))->with('view',true);
    }

    public function formationDelete($id){
        $formation = Formation::find($id);
        $formation->delete();

        return redirect('/admin/formation/list/1');
    }

    public function formationCreate(Request $request){
        $validRequest = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'location' => ['nullable'],
            'time' => ['nullable'],
            'phone' => ['nullable'],
            'proximity' => ['string','max:255']
        ]);

        Formation::create([
            'title' => $validRequest['title'],
            'description' => $validRequest['description'],
            'email' => $validRequest['email'],
            'location' => $validRequest['location'],
            'time' => $validRequest['time'],
            'phone' => $validRequest['phone'],
            'proximity' => $validRequest['proximity'],
        ]);

        return view('admin.formation.formation_create')->with('updated',true);
    }

    public function formationEdit(Request $request, $id){
        $validRequest = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'location' => ['nullable'],
            'time' => ['nullable'],
            'phone' => ['nullable'],
            'proximity' => ['string','max:255']
        ]);

        $formation = Formation::find($id);
        $formation->title = $validRequest['title'];
        $formation->description = $validRequest['description'];
        $formation->email = $validRequest['email'];
        $formation->location = $validRequest['location'];
        $formation->time = $validRequest['time'];
        $formation->phone = $validRequest['phone'];
        $formation->proximity = $validRequest['proximity'];
        $formation->save();

        return view('admin.formation.formation_edit',compact('formation'))->with('updated',true);
    }

    //job
    public function job($page){
        $jobs = Job::skip((int)($page-1)*20)->take(20)->get();
        return view('admin.job.job',compact('jobs'));
    }

    public function jobCreateView(){
        return view('admin.job.job_create')->with('updated',false);
    }

    public function jobEditView($id){
        $job = Job::findOrFail($id);
        return view('admin.job.job_edit',compact('job'))->with('updated',false);
    }

    public function jobDeleteView($id){
        $job = Job::findOrFail($id);
        return view('admin.job.job_delete_confirm',compact('job'))->with('view',true);
    }

    public function jobDelete($id){
        $job = Job::find($id);
        $job->delete();

        return redirect('/admin/job/list/1');
    }

    public function jobCreate(Request $request){
        $validRequest = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'location' => ['nullable'],
            'deadline' => ['nullable'],
            'type' => ['nullable'],
            'skills_needed' => ['nullable'],
            'company' => ['nullable'],
            'contact_people' => ['nullable'],
            'proximity' => ['nullable'],
        ]);

        Job::create([
            'title' => $validRequest['title'],
            'description' => $validRequest['description'],
            'email' => $validRequest['email'],
            'location' => $validRequest['location'],
            'deadline' => $validRequest['deadline'],
            'type' => $validRequest['type'],
            'skills_needed' => $validRequest['skills_needed'],
            'company' => $validRequest['company'],
            'contact_people' => $validRequest['contact_people'],
            'proximity' => $validRequest['proximity']
        ]);

        return view('admin.job.job_create')->with('updated',true);
    }

    public function jobEdit(Request $request, $id){
        $validRequest = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'location' => ['nullable'],
            'deadline' => ['nullable'],
            'type' => ['nullable'],
            'skills_needed' => ['nullable'],
            'company' => ['nullable'],
            'contact_people' => ['nullable'],
            'proximity' => ['nullable']
        ]);

        $job = Job::find($id);
        $job->title = $validRequest['title'];
        $job->description = $validRequest['description'];
        $job->email = $validRequest['email'];
        $job->location = $validRequest['location'];
        $job->deadline = $validRequest['deadline'];
        $job->type = $validRequest['type'];
        $job->skills_needed = $validRequest['skills_needed'];
        $job->company = $validRequest['company'];
        $job->contact_people = $validRequest['contact_people'];
        $job->proximity = $validRequest['proximity'];
        $job->save();
        
        return view('admin.job.job_edit',compact('job'))->with('updated',true);
    }

    //User
    public function user($page)
    {
        $users = User::skip((int)($page-1)*20)->take(20)->get();
        return view('admin.user.user',compact('users'));
    }

    public function userCreateView()
    {   
        return view('admin.user.user_create')->with('updated',false);
    }

    public function userEditView($id)
    {   
        $user = User::findOrFail($id);
        $coachs = User::where('role',1)->get();
        $sponsors = User::where('role',2)->get();
        return view('admin.user.user_edit',compact('user','coachs','sponsors'))->with('updated',false);
    }

    public function userEdit(Request $request, $id)
    {   
        $validRequest = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required','string','max:255'],
            'role' => ['required','integer'],
            'location' => ['nullable','max:255'],
            'birth_date' => ['nullable'],
            'cpas_status' => ['nullable','max:255'],
            'description' => ['nullable'],
            'sponsor_id' => ['nullable'],
            'coach_id' => ['nullable']
        ]);
        
        $user = User::find($id);
        $user->first_name = $validRequest['first_name'];
        $user->last_name = $validRequest['last_name'];
        $user->email = $validRequest['email'];
        $user->role = $validRequest['role'];
        $user->location = $validRequest['location'];
        $user->birth_date = $validRequest['birth_date'];
        $user->cpas_status = $validRequest['cpas_status'];
        $user->description = $validRequest['description'];
        $user->sponsor_id = $validRequest['sponsor_id'];
        $user->coach_id = $validRequest['coach_id'];

        $user->save();
        $coachs = User::where('role',1)->get();
        $sponsors = User::where('role',2)->get();
        return view('admin.user.user_edit',compact('user','coachs','sponsors'))->with('updated',true);
    }

    public function userCreate(Request $request)
    {
        $validRequest = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'first_name' => $validRequest['first_name'],
            'last_name' => $validRequest['last_name'],
            'email' => $validRequest['email'],
            'password' => Hash::make($validRequest['password']),
            'first_login' => true,
            'role' => 0,
        ]);

        return view('admin.user.user_create')->with('updated',true);
    }

    public function userDeleteView($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.user_delete_confirm',compact('user'))->with('view',true);
    }

    public function userDelete(Request $request, $id){
        $user = User::find($id);
        $user->delete();

        return redirect('/admin/user/list/1');
    }
}