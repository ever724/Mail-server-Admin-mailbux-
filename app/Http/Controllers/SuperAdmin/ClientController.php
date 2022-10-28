<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Console\Commands\SynchronizeInboxClientData;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Client\Store;
use App\Http\Requests\SuperAdmin\Client\Update;
use App\Jobs\CreateMailUser;
use App\Jobs\DeleteClient;
use App\Jobs\UpdateClient;
use App\Models\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(
        Dispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $includePremiumDomain = filter_var($request->input('include_premium_domain', false), FILTER_VALIDATE_BOOL);
        $existsOnMailServer = $request->input('exists_on_mail_server');

        $clientsQuery = Client::query();
        $lastSyncedAt = Carbon::make(Client::query()->max('last_synced_at'));

        if (!$includePremiumDomain) {
            $clientsQuery->where('domain', 'mailbux.com');
        }

        if ($existsOnMailServer == '1') {
            $clientsQuery->where('exists_on_mail_server', true);
        } elseif ($existsOnMailServer == '0') {
            $clientsQuery->where('exists_on_mail_server', false);
        }

        $clients = $clientsQuery->paginate()
            ->appends(request()->query());

        return view(
            'super_admin.clients.index',
            compact('clients', 'lastSyncedAt')
        );
    }

    /**
     * @return RedirectResponse
     */
    public function sync(): RedirectResponse
    {
        Artisan::call(SynchronizeInboxClientData::class);

        session()->flash('alert-success', __('messages.sync_successful'));

        return redirect()->route('super_admin.clients');
    }

    /**
     * @param Request $request
     * @param Client  $client
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Client $client): RedirectResponse
    {
        $success = false;
        $errorMessage = null;

        try {
            $success = $this->dispatcher->dispatchNow(
                new DeleteClient(
                    $client->id
                )
            );
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
        }

        if ($success) {
            session()->flash('alert-success', __('messages.client_deleted'));
        } else {
            session()->flash('alert-error', $errorMessage ?? __('messages.couldnt_delete_client'));
        }

        return redirect()->route('super_admin.clients');
    }

    /**
     * @param Client $client
     *
     * @return View
     */
    public function edit(Client $client): View
    {
        return view('super_admin.clients.edit', [
            'client' => $client,
        ]);
    }

    public function update(
        Update $request,
        Client $client
    ) {
        $data = $request->validated();

        try {
            $this->dispatcher->dispatchNow(
                new UpdateClient(
                    $client,
                    $data['name'],
                    $data['organization'],
                    $data['password'],
                    $data['api_access'] == '1',
                    $data['enabled'] == '1',
                    $data['recovery_email'],
                    $client->language,
                    $client->storagequota_total
                )
            );

            session()->flash('alert-success', 'Mail User Updated');
        } catch (\Exception $e) {
            session()->flash('alert-error', $e->getMessage());
        }

        return redirect()->route('super_admin.clients.edit', [$client]);
    }

    public function create(Request $request)
    {
        $domain = 'mailbux.com'; // TODO: currently hardcoded - add logic if we will support domainadmins here

        return view('super_admin.clients.create', compact('domain'));
    }

    public function store(Store $request)
    {
        $data = $request->all();
        $email = sprintf('%s@%s', $data['username'], $data['domain']);

        try {
            $client = $this->dispatchNow(
                new CreateMailUser(
                    $data['name'],
                    $data['organization'],
                    $data['domain'],
                    $email,
                    $data['password'],
                    $data['api_access'],
                    $data['enabled'],
                    $data['recovery_email']
                )
            );

            session()->flash('alert-success', 'Mail User created');

            return redirect('super_admin.clients.edit', $client);
        } catch (GuzzleException | \Exception $e) {
            session()->flash('alert-error', $e->getMessage());

            return redirect()->back();
        }
    }
}
