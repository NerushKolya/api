<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Support\Facades\Auth;
use Validator;


class NotesController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $user = Auth::user();

        $input['user_id'] = $user->id;
        $note = Note::create($input);
        return response()->json(['status' => 1, 'data' => ['note_id' => $note->id]], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            Note::findOrFail($id)->update($request->all());
            return response()->json(['status' => 1, 'data' => ['note_id' => $request->id]], 200);
        } catch (\Exception  $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 401);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $user = Auth::user();
        $notes = Note::where('user_id', $user->id)->get(['id', 'title', 'description'])->toArray();
        return response()->json(['status' => 1, 'data' => ['note_list' => $notes]], 200);
    }

}
