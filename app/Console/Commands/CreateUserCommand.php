<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:Create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name']=$this->ask(' Name of a new user');
        $user['email']=$this->ask(' Email of a new user');
        $user['password']=$this->secret('Password of a new user');
        $roleName=$this ->choice('Role of the new user',['admin','editor'],1);

        $role=Role::where('name',$roleName)->first();
        if(! $role)
        {
            $this->error('Role not found');
            return -1;  
        }

        $validator=Validator::make($user,[
            'name'=>['required','string','max:255'],
            'email'=>['required','string','email','max:255','uniqid:'.User::class],
            'password'=>['required','string',Password::default()],
        ]);
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $error ){
                $this->error($error);
            }
            return-1;
        }

        DB::transaction(function()use($user,$role){

            $user['password']=Hash::make($user['password']);
            $newUser=User::create($user);
            $newUser->roles()->attach($role->id);
    
        });
       
        $this-> info('User '.$user['email'].' cerate successfully');
    }
}
