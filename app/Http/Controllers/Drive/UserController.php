<?php

namespace App\Http\Controllers\Drive;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\Core\Result;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Contracts\ICompanyRepository;
use App\Http\Resources\UserCollection;
use Auth;

class UserController extends Controller
{
   
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	/**
	 * @var ICompanyRepository
	 */
	private $companyRepository;

	/**
	 * UserController constructor.
	 * @param IUserRepository $userRepository
	 */
	public function __construct(IUserRepository $userRepository,ICompanyRepository $companyRepository)
	{
	    $this->userRepository = $userRepository;
	    $this->companyRepository = $companyRepository;
	}

	/**
     * Create a newly created user in db.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   	public function registerUser(Request $request){
   		$validate = validator($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'company_name' => 'required',            
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                403
            );
        }

        $request->request->add(['is_parent' => '1']);
        $request->request->add(['phone' => '']);

        $results = $this->userRepository->create($request->all());

        $result_data=$results->getData();
        $result_data->company_name= $request->company_name;
        $company_result=$this->companyRepository->create($result_data);      

        return $this->sendResponse(
            $results->getMessage(),
            new UserCollection($result_data)
        );
   	}

   	/**
     * Logout User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   	public function logout(Request $request){
   		if (Auth::check())
   		{

	        Auth::user()->AauthAcessToken()->delete();
	       	return $this->sendResponse(
                'Logout successfully',
                null,
                201
            );

	    }
	    else
	    {
	    	return $this->sendResponse(
                "No Logged in user!",
                null,
                201
            );
	    }
   	}
}
