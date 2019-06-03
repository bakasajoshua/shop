<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserType;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     **/
    public function index()
    {
        $columns = $this->_columnBuilder(['#','Full Names','Email Address','Account Type','Last Access','Action']);
        $row = "";

        $users = User::select('users.*','user_types.user_type')->join('user_types', 'user_types.id', '=', 'users.user_type_id')->where('users.user_type_id', '<>', 5)->where('email', '!=', 'rufus.nyaga@ken.aphl.org')->get();

        foreach ($users as $key => $value) {
            $id = md5($value->id);
            $passreset = url("user/passwordReset/$id");
            $statusChange = url("user/status/$id");
            $delete = url("user/delete/$id");
            $row .= '<tr>';
            $row .= '<td>'.($key+1).'</td>';
            $row .= '<td>'.$value->full_name.'</td>';
            $row .= '<td>'.$value->email.'</td>';
            $row .= '<td>'.$value->user_type.'</td>';
            $row .= '<td>'.gmdate('l, d F Y', strtotime($value->last_access)).'</td>';
            $row .= '<td><a href="'.$passreset.'">Reset Password</a> | <a href="'.$statusChange.'">Delete</a> | <a href="'.url('user/'.$value->id).'">Edit</a></td>';
            $row .= '</tr>';
        }

        return view('tables.display', compact('columns','row'))->with('pageTitle', 'Users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accounts = UserType::whereNull('deleted_at')->where('id', '<>', 5)->get();

        return view('forms.users', compact('accounts'))->with('pageTitle', 'Add User');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', '=', $request->email)->count() > 0) {
            session(['toast_message'=>'User already exists', 'toast_error'=>1]);
            return redirect()->route('user.add');
        } else {
            $user = factory(User::class, 1)->create([
                        'user_type_id' => $request->user_type,
                        'lab_id' => auth()->user()->lab_id,
                        'surname' => $request->surname,
                        'oname' => $request->oname,
                        'email' => $request->email,
                        'password' => $request->password
                        ,
                        // 'telephone' => $request->telephone,
                    ]);
            session(['toast_message'=>'User created succesfully']);

            if ($request->submit_type == 'release')
                return redirect()->route('users');

            if ($request->submit_type == 'add')
                return redirect()->route('user.add');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {

        $accounts = UserType::whereNull('deleted_at')->where('id', '<>', 5)->get();

        return view('forms.users', compact('accounts', 'user'))->with('pageTitle', 'Add User');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->input('password') == "") { // No password for edit
            $userData = $request->only(['user_type','email','surname','oname','telephone']);
            $userData['user_type_id'] = $userData['user_type'];
            unset($userData['user_type']);
            
            $user = User::find($id);
            $user->fill($userData);
            $user->save();
        } else {
            $user = self::__unHashUser($id);

            if (!empty($user)) {
                $user->password = $request->password;
                $user->update();
                session(['toast_message'=>'User password succesfully updated']);
            } else {
                session(['toast_message'=>'User password succesfully updated','toast_error'=>1]);
            }
        }
                
        if (isset($request->user)) {
            return back();
        } else {
            return redirect()->route('users');
        }      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function delete($id) {
        $user = self::__unHashUser($id);
        $user->delete();

        return back();
    }

    public function activity($user_id = null, $year = null, $month = null) {
        if ($year==null || $year=='null'){
            if (session('activityYear')==null)
                session(['activityYear' => Date('Y')]);
        } else {
            session(['activityYear'=>$year]);
        }

        if ($month==null || $month=='null'){
            session()->forget('activityMonth');
        } else {
            session(['activityMonth'=>(strlen($month)==1) ? '0'.$month : $month]);
        }

        $year = session('activityYear');
        $month = session('activityMonth');
        $monthName = "";
        
        if (null !== $month) 
            $monthName = "- ".date("F", mktime(null, null, null, $month));

        $data = (object)['year'=>$year,'monthName'=>$monthName, 'month'=>$month];
        // dd($data);
        // if (isset($user_id)) {
        //     $users = User::whereNotIn('user_type_id', [2,5,6])->get();
        //     return view('users.user-activity', compact('users'))->with('pageTitle', 'Users Activity');
        // } else {
        $users = User::whereNotIn('user_type_id', [2,5,6])->get();
        return view('tables.users-activity', compact('users'), compact('data'))->with('pageTitle', 'Users Activity');
        // }
    }

    public function switch_user($id)
    {
        $this->auth_user(0);
        $user = User::findOrFail($id);
        Auth::logout();
        Auth::login($user);
        return back();
    }

    public function passwordreset($id = null)
    {
        $user = null;
        if (null == $id) {
            $user = 'personal';
            return view('forms.passwordReset', compact('user'))->with('pageTitle', 'Password Reset');
        } else {
            $user = self::__unHashUser($id);
            return view('forms.passwordReset', compact('user'))->with('pageTitle', 'Password Reset');
        }
    }

    private static function __unHashUser($hashed){
        $user = [];
        foreach (User::get() as $key => $value) {
            if ($hashed == md5($value->id)) {
                $user = $value;
                break;
            }
        }

        return $user;
    }
}
