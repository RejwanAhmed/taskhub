<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Invitation\CreateInvitationRequest;
use App\Services\InvitationService;
// use Illuminate\Http\Request;

class InvitationController extends Controller
{
    protected InvitationService $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function store(CreateInvitationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->invitationService->createInvitation(auth()->user(), $validatedData);
            $message = "Inviatation {$result} successfully";
            return redirect()->route('organizations.member')->with(Constants::SUCCESS, $message);
        } catch (BusinessException $e) {
            return redirect()->back()->withErrors(['email' => $e->getMessage()])->withInput(); // sends to formData.errors
        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('organizations.member')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }
}
