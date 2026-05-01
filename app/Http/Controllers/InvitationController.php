<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Invitation\CreateInvitationLoginRequest;
use App\Http\Requests\Invitation\CreateInvitationRegistrationRequest;
use App\Http\Requests\Invitation\CreateInvitationRequest;
use App\Services\InvitationService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InvitationController extends Controller
{
    protected InvitationService $invitationService;
    protected $currentOrganizationId;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
        $this->currentOrganizationId = OrganizationSession::getCurrentOrg();
    }

    public function store(CreateInvitationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->invitationService->createInvitation(auth()->user(), $validatedData, $this->currentOrganizationId);
            $message = "Inviatation {$result} successfully";
            return redirect()->route('members')->with(Constants::SUCCESS, $message);
        } catch (BusinessException $e) {
            return redirect()->back()->withErrors(['email' => $e->getMessage()])->withInput(); // sends to formData.errors
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('members')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function show(string $token)
    {
        try {
            $invitationDetails = $this->invitationService->getInvitation($token);
            $responseData = [
                'token' => $token,
                'invitation' => $invitationDetails,
            ];
            return Inertia::render('Invitation/Accept', $responseData);
        } catch (BusinessException $e) {
            \Log::error($e->getMessage());
            return Inertia::render('Error/InvitationError', [
                'message' => $e->getMessage(),
            ])->toResponse(request())->setStatusCode(403);
        } catch (Exception $e) {
            Log::error('Invitation show error: ' . $e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function accept(Request $request, string $token)
    {
        try {
            $invitationDetails = $this->invitationService->getInvitationDetails($token);
            $hasAccount = $invitationDetails['hasAccount'];
            $invitation = $invitationDetails['invitation'];
            if (!$hasAccount) {
                // Scenario A: No account
                return redirect()->route('invitations.register', ['token' => $token]);
            } elseif ($hasAccount) {
                
                if (!Auth::check()) {
                    // Scenario B: Has Account but not logged in
                    return redirect()->route('invitations.login', ['token' => $token]);
                } elseif (Auth::check()) {
                    if (Auth::user()->email == $invitation->email) {
                        // Scenario C: Has Account and logged in with same account
                        $this->invitationService->acceptInvitation(auth()->user(), $token);
                        return redirect(route('dashboard', absolute: false));
                    } else {
                        // Scenario D: Has Account but logged in with another account
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        
                        return redirect()->route('invitations.login', ['token' => $token])->with('status', 'Please log in with the invited email address.');
                    }
                }
            }
        } catch (BusinessException $e) {
            return back()->withErrors([Constants::ERROR => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Invitation acceptance error: ' . $e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function register(string $token)
    {
        try {
            $invitationDetails = $this->invitationService->getInvitationDetails($token);
            $invitation = $invitationDetails['invitation'];
            
            return Inertia::render('Auth/InvitationRegistration', [
                'token' => $token,
                'email' => $invitation->email,
                'organizationName' => $invitation->organization->name,
            ]);
            
        } catch (BusinessException $e) {
            return redirect()->route('login')->withErrors([Constants::ERROR => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Invitation registration page error: ' . $e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function storeRegistration(CreateInvitationRegistrationRequest $request, string $token)
    {
        try {
            $validatedData = $request->validated();
            $invitationDetails = $this->invitationService->getInvitationDetails($token);
            $this->invitationService->registerInvitedUser($validatedData, $invitationDetails);
            return redirect(route('dashboard', absolute: false));
        } catch (BusinessException $e) {
            return back()->with(Constants::ERROR, $e->getMessage());
        } catch (Exception $e) {
            Log::error('Invitation registration error: ' . $e->getMessage());
            return back()->with([Constants::ERROR => Constants::DEFAULTMESSAGE]);
        }
    }

    public function login(string $token)
    {
        try {
            $invitationDetails = $this->invitationService->getInvitationDetails($token);
            $invitation = $invitationDetails['invitation'];
            
            return Inertia::render('Auth/InvitationLogin', [
                'token' => $token,
                'email' => $invitation->email,
                'organizationName' => $invitation->organization->name,
            ]);
            
        } catch (BusinessException $e) {
            return redirect()->route('login')->withErrors([Constants::ERROR => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Invitation login page error: ' . $e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function storeLogin(CreateInvitationLoginRequest $request, $token)
    {
        try {
            $validatedData = $request->validated();
            $invitationDetails = $this->invitationService->getInvitationDetails($token);
            if (! Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password'],], $validatedData['remember'] ?? false)) {
                throw new BusinessException('Invalid credentials.');
            }
            $request->session()->regenerate();
            $user = Auth::user();
            
            $this->invitationService->loginInvitedUser($user, $invitationDetails);
            return redirect(route('dashboard', absolute: false));
        } catch (BusinessException $e) {            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with(Constants::ERROR, $e->getMessage());
        } catch (Exception $e) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::error('Invitation Login error: ' . $e->getMessage());
            return back()->with([Constants::ERROR => Constants::DEFAULTMESSAGE]);
        }
    }
}
