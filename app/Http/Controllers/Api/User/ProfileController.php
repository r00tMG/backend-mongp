<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $profiles = Profile::where('user_id', Auth::id())->get();
        //dd(new ProfileResource($profiles));
        return \response()->json([
            'error' => false,
            'message' => "Votre requête a bien réussie",
            'profiles' =>  ProfileResource::collection($profiles)
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*address
    hobbies
    job
    skill*/
    public function store(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'address' => ['required','string','min:3'],
            'hobbies' => ['required','string','min:3'],
            'job' => ['required','string','min:2'],
            'skill' => ['required','string','min:2']
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'error' => true,
                'message' => 'Validation des données échouée',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $profile = Profile::create([
            'address' => $request->address,
            'hobbies' => $request->hobbies,
            'job' => $request->job,
            'skill' => $request->skill,
            'user_id' => Auth::id()
        ]);
        //dd($request->all());

        return response()->json([
            'error' => false,
            'message' => "Votre profile a été bien créé",
            'profile' => new ProfileResource($profile)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Profile $profile)
    {
        //$profiles = Profile::find('user_id', 19)->get();
        //dd(new ProfileResource($profiles));
        return \response()->json([
            'error' => false,
            'message' => "Votre requête a bien réussie",
            'profiles' =>   ProfileResource::collection($profile->where('user_id',Auth::id())->get())
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Profile $profile)
    {
        $validator = Validator::make($request->all(), [
            'address' => ['required','string','min:3'],
            'hobbies' => ['required','string','min:3'],
            'job' => ['required','string','min:2'],
            'skill' => ['required','string','min:2']
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'error' => true,
                'message' => 'Validation des données échouée',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $profile->update([
            'address' => $request->address,
            'hobbies' => $request->hobbies,
            'job' => $request->job,
            'skill' => $request->skill,
            'user_id' => Auth::id()
        ]);
        //dd($request->all());

        return response()->json([
            'error' => false,
            'message' => "Votre profile a été bien créé",
            'profile' => new ProfileResource($profile)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
